<?php

session_start();

// Verifica si hay una sesión activa
if(isset($_SESSION['usuario_id'])) {
    //Registrar logout en base de datos o logs
    $usuario_id = $_SESSION['id'];
    $usuario_nombre = $_SESSION['nombre_completo'] ?? 'Desconocido';
    
    // registrar el logout
    error_log("Logout: Usuario ID $usuario_id ($usuario_nombre) cerró sesión");
}

// Limpiar todas las variables de sesión
session_unset();

// Destruir la sesión completamente
session_destroy();

// Redirigir
header("Location: index_login.php");
exit();
?>