<?php
// php/modulos/pagos/aprobados.php
session_start();
require_once '../../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../../auth/index_login.php');
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = $_SESSION['usuario_user'] ?? 'Usuario';
$titulo = "Historial de Pagos Aprobados";

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Fitcamp</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../../styles/aprobados.css">
    <link rel="stylesheet" href="../../../styles/styles_dashboard.css">
    <link rel="stylesheet" href="../../../styles/styles_pagos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
    <!-- BARRA LATERAL ESPECÍFICA PARA PAGOS -->
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
                    <a href="../../../dashboard/admin/index_admin.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="nav-item active">
                        <i class="fas fa-check-circle"></i>
                        <span>Pagos aprobados</span>
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
                <h2><i class="fas fa-check-circle"></i> <?php echo $titulo; ?></h2>
            </div>
            
            <!-- Lista de Pagos Aprobados -->
            <div class="seccion-metodos">
                <h3><i class="fas fa-history"></i> Historial de Pagos Aprobados</h3>
                <div id="lista-aprobados">
                    <div class="cargando-metodos">
                        <div class="spinner"></div>
                        <p>Cargando Pagos Aprobados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../../../scripts/pagos_aprobados.js"></script>
</body>
</html>