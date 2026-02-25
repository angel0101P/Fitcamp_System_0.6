<?php
session_start();
// Evitar que warnings/notice rompan la salida JSON
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// Responder siempre JSON
header('Content-Type: application/json; charset=utf-8');
// Ajuste de la ruta al archivo de conexión (subimos 3 niveles hasta `php/`)
require_once '../../../config/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit();
}

$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;

if (empty($nombre) || $precio <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Nombre y precio son obligatorios']);
    exit();
}

$uploadDir = __DIR__ . '/../../../../uploads/productos/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$imagen_path = '';
if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['imagen']['tmp_name'];
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME));
    $newName = $safeName . '_' . time() . '.' . $ext;
    $dest = $uploadDir . $newName;
    // Asegurarnos de que el archivo fue subido vía HTTP POST
    if (is_uploaded_file($tmp)) {
        if (move_uploaded_file($tmp, $dest)) {
            // Guardar ruta relativa al proyecto para uso en front
            $imagen_path = 'uploads/productos/' . $newName;
        } else {
            // Responder con error claro para depuración (permiso o ruta incorrecta)
            echo json_encode(['status' => 'error', 'message' => 'No se pudo mover el archivo al destino. Comprueba permisos de la carpeta uploads/']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Archivo no válido o no subido correctamente.']);
        exit();
    }
}

$stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, categoria, precio, stock, imagen_path, creado_por, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
if ($stmt) {
    $stmt->bind_param('sssdisi', $nombre, $descripcion, $categoria, $precio, $stock, $imagen_path, $usuario_id);
    if ($stmt->execute()) {
        $insertId = $stmt->insert_id;
        // Responder con JSON para que el JS pueda agregarlo al DOM
        $respuesta = [
            'status' => 'success',
            'producto' => [
                'id' => $insertId,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'categoria' => $categoria,
                'precio' => number_format($precio,2),
                'stock' => $stock,
                'imagen_path' => $imagen_path
            ]
        ];
        echo json_encode($respuesta);
        $stmt->close();
        mysqli_close($conexion);
        exit();
    }
    $stmt->close();
}

mysqli_close($conexion);
echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar el producto']);
exit();
