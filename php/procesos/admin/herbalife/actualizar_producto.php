<?php
session_start();
// Evitar warnings que rompan JSON
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json; charset=utf-8');
require_once '../../../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'No autenticado']);
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$id) {
    echo json_encode(['status'=>'error','message'=>'ID inválido']);
    exit();
}

// Obtener producto existente
$stmt = $conexion->prepare('SELECT imagen_path FROM productos WHERE id = ?');
$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    echo json_encode(['status'=>'error','message'=>'Producto no encontrado']);
    exit();
}
$row = $res->fetch_assoc();
$current_image = $row['imagen_path'];
$stmt->close();

$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

if (empty($nombre) || $precio <= 0) {
    echo json_encode(['status'=>'error','message'=>'Nombre y precio son obligatorios']);
    exit();
}

$uploadDir = __DIR__ . '/../../../../uploads/productos/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir,0755,true);
}

$imagen_path = $current_image;
if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['imagen']['tmp_name'];
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME));
    $newName = $safeName . '_' . time() . '.' . $ext;
    $dest = $uploadDir . $newName;
    if (is_uploaded_file($tmp) && move_uploaded_file($tmp, $dest)) {
        $imagen_path = 'uploads/productos/' . $newName;
        // intentar eliminar la imagen anterior (si existe)
        if (!empty($current_image) && file_exists(__DIR__ . '/../../../../' . ltrim($current_image,'/'))) {
            @unlink(__DIR__ . '/../../../../' . ltrim($current_image,'/'));
        }
    } else {
        echo json_encode(['status'=>'error','message'=>'No se pudo mover la nueva imagen']);
        exit();
    }
}

$stmt = $conexion->prepare('UPDATE productos SET nombre=?, descripcion=?, categoria=?, precio=?, stock=?, imagen_path=?, updated_at=NOW() WHERE id=?');
if ($stmt) {
    $stmt->bind_param('sssdisi', $nombre, $descripcion, $categoria, $precio, $stock, $imagen_path, $id);
    if ($stmt->execute()) {
        echo json_encode(['status'=>'success','producto'=>['id'=>$id,'nombre'=>$nombre,'descripcion'=>$descripcion,'categoria'=>$categoria,'precio'=>number_format($precio,2),'stock'=>$stock,'imagen_path'=>$imagen_path]]);
        $stmt->close();
        mysqli_close($conexion);
        exit();
    }
    $stmt->close();
}

mysqli_close($conexion);
echo json_encode(['status'=>'error','message'=>'No se pudo actualizar el producto']);
exit();
