<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) { echo json_encode(['solicitudes'=>[]]); exit(); }
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;

$res = @mysqli_query($conexion, "SELECT s.id, s.tipo, s.referencia_id, s.cantidad, s.total, s.estado, s.created_at,
    p.nombre AS producto_nombre, c.nombre AS combo_nombre
    FROM solicitudes_productos s
    LEFT JOIN productos p ON (s.tipo='producto' AND s.referencia_id = p.id)
    LEFT JOIN combos c ON (s.tipo='combo' AND s.referencia_id = c.id)
    WHERE s.usuario_id = " . intval($usuario_id) . " ORDER BY s.created_at DESC LIMIT 50");

$rows = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
}

echo json_encode(['solicitudes'=>$rows]);
exit();
?>