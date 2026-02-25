<?php
session_start();
require_once '../../../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_user'])) { echo json_encode(['success'=>false,'error'=>'Sesión no iniciada']); exit(); }

$id = isset($_POST['solicitud_id']) ? intval($_POST['solicitud_id']) : 0;
if ($id <= 0) { echo json_encode(['success'=>false,'error'=>'ID inválido']); exit(); }

// Optionally: fetch comprobante path to delete file
$res = mysqli_query($conexion, "SELECT comprobante_path FROM solicitudes_productos WHERE id = " . $id . " LIMIT 1");
$row = mysqli_fetch_assoc($res);
$comprobante = $row['comprobante_path'] ?? '';

$del = mysqli_query($conexion, "DELETE FROM solicitudes_productos WHERE id = " . $id);
if ($del) {
    // try to delete file if exists
    if ($comprobante) {
        $path = __DIR__ . '/../../../' . $comprobante;
        if (file_exists($path)) @unlink($path);
    }
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>mysqli_error($conexion)]);
}

mysqli_close($conexion);
exit();
?>
