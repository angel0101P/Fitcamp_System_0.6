<?php
// php/modulos/pagos/index.php
session_start();
require_once '../../config/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../../auth/index_login.php');
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = $_SESSION['usuario_user'] ?? 'Usuario';
$titulo = "Pago de Mensualidad";

// Cerrar conexión temprano
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Fitcamp</title>
    
    <!-- CSS -->
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
                    <a href="../../../dashboard/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item active">
                        <i class="fas fa-home"></i>
                        <span>Inicio Pagos</span>
                    </a>
                </li>
                <li>
                    <a href="ver_pagos.php" class="nav-item">
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
                <h2><i class="fas fa-credit-card"></i> <?php echo $titulo; ?></h2>
            </div>
            
            <!-- Métodos de Pago Disponibles -->
            <div class="seccion-metodos">
                <h3><i class="fas fa-wallet"></i> Métodos de Pago Disponibles</h3>
                <div id="lista-metodos" class="grid-metodos">
                    <div class="cargando-metodos">
                        <div class="spinner"></div>
                        <p>Cargando métodos de pago...</p>
                    </div>
                </div>
            </div>
            
            <!-- Formulario de Pago (se mostrará cuando seleccione método) -->
            <div id="formulario-pago" class="formulario-pago" style="display:none;">
                <!-- Se cargará dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        // CONFIGURACIÓN MÍNIMA
        const PAGOS_CONFIG = {
            api: {
                metodos: '../../procesos/admin/metodos_pago/obtener_metodos_pago.php',
                registrar: '../../procesos/pagos/registrar_pago.php'
            }
        };
        
        const USUARIO_ID = <?php echo $usuario_id; ?>;
        
        console.log('Config cargada. Usuario:', USUARIO_ID);
        console.log('Endpoint métodos:', PAGOS_CONFIG.api.metodos);
    </script>

    <!-- JavaScript -->
    <script src="../../../scripts/pagos.js"></script>
</body>
</html>