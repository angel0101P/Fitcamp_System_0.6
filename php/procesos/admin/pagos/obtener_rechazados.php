<?php
// php/modulos/pagos/obtener_rechazados.php
session_start();
require_once '../../../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

if (!$conexion) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a BD']);
    exit();
}

// Consulta pagos rechazados
$query = "SELECT 
            id,
            pago_id,
            usuario_id,
            metodo_pago_id,
            monto,
            mes_pagado,
            referencia,
            comprobante,
            observaciones,
            razon_rechazo,
            fecha_pago,
            fecha_rechazo,
            rechazado_por
          FROM pagos_rechazados 
          ORDER BY fecha_rechazo DESC";

$result = mysqli_query($conexion, $query);

if (!$result) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . mysqli_error($conexion)]);
    exit();
}

$pagos = [];
$total_rechazado = 0;
$total_registros = 0;

while ($row = mysqli_fetch_assoc($result)) {
    // Formatear fechas
    $fecha_pago_formateada = 'Sin fecha';
    $fecha_rechazo_formateada = 'Sin fecha';
    
    if (!empty($row['fecha_pago']) && $row['fecha_pago'] != '0000-00-00 00:00:00') {
        $fecha_pago_formateada = date('d/m/Y H:i', strtotime($row['fecha_pago']));
    }
    
    if (!empty($row['fecha_rechazo']) && $row['fecha_rechazo'] != '0000-00-00 00:00:00') {
        $fecha_rechazo_formateada = date('d/m/Y H:i', strtotime($row['fecha_rechazo']));
    }
    
    // Nombre del usuario desde observaciones
    $nombre_usuario = $row['observaciones'] ?? 'Usuario #' . $row['usuario_id'];
    
    $pago = [
        'id' => $row['id'],
        'pago_id' => $row['pago_id'],
        'usuario_id' => $row['usuario_id'],
        'usuario_nombre' => $nombre_usuario,
        'metodo_pago_id' => $row['metodo_pago_id'],
        'metodo_nombre' => 'Método #' . $row['metodo_pago_id'],
        'monto' => floatval($row['monto']),
        'mes_pagado' => $row['mes_pagado'] ?? '',
        'referencia' => $row['referencia'] ?? '',
        'comprobante' => $row['comprobante'] ?? '',
        'observaciones' => $row['observaciones'] ?? '',
        'razon_rechazo' => $row['razon_rechazo'] ?? '',
        'fecha_pago' => $row['fecha_pago'],
        'fecha_pago_formateada' => $fecha_pago_formateada,
        'fecha_rechazo' => $row['fecha_rechazo'],
        'fecha_rechazo_formateada' => $fecha_rechazo_formateada,
        'rechazado_por' => $row['rechazado_por'] ?? 'admin'
    ];
    
    $pagos[] = $pago;
    $total_rechazado += floatval($row['monto']);
    $total_registros++;
}

mysqli_free_result($result);
mysqli_close($conexion);

echo json_encode([
    'success' => true,
    'pagos' => $pagos,
    'estadisticas' => [
        'total_rechazado' => $total_rechazado,
        'cantidad_pagos' => $total_registros
    ]
]);

?>