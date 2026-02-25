// scripts/pagos.js - VERSIÓN MÍNIMA Y FUNCIONAL CON ESTILOS CSS
console.log('🚀 Módulo de pagos iniciado');

// =============================================
// 1. VARIABLES GLOBALES
// =============================================
let metodosDisponibles = [];
let metodoSeleccionado = null;

// =============================================
// 2. CARGAR MÉTODOS DE PAGO
// =============================================
async function cargarMetodosPago() {
    console.log('📡 Cargando métodos desde:', PAGOS_CONFIG.api.metodos);
    
    const contenedor = document.getElementById('lista-metodos');
    if (!contenedor) {
        console.error('ERROR: No existe #lista-metodos');
        return;
    }
    
    try {
        const respuesta = await fetch(PAGOS_CONFIG.api.metodos, {
            credentials: 'include'
        });
        
        console.log('✅ Respuesta recibida. Status:', respuesta.status);
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('📊 Datos recibidos:', datos);
        
        if (datos.success && datos.metodos && datos.metodos.length > 0) {
            metodosDisponibles = datos.metodos;
            mostrarMetodosPago(datos.metodos);
        } else {
            throw new Error(datos.error || 'No hay métodos disponibles');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        
        const contenedor = document.getElementById('lista-metodos');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="estado-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>Error</h4>
                    <p>${error.message}</p>
                    <button onclick="cargarMetodosPago()" class="btn-reintentar">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }
}

// =============================================
// 3. MOSTRAR MÉTODOS DE PAGO
// =============================================
function mostrarMetodosPago(metodos) {
    const contenedor = document.getElementById('lista-metodos');
    if (!contenedor) return;
    
    if (!metodos || metodos.length === 0) {
        contenedor.innerHTML = `
            <div class="estado-vacio">
                <i class="fas fa-credit-card"></i>
                <h4>No hay métodos disponibles</h4>
                <p>Contacta al administrador</p>
            </div>
        `;
        return;
    }
    
    contenedor.innerHTML = metodos.map(metodo => `
        <div class="tarjeta-metodo" 
             data-metodo-id="${metodo.id}"
             onclick="seleccionarMetodo(${metodo.id})">
            <div class="metodo-header">
                <div class="metodo-icono" style="background: ${metodo.color || '#4B0082'}20; color: ${metodo.color || '#9370DB'}">
                    <i class="fas ${metodo.icono || 'fa-credit-card'}"></i>
                </div>
                <div class="metodo-info">
                    <h3>${metodo.nombre}</h3>
                    <div class="metodo-hint">
                        <i class="fas fa-mouse-pointer"></i> Haz clic para seleccionar
                    </div>
                </div>
            </div>
            
            <div class="metodo-descripcion">
                <p>${metodo.descripcion || 'Sin descripción disponible'}</p>
            </div>
            
            ${metodo.instrucciones ? `
            <div class="metodo-instrucciones">
                <strong><i class="fas fa-list-ol"></i> Instrucciones:</strong>
                <p>${metodo.instrucciones}</p>
            </div>
            ` : ''}
        </div>
    `).join('');
}

// =============================================
// 4. SELECCIONAR MÉTODO
// =============================================
function seleccionarMetodo(metodoId) {
    console.log('👉 Seleccionando método ID:', metodoId);
    
    const metodo = metodosDisponibles.find(m => m.id == metodoId);
    if (!metodo) {
        console.error('Método no encontrado');
        return;
    }
    
    metodoSeleccionado = metodo;
    
    // Remover selección anterior
    document.querySelectorAll('.tarjeta-metodo').forEach(card => {
        card.classList.remove('seleccionada');
    });
    
    // Agregar selección actual
    const cardSeleccionada = document.querySelector(`[data-metodo-id="${metodoId}"]`);
    if (cardSeleccionada) {
        cardSeleccionada.classList.add('seleccionada');
    }
    
    // Mostrar formulario
    mostrarFormularioPago(metodo);
}

// =============================================
// 5. MOSTRAR FORMULARIO (VERSIÓN ACTUALIZADA)
// =============================================
function mostrarFormularioPago(metodo) {
    const contenedor = document.getElementById('formulario-pago');
    if (!contenedor) return;
    
    console.log('📝 Mostrando formulario para:', metodo.nombre);
    
    // Generar opciones de meses
    const fecha = new Date();
    const meses = [];
    for (let i = 0; i < 6; i++) {
        const mesFecha = new Date(fecha.getFullYear(), fecha.getMonth() - i, 1);
        const texto = mesFecha.toLocaleDateString('es-ES', { 
            month: 'long', 
            year: 'numeric' 
        });
        const valor = mesFecha.toISOString().slice(0, 7);
        meses.push({ texto, valor });
    }
    
    contenedor.style.display = 'block';
    contenedor.innerHTML = `
        <div class="form-header">
            <h3><i class="fas fa-file-invoice-dollar"></i> Registrar Pago - ${metodo.nombre}</h3>
            <button class="btn-volver" onclick="ocultarFormulario()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
        
        <form id="form-registro-pago" onsubmit="registrarPago(event)">
            <input type="hidden" name="metodo_id" value="${metodo.id}">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="monto"><i class="fas fa-money-bill"></i> Monto a Pagar</label>
                    <input type="number" id="monto" name="monto" 
                           value="18.00" step="0.01" min="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="mes"><i class="fas fa-calendar-alt"></i> Mes a Pagar</label>
                    <select id="mes" name="mes" required>
                        ${meses.map(m => `<option value="${m.valor}">${m.texto}</option>`).join('')}
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="referencia"><i class="fas fa-hashtag"></i> Referencia</label>
                    <input type="text" id="referencia" name="referencia" 
                           placeholder="Número de operación, transacción, etc.">
                </div>
                
                <div class="form-group full-width">
                    <label><i class="fas fa-file-upload"></i> Comprobante de Pago</label>
                    <div class="input-file-container">
                        <input type="file" id="comprobante" name="comprobante" 
                               class="input-file" accept="image/*,.pdf" required
                               onchange="mostrarNombreArchivo(this)">
                        <label for="comprobante" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Subir comprobante</span>
                            <p class="formato-archivos">Formatos: JPG, PNG, PDF, GIF (Max 5MB)</p>
                        </label>
                        <div id="nombre-archivo" class="file-name"></div>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="observaciones"><i class="fas fa-comment"></i> Nombre y Apellido: </label>
                    <textarea id="observaciones" name="observaciones" rows="3" 
                              placeholder="Nombre y Apellido"></textarea>
                </div>
            </div>
            
            <div class="info-importante">
                <div class="advertencia">
                    <h4><i class="fas fa-exclamation-triangle"></i> Importante</h4>
                    <p>• Sube un comprobante claro y legible<br>
                       • Incluye la referencia de pago si aplica<br>
                       • El administrador verificará tu pago<br>
                       • Recibirás una notificación cuando sea aprobado</p>
                </div>
            </div>
            
            <button type="submit" class="btn-pago">
                <i class="fas fa-paper-plane"></i> Enviar Pago para Verificación
            </button>
        </form>
    `;
    
    // Scroll al formulario
    contenedor.scrollIntoView({ behavior: 'smooth' });
}



// =============================================
// 6. FUNCIONES AUXILIARES
// =============================================
function ocultarFormulario() {
    const contenedor = document.getElementById('formulario-pago');
    if (contenedor) {
        contenedor.style.display = 'none';
        metodoSeleccionado = null;
        
        // Quitar selección
        document.querySelectorAll('.tarjeta-metodo').forEach(card => {
            card.classList.remove('seleccionada');
        });
    }
}

function mostrarNombreArchivo(input) {
    const elemento = document.getElementById('nombre-archivo');
    if (elemento && input.files.length > 0) {
        elemento.textContent = `📎 ${input.files[0].name}`;
    }
}

// =============================================
// VALIDACIÓN DE ARCHIVO
// =============================================
function validarArchivo(archivo) {
    if (!archivo) {
        return { valido: false, error: 'No se seleccionó ningún archivo' };
    }
    
    // Extensiones permitidas
    const extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
    const extension = archivo.name.toLowerCase().split('.').pop();
    
    if (!extensionesPermitidas.includes(extension)) {
        return { 
            valido: false, 
            error: 'Formato no permitido. Use JPG, PNG o PDF.' 
        };
    }
    
    // Tamaño máximo (5MB)
    const tamañoMaximo = 5 * 1024 * 1024; // 5MB en bytes
    if (archivo.size > tamañoMaximo) {
        return { 
            valido: false, 
            error: 'El archivo es demasiado grande. Máximo 5MB.' 
        };
    }
    
    return { valido: true };
}

// =============================================
// MODIFICAR registrarPago PARA MEJOR MANEJO
// =============================================
async function registrarPago(event) {
    event.preventDefault();
    console.log('📤 Enviando pago...');
    
    const formulario = event.target;
    const datos = new FormData(formulario);
    
    // No añadas usuario_id aquí - PHP lo obtiene de la sesión
    // datos.append('usuario_id', USUARIO_ID);
    
    const boton = formulario.querySelector('button[type="submit"]');
    const textoOriginal = boton.innerHTML;
    
    try {
        boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        boton.disabled = true;
        
        console.log('🌐 Enviando a:', PAGOS_CONFIG.api.registrar);
        
        const respuesta = await fetch(PAGOS_CONFIG.api.registrar, {
            method: 'POST',
            body: datos,
            credentials: 'include'  // Importante para enviar cookies de sesión
        });
        
        console.log('📥 Status:', respuesta.status);
        
        const texto = await respuesta.text();
        console.log('📄 Respuesta completa:', texto);
        
        // INTENTAR PARSEAR EL JSON
        let resultado;
        try {
            resultado = JSON.parse(texto);
        } catch (e) {
            console.error('❌ Error parseando JSON:', e);
            console.log('📄 Texto que falló:', texto.substring(0, 200));
            
            // Intentar limpiar el texto
            const textoLimpio = texto.replace(/^\s*<\?php[\s\S]*?\?>\s*/g, '')
                                     .replace(/^\s*<!--[\s\S]*?-->\s*/g, '')
                                     .trim();
            
            try {
                resultado = JSON.parse(textoLimpio);
                console.log('✅ JSON limpiado exitoso:', resultado);
            } catch (e2) {
                throw new Error('El servidor no devolvió JSON válido: ' + texto.substring(0, 100));
            }
        }
        
        console.log('📊 Resultado:', resultado);
        
        if (resultado.success) {
            alert(resultado.message || '✅ Pago registrado exitosamente');
            ocultarFormulario();
            formulario.reset();
        } else {
            const mensajeError = resultado.error || 'Error desconocido';
            alert('❌ ' + mensajeError);
            
            if (resultado.debug) {
                console.log('Debug info:', resultado.debug);
            }
        }
        
    } catch (error) {
        console.error('❌ Error completo:', error);
        alert('❌ Error: ' + error.message);
    } finally {
        boton.innerHTML = textoOriginal;
        boton.disabled = false;
    }
}



// =============================================
// 7. INICIALIZACIÓN
// =============================================
// Cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado - Iniciando carga de métodos');
    cargarMetodosPago();
});

// Si el DOM ya está cargado
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    console.log('✅ DOM ya listo - Cargando ahora');
    setTimeout(cargarMetodosPago, 100);
}