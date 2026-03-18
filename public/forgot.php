<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/utils.php';

// PHPMailer (composer)
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$msg = '';
$input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['login'] ?? '');

    if ($input === '') {
        $error = 'Escribe tu usuario o tu correo.';
    } else {
        // Buscar por nombre o correo
        $stmt = $pdo->prepare("SELECT ID, NOMBRE, CORREO FROM USUARIO WHERE NOMBRE = ? OR CORREO = ?");
        $stmt->execute([$input, $input]);
        $u = $stmt->fetch();

        // Mensaje genérico si no existe (para no filtrar usuarios)
        if (!$u || empty($u['CORREO'])) {
            $msg = 'Si los datos existen, se enviará un correo de recuperación.';
        } else {
            // Token + caducidad (30 min)
            $token = bin2hex(random_bytes(16)); // 32 hex chars
            $expira = date('Y-m-d H:i:s', time() + 30 * 60);

            $stmt = $pdo->prepare("UPDATE USUARIO SET RESET_TOKEN = ?, RESET_EXPIRA = ? WHERE ID = ?");
            $stmt->execute([$token, $expira, $u['ID']]);

            // Construir enlace correcto con host y puerto actual
            $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST'];

            $link = $base . dirname($_SERVER['PHP_SELF']) . "/reset.php?token=" . urlencode($token);

            // Cargar config SMTP
            $cfgPath = __DIR__ . '/../app/mail_config.php';
            if (!file_exists($cfgPath)) {
                $error = 'Falta app/mail_config.php (config SMTP).';
            } else {
                $cfg = require $cfgPath;

                // Enviar correo
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $cfg['host'];
                    $mail->Port = (int)$cfg['port'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $cfg['username'];
                    $mail->Password = $cfg['password'];
                    $mail->SMTPSecure = $cfg['secure']; // 'tls'
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom($cfg['from_email'], $cfg['from_name']);
                    $mail->addAddress($u['CORREO'], $u['NOMBRE']);

                    $mail->Subject = 'Recuperación de contraseña - PharmaSphere';
                    $mail->Body = "Hola {$u['NOMBRE']},\n\n"
                        . "Has solicitado cambiar tu contraseña.\n"
                        . "Abre este enlace (caduca en 30 minutos):\n\n"
                        . $link . "\n\n"
                        . "Si no lo pediste tú, ignora este correo.\n";

                    $mail->send();
                    $msg = 'Si los datos existen, se enviará un correo de recuperación.';
                } catch (Exception $e) {
                    $error = 'No se pudo enviar el correo. Revisa SMTP.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSphere - Recuperar contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="media/pharmasphere_sinfondo.png" type="image/png" sizes="512x512">
</head>
<body>

    <div class="login-wrapper">
        <div class="brand-section">
            <div class="logo-container">
                <img src="media/pharmasphere_sinfondo.png" alt="Logo PharmaSphere">
            </div>
            <h1 class="brand-title">PharmaSphere</h1>
            <p class="brand-subtitle">Sistema de Gestión Farmacéutica</p>
        </div>

        <div class="login-card">
            <div class="card-header">
                <h2 class="card-title">Recuperar contraseña</h2>
                <p class="card-subtitle">Escribe tu usuario o correo</p>
            </div>

            <?php if ($error): ?>
                <div class="error-banner">
                    <span><?= h($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($msg): ?>
                <div class="error-banner" style="background:#dcfce7;border-color:#bbf7d0;color:#166534;">
                    <span><?= h($msg) ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label for="login">Usuario o email</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon-left"></i>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            class="form-input"
                            value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                            placeholder="Ej. admin o correo@gmail.com"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Enviar correo de recuperación
                </button>
            </form>

            <p style="margin-top:1rem; text-align:center;">
                <a href="login.php">Volver al login</a>
            </p>
        </div>
    </div>

</body>
</html>