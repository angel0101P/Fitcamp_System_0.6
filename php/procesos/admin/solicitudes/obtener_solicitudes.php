<?php
session_start();
require_once '../../../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_user'])) {
    echo json_encode(['success'=>false,'error'=>'Sesión no iniciada']); exit();
}

$estadoFiltro = '';
if (isset($_GET['estado']) && in_array($_GET['estado'], ['pendiente','pagado','rechazado'])) {
    $estadoFiltro = " WHERE s.estado = '" . $_GET['estado'] . "'";
}

$query = "SELECT s.id, s.usuario_id, s.tipo, s.referencia_id, s.cantidad, s.total, s.metodo_pago_id, s.observaciones, s.estado, s.comprobante_path, s.referencia_pago, s.created_at,
    p.nombre AS producto_nombre, c.nombre AS combo_nombre, mp.nombre AS metodo_nombre
    FROM solicitudes_productos s
    LEFT JOIN productos p ON (s.tipo='producto' AND s.referencia_id = p.id)
    LEFT JOIN combos c ON (s.tipo='combo' AND s.referencia_id = c.id)
    LEFT JOIN metodos_pago mp ON s.metodo_pago_id = mp.id
    " . $estadoFiltro . " ORDER BY s.created_at DESC LIMIT 200";

$res = mysqli_query($conexion, $query);
if (!$res) {
    echo json_encode(['success'=>false,'error'=>mysqli_error($conexion)]); exit();
}

$sol = [];
$count = 0; $total = 0.0;
while ($r = mysqli_fetch_assoc($res)){
    $r['referencia_nombre'] = $r['producto_nombre'] ?: $r['combo_nombre'];
    $sol[] = $r; $count++; $total += floatval($r['total']);
}

mysqli_free_result($res);
mysqli_close($conexion);

echo json_encode(['success'=>true,'solicitudes'=>$sol,'estadisticas'=>['count'=>$count,'total'=>$total]]);
exit();
?>
