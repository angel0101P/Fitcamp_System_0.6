<?php
// php/procesos/admin/pagos/cambiar_estados.php - VERSIÓN CON HISTORIAL

session_start();
require_once '../../../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit();
}

// Obtener datos
$pago_id = $_POST['pago_id'] ?? 0;
$nuevo_estado = $_POST['nuevo_estado'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';

// Validar
if ($pago_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit();
}

if (!in_array($nuevo_estado, ['verificado', 'rechazado'])) {
    echo json_encode(['success' => false, 'error' => 'Estado inválido. Use: verificado o rechazado']);
    exit();
}

// Verificar conexión
if (!$conexion) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a BD']);
    exit();
}

// Quién realiza la acción
$usuario_admin = $_SESSION['usuario_user'] ?? 'admin';

// OBTENER DATOS DEL PAGO PRIMERO
$query_select = "SELECT * FROM pagos_usuarios WHERE id = ? AND estado = 'pendiente'";
$stmt_select = mysqli_prepare($conexion, $query_select);
mysqli_stmt_bind_param($stmt_select, "i", $pago_id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$pago_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$pago_data) {
    echo json_encode(['success' => false, 'error' => 'Pago no encontrado o ya procesado']);
    exit();
}

// INICIAR TRANSACCIÓN
mysqli_begin_transaction($conexion);

try {
    if ($nuevo_estado === 'verificado') {
        // 1. INSERTAR EN pagos_aprobados
        $query_aprobados = "INSERT INTO pagos_aprobados 
                           (pago_id, usuario_id, metodo_pago_id, monto, mes_pagado, 
                            referencia, comprobante, observaciones, fecha_pago, aprobado_por)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_aprobados = mysqli_prepare($conexion, $query_aprobados);
        mysqli_stmt_bind_param($stmt_aprobados, "iiidssssss", 
            $pago_id,
            $pago_data['usuario_id'],
            $pago_data['metodo_pago_id'],
            $pago_data['monto'],
            $pago_data['mes_pagado'],
            $pago_data['referencia'],
            $pago_data['comprobante'],
            $observaciones,
            $pago_data['fecha_pago'],
            $usuario_admin
        );
        mysqli_stmt_execute($stmt_aprobados);
        mysqli_stmt_close($stmt_aprobados);
        
        // 2. ELIMINAR DE pagos_usuarios
        $query_delete = "DELETE FROM pagos_usuarios WHERE id = ?";
        $stmt_delete = mysqli_prepare($conexion, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $pago_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
        
        $mensaje = "Pago #$pago_id APROBADO y movido a historial";
        
    } else { // RECHAZADO
        // 1. INSERTAR EN pagos_rechazados
        $query_rechazados = "INSERT INTO pagos_rechazados 
                            (pago_id, usuario_id, metodo_pago_id, monto, mes_pagado, 
                             referencia, comprobante, observaciones, razon_rechazo, fecha_pago, rechazado_por)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_rechazados = mysqli_prepare($conexion, $query_rechazados);
        mysqli_stmt_bind_param($stmt_rechazados, "iiidsssssss", 
            $pago_id,
            $pago_data['usuario_id'],
            $pago_data['metodo_pago_id'],
            $pago_data['monto'],
            $pago_data['mes_pagado'],
            $pago_data['referencia'],
            $pago_data['comprobante'],
            $pago_data['observaciones'],
            $observaciones,  // Aquí va la razón del rechazo
            $pago_data['fecha_pago'],
            $usuario_admin
        );
        mysqli_stmt_execute($stmt_rechazados);
        mysqli_stmt_close($stmt_rechazados);
        
        // 2. ELIMINAR DE pagos_usuarios
        $query_delete = "DELETE FROM pagos_usuarios WHERE id = ?";
        $stmt_delete = mysqli_prepare($conexion, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $pago_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
        
        $mensaje = "Pago #$pago_id RECHAZADO y movido a historial";
    }
    
    // CONFIRMAR TRANSACCIÓN
    mysqli_commit($conexion);
    
    echo json_encode([
        'success' => true, 
        'message' => $mensaje,
        'pago_id' => $pago_id,
        'accion' => $nuevo_estado
    ]);
    
} catch (Exception $e) {
    // REVERTIR EN CASO DE ERROR
    mysqli_rollback($conexion);
    
    echo json_encode([
        'success' => false, 
        'error' => 'Error en transacción: ' . $e->getMessage()
    ]);
}

mysqli_close($conexion);

?>