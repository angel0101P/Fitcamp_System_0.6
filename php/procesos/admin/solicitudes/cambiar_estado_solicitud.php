<?php
session_start();
require_once '../../../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_user'])) { echo json_encode(['success'=>false,'error'=>'Sesión no iniciada']); exit(); }

$id = isset($_POST['solicitud_id']) ? intval($_POST['solicitud_id']) : 0;
$nuevo = $_POST['nuevo_estado'] ?? '';
$obs = $_POST['observaciones'] ?? '';

if ($id <= 0 || !in_array($nuevo, ['pagado','rechazado','pendiente'])) {
    echo json_encode(['success'=>false,'error'=>'Parámetros inválidos']); exit();
}

$stmt = $conexion->prepare("UPDATE solicitudes_productos SET estado = ?, observaciones = CONCAT(IFNULL(observaciones,''), ?) WHERE id = ?");
if (!$stmt) { echo json_encode(['success'=>false,'error'=>mysqli_error($conexion)]); exit(); }

$append = '';
if ($obs !== '') {
    $append = "\n[ADMIN: " . date('Y-m-d H:i') . "] " . $conexion->real_escape_string($obs);
}

$stmt->bind_param('ssi', $nuevo, $append, $id);
if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
    $stmt->close(); mysqli_close($conexion); exit();
}

echo json_encode(['success'=>false,'error'=>$stmt->error ?: mysqli_error($conexion)]);
exit();
?>
