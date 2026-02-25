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

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
if (!$id) {
    echo json_encode(['status'=>'error','message'=>'ID inválido']);
    exit();
}

// Obtener imagen actual para eliminar archivo
$stmt = $conexion->prepare('SELECT imagen_path FROM productos WHERE id = ?');
$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    echo json_encode(['status'=>'error','message'=>'Producto no encontrado']);
    exit();
}
$row = $res->fetch_assoc();
$imagen = $row['imagen_path'];
$stmt->close();

$stmt = $conexion->prepare('DELETE FROM productos WHERE id = ?');
$stmt->bind_param('i',$id);
if ($stmt->execute()) {
    // eliminar imagen si existe
    if (!empty($imagen) && file_exists(__DIR__ . '/../../../../' . ltrim($imagen,'/'))) {
        @unlink(__DIR__ . '/../../../../' . ltrim($imagen,'/'));
    }
    echo json_encode(['status'=>'success','message'=>'Producto eliminado']);
    $stmt->close();
    mysqli_close($conexion);
    exit();
}

$stmt->close();
mysqli_close($conexion);
echo json_encode(['status'=>'error','message'=>'No se pudo eliminar']);
exit();
