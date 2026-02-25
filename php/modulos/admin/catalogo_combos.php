<?php
session_start();
require_once '../../config/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../../auth/index_login.php');
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = $_SESSION['usuario_user'] ?? 'Usuario';
$titulo = "Catálogo de Combos - Productos";

// Obtener productos disponibles para seleccionar en combos
$productos = [];
$sql = "SELECT id, nombre, precio, stock, imagen_path FROM productos ORDER BY nombre";
if ($result = mysqli_query($conexion, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
    mysqli_free_result($result);
}

// Obtener combos existentes (si existen tablas)
$combos = [];
$sql2 = "SELECT id, nombre, descripcion, precio, imagen_path, created_at FROM combos ORDER BY id DESC";
if ($result2 = @mysqli_query($conexion, $sql2)) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $combos[] = $row;
    }
    mysqli_free_result($result2);
}

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Fitcamp</title>
    <link rel="stylesheet" href="../../../styles/styles_dashboard.css">
    <link rel="stylesheet" href="../../../styles/styles_admin.css">
    <link rel="stylesheet" href="../../../styles/styles_products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="sidebar">
        <div class="user-section">
            <div class="profile-img-container">
                <img src="../../../images/Fitcamp_Logo.png" alt="Perfil">
            </div>
            <h3><?php echo htmlspecialchars($nombre_usuario); ?></h3>
            <p>Administrador del Sistema</p>
        </div>

        <div class="nav-container">
            <ul class="nav-links">
                <li>
                    <a href="../../../dashboard/admin/index_admin.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver al Dashboard</span>
                    </a>
                </li>
                <li class="has-submenu">
                    <div class="nav-item" onclick="toggleSubmenu(event, 'sub-productos')">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Productos Herbalife</span>
                    </div>

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

    <div class="content" style="padding:24px;">
        <div class="card modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-boxes"></i> <?php echo $titulo; ?></h2>
            </div>

            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div style="flex:1; min-width:320px;">
                    <h3>Crear Combo</h3>
                    <form id="form-crear-combo" enctype="multipart/form-data">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="combo-nombre" class="input-field" required>

                        <label>Descripción</label>
                        <textarea name="descripcion" id="combo-descripcion" class="input-field" rows="3"></textarea>

                        <label>Precio del combo</label>
                        <input type="number" step="0.01" name="precio" id="combo-precio" class="input-field" required>

                        <label>Imagen del combo (opcional)</label>
                        <input type="file" name="imagen" id="combo-imagen" accept="image/*" class="input-field">

                        <label>Productos (selecciona y asigna cantidad)</label>
                        <div style="max-height:320px; overflow:auto; border:1px solid #4B0082; padding:8px; border-radius:8px;">
                            <?php foreach ($productos as $prod): ?>
                                <div style="display:flex; align-items:center; gap:10px; padding:6px; border-bottom:1px dashed rgba(255,255,255,0.03);">
                                    <input type="checkbox" class="prod-checkbox" data-id="<?php echo $prod['id']; ?>">
                                    <img src="<?php echo !empty($prod['imagen_path']) ? '../../../'.ltrim($prod['imagen_path'],'/') : '../../../images/Fitcamp_Logo.png'; ?>" style="width:60px; height:45px; object-fit:cover; border-radius:6px;">
                                    <div style="flex:1;">
                                        <div style="font-weight:600; color:#9370DB"><?php echo htmlspecialchars($prod['nombre']); ?></div>
                                        <div style="font-size:0.9rem; color:#ccc">$<?php echo number_format($prod['precio'],2); ?> — Stock: <?php echo (int)$prod['stock']; ?></div>
                                    </div>
                                    <div style="width:80px;">
                                        <input type="number" class="prod-cantidad input-field" data-id="<?php echo $prod['id']; ?>" value="1" min="1">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div style="margin-top:12px; display:flex; gap:10px; justify-content:flex-end;">
                            <button type="submit" class="btn-primary">Crear Combo</button>
                        </div>
                        <div id="combo-mensaje" style="margin-top:8px;"></div>
                    </form>
                </div>

                <div style="flex:2; min-width:320px;">
                    <h3>Combos existentes</h3>
                    <div id="lista-combos" class="grid-productos">
                        <?php foreach ($combos as $c): ?>
                            <div class="card-producto">
                                <?php if (!empty($c['imagen_path']) && file_exists(__DIR__ . '/../../../' . ltrim($c['imagen_path'],'/'))): ?>
                                    <img src="../../../<?php echo ltrim($c['imagen_path'],'/'); ?>">
                                <?php else: ?>
                                    <img src="../../../images/Fitcamp_Logo.png">
                                <?php endif; ?>
                                <h4><?php echo htmlspecialchars($c['nombre']); ?></h4>
                                <p><?php echo htmlspecialchars($c['descripcion']); ?></p>
                                <p>Precio: $<?php echo number_format($c['precio'],2); ?></p>
                                <small style="color:#aaa;">Creado: <?php echo $c['created_at']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="../../../scripts/catalogo_combos.js"></script>
</body>
</html>
