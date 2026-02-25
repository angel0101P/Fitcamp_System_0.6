<?php
session_start();
ini_set('display_errors','0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/conexion.php';

// Handler ready for production: no debug logs printed.

if (!isset($_SESSION['usuario_user'])) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'No autenticado']);
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$tipo = $_POST['tipo'] ?? '';

$referencia_id = isset($_POST['referencia_id']) ? intval($_POST['referencia_id']) : 0;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
$metodo_pago_id = isset($_POST['metodo_pago_id']) ? intval($_POST['metodo_pago_id']) : null;
$observaciones = $_POST['observaciones'] ?? '';
$referencia_pago = $_POST['referencia'] ?? '';

// Manejo de comprobante
$comprobante_path = null;
if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status'=>'error','message'=>'Error en la subida del comprobante']); exit();
    }
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($_FILES['comprobante']['size'] > $maxSize) {
        echo json_encode(['status'=>'error','message'=>'El comprobante excede el tamaño máximo permitido (5MB)']); exit();
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['comprobante']['tmp_name']);
    finfo_close($finfo);
    $mimeAllowed = ['image/jpeg','image/png','image/gif','application/pdf'];
    if (!in_array($mime, $mimeAllowed)) {
        echo json_encode(['status'=>'error','message'=>'Formato de comprobante no permitido']); exit();
    }
    $ext = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
    $dir = __DIR__ . '/../../../uploads/comprobantes/';
    if (!is_dir($dir) && !@mkdir($dir,0755,true)) {
        echo json_encode(['status'=>'error','message'=>'Error en el servidor (no se puede crear carpeta de comprobantes)']); exit();
    }
    $nombre_archivo = 'comp_'.date('Ymd_His').'_'.uniqid().'.'.$ext;
    $destino = $dir.$nombre_archivo;
    if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $destino)) {
        echo json_encode(['status'=>'error','message'=>'No se pudo guardar el comprobante en el servidor']); exit();
    }
    // Opcional: ajustar permisos
    @chmod($destino, 0644);
    $comprobante_path = 'uploads/comprobantes/'.$nombre_archivo;
}

if (!in_array($tipo,['producto','combo']) || $referencia_id <= 0 || $cantidad <= 0 || !$metodo_pago_id) {
    echo json_encode(['status'=>'error','message'=>'Datos inválidos']);
    exit();
}

// calcular total
if ($tipo === 'producto') {
    $stmt = $conexion->prepare('SELECT nombre, precio, stock FROM productos WHERE id = ? LIMIT 1');
    $stmt->bind_param('i',$referencia_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows === 0) { echo json_encode(['status'=>'error','message'=>'Producto no encontrado']); exit(); }
    $row = $res->fetch_assoc();
    $unit = floatval($row['precio']);
    $nombre_ref = $row['nombre'];
    $stmt->close();
} else {
    $stmt = $conexion->prepare('SELECT nombre, precio FROM combos WHERE id = ? LIMIT 1');
    $stmt->bind_param('i',$referencia_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows === 0) { echo json_encode(['status'=>'error','message'=>'Combo no encontrado']); exit(); }
    $row = $res->fetch_assoc();
    $unit = floatval($row['precio']);
    $nombre_ref = $row['nombre'];
    $stmt->close();
}
$total = $unit * $cantidad;
$estado = 'pendiente';
// insertar solicitud
$stmt = $conexion->prepare('INSERT INTO solicitudes_productos (usuario_id, tipo, referencia_id, cantidad, metodo_pago_id, total, observaciones, estado, comprobante_path, referencia_pago, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
if (!$stmt) {
    echo json_encode(['status'=>'error','message'=>'Error en prepare: '.mysqli_error($conexion)]); exit();
}
$stmt->bind_param('isiiidssss', $usuario_id, $tipo, $referencia_id, $cantidad, $metodo_pago_id, $total, $observaciones, $estado, $comprobante_path, $referencia_pago);
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo json_encode(['status'=>'success','id'=>$id]);
    $stmt->close();
    mysqli_close($conexion);
    exit();
}
$errorMsg = $stmt->error ?: mysqli_error($conexion);
// Preparar debug (limitar tamaño de strings)
$debug = [
    'usuario_id' => $usuario_id,
    'tipo' => $tipo,
    'referencia_id' => $referencia_id,
    'cantidad' => $cantidad,
    'metodo_pago_id' => $metodo_pago_id,
    'total' => $total,
    'observaciones' => $observaciones,
    'estado' => $estado,
    'comprobante_path' => $comprobante_path,
    'referencia_pago' => $referencia_pago,
];
foreach($debug as $k=>$v){
    if(is_string($v) && strlen($v)>200) $debug[$k] = substr($v,0,200).'...';
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status'=>'error',
    'message'=>'No se pudo crear la solicitud: '.$errorMsg,
    'debug'=>$debug
]);
exit();
?>