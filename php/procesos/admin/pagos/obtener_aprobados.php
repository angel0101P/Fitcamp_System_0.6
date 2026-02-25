<?php
// php/modulos/pagos/obtener_aprobados.php
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

// Consulta pagos aprobados
$query = "SELECT 
            pa.id,
            pa.pago_id,
            pa.usuario_id,
            pa.metodo_pago_id,
            pa.monto,
            pa.mes_pagado,
            pa.referencia,
            pa.comprobante,
            pa.observaciones,
            pa.fecha_pago,
            pa.fecha_aprobacion,
            pa.aprobado_por,
            mp.nombre as metodo_nombre,
            mp.icono as metodo_icono,
            mp.color as metodo_color
          FROM pagos_aprobados pa
          LEFT JOIN metodos_pago mp ON pa.metodo_pago_id = mp.id
          ORDER BY pa.fecha_aprobacion DESC";

$result = mysqli_query($conexion, $query);

if (!$result) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . mysqli_error($conexion)]);
    exit();
}

$pagos = [];
$total_aprobado = 0;

while ($row = mysqli_fetch_assoc($result)) {
    // Formatear fechas
    $fecha_pago_formateada = 'Sin fecha';
    $fecha_aprobacion_formateada = 'Sin fecha';
    
    if (!empty($row['fecha_pago']) && $row['fecha_pago'] != '0000-00-00 00:00:00') {
        $fecha_pago_formateada = date('d/m/Y H:i', strtotime($row['fecha_pago']));
    }
    
    if (!empty($row['fecha_aprobacion']) && $row['fecha_aprobacion'] != '0000-00-00 00:00:00') {
        $fecha_aprobacion_formateada = date('d/m/Y H:i', strtotime($row['fecha_aprobacion']));
    }
    
    // Extraer nombre de observaciones
    $nombre_usuario = $row['observaciones'] ?? 'Usuario #' . $row['usuario_id'];
    
    $pagos[] = [
        'id' => $row['id'],
        'pago_id' => $row['pago_id'],
        'usuario_id' => $row['usuario_id'],
        'usuario_nombre' => $nombre_usuario,
        'metodo_pago_id' => $row['metodo_pago_id'],
        'metodo_nombre' => $row['metodo_nombre'] ?? 'Método #' . $row['metodo_pago_id'],
        'metodo_icono' => $row['metodo_icono'] ?? 'fa-credit-card',
        'metodo_color' => $row['metodo_color'] ?? '#4B0082',
        'monto' => floatval($row['monto']),
        'mes_pagado' => $row['mes_pagado'] ?? '',
        'referencia' => $row['referencia'] ?? '',
        'comprobante' => $row['comprobante'] ?? '',
        'observaciones' => $row['observaciones'] ?? '',
        'fecha_pago' => $row['fecha_pago'],
        'fecha_pago_formateada' => $fecha_pago_formateada,
        'fecha_aprobacion' => $row['fecha_aprobacion'],
        'fecha_aprobacion_formateada' => $fecha_aprobacion_formateada,
        'aprobado_por' => $row['aprobado_por'] ?? 'admin'
    ];
    
    $total_aprobado += floatval($row['monto']);
}

mysqli_free_result($result);
mysqli_close($conexion);

echo json_encode([
    'success' => true,
    'pagos' => $pagos,
    'estadisticas' => [
        'total_aprobado' => $total_aprobado,
        'cantidad_pagos' => count($pagos)
    ]
]);

?>