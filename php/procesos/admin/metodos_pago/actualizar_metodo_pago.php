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
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit();
}

try {
    $id = (int)$data['id'];
    $nombre = mysqli_real_escape_string($conexion, trim($data['nombre'] ?? ''));
    $tipo = mysqli_real_escape_string($conexion, trim($data['tipo'] ?? ''));
    $descripcion = mysqli_real_escape_string($conexion, trim($data['descripcion'] ?? ''));
    $instrucciones = mysqli_real_escape_string($conexion, trim($data['instrucciones'] ?? ''));
    $estado = mysqli_real_escape_string($conexion, trim($data['estado'] ?? 'activo'));
    $icono = mysqli_real_escape_string($conexion, trim($data['icono'] ?? ''));
    $color = mysqli_real_escape_string($conexion, trim($data['color'] ?? ''));
    $datos_adicionales = mysqli_real_escape_string($conexion, trim($data['datos_adicionales'] ?? ''));
    
    // Actualizar método
    $query = "UPDATE metodos_pago SET
                nombre = '$nombre',
                tipo = '$tipo',
                descripcion = '$descripcion',
                instrucciones = '$instrucciones',
                estado = '$estado',
                icono = '$icono',
                color = '$color',
                datos_adicionales = '$datos_adicionales',
                fecha_actualizacion = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Método actualizado exitosamente'
        ]);
    } else {
        throw new Exception("Error al actualizar: " . mysqli_error($conexion));
    }
    
    mysqli_close($conexion);
    
} catch(Exception $e) {
    if (isset($conexion)) mysqli_close($conexion);
    
    echo json_encode([
        'success' => false,
        'error' => 'Error al actualizar método',
        'detalle' => $e->getMessage()
    ]);
}
?>