<?php
// php/modulos/pagos.php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../auth/index_login.php');
    exit();
}

// Redirigir al módulo de pagos (archivo index.php dentro de la carpeta pagos/)
header('Location: pagos/index.php');
exit();
?>