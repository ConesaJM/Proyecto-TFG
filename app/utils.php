<?php
// app/utils.php
// Versión acorde a la base de datos "pharmasphere_db"

/* ========= 1. FUNCIONES DE USUARIO =========== */

// Busca un usuario por su nombre (para el login).
function buscarUsuarioPorNombre(PDO $pdo, string $nombre_usuario): ?array {
    $sql = "SELECT * FROM USUARIO WHERE NOMBRE = ?"; // Tabla y columna actualizadas
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre_usuario]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

// Inserta un nuevo usuario en la base de datos (para el panel de admin).
function crearUsuario(PDO $pdo, string $nombre_usuario, string $password_hash, string $rol): bool {
    try {
        // Columnas actualizadas
        $sql = "INSERT INTO USUARIO (NOMBRE, CONTRASENHIA, ROL) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_usuario, $password_hash, $rol]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false; // Error de creación de usuario
    }
}

/* ========= 2. FUNCIONES DE PRODUCTO =========== */

// Crea un nuevo producto 
function crearProducto(PDO $pdo, string $nombre, string $categoria, int $receta, float $precio, int $stock, int $marca_id): int {
    // Tabla y columnas actualizadas
    $sql = "INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $receta, $precio, $stock, $marca_id]);
    return (int)$pdo->lastInsertId();
}

// Sistema de busqueda de producto por id
function leerProductoPorId(PDO $pdo, int $id): ?array {
    $sql = "SELECT * FROM PRODUCTO WHERE ID = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

// READ-N: Listar los productos con búsqueda y paginación
function listarProductos(PDO $pdo, ?string $buscar = null, int $limit = 10, int $offset = 0): array {
    // Seleccionamos todo de PRODUCTO (P.*) y el NOMBRE de MARCA (M.NOMBRE)
    $campos = "P.*, M.NOMBRE AS MARCA_NOMBRE";
    
    if ($buscar) {
        $sql = "SELECT $campos 
                FROM PRODUCTO P
                LEFT JOIN MARCA M ON P.MARCA_ID = M.ID
                WHERE P.NOMBRE LIKE ? 
                ORDER BY P.ID DESC 
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%{$buscar}%", $limit, $offset]);
    } else {
        $sql = "SELECT $campos 
                FROM PRODUCTO P
                LEFT JOIN MARCA M ON P.MARCA_ID = M.ID
                ORDER BY P.ID DESC 
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
    }
    return $stmt->fetchAll();
}

// UPDATE: Para actualizar un producto ya existente
function actualizarProducto(PDO $pdo, int $id, string $nombre, string $categoria, int $receta, float $precio, int $stock, int $marca_id): bool {
    $sql = "UPDATE PRODUCTO SET 
                NOMBRE = ?, 
                CATEGORIA = ?, 
                RECETA = ?, 
                PRECIO = ?, 
                STOCK_DISPONIBLE = ?, 
                MARCA_ID = ? 
            WHERE ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $receta, $precio, $stock, $marca_id, $id]);
    return $stmt->rowCount() > 0;
}

// DELETE: Para borrar producto
function borrarProducto(PDO $pdo, int $id): bool {
    $sql = "DELETE FROM PRODUCTO WHERE ID = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0;
}

/* ========= 3. FUNCION DE FILTRADO POR MARCA =========== */

// Listar todas las marcas.
// Será usada en el formulario de productos para un filtrar por marcas.
function listarMarcas(PDO $pdo): array {
    $sql = "SELECT ID, NOMBRE FROM MARCA ORDER BY NOMBRE ASC";
    $stmt = $pdo->query($sql); 
    return $stmt->fetchAll();
}

/* ========= 4. FUNCIONES DE AUDITORÍA =========== */

// REGISTRAR: Guarda un evento en la tabla AUDITORIA
// Se usará dentro de items_delete.php

// Registra los datos del producto borrado dentro de la tabla AUDITORIA
function registrarAuditoria(PDO $pdo, string $accion, string $detalle): bool {
    $sql = "INSERT INTO AUDITORIA (NOMBRE, DETALLE) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$accion, $detalle]);
}

// LISTAR: Devuelve todo el historial de cambios
function auditoria_list(PDO $pdo): array {
    try {
        $sql = "SELECT * FROM AUDITORIA ORDER BY FECHA DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return []; // Si falla, devolvemos array vacío
    }
}

/* ========= 5. FUNCIÓN IA (GEMINI - PROCESAMIENTO IMAGEN) =========== */

function analizarImagenGemini(?string $rutaImagen = null, string $preguntaUsuario = ''): string {
    $apiKey = "AIzaSyBWEtAFD7KrQ0OQ23OUYJBzkZ3HjakL6AQ"; // <--- ¡TU CLAVE!
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview:generateContent?key=$apiKey";

    // 1. PREPARAMOS EL PAYLOAD
    $parts = [];

    if (!empty($preguntaUsuario)) {
        $parts[] = ["text" => $preguntaUsuario];
    } else {
        $parts[] = ["text" => "Identifica el NOMBRE COMERCIAL o PRINCIPIO ACTIVO en la imagen. Responde SOLO con el nombre limpio."];
    }

    if ($rutaImagen && file_exists($rutaImagen)) {
        $info = getimagesize($rutaImagen);
        if ($info) {
            $dataImg = base64_encode(file_get_contents($rutaImagen));
            $parts[] = [
                "inline_data" => [
                    "mime_type" => $info['mime'],
                    "data" => $dataImg
                ]
            ];
        }
    }

    if (empty($parts)) return "";

    // 2. CONSTRUIR JSON
    $data = ["contents" => [["parts" => $parts]]];

    if (strpos($preguntaUsuario, 'JSON') !== false) {
        $data['generationConfig'] = [
            "responseMimeType" => "application/json"
        ];
    }

    // 3. ENVIAR
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $respuesta = curl_exec($ch);
    
    if (curl_errno($ch)) return '{"intencion": "NULL", "error": "Curl error"}';
    curl_close($ch);

    $json = json_decode($respuesta, true);
    
    if (isset($json['error'])) {
        return json_encode(["intencion" => "NULL", "error" => $json['error']['message']]);
    }
    
    // 4. LIMPIEZA DE FORMATO
    $textoRaw = trim($json['candidates'][0]['content']['parts'][0]['text'] ?? '');
    
    if (strpos($preguntaUsuario, 'JSON') === false) {
        $textoRaw = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $textoRaw);
    }
    
    return $textoRaw;
}

/* ========= 6. FUNCIÓN CHAT IA (CON MEMORIA) =========== */

function enviarChatGemini(array $historial): string {
    $apiKey = "AIzaSyBWEtAFD7KrQ0OQ23OUYJBzkZ3HjakL6AQ"; // <--- ¡TU CLAVE OTRA VEZ!
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=$apiKey";

    $data = [
        "contents" => $historial
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $respuesta = curl_exec($ch);
    
    // Control de errores de conexión cURL
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "⚠️ Error interno de cURL: " . $error;
    }
    curl_close($ch);

    // 1. Decodificar la respuesta JSON de Google
    $json = json_decode($respuesta, true);

    // 2. Controlar si Google devuelve un error (ej. Cuota excedida, clave incorrecta)
    if (isset($json['error'])) {
        return "⚠️ Error de la API de Gemini: " . $json['error']['message'];
    }

    // 3. Extraer el texto limpio de la respuesta
    // Navegamos por: candidates[0] -> content -> parts[0] -> text
    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        return trim($json['candidates'][0]['content']['parts'][0]['text']);
    }

    // 4. Si la estructura no es la esperada
    return "⚠️ Respuesta inesperada de la IA.";
}