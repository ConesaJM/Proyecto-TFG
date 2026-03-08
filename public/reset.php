<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/utils.php';

$error = '';
$msg = '';
$token = trim($_GET['token'] ?? '');

// Si no viene token, no se puede continuar
if ($token === '') {
    $error = 'Enlace no válido.';
} else {
    // Buscar el usuario que tenga ese token
    $stmt = $pdo->prepare("SELECT ID, NOMBRE, RESET_EXPIRA FROM USUARIO WHERE RESET_TOKEN = ?");
    $stmt->execute([$token]);
    $u = $stmt->fetch();

    if (!$u) {
        $error = 'El enlace no es válido.';
    } else {
        // Comprobar si el token ha caducado
        if (empty($u['RESET_EXPIRA']) || strtotime($u['RESET_EXPIRA']) < time()) {
            $error = 'El enlace ha caducado. Pide otro nuevo.';
        }
    }
}

// Si se envía el formulario y no hay errores previos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === '') {
    $password1 = trim($_POST['password1'] ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    // Validar campos vacíos
    if ($password1 === '' || $password2 === '') {
        $error = 'Por favor, rellene todos los campos.';
    } elseif ($password1 !== $password2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Cifrar nueva contraseña
        $hash = password_hash($password1, PASSWORD_DEFAULT);

        // Guardar nueva contraseña y borrar token
        $stmt = $pdo->prepare(
            "UPDATE USUARIO 
             SET CONTRASENHIA = ?, RESET_TOKEN = NULL, RESET_EXPIRA = NULL
             WHERE ID = ?"
        );
        $stmt->execute([$hash, $u['ID']]);

        // Redirigir al login después de cambiar la contraseña
        header('Location: login.php?reset=ok');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSphere - Restablecer contraseña</title>
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
                <h2 class="card-title">Nueva contraseña</h2>
                <p class="card-subtitle">Escribe tu nueva contraseña</p>
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

                <p style="margin-top:1rem; text-align:center;">
                    <a href="login.php">Volver al login</a>
                </p>
            <?php endif; ?>

            <?php if (!$msg && !$error): ?>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="password1">Nueva contraseña</label>
                        <div class="input-group">
                            <i class="fa-solid fa-lock input-icon-left"></i>
                            <input
                                type="password"
                                id="password1"
                                name="password1"
                                class="form-input"
                                placeholder="••••••••"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password2">Repite la contraseña</label>
                        <div class="input-group">
                            <i class="fa-solid fa-lock input-icon-left"></i>
                            <input
                                type="password"
                                id="password2"
                                name="password2"
                                class="form-input"
                                placeholder="••••••••"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-key"></i> Cambiar contraseña
                    </button>
                </form>
            <?php endif; ?>

            <?php if (!$msg): ?>
                <p style="margin-top:1rem; text-align:center;">
                    <a href="login.php">Volver al login</a>
                </p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>