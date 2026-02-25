<?php
session_start();
include '../../../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID no recibido']);
    exit();
}

try {
    $id = (int)$data['id'];
    
    // Verificar si existe
    $queryCheck = "SELECT id FROM metodos_pago WHERE id = $id";
    $resultCheck = mysqli_query($conexion, $queryCheck);
    
    if (mysqli_num_rows($resultCheck) == 0) {
        echo json_encode(['success' => false, 'error' => 'Método no encontrado']);
        exit();
    }
    
    // Eliminar método
    $query = "DELETE FROM metodos_pago WHERE id = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Método eliminado exitosamente'
        ]);
    } else {
        throw new Exception("Error al eliminar: " . mysqli_error($conexion));
    }
    
    mysqli_close($conexion);
    
} catch(Exception $e) {
    if (isset($conexion)) mysqli_close($conexion);
    
    echo json_encode([
        'success' => false,
        'error' => 'Error al eliminar método',
        'detalle' => $e->getMessage()
    ]);
}
?>