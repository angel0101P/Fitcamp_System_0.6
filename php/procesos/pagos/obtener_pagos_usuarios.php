<?php
// procesos/pagos/obtener_pagos_usuarios.php
session_start();
require_once '../../config/conexion.php';

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$estado = $_POST['estado'] ?? 'todos';

try {
    $pagos = [];
    
    // Obtener datos según el estado solicitado
    switch($estado) {
        case 'pendiente':
            // Pagos pendientes
            $query = "SELECT *, 'pendiente' as estado FROM pagos_usuarios 
                     WHERE usuario_id = ? AND estado = 'pendiente' 
                     ORDER BY fecha_pago DESC";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }
            mysqli_stmt_close($stmt);
            break;
            
        case 'aprobado':
            // Pagos aprobados
            $query = "SELECT *, 'aprobado' as estado FROM pagos_aprobados 
                     WHERE usuario_id = ? 
                     ORDER BY fecha_aprobacion DESC";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }
            mysqli_stmt_close($stmt);
            break;
            
        case 'rechazado':
            // Pagos rechazados
            $query = "SELECT *, 'rechazado' as estado FROM pagos_rechazados 
                     WHERE usuario_id = ? 
                     ORDER BY fecha_rechazo DESC";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }
            mysqli_stmt_close($stmt);
            break;
            
        case 'todos':
        default:
            // Todos los pagos (combinar las tres tablas)
            
            // 1. Pendientes
            $query = "SELECT *, 'pendiente' as estado FROM pagos_usuarios 
                     WHERE usuario_id = ? AND estado = 'pendiente' 
                     ORDER BY fecha_pago DESC";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }
            mysqli_stmt_close($stmt);
            
            // 2. Aprobados
            $query = "SELECT *, 'aprobado' as estado FROM pagos_aprobados 
                     WHERE usuario_id = ? 
                     ORDER BY fecha_aprobacion DESC";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }
            mysqli_stmt_close($stmt);
            
            // 3. Rechazados
            $query = "SELECT *, 'rechazado' as estado FROM pagos_rechazados 
                     WHERE usuario_id = ? 
                     ORDER BY fecha_rechazo DESC";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "i", $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }
            mysqli_stmt_close($stmt);
    }
    
    echo json_encode([
        'success' => true,
        'pagos' => $pagos,
        'total' => count($pagos),
        'estado' => $estado
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la consulta: ' . $e->getMessage()
    ]);
}

mysqli_close($conexion);
?>