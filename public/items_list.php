<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/style.php'; // (3º: Carga los estilos CSS)
require_once __DIR__ . '/../app/utils.php'; // (4º: Carga nuestras funciones)
require_once __DIR__ . '/../app/csrf.php'; // (5º: CSRF PROTECCION POR TOKEN)

// 2. PROTECCIÓN DE LA PÁGINA 
// Esta función de auth.php comprobará si hay una sesión iniciada.
// Si no la hay, redirige a login.php y el script muere aquí.
require_login(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mostrar mensaje de éxito si viene en la URL
if (isset($_GET['exito'])) { // 1. Primero, comprueba si 'exito' existe

    // 2. Si existe, comprueba qué valor tiene
    if ($_GET['exito'] === 'guardado') {
        echo '<div class="alert success">Producto guardado correctamente.</div>';
    } elseif ($_GET['exito'] === 'borrado') {
        // Ahora sí compara el valor de 'exito' con 'borrado'
        echo '<div class="alert error">Producto borrado correctamente.</div>';
    }
}

// ====================================================================
// --- LÓGICA DE CHAT CON MEMORIA (CONTEXTO INYECTADO) ---
// ====================================================================
$q = trim($_GET['q'] ?? '');

// Inicializar Historial
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Botón para borrar chat 
if (isset($_GET['action']) && $_GET['action'] === 'clear_chat') {
    $_SESSION['chat_history'] = [];
    header('Location: items_list.php'); 
    exit;
}

// Procesar mensaje nuevo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensaje_chat'])) {
    
    $textoUsuario = trim($_POST['mensaje_chat']);
    $partesMensaje = [];
    $infoInventario = "";
    
    // A. PROCESAMIENTO DE IMAGEN
    if (isset($_FILES['imagen_chat']) && $_FILES['imagen_chat']['error'] === 0) {
        $rutaTemporal = $_FILES['imagen_chat']['tmp_name'];
        $info = getimagesize($rutaTemporal);
        if ($info) {
            $dataImg = base64_encode(file_get_contents($rutaTemporal));
            $partesMensaje[] = ["inline_data" => ["mime_type" => $info['mime'], "data" => $dataImg]];
        }
    }
    
    if (!empty($textoUsuario)) {
        
        // --- BÚSQUEDA RÁPIDA EN LA BASE DE DATOS (SIN LLAMAR A LA IA AÚN) ---
        $palabras = str_word_count(strtolower($textoUsuario), 1, 'áéíóúüñ0123456789');
        $palabrasIgnorar = ['hola', 'que', 'precio', 'tiene', 'teneis', 'hay', 'stock', 'cuanto', 'cuesta', 'el', 'la', 'los', 'las', 'un', 'una', 'para', 'sirve', 'medicamento', 'farmacia', 'pharmasphere'];
        
        $productosEncontrados = [];
        
        foreach ($palabras as $palabra) {
            if (strlen($palabra) >= 3 && !in_array($palabra, $palabrasIgnorar)) {
                $resultados = listarProductos($pdo, $palabra, 3);
                if (!empty($resultados)) {
                    $productosEncontrados = array_merge($productosEncontrados, $resultados);
                }
            }
        }
        
        // Eliminamos duplicados por ID
        $productosUnicos = [];
        foreach ($productosEncontrados as $p) {
            $productosUnicos[$p['ID']] = $p;
        }

        // --- CONSTRUIMOS EL CONTEXTO INVISIBLE PARA LA IA ---
        if (!empty($productosUnicos)) {
            $infoInventario .= "\n\n[CONTEXTO DEL SISTEMA (No lo leas textualmente, úsalo para responder)]: El usuario parece preguntar por productos. Aquí tienes la información actual de la base de datos relacionada con su mensaje:\n";
            foreach ($productosUnicos as $p) {
                $estadoStock = ($p['STOCK_DISPONIBLE'] > 0) ? "En stock ({$p['STOCK_DISPONIBLE']} unidades)" : "Agotado";
                $receta = $p['RECETA'] ? "Requiere receta médica" : "Venta libre";
                $infoInventario .= "- {$p['NOMBRE']} | Precio: {$p['PRECIO']}€ | Estado: $estadoStock | Info: $receta.\n";
            }
        } else {
            if (strpos(strtolower($textoUsuario), 'catalogo') !== false || strpos(strtolower($textoUsuario), 'que tienes') !== false || strpos(strtolower($textoUsuario), 'productos') !== false) {
                 $stmt = $pdo->query("SELECT NOMBRE, PRECIO FROM PRODUCTO WHERE STOCK_DISPONIBLE > 0 ORDER BY RAND() LIMIT 4");
                 $ejemplos = $stmt->fetchAll();
                 if ($ejemplos) {
                     $infoInventario .= "\n\n[CONTEXTO DEL SISTEMA]: El usuario pregunta por el catálogo general. Dile que tenemos muchos productos, por ejemplo:\n";
                     foreach ($ejemplos as $p) {
                         $infoInventario .= "- {$p['NOMBRE']} ({$p['PRECIO']}€)\n";
                     }
                 }
            }
        }
        
        $partesMensaje[] = ["text" => $textoUsuario . $infoInventario];
    }

    // --- ENVIAMOS EL MENSAJE A LA IA ---
    if (!empty($partesMensaje)) {
        
        $_SESSION['chat_history'][] = [
            "role" => "user",
            "parts" => [["text" => $textoUsuario]]
        ];

        $historialParaEnviar = $_SESSION['chat_history'];
        $ultimoIndice = count($historialParaEnviar) - 1;
        $historialParaEnviar[$ultimoIndice]['parts'] = $partesMensaje;

        $instruccionSistema = "[INSTRUCCIÓN: Eres el asistente de Pharmasphere. Responde siempre de forma amable. Si el sistema te proporciona datos de stock, úsalos. Responde con HTML simple para formato (negritas, listas)]\n\n";
        
        foreach ($historialParaEnviar[$ultimoIndice]['parts'] as &$part) {
             if (isset($part['text'])) {
                 $part['text'] = $instruccionSistema . $part['text'];
                 break;
             }
        }

        $respuestaTexto = enviarChatGemini($historialParaEnviar);

        $_SESSION['chat_history'][] = [
            "role" => "model",
            "parts" => [["text" => $respuestaTexto]]
        ];
    }
}
// ====================================================================

// 3. MOSTRAR LA PÁGINA
// Si el script llega aquí, el usuario SÍ está logueado.
// Con la función headerHtml() mostramos el HTML creado dentro de utils.php
headerHtml('');
?>

<?php
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

// Productos por página
$limit = 10;
$offset = ($page - 1) * $limit;

// Si no hay texto de busqueda, pasamos null a listarProductos
$buscar = ($q === '') ? null : $q;

// Llamamos productos con función utils.php
$productos = listarProductos($pdo, $buscar, $limit, $offset);

// Contar cuántos hay en total (para la paginación)
if ($buscar !== null) {
    $sqlCount = "SELECT COUNT(*) AS total FROM PRODUCTO WHERE NOMBRE LIKE ?";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute(["%{$buscar}%"]);
} else {
    $sqlCount = "SELECT COUNT(*) AS total FROM PRODUCTO";
    $stmtCount = $pdo->query($sqlCount);
}

$filaCount = $stmtCount->fetch();
$total = (int)($filaCount['total'] ?? 0);

$total_pages = ($total > 0 && $limit > 0)
    ? (int)ceil($total / $limit)
    : 1;
?>

<div class="chat-container" style="margin-top: 20px;">
    <div class="chat-header">
        <span><i class="fa-solid fa-robot"></i> HelpSphere</span>
        <a href="items_list.php?action=clear_chat" style="color: white; font-size: 0.85rem; text-decoration: underline;">
            <i class="fa-solid fa-trash"></i> Borrar Chat
        </a>
    </div>

    <div class="chat-window" id="cajaChat">
        <?php if (empty($_SESSION['chat_history'])): ?>
            <div style="text-align:center; color: var(--color-texto-muted); margin-top: 50px;">
                <i class="fa-solid fa-hand-holding-medical fa-3x" style="margin-bottom: 15px; display:block;"></i>
                <p>¡Hola! Soy tu asistente. Sube una foto o hazme una pregunta.</p>
            </div>
        <?php else: ?>
            <?php foreach ($_SESSION['chat_history'] as $msg): ?>
                <div class="msg <?= ($msg['role'] === 'user') ? 'msg-user' : 'msg-model' ?>">
                    <?php if (isset($msg['parts'][0]['inline_data'])): ?>
                        <div style="font-size:0.8em; opacity:0.8; margin-bottom:5px;">
                            <i class="fa-solid fa-image"></i> [Imagen enviada]
                        </div>
                    <?php endif; ?>
                    
                    <?php 
                        $texto = "";
                        foreach($msg['parts'] as $part) {
                            if (isset($part['text'])) $texto = $part['text'];
                        }
                        $texto = str_replace(['```html', '```'], '', $texto);
                        $texto = trim($texto); 
                        $texto = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $texto);
                        echo nl2br($texto); 
                    ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="chat-controls">
        <form method="post" enctype="multipart/form-data" style="display: flex; gap: 10px; margin:0; padding:0; box-shadow:none; background:transparent; border:none; max-width:100%;">
            
            <label for="foto_chat" class="btn-camera" title="Subir foto al chat" style="cursor:pointer; padding: 10px;">
                <i class="fa-solid fa-camera fa-xl"></i>
            </label>
            <input type="file" name="imagen_chat" id="foto_chat" accept="image/*" style="display: none;" onchange="document.getElementById('preview-txt').innerText = '📸 Imagen lista'">
            
            <input type="text" name="mensaje_chat" placeholder="Escribe tu consulta..." autocomplete="off" style="margin:0; flex-grow:1; border: 1px solid var(--color-borde);">
            
            <button type="submit" style="width: 45px; height: 45px; border-radius: 50%; padding:0; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
        <small id="preview-txt" style="color: var(--color-primario); font-weight:bold; margin-left: 40px; display:block; margin-top:5px;"></small>
    </div>
</div>

<script>
    var caja = document.getElementById("cajaChat");
    if(caja) { caja.scrollTop = caja.scrollHeight; }
</script>
<h1>Listado de productos</h2>

  <form method="get" class="search-form">
      <div class="search-input-group">
          <input type="text" name="q" value="<?= h($q) ?>" placeholder="Buscar producto por nombre...">
      </div>
      
      <button type="submit">
          <i class="fa-solid fa-magnifying-glass"></i> Buscar
      </button>
  </form>


<table>
  <thead>
      <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Categoria</th>
          <th>Receta</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Marca</th>
          <?php if ($_SESSION['user_rol'] === 'Administrador'): ?>
          <th>Acciones</th>
          <?php endif; ?>
          <?php if ($_SESSION['user_rol'] != 'Administrador'): ?>
              <th>Compra</th>
          <?php endif; ?>
      </tr>
  </thead>
  <tbody>
  <?php if (empty($productos)): ?>
      <tr>
          <td colspan="7">No se han encontrado productos.</td>
      </tr>
  <?php else: ?>
      <?php foreach ($productos as $p): 
            $stock = (int)$p['STOCK_DISPONIBLE'];
            $stockClass = '';
            $stockIcon = 'fa-circle'; // Círculo relleno

            if ($stock <= 50) {
                $stockClass = 'stock-low'; // Rojo
            } elseif ($stock <= 150) {
                $stockClass = 'stock-med'; // Naranja
            } else {
                $stockClass = 'stock-high'; // Verde
            }
        ?>
            <tr class="clickable-row" onclick="window.location.href='items_show.php?ID=<?= h($p['ID']) ?>';">
                <td><?= h($p['ID']) ?></td>
                <td><?= h($p['NOMBRE']) ?></td>
                <td><?= h($p['CATEGORIA']) ?></td>
                <td><?= $p['RECETA'] ? 'Sí' : 'No' ?></td>
                <td><?= h($p['PRECIO']) ?> €</td>
                
                <td>
                    <i class="fa-solid fa-circle <?= $stockClass ?>" 
                       style="font-size: 1.2em; vertical-align: middle;"></i>
                </td>

                <td>
                    <?= h($p['MARCA_NOMBRE'] ?? 'Sin Marca') ?> 
                </td>
                
                <td onclick="event.stopPropagation();">
                <?php if ($_SESSION['user_rol'] === 'Administrador'): ?>
                    
                    <div class="action-buttons">
                        <a href="items_form.php?ID=<?php echo h($p['ID']); ?>" class="btn-table btn-edit">
                            <i class="fa-solid fa-pen"></i> Editar
                        </a>

                        <a href="items_delete.php?ID=<?php echo h($p['ID']); ?>" 
                           class="btn-table btn-delete"
                           onclick="return confirm('¿Estás seguro de que quieres iniciar el borrado de este producto?');">
                           <i class="fa-solid fa-trash"></i> Borrar
                        </a>
                    </div>

                <?php else: ?>
                
                    <div class="action-buttons">
                        <a href="carrito_add.php?ID=<?php echo h($p['ID']); ?>" class="btn-table btn-buy">
                            <i class="fa-solid fa-cart-plus"></i> Añadir
                        </a>
                    </div>

                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php

    for ($i = 1; $i <= $total_pages; $i++) {
        // Mostramos la página actual
        $isCurrent = ($i === $page);
        
        // Construimos el link
        $link = "items_list.php?page=$i&q=" . urlencode($q);

        if ($isCurrent) {
            echo "<strong>$i</strong>"; 
        } else {
            // Otras páginas
            echo "<a href='$link'>$i</a>";
        }
    }
    ?>
</div>

<?php 
footerHtml(); 
?>