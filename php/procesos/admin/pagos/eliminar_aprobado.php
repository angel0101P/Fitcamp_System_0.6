<?php
// php/modulos/pagos/eliminar_aprobado.php
session_start();
require_once '../../../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

// Obtener datos
$pago_id = $_POST['pago_id'] ?? 0;

if ($pago_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit();
}

if (!$conexion) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a BD']);
    exit();
}

// Eliminar el pago aprobado
$query = "DELETE FROM pagos_aprobados WHERE id = ?";
$stmt = mysqli_prepare($conexion, $query);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error preparando consulta']);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $pago_id);

if (mysqli_stmt_execute($stmt)) {
    $filas = mysqli_stmt_affected_rows($stmt);
    
    if ($filas > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "Pago aprobado #$pago_id eliminado del historial",
            'pago_id' => $pago_id
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Pago no encontrado'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Error: ' . mysqli_stmt_error($stmt)
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>