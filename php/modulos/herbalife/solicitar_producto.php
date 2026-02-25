    <!-- MODAL EMERGENTE DE COMPRA -->
    <div id="modal-compra" class="modal-compra" style="display:none;">
        <div class="modal-compra-bg"></div>
        <div class="modal-compra-content panel">
            <button class="modal-close" id="modal-close-btn">&times;</button>
            <div class="modal-producto-info">
                <img id="modal-img" src="../../../images/Fitcamp_Logo.png" alt="Imagen" class="modal-img">
                <div>
                    <h3 id="modal-nombre">Producto/Combo</h3>
                    <p id="modal-precio">$0.00</p>
                </div>
            </div>
            <form id="modal-form" enctype="multipart/form-data" autocomplete="off">
                <div class="form-row">
                    <label for="modal-metodo">Método de pago</label>
                    <select id="modal-metodo" name="metodo_pago_id" class="input-field" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($metodos as $m): ?>
                            <option value="<?php echo $m['id']; ?>" data-instrucciones="<?php echo htmlspecialchars($m['instrucciones']); ?>"><?php echo htmlspecialchars($m['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="modal-info-metodo" class="metodo-detalle" style="display:none;"></div>
                <div class="form-row">
                    <label for="modal-cantidad">Cantidad</label>
                    <input type="number" id="modal-cantidad" name="cantidad" class="input-field" value="1" min="1" required>
                </div>
                <div class="form-row">
                    <label for="modal-referencia">Número de referencia</label>
                    <input type="text" id="modal-referencia" name="referencia" class="input-field" maxlength="32" placeholder="Ej: 12345678">
                </div>
                <div class="form-row">
                    <label for="modal-comprobante">Adjuntar comprobante</label>
                    <input type="file" id="modal-comprobante" name="comprobante" accept="image/*,application/pdf">
                </div>
                <div class="form-row">
                    <label for="modal-observaciones">Nombre y apellido</label>
                    <textarea id="modal-observaciones" name="observaciones" class="input-field" rows="2" placeholder="Nombre y apellido" required></textarea>
                </div>
                <input type="hidden" id="modal-tipo" name="tipo">
                <input type="hidden" id="modal-id" name="referencia_id">
                <div class="form-row actions">
                    <button type="submit" class="btn-primary">Enviar solicitud</button>
                </div>
                <div id="modal-mensaje-resultado" class="mensaje-resultado"></div>
            </form>
        </div>
    </div>
<?php
session_start();
require_once '../../config/conexion.php';

// Verificar sesión de usuario
if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../auth/index_login.php');
    exit();
}

$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = $_SESSION['usuario_user'] ?? 'Usuario';
$titulo = "Solicitar Producto / Combo";

// Obtener productos y combos para mostrar
$productos = [];
$res = mysqli_query($conexion, "SELECT id, nombre, precio, stock, imagen_path FROM productos WHERE stock > 0 ORDER BY nombre");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) $productos[] = $row;
    mysqli_free_result($res);
}

$combos = [];
$res2 = @mysqli_query($conexion, "SELECT id, nombre, descripcion, precio, imagen_path FROM combos ORDER BY nombre");
if ($res2) {
    while ($row = mysqli_fetch_assoc($res2)) $combos[] = $row;
    mysqli_free_result($res2);
}

// Obtener métodos de pago (si existe tabla de metodos)
$metodos = [];
$res3 = @mysqli_query($conexion, "SELECT id, nombre, instrucciones, estado FROM metodos_pago WHERE estado = 'activo' ORDER BY id");
if ($res3) {
    while ($row = mysqli_fetch_assoc($res3)) $metodos[] = $row;
    mysqli_free_result($res3);
}

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo; ?> - Fitcamp</title>
    <link rel="stylesheet" href="../../../styles/styles_dashboard.css">
    <link rel="stylesheet" href="../../../styles/styles_products.css">
    <link rel="stylesheet" href="../../../styles/styles_herbalife.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="sidebar">
        <div class="user-section">
            <div class="profile-img-container">
                <img src="../../../images/Fitcamp_Logo.png" alt="Perfil">
            </div>
            <h3><?php echo htmlspecialchars($nombre_usuario); ?></h3>
            <p>Cliente</p>
        </div>
        <div class="nav-container">
            <ul class="nav-links">
                <li>
                    <a href="../../../dashboard/index.php" class="nav-item">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item active">
                        <i class="fas fa-cart-plus"></i>
                        <span>Solicitar Producto</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content" style="padding:24px;">
        <div class="card modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-cart-plus"></i> <?php echo $titulo; ?></h2>
            </div>

            <div class="herbalife-layout">
                <!-- Panel izquierdo eliminado: solo catálogo y modal -->

                <aside class="herbalife-right">
                    <div class="panel resumen-panel">
                        <h3>Resumen de la orden</h3>
                        <div class="resumen-flex">
                            <div class="resumen-media"><img id="resumen-img" src="../../../images/Fitcamp_Logo.png" alt="imagen"></div>
                            <div class="resumen-body">
                                <h4 id="resumen-nombre">-</h4>
                                <p id="resumen-detalle">Selecciona un producto o combo.</p>
                                <p id="resumen-precio">Precio unitario: -</p>
                                <p id="resumen-cantidad">Cantidad: -</p>
                                <p id="resumen-total">Total: -</p>
                            </div>
                        </div>
                    </div>

                    <div class="panel lista-panel">
                        <h3>Productos y Combos</h3>
                        <div id="grid-listado" class="grid-listado">
                            <?php foreach ($productos as $p):
                                $img = (isset($p['imagen_path']) && $p['imagen_path'] && file_exists(__DIR__.'/../../../'.$p['imagen_path']))
                                    ? '../../../'.$p['imagen_path'] : '../../../images/Fitcamp_Logo.png'; ?>
                                <div class="card-grid-item">
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" onerror="this.onerror=null;this.src='../../../images/Fitcamp_Logo.png';">
                                    <div class="card-grid-body">
                                        <h4><?php echo htmlspecialchars($p['nombre']); ?></h4>
                                        <p class="price">$<?php echo number_format($p['precio'],2); ?></p>
                                        <button class="btn-outline btn-seleccionar" data-tipo="producto" data-id="<?php echo $p['id']; ?>" data-precio="<?php echo $p['precio']; ?>" data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>" data-img="<?php echo htmlspecialchars($img); ?>">Seleccionar</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($combos as $c):
                                $img = (isset($c['imagen_path']) && $c['imagen_path'] && file_exists(__DIR__.'/../../../'.$c['imagen_path']))
                                    ? '../../../'.$c['imagen_path'] : '../../../images/Fitcamp_Logo.png'; ?>
                                <div class="card-grid-item">
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($c['nombre']); ?>" onerror="this.onerror=null;this.src='../../../images/Fitcamp_Logo.png';">
                                    <div class="card-grid-body">
                                        <h4><?php echo htmlspecialchars($c['nombre']); ?></h4>
                                        <p class="price">$<?php echo number_format($c['precio'],2); ?></p>
                                        <button class="btn-outline btn-seleccionar" data-tipo="combo" data-id="<?php echo $c['id']; ?>" data-precio="<?php echo $c['precio']; ?>" data-nombre="<?php echo htmlspecialchars($c['nombre']); ?>" data-img="<?php echo htmlspecialchars($img); ?>">Seleccionar</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="panel historial-panel">
                        <h3>Tu historial de solicitudes</h3>
                        <div id="lista-solicitudes" class="grid-productos">
                            <!-- se cargarán via fetch -->
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script src="../../../scripts/solicitar_producto.js"></script>
    <script>
    // Modal emergente de compra mejorado
    document.addEventListener('DOMContentLoaded', function(){
        const modal = document.getElementById('modal-compra');
        const modalBg = document.querySelector('.modal-compra-bg');
        const modalClose = document.getElementById('modal-close-btn');
        const modalForm = document.getElementById('modal-form');
        const modalMetodo = document.getElementById('modal-metodo');
        const modalInfoMetodo = document.getElementById('modal-info-metodo');
        let lastBtn = null;
        function openModal(data){
            document.getElementById('modal-img').src = data.img || '../../../images/Fitcamp_Logo.png';
            document.getElementById('modal-nombre').textContent = data.nombre;
            document.getElementById('modal-precio').textContent = '$'+parseFloat(data.precio).toFixed(2);
            document.getElementById('modal-tipo').value = data.tipo;
            document.getElementById('modal-id').value = data.id;
            document.getElementById('modal-cantidad').value = 1;
            // Recargar métodos de pago en cada apertura
            let metodos = <?php echo json_encode($metodos); ?>;
            modalMetodo.innerHTML = '<option value="">Seleccionar</option>' + metodos.map(m => `<option value="${m.id}" data-instrucciones="${m.instrucciones ? m.instrucciones.replace(/"/g,'&quot;') : ''}">${m.nombre}</option>`).join('');
            modalInfoMetodo.innerHTML = '';
            modalInfoMetodo.style.display = 'none';
            document.getElementById('modal-referencia').value = '';
            document.getElementById('modal-comprobante').value = '';
            document.getElementById('modal-observaciones').value = '';
            document.getElementById('modal-mensaje-resultado').textContent = '';
            document.getElementById('modal-mensaje-resultado').style.color = '';
            modal.style.display = 'block';
            setTimeout(()=>{document.querySelector('.modal-compra-content').focus();},100);
        }
        function closeModal(){ modal.style.display = 'none'; }
        modalBg.onclick = closeModal;
        modalClose.onclick = closeModal;
        window.addEventListener('keydown', function(e){ if (modal.style.display==='block' && e.key==='Escape') closeModal(); });
        document.querySelectorAll('.btn-seleccionar').forEach(btn=>{
            btn.addEventListener('click', function(e){
                e.preventDefault();
                lastBtn = btn;
                openModal({
                    tipo: btn.getAttribute('data-tipo'),
                    id: btn.getAttribute('data-id'),
                    nombre: btn.getAttribute('data-nombre'),
                    precio: btn.getAttribute('data-precio'),
                    img: btn.getAttribute('data-img')
                });
            });
        });
        // Métodos de pago en modal
        modalMetodo.addEventListener('change', function(){
            const opt = this.options[this.selectedIndex];
            const instr = opt.getAttribute('data-instrucciones') || '';
            if (this.value) {
                modalInfoMetodo.innerHTML = instr ? '<pre>'+instr+'</pre>' : '<span class="muted">Sin instrucciones.</span>';
                modalInfoMetodo.style.display = 'block';
            } else {
                modalInfoMetodo.innerHTML = '';
                modalInfoMetodo.style.display = 'none';
            }
        });
        // Envío de solicitud desde modal
        modalForm.onsubmit = function(e){
            e.preventDefault();
            if (!modalMetodo.value) {
                modalInfoMetodo.innerHTML = '<span style="color:#ffb300">Selecciona un método de pago.</span>';
                modalInfoMetodo.style.display = 'block';
                return;
            }
            const formData = new FormData(modalForm);
            document.getElementById('modal-mensaje-resultado').textContent = 'Enviando...';
            document.getElementById('modal-mensaje-resultado').style.color = '#ffb300';
            fetch('../../procesos/herbalife/solicitar_producto.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res=>{
                const ctype = res.headers.get('content-type')||'';
                if(ctype.indexOf('application/json')>-1) return res.json();
                return res.text().then(t=>({status:'error',message:t}));
            })
            .then(j=>{
                if(j.status==='success'){
                    document.getElementById('modal-mensaje-resultado').style.color='#2bff7a';
                    document.getElementById('modal-mensaje-resultado').textContent='Solicitud enviada correctamente';
                    setTimeout(closeModal, 1200);
                }else{
                    document.getElementById('modal-mensaje-resultado').style.color='#ff4b4b';
                    document.getElementById('modal-mensaje-resultado').textContent=j.message||'Error al enviar';
                }
            })
            .catch(()=>{
                document.getElementById('modal-mensaje-resultado').style.color='#ff4b4b';
                document.getElementById('modal-mensaje-resultado').textContent='Error de red';
            });
        };
    });
    </script>
</body>
</html>
