<?php
session_start();
include '../config/conexion.php';

header('Content-Type: application/json');

try {
    // CONSULTA SIN VERIFICAR SESIÓN
    $query = "SELECT 
                id,
                titulo_mensaje,
                mensaje,
                admin_remitente
              FROM notificaciones_generales 
              ORDER BY id DESC";
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        throw new Exception("Error SQL: " . mysqli_error($conexion));
    }
    
    $notificaciones = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $notificaciones[] = [
            'id' => (int)$row['id'],
            'titulo' => $row['titulo_mensaje'],
            'mensaje' => $row['mensaje'],
            'remitente' => $row['admin_remitente'],
            'tipo' => 'general'
        ];
    }
    
    mysqli_close($conexion);
    
    echo json_encode([
        'success' => true,
        'notificaciones' => $notificaciones
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>