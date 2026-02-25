<?php
session_start();

// Solo admin puede buscar
if (!isset($_SESSION['usuario_user'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Incluir conexión
include '../config/conexion.php';

// Verificar conexión
if (!$conexion) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

$busqueda = $_GET['q'] ?? '';
$limit = min($_GET['limit'] ?? 20, 50);

// USAR TUS COLUMNAS EXACTAS:
// id, name_, surname, email, user (no username), password_
$query = "SELECT id, user, name_, surname, email 
          FROM usuarios 
          WHERE (user LIKE ? 
             OR name_ LIKE ? 
             OR surname LIKE ? 
             OR email LIKE ?)
          ORDER BY name_, surname
          LIMIT ?";

$stmt = mysqli_prepare($conexion, $query);

if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$param_busqueda = "%" . $busqueda . "%";
mysqli_stmt_bind_param($stmt, "ssssi", 
    $param_busqueda, 
    $param_busqueda, 
    $param_busqueda, 
    $param_busqueda, 
    $limit
);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$usuarios = [];
while($row = mysqli_fetch_assoc($result)){
    $usuarios[] = [
        'id' => $row['id'],
        'username' => $row['user'], // Mapear 'user' a 'username' para el JavaScript
        'nombre_completo' => $row['name_'] . ' ' . $row['surname'],
        'email' => $row['email'],
        'nombre' => $row['name_'],
        'apellido' => $row['surname']
    ];
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'usuarios' => $usuarios,
    'total' => count($usuarios)
]);
?>