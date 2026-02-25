<?php
session_start();
require_once '../../config/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_user'])) {
    header('Location: ../../../auth/index_login.php');
    exit();
}

$titulo = "Configuración de Métodos de Pago";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Admin Fitcamp</title>
    
    <!-- Mismo CSS del admin -->
    <link rel="stylesheet" href="../../../styles/styles_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS específico para métodos de pago -->
    <style>
        /* ESTILOS DEL MÓDULO DE PAGOS */
        .modulo-container {
            background: rgba(30, 30, 30, 0.9);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid #4B0082;
            margin: 20px 0;
        }
        
        .modulo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4B0082;
            padding-bottom: 15px;
        }
        
        .modulo-header h2 {
            color: #9370DB;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modulo-acciones {
            display: flex;
            gap: 10px;
        }
        
        .btn-nuevo, .btn-actualizar {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-nuevo {
            background: #4B0082;
            color: white;
        }
        
        .btn-nuevo:hover {
            background: #5a1a99;
            transform: translateY(-2px);
        }
        
        .btn-actualizar {
            background: #2c2c2c;
            color: #aaa;
            border: 1px solid #444;
        }
        
        .btn-actualizar:hover {
            background: #3a3a3a;
            border-color: #9370DB;
        }
        
        /* Grid de métodos */
        .grid-metodos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .tarjeta-metodo {
            background: rgba(40, 40, 40, 0.8);
            border-radius: 12px;
            padding: 20px;
            border-left: 5px solid #4B0082;
            transition: all 0.3s;
        }
        
        .tarjeta-metodo:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .tarjeta-metodo.inactivo {
            opacity: 0.7;
            background: rgba(40, 40, 40, 0.5);
        }
        
        .metodo-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .metodo-icono {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .metodo-info h3 {
            color: white;
            margin: 0 0 5px 0;
            font-size: 1.2rem;
        }
        
        .badge-tipo {
            display: inline-block;
            background: rgba(147, 112, 219, 0.2);
            color: #9370DB;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-right: 8px;
        }
        
        .badge-estado {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-estado.activo {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
        }
        
        .badge-estado.inactivo {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }
        
        .metodo-descripcion {
            color: #bbb;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .metodo-instrucciones {
            background: rgba(0, 0, 0, 0.2);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .metodo-instrucciones strong {
            color: #9370DB;
            display: block;
            margin-bottom: 5px;
        }
        
        .metodo-instrucciones p {
            color: #aaa;
            font-size: 0.85rem;
            line-height: 1.4;
            margin: 0;
        }
        
        .metodo-acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .metodo-acciones button {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            flex-grow: 1;
            justify-content: center;
        }
        
        .btn-editar {
            background: rgba(33, 150, 243, 0.2);
            color: #2196F3;
        }
        
        .btn-editar:hover {
            background: rgba(33, 150, 243, 0.3);
        }
        
        .btn-activar {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
        }
        
        .btn-activar:hover {
            background: rgba(76, 175, 80, 0.3);
        }
        
        .btn-desactivar {
            background: rgba(255, 152, 0, 0.2);
            color: #FF9800;
        }
        
        .btn-desactivar:hover {
            background: rgba(255, 152, 0, 0.3);
        }
        
        .btn-eliminar {
            background: rgba(244, 67, 54, 0.2);
            color: #F44336;
        }
        
        .btn-eliminar:hover {
            background: rgba(244, 67, 54, 0.3);
        }
        
        /* Estado vacío */
        .estado-vacio {
            text-align: center;
            padding: 50px 20px;
            color: #888;
        }
        
        .estado-vacio i {
            font-size: 4rem;
            color: #4B0082;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        /* Loading */
        .cargando-metodos {
            text-align: center;
            padding: 50px;
            color: #aaa;
        }
        
        .spinner-metodos {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(147, 112, 219, 0.2);
            border-radius: 50%;
            border-top-color: #9370DB;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .grid-metodos {
                grid-template-columns: 1fr;
            }
            
            .modulo-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .modulo-acciones {
                width: 100%;
            }
            
            .metodo-acciones {
                flex-direction: column;
            }
        }

                /* ESTILOS DEL MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-contenido {
            background: rgba(40, 40, 40, 0.95);
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            border: 2px solid #4B0082;
            box-shadow: 0 0 30px rgba(75, 0, 130, 0.5);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #444;
            background: rgba(30, 30, 30, 0.9);
            border-radius: 15px 15px 0 0;
        }

        .modal-header h3 {
            color: #9370DB;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-cerrar {
            background: none;
            border: none;
            color: #aaa;
            font-size: 28px;
            cursor: pointer;
            transition: 0.3s;
        }

        .modal-cerrar:hover {
            color: #ff6b6b;
        }

        /* FORMULARIO */
        #form-metodo {
            padding: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            color: #ddd;
            margin-bottom: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: rgba(20, 20, 20, 0.8);
            border: 1px solid #444;
            border-radius: 8px;
            padding: 12px 15px;
            color: white;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #9370DB;
            box-shadow: 0 0 0 2px rgba(147, 112, 219, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-group small {
            color: #888;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        /* TOGGLE GROUP */
        .toggle-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }

        .toggle-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #bbb;
            cursor: pointer;
        }

        .toggle-label input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #9370DB;
        }

        /* FOOTER DEL MODAL */
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid #444;
        }

        .btn-cancelar,
        .btn-guardar {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancelar {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }

        .btn-cancelar:hover {
            background: rgba(255, 107, 107, 0.3);
        }

        .btn-guardar {
            background: #4B0082;
            color: white;
        }

        .btn-guardar:hover {
            background: #5a1a99;
            transform: translateY(-2px);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .modal-contenido {
                width: 95%;
            }
        }

    </style>
</head>
<body>
    <!-- BARRA LATERAL (igual que tu admin) -->
    <nav class="sidebar">
        <div class="user-section">
            <div class="profile-img-container">
                <img src="../../../images/Fitcamp_Logo.png" id="profile-pic" alt="Perfil">
            </div>
            <h3>ADMIN-Fitcamp</h3>
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
                <li>
                    <div class="nav-item active">
                        <i class="fas fa-credit-card"></i>
                        <span>Métodos de Pago</span>
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <div class="modulo-container" id="modulo-metodos-pago">
            <div class="modulo-header">
                <h2><i class="fas fa-credit-card"></i> <?php echo $titulo; ?></h2>
                <div class="modulo-acciones">
                    <button class="btn-nuevo" onclick="mostrarFormularioNuevo()">
                        <i class="fas fa-plus"></i> Nuevo Método
                    </button>
                    <button class="btn-actualizar" onclick="cargarMetodos()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
            
            <!-- Aquí se cargarán los métodos dinámicamente -->
            <div id="lista-metodos">
                <div class="cargando-metodos">
                    <div class="spinner-metodos"></div>
                    <p>Cargando métodos de pago...</p>
                </div>
            </div>
        </div>
    </div>

        <!-- MODAL PARA NUEVO/EDITAR MÉTODO -->
    <div id="modal-metodo" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3 id="modal-titulo"><i class="fas fa-credit-card"></i> Nuevo Método de Pago</h3>
                <button class="modal-cerrar" onclick="cerrarModal()">&times;</button>
            </div>
            
            <form id="form-metodo" onsubmit="guardarMetodo(event)">
                <input type="hidden" id="metodo-id" name="id" value="">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre"><i class="fas fa-tag"></i> Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required 
                            placeholder="Ej: Transferencia Bancaria, PayPal, etc.">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo"><i class="fas fa-list"></i> Tipo *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="digital">Pago Digital</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion"><i class="fas fa-align-left"></i> Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="2" 
                                placeholder="Breve descripción del método..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="instrucciones"><i class="fas fa-info-circle"></i> Instrucciones</label>
                        <textarea id="instrucciones" name="instrucciones" rows="3"
                                placeholder="Instrucciones para los usuarios..."></textarea>
                        <small>Ej: "Transferir al banco X, cuenta Y..."</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icono"><i class="fas fa-icons"></i> Icono</label>
                        <input type="text" id="icono" name="icono" 
                            placeholder="fa-credit-card" value="fa-credit-card">
                        <small>Clase de FontAwesome</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="color"><i class="fas fa-palette"></i> Color</label>
                        <input type="color" id="color" name="color" value="#4B0082">
                    </div>
                    
                    <div class="form-group">
                        <label for="orden_visual"><i class="fas fa-sort-numeric-down"></i> Orden Visual</label>
                        <input type="number" id="orden_visual" name="orden_visual" value="0" min="0">
                        <small>Menor número = aparece primero</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Estado</label>
                        <div class="toggle-group">
                            <label class="toggle-label">
                                <input type="radio" name="estado" value="activo" checked> Activo
                            </label>
                            <label class="toggle-label">
                                <input type="radio" name="estado" value="inactivo"> Inactivo
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-guardar">
                        <i class="fas fa-save"></i> Guardar Método
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Configuración
        const API_BASE = '../../procesos/admin/metodos_pago/';
        
        // Estado
        let metodos = [];
        let filtro = 'todos';
        
        // Cargar métodos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Módulo de métodos de pago cargado');
            cargarMetodos();
            
            // Cerrar modal al hacer clic fuera
            document.getElementById('modal-metodo').addEventListener('click', function(event) {
                if (event.target === this) {
                    cerrarModal();
                }
            });
        });
        
        // Función para cargar métodos
        async function cargarMetodos() {
            try {
                mostrarLoading();
                
                const url = API_BASE + 'obtener_metodos_pago.php';
                console.log('🔗 URL:', url);
                
                const response = await fetch(url, { 
                    credentials: 'include',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                console.log('📡 Status:', response.status, response.statusText);
                
                const textResponse = await response.text();
                
                // Intentar parsear como JSON
                try {
                    const data = JSON.parse(textResponse);
                    console.log('✅ JSON parseado:', data);
                    
                    if (data.success) {
                        metodos = data.metodos;
                        renderizarMetodos();
                    } else {
                        mostrarError(data.error || 'Error en API');
                    }
                } catch (jsonError) {
                    console.error('❌ Error parseando JSON:', jsonError);
                    mostrarError('La API no devuelve JSON válido. Respuesta: ' + textResponse.substring(0, 100));
                }
                
            } catch (error) {
                console.error('💥 Error fetch:', error);
                mostrarError('Error de conexión: ' + error.message);
            }
        }
        
        // Renderizar métodos
        function renderizarMetodos() {
            const container = document.getElementById('lista-metodos');
            
            if (!metodos || metodos.length === 0) {
                container.innerHTML = `
                    <div class="estado-vacio">
                        <i class="fas fa-credit-card"></i>
                        <h3>No hay métodos de pago</h3>
                        <p>Agrega tu primer método de pago haciendo clic en "Nuevo Método"</p>
                    </div>
                `;
                return;
            }
            
            let metodosFiltrados = metodos;
            if (filtro === 'activos') {
                metodosFiltrados = metodos.filter(m => m.estado === 'activo');
            } else if (filtro === 'inactivos') {
                metodosFiltrados = metodos.filter(m => m.estado === 'inactivo');
            }
            
            container.innerHTML = `
                <div class="modulo-filtros">
                    <button class="filtro-btn ${filtro === 'todos' ? 'active' : ''}" 
                            onclick="cambiarFiltro('todos')">
                        Todos (${metodos.length})
                    </button>
                    <button class="filtro-btn ${filtro === 'activos' ? 'active' : ''}" 
                            onclick="cambiarFiltro('activos')">
                        Activos (${metodos.filter(m => m.estado === 'activo').length})
                    </button>
                    <button class="filtro-btn ${filtro === 'inactivos' ? 'active' : ''}" 
                            onclick="cambiarFiltro('inactivos')">
                        Inactivos (${metodos.filter(m => m.estado === 'inactivo').length})
                    </button>
                </div>
                
                <div class="grid-metodos">
                    ${metodosFiltrados.map(metodo => `
                        <div class="tarjeta-metodo ${metodo.estado === 'inactivo' ? 'inactivo' : ''}" 
                            style="border-left-color: ${metodo.color || '#4B0082'}">
                            <div class="metodo-header">
                                <div class="metodo-icono" style="background: ${metodo.color || '#4B0082'}20">
                                    <i class="fas ${metodo.icono || 'fa-credit-card'}"></i>
                                </div>
                                <div class="metodo-info">
                                    <h3>${metodo.nombre}</h3>
                                    <span class="badge-tipo">${metodo.tipo}</span>
                                    <span class="badge-estado ${metodo.estado}">
                                        ${metodo.estado === 'activo' ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="metodo-descripcion">
                                <p>${metodo.descripcion || 'Sin descripción'}</p>
                            </div>
                            
                            ${metodo.instrucciones ? `
                            <div class="metodo-instrucciones">
                                <strong>Instrucciones:</strong>
                                <p>${metodo.instrucciones}</p>
                            </div>
                            ` : ''}
                            
                            <div class="metodo-acciones">
                                <button class="btn-editar" onclick="editarMetodo(${metodo.id})">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="${metodo.estado === 'activo' ? 'btn-desactivar' : 'btn-activar'}" 
                                        onclick="cambiarEstado(${metodo.id}, '${metodo.estado === 'activo' ? 'inactivo' : 'activo'}')">
                                    <i class="fas ${metodo.estado === 'activo' ? 'fa-ban' : 'fa-check'}"></i>
                                    ${metodo.estado === 'activo' ? 'Desactivar' : 'Activar'}
                                </button>
                                <button class="btn-eliminar" onclick="eliminarMetodo(${metodo.id}, '${metodo.nombre}')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
        
        // Funciones auxiliares
        function cambiarFiltro(nuevoFiltro) {
            filtro = nuevoFiltro;
            renderizarMetodos();
        }
        
        function mostrarLoading() {
            const container = document.getElementById('lista-metodos');
            container.innerHTML = `
                <div class="cargando-metodos">
                    <div class="spinner-metodos"></div>
                    <p>Cargando métodos de pago...</p>
                </div>
            `;
        }
        
        function mostrarError(mensaje) {
            const container = document.getElementById('lista-metodos');
            container.innerHTML = `
                <div class="estado-vacio">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error</h3>
                    <p>${mensaje}</p>
                    <button onclick="cargarMetodos()" style="margin-top:15px; padding:8px 15px; background:#4B0082; color:white; border:none; border-radius:5px; cursor:pointer;">
                        Reintentar
                    </button>
                </div>
            `;
        }
        
        // FUNCIONES DEL MODAL
        function mostrarFormularioNuevo() {
            // Limpiar formulario
            document.getElementById('form-metodo').reset();
            document.getElementById('metodo-id').value = '';
            document.getElementById('modal-titulo').innerHTML = '<i class="fas fa-plus"></i> Nuevo Método de Pago';
            document.getElementById('color').value = '#4B0082';
            
            // Mostrar modal
            document.getElementById('modal-metodo').style.display = 'flex';
        }
        
        function editarMetodo(id) {
            const metodo = metodos.find(m => m.id === id);
            if (!metodo) {
                alert('Método no encontrado');
                return;
            }
            
            // Llenar formulario con datos del método
            document.getElementById('metodo-id').value = metodo.id;
            document.getElementById('nombre').value = metodo.nombre;
            document.getElementById('tipo').value = metodo.tipo;
            document.getElementById('descripcion').value = metodo.descripcion || '';
            document.getElementById('instrucciones').value = metodo.instrucciones || '';
            document.getElementById('icono').value = metodo.icono || 'fa-credit-card';
            document.getElementById('color').value = metodo.color || '#4B0082';
            document.getElementById('orden_visual').value = metodo.orden_visual || 0;
            
            // Estado
            document.querySelectorAll('input[name="estado"]').forEach(radio => {
                radio.checked = radio.value === metodo.estado;
            });
            
            document.getElementById('modal-titulo').innerHTML = `<i class="fas fa-edit"></i> Editar: ${metodo.nombre}`;
            
            // Mostrar modal
            document.getElementById('modal-metodo').style.display = 'flex';
        }
        
        function cerrarModal() {
            document.getElementById('modal-metodo').style.display = 'none';
        }
        
        async function guardarMetodo(event) {
            event.preventDefault();
            
            // Validar campos requeridos
            if (!document.getElementById('nombre').value.trim()) {
                alert('El nombre es requerido');
                return;
            }
            
            if (!document.getElementById('tipo').value) {
                alert('El tipo es requerido');
                return;
            }
            
            // Obtener datos del formulario
            const formData = {
                id: document.getElementById('metodo-id').value || null,
                nombre: document.getElementById('nombre').value.trim(),
                tipo: document.getElementById('tipo').value,
                descripcion: document.getElementById('descripcion').value.trim(),
                instrucciones: document.getElementById('instrucciones').value.trim(),
                icono: document.getElementById('icono').value.trim() || 'fa-credit-card',
                color: document.getElementById('color').value,
                orden_visual: parseInt(document.getElementById('orden_visual').value) || 0,
                estado: document.querySelector('input[name="estado"]:checked').value
            };
            
            try {
                // Mostrar loading en botón
                const guardarBtn = document.querySelector('.btn-guardar');
                const originalText = guardarBtn.innerHTML;
                guardarBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                guardarBtn.disabled = true;
                
                // Enviar al servidor
                const response = await fetch(API_BASE + 'guardar_metodo_pago.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData),
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    cerrarModal();
                    cargarMetodos(); // Recargar lista
                } else {
                    alert('Error: ' + data.error);
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión: ' + error.message);
            } finally {
                // Restaurar botón
                const guardarBtn = document.querySelector('.btn-guardar');
                if (guardarBtn) {
                    guardarBtn.innerHTML = originalText;
                    guardarBtn.disabled = false;
                }
            }
        }
        
        async function cambiarEstado(id, nuevoEstado) {
            if (!confirm(`¿${nuevoEstado === 'activo' ? 'Activar' : 'Desactivar'} este método de pago?`)) return;
            
            try {
                const response = await fetch(API_BASE + 'cambiar_estado_metodo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, estado: nuevoEstado })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cargarMetodos(); // Recargar lista
                } else {
                    alert('Error: ' + (data.error || 'No se pudo cambiar estado'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
        
        async function eliminarMetodo(id, nombre) {
            if (!confirm(`¿Eliminar permanentemente el método "${nombre}"?`)) return;
            
            try {
                const response = await fetch(API_BASE + 'eliminar_metodo_pago.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cargarMetodos(); // Recargar lista
                } else {
                    alert('Error: ' + (data.error || 'No se pudo eliminar'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>