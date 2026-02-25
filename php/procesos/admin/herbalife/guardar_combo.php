<?php
session_start();
ini_set('display_errors','0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json; charset=utf-8');
require_once '../../../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'No autenticado']);
    exit();
}

$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
$productos_json = $_POST['productos'] ?? '[]';
$productos = json_decode($productos_json, true) ?: [];
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;

if (empty($nombre) || $precio <= 0 || empty($productos)) {
    echo json_encode(['status'=>'error','message'=>'Nombre, precio y al menos un producto son requeridos']);
    exit();
}

$uploadDir = __DIR__ . '/../../../../uploads/productos/';
if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
$imagen_path = '';
if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['imagen']['tmp_name'];
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/','_', pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME));
    $newName = $safeName . '_' . time() . '.' . $ext;
    $dest = $uploadDir . $newName;
    if (is_uploaded_file($tmp) && move_uploaded_file($tmp, $dest)) {
        $imagen_path = 'uploads/productos/' . $newName;
    }
}

$conexion->begin_transaction();
try {
    $stmt = $conexion->prepare('INSERT INTO combos (nombre, descripcion, precio, imagen_path, creado_por, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->bind_param('ssdsi', $nombre, $descripcion, $precio, $imagen_path, $usuario_id);
    $stmt->execute();
    $combo_id = $stmt->insert_id;
    $stmt->close();

    $stmtItem = $conexion->prepare('INSERT INTO combo_items (combo_id, producto_id, cantidad) VALUES (?, ?, ?)');
    foreach ($productos as $p) {
        $pid = intval($p['id']); $cant = intval($p['cantidad']);
        $stmtItem->bind_param('iii', $combo_id, $pid, $cant);
        $stmtItem->execute();
    }
    $stmtItem->close();

    $conexion->commit();
    echo json_encode(['status'=>'success','combo'=>['id'=>$combo_id,'nombre'=>$nombre,'descripcion'=>$descripcion,'precio'=>number_format($precio,2),'imagen_path'=>$imagen_path]]);
    exit();
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['status'=>'error','message'=>'Error guardando combo: '.$e->getMessage()]);
    exit();
}
?>
