<?php
session_start();
include '../../../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || !isset($data['estado'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit();
}

try {
    $id = (int)$data['id'];
    $estado = mysqli_real_escape_string($conexion, trim($data['estado']));
    
    // Validar estado
    if (!in_array($estado, ['activo', 'inactivo'])) {
        echo json_encode(['success' => false, 'error' => 'Estado inválido']);
        exit();
    }
    
    // Actualizar estado
    $query = "UPDATE metodos_pago SET 
                estado = '$estado',
                fecha_actualizacion = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado exitosamente'
        ]);
    } else {
        throw new Exception("Error al actualizar estado: " . mysqli_error($conexion));
    }
    
    mysqli_close($conexion);
    
} catch(Exception $e) {
    if (isset($conexion)) mysqli_close($conexion);
    
    echo json_encode([
        'success' => false,
        'error' => 'Error al cambiar estado',
        'detalle' => $e->getMessage()
    ]);
}
?>