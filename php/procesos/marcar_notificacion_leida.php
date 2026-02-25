<?php
// /php/procesos/marcar_notificacion_leida.php
session_start();

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

include '../config/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$usuario = $_SESSION['usuario_user'];

if (isset($data['todas']) && $data['todas'] === true && isset($data['ids'])) {
    // Marcar todas las notificaciones como leídas
    $ids = array_map('intval', $data['ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "UPDATE notificaciones_privadas SET leido = 1 
              WHERE id IN ($placeholders) AND usuario_receptor = ?";
    
    $params = array_merge($ids, [$usuario]);
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, str_repeat('i', count($ids)) . 's', ...$params);
    
} else if (isset($data['id'])) {
    // Marcar una notificación como leída
    $id = intval($data['id']);
    
    $query = "UPDATE notificaciones_privadas SET leido = 1 
              WHERE id = ? AND usuario_receptor = ?";
    
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "is", $id, $usuario);
} else {
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit;
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Notificación marcada como leída']);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conexion)]);
}
?>