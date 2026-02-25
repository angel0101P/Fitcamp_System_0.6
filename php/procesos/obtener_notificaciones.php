<?php
// /php/modulos/obtener_notificaciones.php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Incluir conexión
include '../config/conexion.php';

// Obtener el usuario actual de la sesión
$usuario_actual = $_SESSION['usuario_user'];

// CONSULTA CORREGIDA: Solo buscar notificaciones para este usuario
$query = "SELECT id, titulo_mensaje, mensaje, usuario_receptor, admin_remitente 
          FROM notificaciones_privadas 
          WHERE usuario_receptor = ? 
          ORDER BY id DESC";

// Preparar la consulta
$stmt = mysqli_prepare($conexion, $query);

// Verificar si hubo error al preparar
if (!$stmt) {
    echo json_encode(['error' => 'Error en la consulta: ']);
    exit;
}

// Vincular parámetro
mysqli_stmt_bind_param($stmt, "s", $usuario_actual);

// Ejecutar
$ejecutado = mysqli_stmt_execute($stmt);

if (!$ejecutado) {
    echo json_encode(['error' => 'Error al ejecutar: ' . mysqli_error($conexion)]);
    exit;
}

// Obtener resultados
$resultado = mysqli_stmt_get_result($stmt);

// Array para almacenar notificaciones
$notificaciones = [];

// Obtener todas las filas
while ($fila = mysqli_fetch_assoc($resultado)) {
    $notificaciones[] = [
        'id' => $fila['id'],
        'titulo' => $fila['titulo_mensaje'],
        'mensaje' => $fila['mensaje'],
        'receptor' => $fila['usuario_receptor'],
        'remitente' => $fila['admin_remitente'],
        'tipo' => 'privada'
    ];
}

// Cerrar statement
mysqli_stmt_close($stmt);

// Responder como JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'notificaciones' => $notificaciones,
    'total' => count($notificaciones),
    'usuario' => $usuario_actual
]);
?>