<?php
// guardar_metodo_pago.php
session_start();
include '../../../config/conexion.php';

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Sesión no iniciada'
    ]);
    exit();
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
    exit();
}

// Leer JSON del cuerpo de la petición
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Validar que se recibió JSON válido
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'JSON inválido: ' . json_last_error_msg()
    ]);
    exit();
}

// Validar datos requeridos
$errores = [];

if (empty($input['nombre'])) {
    $errores[] = 'El nombre es requerido';
}

if (empty($input['tipo'])) {
    $errores[] = 'El tipo es requerido';
}

if (count($errores) > 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => implode(', ', $errores)
    ]);
    exit();
}

try {
    // Preparar datos
    $id = isset($input['id']) && !empty($input['id']) ? intval($input['id']) : null;
    $nombre = mysqli_real_escape_string($conexion, trim($input['nombre']));
    $tipo = mysqli_real_escape_string($conexion, trim($input['tipo']));
    $descripcion = isset($input['descripcion']) ? mysqli_real_escape_string($conexion, trim($input['descripcion'])) : '';
    $instrucciones = isset($input['instrucciones']) ? mysqli_real_escape_string($conexion, trim($input['instrucciones'])) : '';
    $estado = isset($input['estado']) ? mysqli_real_escape_string($conexion, trim($input['estado'])) : 'activo';
    $icono = isset($input['icono']) ? mysqli_real_escape_string($conexion, trim($input['icono'])) : 'fa-credit-card';
    $color = isset($input['color']) ? mysqli_real_escape_string($conexion, trim($input['color'])) : '#4B0082';
    $datos_adicionales = isset($input['datos_adicionales']) ? mysqli_real_escape_string($conexion, trim($input['datos_adicionales'])) : '{}';
    $orden_visual = isset($input['orden_visual']) ? intval($input['orden_visual']) : 0;

    // Validar estado
    if (!in_array($estado, ['activo', 'inactivo'])) {
        $estado = 'activo';
    }

    // Validar datos_adicionales como JSON
    if (!empty($datos_adicionales) && $datos_adicionales !== '{}') {
        json_decode($datos_adicionales);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $datos_adicionales = '{}';
        }
    }

    if ($id) {
        // ACTUALIZAR método existente
        $query = "UPDATE metodos_pago SET 
                    nombre = '$nombre',
                    tipo = '$tipo',
                    descripcion = '$descripcion',
                    instrucciones = '$instrucciones',
                    estado = '$estado',
                    icono = '$icono',
                    color = '$color',
                    datos_adicionales = '$datos_adicionales',
                    orden_visual = $orden_visual,
                    fecha_actualizacion = NOW()
                  WHERE id = $id";
        
        $mensaje = "Método actualizado correctamente";
    } else {
        // INSERTAR nuevo método
        $query = "INSERT INTO metodos_pago 
                  (nombre, tipo, descripcion, instrucciones, estado, icono, color, datos_adicionales, orden_visual)
                  VALUES (
                    '$nombre',
                    '$tipo',
                    '$descripcion',
                    '$instrucciones',
                    '$estado',
                    '$icono',
                    '$color',
                    '$datos_adicionales',
                    $orden_visual
                  )";
        
        $mensaje = "Método creado correctamente";
    }

    // Ejecutar consulta
    $result = mysqli_query($conexion, $query);

    if (!$result) {
        throw new Exception("Error en la base de datos: " . mysqli_error($conexion));
    }

    // Obtener el ID si es nuevo
    if (!$id) {
        $id = mysqli_insert_id($conexion);
    }

    // Cerrar conexión
    mysqli_close($conexion);

    // Responder con éxito
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'id' => $id
    ]);

} catch(Exception $e) {
    // Cerrar conexión en caso de error
    if (isset($conexion)) {
        mysqli_close($conexion);
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar método: ' . $e->getMessage()
    ]);
}
?>