<?php
session_start();
include '../../../config/conexion.php';

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Sesión no iniciada'
    ]);
    exit();
}

try {
    // Consulta para obtener métodos de pago
    $query = "SELECT 
                id,
                nombre,
                tipo,
                descripcion,
                instrucciones,
                estado,
                icono,
                color,
                datos_adicionales,
                orden_visual,
                fecha_creacion,
                fecha_actualizacion
              FROM metodos_pago 
              ORDER BY orden_visual ASC, fecha_creacion DESC";
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        throw new Exception("Error en consulta: " . mysqli_error($conexion));
    }
    
    $metodos = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $metodos[] = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre'],
            'tipo' => $row['tipo'],
            'descripcion' => $row['descripcion'] ?? '',
            'instrucciones' => $row['instrucciones'] ?? '',
            'estado' => $row['estado'],
            'icono' => $row['icono'] ?? 'fa-credit-card',
            'color' => $row['color'] ?? '#4B0082',
            'datos_adicionales' => $row['datos_adicionales'] ?? '',
            'orden_visual' => (int)$row['orden_visual'],
            'fecha_creacion' => $row['fecha_creacion'],
            'fecha_actualizacion' => $row['fecha_actualizacion']
        ];
    }
    
    mysqli_close($conexion);
    
    echo json_encode([
        'success' => true,
        'metodos' => $metodos,
        'total' => count($metodos)
    ]);
    
} catch(Exception $e) {
    if (isset($conexion)) mysqli_close($conexion);
    
    echo json_encode([
        'success' => false,
        'error' => 'Error al cargar métodos de pago',
        'detalle' => $e->getMessage()
    ]);
}
?>