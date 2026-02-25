<?php
// php/modulos/pagos/ver_pagos.php
session_start();
require_once '../../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../../auth/index_login.php');
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = $_SESSION['usuario_user'] ?? 'Usuario';
$titulo = "Mis Pagos";

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Fitcamp</title>
    
    <link rel="stylesheet" href="../../../styles/styles_dashboard.css">
    <link rel="stylesheet" href="../../../styles/styles_pagos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- BARRA LATERAL -->
    <nav class="sidebar-pagos">
        <div class="user-section">
            <div class="profile-img-container">
                <img src="../../../images/Fitcamp_Logo.png" alt="Perfil">
            </div>
            <h3><?php echo htmlspecialchars($nombre_usuario); ?></h3>
            <p>Módulo de Pagos</p>
        </div>

        <div class="nav-container">
            <ul class="nav-links">
                <li>
                    <a href="../../../dashboard/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="index.php" class="nav-item">
                        <i class="fas fa-home"></i>
                        <span>Inicio Pagos</span>
                    </a>
                </li>
                <li>
                    <a href="ver_pagos.php" class="nav-item active">
                        <i class="fas fa-history"></i>
                        <span>Ver Mis Pagos</span>
                    </a>
                </li>
            </ul>
            
            <div class="logout-section">
                <div class="nav-item logout-link" onclick="if(confirm('¿Cerrar sesión?')) window.location.href='../../auth/cerrar_sesion.php'">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content-pagos">
        <div class="modulo-pagos">
            <div class="modulo-header">
                <h2><i class="fas fa-history"></i> <?php echo $titulo; ?></h2>
            </div>
            
            <!-- Pestañas -->
            <div class="contenedor-pestanas">
                <button class="pestana activa" data-estado="todos">
                    <i class="fas fa-list"></i> Todos
                </button>
                <button class="pestana" data-estado="pendiente">
                    <i class="fas fa-clock"></i> Pendientes
                </button>
                <button class="pestana" data-estado="aprobado">
                    <i class="fas fa-check-circle"></i> Aprobados
                </button>
                <button class="pestana" data-estado="rechazado">
                    <i class="fas fa-times-circle"></i> Rechazados
                </button>
            </div>
            
            <div id="formulario-pago" class="contenedor-pagos-usuario">
                <!-- Los pagos se cargarán aquí dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        const USUARIO_ID = <?php echo $usuario_id; ?>;
    </script>

    <script src="../../../scripts/pagos_usuarios.js"></script>
</body>
</html>