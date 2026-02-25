<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fitcamp System Manager</title>
    <link rel="stylesheet" href="../../styles/styles_admin.css">
    <link rel="icon" type="image/png" href="../../images/Fitcamp_Logo.png">
    <!-- FontAwesome CORREGIDO -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- BARRA LATERAL -->
    <nav class="sidebar">
        <div class="user-section">
            <div class="profile-img-container">
                <!-- Imagen de Perfil -->
                <img src="../../images/Fitcamp_Logo.png" id="profile-pic" alt="Perfil">
                <label for="upload-photo" class="edit-photo">
                    <i class="fas fa-camera"></i>
                </label>
                <!-- Input oculto para cambiar foto -->
                <input type="file" id="upload-photo" accept="image/*" style="display:none;">
            </div>
            <h3>ADMIN-Fitcamp</h3>
            <p>Administrador del Sistema</p>
        </div>

        <div class="nav-container">
            <ul class="nav-links">
                <!-- Notificaciones -->
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-notificaciones')">
                        <i class="fas fa-bell"></i>
                        <span>Notificaciones</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <ul id="sub-notificaciones" class="submenu">
                        <li><a href="../../php/modulos/notificaciones/notificaciones_generales.php" onclick="cargarSeccion('escribir')">Escribir</a></li>
                        <li><a href="#" onclick="cargarSeccion('todas-notificaciones')">Todas las Notificaciones</a></li>
                    </ul>
                </li>
                
                <!-- Rutinas y Actividades -->
                <li>
                    <div class="nav-item" onclick="cargarSeccion('rutinas')">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Rutinas y Actividades</span>
                    </div>
                </li>

                <!-- Lista de Pagos -->
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-pagos')">
                        <i class="fas fa-credit-card"></i>
                        <span>Lista de pagos</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <ul id="sub-pagos" class="submenu">
                        <li><a href="#" onclick="cargarSeccion('estadistica')">Estadísticas</a></li>
                        <li><a href="../../php/modulos/admin/pagos_pendientes.php" onclick="cargarSeccion('pendientes')">Pagos Pendientes</a></li>
                        <li><a href="../../php/modulos/admin/aprobados.php" onclick="cargarSeccion('aprobados')">Pagos Aprobados</a></li>
                        <li><a href="../../php/modulos/admin/rechazados.php" onclick="cargarSeccion('aprobados')">Pagos Rechazados</a></li>
                        <li><a href="../../php/modulos/admin/metodos_pago.php" onclick="cargarSeccion('configuracion-metodo')">Configuración de Método</a></li>
                    </ul>
                </li>

                <!-- Productos Herbalife -->
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-productos')">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Productos Herbalife</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <ul id="sub-productos" class="submenu">
                        <li><a href="#" onclick="cargarSeccion('estadisticas-ventas')">Estadísticas de Ventas</a></li>
                        <li><a href="../../php/modulos/admin/catalogo_combos.php" onclick="cargarSeccion('ver-catalogo')">Catálogo de Productos</a></li>
                        <li><a href="../../php/modulos/admin/cargar_productos.php" onclick="cargarSeccion('cargar-productos')">Cargar Productos</a></li>
                        <li><a href="../../php/modulos/admin/ver_solicitudes.php" onclick="cargarSeccion('ver-solicitudes')">Ver Solicitudes</a></li>
                    </ul>
                </li>

                <!-- Nutrición -->
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-nutricion')">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrición</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <ul id="sub-nutricion" class="submenu">
                        <li><a href="#" onclick="cargarSeccion('herbalife')">Herbalife</a></li>
                        <li><a href="#" onclick="cargarSeccion('cotidiana')">Cotidiana</a></li>
                    </ul>   
                </li>

                <!-- Ver Progresos -->
                <li>
                    <div class="nav-item" onclick="cargarSeccion('progresos')">
                        <i class="fas fa-chart-line"></i>
                        <span>Ver Progresos</span>
                    </div>
                </li>
                
                <!-- Lista de Clientes -->
                <li>
                    <div class="nav-item" onclick="cargarSeccion('lista-clientes')">
                        <i class="fas fa-chart-line"></i>
                        <span>Lista de Clientes</span>
                    </div>
                </li>
                
                <!-- Ver Perfiles -->
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-perfiles')">
                        <i class="fas fa-user-circle"></i>
                        <span>Ver Perfiles</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <ul id="sub-perfiles" class="submenu">
                        <li><a href="#" onclick="cargarSeccion('usuarios')">Ver Usuarios</a></li>
                        <li><a href="#" onclick="cargarSeccion('rol-usuario')">Editar Rol de Usuario</a></li>
                    </ul>   
                </li>
                
                <!-- Mi Perfil -->
                <li>
                    <div class="nav-item" onclick="cargarSeccion('perfil')">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
                    </div>
                </li>
            </ul>

            <div class="logout-section">
                <div class="nav-item logout-link" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content" id="contenido-dinamico">
        <div class="card">
            <h3>🔔 Bienvenid@ al Fitcamp System Manager</h3>
            <p>Selecciona una opción del menú lateral para comenzar.</p>
        </div>
    </div>

    <script src="../../scripts/script_admin.js"></script>
</body>

</html>