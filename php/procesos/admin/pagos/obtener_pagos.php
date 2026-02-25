<?php
// php/modulos/pagos/obtener_pendientes.php 

session_start();
require_once '../../../config/conexion.php';

header('Content-Type: application/json');

// Verificar sesión básica
if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

// Verificar conexión
if (!$conexion) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a BD']);
    exit();
}

// CONSULTA SIMPLE - solo los campos que necesitas
$query = "SELECT 
            id, 
            usuario_id, 
            metodo_pago_id, 
            monto, 
            mes_pagado, 
            referencia, 
            comprobante,
            estado, 
            observaciones, 
            fecha_pago, 
            fecha_verificacion
          FROM pagos_usuarios 
          WHERE estado = 'pendiente'
          ORDER BY fecha_pago ASC";

$result = mysqli_query($conexion, $query);

if (!$result) {
    echo json_encode([
        'success' => false, 
        'error' => 'Error en consulta: ' . mysqli_error($conexion)
    ]);
    exit();
}

// Procesar resultados
$pagos = [];
$total_pendiente = 0;
$total_registros = 0;

while ($row = mysqli_fetch_assoc($result)) {
    // Formatear fecha
    $fecha_formateada = 'Sin fecha';
    $dias_transcurridos = 0;
    
    if (!empty($row['fecha_pago']) && $row['fecha_pago'] != '0000-00-00 00:00:00') {
        $fecha_pago = strtotime($row['fecha_pago']);
        $fecha_formateada = date('d/m/Y H:i', $fecha_pago);
        
        // Calcular días transcurridos
        $dias_transcurridos = floor((time() - $fecha_pago) / (60 * 60 * 24));
    }
    
    // Determinar si está atrasado (más de 2 días)
    $es_atrasado = $dias_transcurridos > 2;
    
    $pago = [
        'id' => $row['id'],
        'usuario_id' => $row['usuario_id'],
        
        // Método de pago básico
        'metodo_pago_id' => $row['metodo_pago_id'],
        'metodo_nombre' => 'Método #' . $row['metodo_pago_id'],
        
        // Información del pago
        'monto' => floatval($row['monto']),
        'mes_pagado' => $row['mes_pagado'],
        'referencia' => $row['referencia'] ?? '',
        'comprobante' => $row['comprobante'] ?? '',
        'estado' => $row['estado'],
        'observaciones' => $row['observaciones'] ?? '',
        
        // Fechas
        'fecha_pago' => $row['fecha_pago'],
        'fecha_formateada' => $fecha_formateada,
        'dias_transcurridos' => $dias_transcurridos,
        'es_atrasado' => $es_atrasado
    ];
    
    $pagos[] = $pago;
    $total_pendiente += floatval($row['monto']);
    $total_registros++;
}

mysqli_free_result($result);
mysqli_close($conexion);

echo json_encode([
    'success' => true,
    'pagos' => $pagos,
    'estadisticas' => [
        'total_pendiente' => $total_pendiente,
        'cantidad_pagos' => $total_registros,
        'pagos_atrasados' => count(array_filter($pagos, fn($p) => $p['es_atrasado']))
    ]
]);

?>