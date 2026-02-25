<?php
// procesos/pagos/registrar_pago.php - VERSIÓN CON VALIDACIÓN DE DIRECTORIO
session_start();

// Limpiar buffer
if (ob_get_length()) ob_clean();

header('Content-Type: application/json; charset=utf-8');

// 1. VERIFICAR SESIÓN
$usuario_id = $_SESSION['id'] ?? 0;

if (!$usuario_id) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión']);
    exit();
}

// 2. VERIFICAR MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

// 3. CONEXIÓN A BD
require_once '../../config/conexion.php';

if (!$conexion) {
    echo json_encode(['success' => false, 'error' => 'Error BD']);
    exit();
}

// 4. OBTENER DATOS
$metodo_id = $_POST['metodo_id'] ?? 0;
$monto = $_POST['monto'] ?? 0;
$mes = $_POST['mes'] ?? '';
$referencia = $_POST['referencia'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';

// 5. VALIDACIONES
if ($metodo_id <= 0 || $monto <= 0 || empty($mes)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit();
}

// 6. VERIFICAR ARCHIVO
if (!isset($_FILES['comprobante'])) {
    echo json_encode(['success' => false, 'error' => 'No se subió archivo']);
    exit();
}

$archivo = $_FILES['comprobante'];

// Verificar error de subida
if ($archivo['error'] !== UPLOAD_ERR_OK) {
    $errores_upload = [
        UPLOAD_ERR_INI_SIZE => 'Archivo demasiado grande',
        UPLOAD_ERR_FORM_SIZE => 'Archivo excede el tamaño máximo del formulario',
        UPLOAD_ERR_PARTIAL => 'Archivo subido parcialmente',
        UPLOAD_ERR_NO_FILE => 'No se subió archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'No existe directorio temporal',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
        UPLOAD_ERR_EXTENSION => 'Extensión no permitida'
    ];
    
    echo json_encode([
        'success' => false,
        'error' => 'Error al subir: ' . ($errores_upload[$archivo['error']] ?? 'Error desconocido')
    ]);
    exit();
}

// 7. VALIDAR ARCHIVO
$extensiones = ['jpg', 'jpeg', 'png', 'pdf', 'gif'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $extensiones)) {
    echo json_encode([
        'success' => false,
        'error' => 'Formato no permitido. Use: ' . implode(', ', $extensiones)
    ]);
    exit();
}

// Tamaño máximo: 5MB
if ($archivo['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'Archivo muy grande (máx 5MB)']);
    exit();
}

// 8. CREAR DIRECTORIO - CAMBIO CLAVE AQUÍ
// Cambia de rutas relativas a cálculo dinámico
$directorio_actual = dirname(__FILE__); // /opt/lampp/htdocs/Fitcamp System Manager/php/procesos/pagos
$directorio_base = dirname(dirname(dirname($directorio_actual))) . '/uploads/'; // Retrocede 3 niveles
$directorio_comprobantes = $directorio_base . 'comprobantes/';

// Verificar si existe el directorio base
if (!file_exists($directorio_base)) {
    if (!mkdir($directorio_base, 0755, true)) {
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo crear directorio base',
            'debug' => [
                'directorio' => $directorio_base,
                'ruta_actual' => $directorio_actual,
                'niveles' => 'Se retrocedieron 3 niveles desde: ' . $directorio_actual
            ]
        ]);
        exit();
    }
}

// Verificar si existe el directorio de comprobantes
if (!file_exists($directorio_comprobantes)) {
    if (!mkdir($directorio_comprobantes, 0755, true)) {
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo crear directorio de comprobantes',
            'debug' => ['directorio' => $directorio_comprobantes]
        ]);
        exit();
    }
}

// 9. VERIFICAR PERMISOS
if (!is_writable($directorio_comprobantes)) {
    // Intentar cambiar permisos
    if (!chmod($directorio_comprobantes, 0755)) {
        echo json_encode([
            'success' => false,
            'error' => 'Directorio sin permisos de escritura',
            'debug' => [
                'permisos' => substr(sprintf('%o', fileperms($directorio_comprobantes)), -4),
                'escribible' => is_writable($directorio_comprobantes),
                'directorio' => $directorio_comprobantes
            ]
        ]);
        exit();
    }
}

// 10. CREAR NOMBRE DE ARCHIVO
$nombre_archivo = 'comprobante_' . $usuario_id . '_' . time() . '_' . uniqid() . '.' . $ext;
$ruta_completa = $directorio_comprobantes . $nombre_archivo;

// 11. MOVER ARCHIVO CON VERIFICACIÓN
if (!file_exists($archivo['tmp_name'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Archivo temporal no existe',
        'debug' => ['tmp_name' => $archivo['tmp_name']]
    ]);
    exit();
}

// Verificar que se pueda leer el archivo temporal
if (!is_uploaded_file($archivo['tmp_name'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No es un archivo subido válido'
    ]);
    exit();
}

// Intentar mover el archivo
if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
    // 12. INSERTAR EN BD
    try {
        $query = "INSERT INTO pagos_usuarios (
                    usuario_id, metodo_pago_id, monto, mes_pagado, 
                    referencia, comprobante, observaciones, estado, fecha_pago
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())";
        
        $stmt = mysqli_prepare($conexion, $query);
        
        if (!$stmt) {
            throw new Exception("Error SQL: " . mysqli_error($conexion));
        }
        
        $referencia_safe = !empty($referencia) ? $referencia : null;
        $observaciones_safe = !empty($observaciones) ? $observaciones : null;
        
        mysqli_stmt_bind_param($stmt, "iidssss", 
            $usuario_id, $metodo_id, $monto, $mes, 
            $referencia_safe, $nombre_archivo, $observaciones_safe
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecución: " . mysqli_stmt_error($stmt));
        }
        
        $pago_id = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        
        // 13. RESPUESTA EXITOSA
        echo json_encode([
            'success' => true,
            'message' => '✅ Pago registrado exitosamente',
            'data' => [
                'pago_id' => $pago_id,
                'monto' => number_format($monto, 2),
                'mes' => $mes,
                'archivo' => $nombre_archivo,
                'ruta' => $ruta_completa
            ]
        ]);
        
    } catch (Exception $e) {
        // Eliminar archivo si hubo error en BD
        if (file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }
        
        echo json_encode([
            'success' => false,
            'error' => 'Error BD: ' . $e->getMessage()
        ]);
        
        if (isset($conexion)) {
            mysqli_close($conexion);
        }
    }
    
} else {
    // Error al mover archivo
    $error_info = error_get_last();
    
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar archivo en el servidor',
        'debug' => [
            'tmp_name' => $archivo['tmp_name'],
            'destino' => $ruta_completa,
            'tmp_exists' => file_exists($archivo['tmp_name']),
            'dest_dir_exists' => file_exists($directorio_comprobantes),
            'dest_dir_writable' => is_writable($directorio_comprobantes),
            'last_error' => $error_info,
            'php_errors' => ini_get('display_errors')
        ]
    ]);
}

exit();
?>