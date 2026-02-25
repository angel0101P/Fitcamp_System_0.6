<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitcamp System Manager</title>
    <link rel="icon" href="../images/Fitcamp_Logo.png">
    <link rel="stylesheet" href="../styles/styles_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- BARRA LATERAL -->
    <nav class="sidebar">
        <div class="user-section">
            <div class="profile-img-container">
                <img src="../images/Fitcamp_Logo.png" id="profile-pic" alt="Perfil">
                <label for="upload-photo" class="edit-photo">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="upload-photo" accept="image/*" style="display:none;">
            </div>
            <h3 id="nombre-usuario">Usuario</h3>
            <p>Cliente Activo 2026</p>
            <div class="badge-notificaciones" id="badge-notificaciones">
                <span id="contador-notificaciones">0</span>
            </div>
        </div>

        <div class="nav-container">
            <ul class="nav-links">
                <li>
                    <div class="nav-item active" onclick="cargarNotificaciones()">
                        <i class="fas fa-bell"></i> Notificaciones
                    </div>
                </li>

                <li>
                    <div class="nav-item" onclick="cargarSeccion('rutinas')">
                        <i class="fas fa-calendar-alt"></i> Rutinas y Actividades
                    </div>
                </li>
                <li>
                    <div class="nav-item" onclick="cargarSeccion('pagos')">
                        <i class="fas fa-credit-card"></i> Pago de Mensualidad
                    </div>
                </li>

                <!-- Menú Desplegable Herbalife -->
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-productos')">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Herbalife</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <ul id="sub-productos" class="submenu">
                        <li><a href="/Fitcamp%20System%20Manager/php/modulos/herbalife/solicitar_producto.php" onclick="cargarSeccion('solicitar-producto')">
                            <i class="fas fa-cart-plus"></i> Solicitar Producto
                        </a></li>
                        <li><a href="#" onclick="cargarSeccion('ver-solicitudes')">
                            <i class="fas fa-clipboard-list"></i> Ver solicitudes
                        </a></li>
                    </ul>
                </li>

                <li>
                    <div class="nav-item" onclick="cargarSeccion('nutricion')">
                        <i class="fas fa-apple-whole"></i> Nutrición
                    </div>
                </li>
                <li>
                    <div class="nav-item" onclick="cargarSeccion('progreso')">
                        <i class="fas fa-chart-line"></i> Mi Progreso
                    </div>
                </li>
                <li>
                    <div class="nav-item" onclick="cargarSeccion('perfil')">
                        <i class="fas fa-user-circle"></i> Mi Perfil
                    </div>
                </li>
            </ul>

            <div class="logout-section">
                <div class="nav-item logout-link" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </div>
            </div>
        </div>
    </nav>

    <!-- ÁREA DE CONTENIDO -->
    <div class="content" id="contenido-dinamico">
        <div class="card">
            <h3>🔔 Bienvenid@ al Fitcamp System Manager</h3>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../scripts/script_dasboard.js"></script>
</body>
</html>