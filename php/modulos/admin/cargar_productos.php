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
$titulo = "Cargar Productos - Herbalife";

// Obtener productos desde la base de datos
$productos = [];
$sql = "SELECT id, nombre, descripcion, precio, stock, imagen_path, categoria FROM productos ORDER BY id DESC";
if ($result = mysqli_query($conexion, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
    mysqli_free_result($result);
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
            <p>Administrador - Productos</p>
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
                        <i class="fas fa-box-open"></i>
                        <span>Productos Herbalife</span>
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

    <div class="content" style="padding:24px;">
        <div class="card">
            <h2><i class="fas fa-box-open"></i> <?php echo $titulo; ?></h2>
            <p>Desde aquí puedes agregar nuevos productos al catálogo y ver el listado actual.</p>
        </div>

        <div class="card" style="margin-top:16px; display:flex; gap:20px; flex-wrap:wrap;">
            <div style="flex:1; min-width:320px;">
                <h3>Agregar Producto</h3>
                <form id="form-cargar-producto" method="POST" action="../../procesos/admin/herbalife/productos_upload.php" enctype="multipart/form-data">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required class="input-field">

                    <label>Descripción</label>
                    <textarea name="descripcion" rows="4" class="input-field"></textarea>

                    <label>Categoría</label>
                    <input type="text" name="categoria" class="input-field">

                    <label>Precio</label>
                    <input type="number" step="0.01" name="precio" required class="input-field">

                    <label>Stock</label>
                    <input type="number" name="stock" value="0" class="input-field">

                    <label>Imagen</label>
                    <input type="file" name="imagen" accept="image/*" class="input-field">

                    <div style="margin-top:12px;">
                        <button type="submit" class="btn-primary">Subir Producto</button>
                    </div>
                </form>
                <div id="mensaje-resultado" style="margin-top:12px;"></div>
            </div>

            <div style="flex:2; min-width:320px;">
                <h3>Listado de Productos</h3>
                <div id="lista-productos" class="grid-productos">
                    <?php foreach ($productos as $p): ?>
                        <?php
                        $img_rel = $p['imagen_path'];
                        $img_exists = !empty($img_rel) && file_exists(__DIR__ . '/../../../' . ltrim($img_rel, '/'));
                        $img_src = $img_exists ? '../../../' . ltrim($img_rel, '/') : '../../../images/Fitcamp_Logo.png';
                        ?>
                        <div class="card-producto" 
                             data-id="<?php echo $p['id']; ?>"
                             data-nombre="<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>"
                             data-descripcion="<?php echo htmlspecialchars($p['descripcion'], ENT_QUOTES); ?>"
                             data-categoria="<?php echo htmlspecialchars($p['categoria'], ENT_QUOTES); ?>"
                             data-precio="<?php echo htmlspecialchars(number_format($p['precio'],2), ENT_QUOTES); ?>"
                             data-stock="<?php echo (int)$p['stock']; ?>"
                             data-imagen="<?php echo htmlspecialchars($p['imagen_path'], ENT_QUOTES); ?>">
                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                            <h4><?php echo htmlspecialchars($p['nombre']); ?></h4>
                            <p><?php echo htmlspecialchars($p['categoria']); ?> — $<?php echo number_format($p['precio'],2); ?></p>
                            <p>Stock: <?php echo (int)$p['stock']; ?></p>
                            <div class="acciones-producto">
                                <button class="btn-small btn-edit">Editar</button>
                                <button class="btn-small btn-delete">Eliminar</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE EDICIÓN DE PRODUCTO -->
    <div id="modal-producto" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3 id="modal-producto-titulo"><i class="fas fa-edit"></i> Editar Producto</h3>
                <button class="modal-cerrar" onclick="cerrarModalProducto()">&times;</button>
            </div>
            <form id="form-editar-producto" enctype="multipart/form-data">
                <input type="hidden" id="edit-id" name="id" value="">
                <div style="padding:20px; display:grid; gap:10px;">
                    <label>Nombre</label>
                    <input type="text" id="edit-nombre" name="nombre" class="input-field" required>

                    <label>Descripción</label>
                    <textarea id="edit-descripcion" name="descripcion" rows="4" class="input-field"></textarea>

                    <label>Categoría</label>
                    <input type="text" id="edit-categoria" name="categoria" class="input-field">

                    <label>Precio</label>
                    <input type="number" step="0.01" id="edit-precio" name="precio" class="input-field" required>

                    <label>Stock</label>
                    <input type="number" id="edit-stock" name="stock" class="input-field">

                    <label>Imagen (opcional - reemplaza la actual)</label>
                    <input type="file" id="edit-imagen" name="imagen" accept="image/*" class="input-field">

                    <div id="preview-imagen" style="display:flex; gap:10px; align-items:center;">
                        <img id="preview-img-el" src="" style="width:100px; height:80px; object-fit:cover; border-radius:6px;" alt="Preview">
                        <div id="preview-text" style="color:#ccc;">Imagen actual</div>
                    </div>

                    <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:10px;">
                        <button type="button" class="btn-cancelar btn-small" onclick="cerrarModalProducto()">Cancelar</button>
                        <button type="submit" class="btn-primary btn-small">Guardar cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../scripts/cargar_productos.js"></script>
</body>
</html>
