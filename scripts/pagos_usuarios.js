console.log('📋 Módulo "Mis Pagos" - Con Animaciones');

// Configuración
const config = {
    api: '../../procesos/pagos/obtener_pagos_usuarios.php'
};

// Variables globales
let pagos = [];
let estadoFiltro = 'todos';

// Función principal para cargar pagos (con animaciones)
async function cargarPagos(estado = 'todos') {
    console.log('📡 Cargando pagos:', estado);
    
    estadoFiltro = estado;
    
    // Mostrar animación de carga
    mostrarCargaAnimada(estado);
    
    try {
        const formData = new FormData();
        formData.append('estado', estado);
        
        // Pequeño delay para que se vea la animación
        await new Promise(resolve => setTimeout(resolve, 300));
        
        const respuesta = await fetch(config.api, {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        
        // Pequeño delay adicional para suavizar
        await new Promise(resolve => setTimeout(resolve, 200));
        
        if (datos.success) {
            pagos = datos.pagos || [];
            mostrarPagosAnimado(pagos);
        } else {
            mostrarErrorAnimado(datos.error || 'Error al cargar pagos');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        mostrarErrorAnimado('Error de conexión: ' + error.message);
    }
}

// Mostrar carga con animación
function mostrarCargaAnimada(estado) {
    const contenedor = document.getElementById('formulario-pago');
    
    // Determinar color del spinner según estado
    let spinnerColor = '';
    switch(estado) {
        case 'todos': spinnerColor = ''; break;
        case 'pendiente': spinnerColor = 'naranja'; break;
        case 'aprobado': spinnerColor = 'verde'; break;
        case 'rechazado': spinnerColor = 'rojo'; break;
        default: spinnerColor = 'azul';
    }
    
    // Determinar texto según estado
    let textoEstado = '';
    switch(estado) {
        case 'todos': textoEstado = 'todos los pagos'; break;
        case 'pendiente': textoEstado = 'pagos pendientes'; break;
        case 'aprobado': textoEstado = 'pagos aprobados'; break;
        case 'rechazado': textoEstado = 'pagos rechazados'; break;
        default: textoEstado = 'pagos';
    }
    
    contenedor.innerHTML = `
        <div class="contenedor-carga">
            <div class="barra-progreso"></div>
            <div class="spinner-cargando ${spinnerColor}"></div>
            <h3 style="color: #9370DB; margin-bottom: 10px;">Cargando ${textoEstado}</h3>
            <p style="color: #94A3B8;">Por favor espera...</p>
        </div>
    `;
    contenedor.style.display = 'block';
}

// Mostrar error con animación
function mostrarErrorAnimado(mensaje) {
    const contenedor = document.getElementById('formulario-pago');
    
    contenedor.innerHTML = `
        <div class="estado-error" style="animation: fadeInUp 0.6s ease-out;">
            <i class="fas fa-exclamation-circle"></i>
            <h3>Error</h3>
            <p>${mensaje}</p>
            <button onclick="cargarPagos('${estadoFiltro}')" class="btn-reintentar">
                <i class="fas fa-redo"></i> Reintentar
            </button>
        </div>
    `;
}

// Mostrar pagos con animación
function mostrarPagosAnimado(listaPagos) {
    const contenedor = document.getElementById('formulario-pago');
    
    if (!listaPagos || listaPagos.length === 0) {
        contenedor.innerHTML = `
            <div class="estado-vacio-pagos">
                <div class="icono-vacio">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3>No hay pagos ${estadoFiltro === 'todos' ? 'registrados' : estadoFiltro + 's'}</h3>
                <p>${getMensajeVacio(estadoFiltro)}</p>
                ${estadoFiltro === 'pendiente' ? `
                <a href="index.php" class="boton-primer-pago">
                    <i class="fas fa-credit-card"></i> Realizar mi primer pago
                </a>
                ` : ''}
            </div>
        `;
        return;
    }
    
    // Ordenar por fecha (más reciente primero)
    listaPagos.sort((a, b) => {
        const fechaA = obtenerFechaPago(a);
        const fechaB = obtenerFechaPago(b);
        return fechaB - fechaA;
    });
    
    // Generar HTML
    contenedor.innerHTML = `
        <div class="contenedor-pagos-usuario">
            ${listaPagos.map((pago, index) => generarTarjetaPago(pago, index)).join('')}
        </div>
    `;
    
    // Añadir efecto de aparición secuencial
    setTimeout(() => {
        const tarjetas = contenedor.querySelectorAll('.tarjeta-pago-usuario');
        tarjetas.forEach((tarjeta, index) => {
            tarjeta.style.animationDelay = `${index * 0.1}s`;
        });
    }, 50);
}

// Generar tarjeta de pago individual (con delay para animación)
function generarTarjetaPago(pago, index) {
    const estado = pago.estado?.toLowerCase() || estadoFiltro;
    const estadoClase = estado;
    const estadoIcono = getIconoEstado(estado);
    const estadoTexto = estado.toUpperCase();
    const fecha = obtenerFechaFormateada(pago);
    const fechaTipo = obtenerTipoFecha(pago);
    
    return `
        <div class="tarjeta-pago-usuario ${estadoClase}" style="animation-delay: ${index * 0.1}s">
            <!-- Encabezado -->
            <div class="encabezado-pago">
                <div class="info-principal-pago">
                    <span class="estado-pago">
                        <i class="fas ${estadoIcono}"></i>
                        ${estadoTexto}
                    </span>
                    <span class="numero-pago">
                        Pago #${pago.id || pago.pago_id || 'N/A'}
                    </span>
                </div>
                <div class="fecha-pago">
                    <i class="far fa-calendar"></i>
                    <span>${fechaTipo}: ${fecha}</span>
                </div>
            </div>
            
            <!-- Detalles -->
            <div class="detalles-pago">
                <div class="item-detalle">
                    <div class="etiqueta-detalle">
                        <i class="fas fa-money-bill"></i>
                        <span>Monto</span>
                    </div>
                    <div class="valor-detalle monto">
                        S/ ${parseFloat(pago.monto || 0).toFixed(2)}
                    </div>
                </div>
                
                ${pago.mes_pagado ? `
                <div class="item-detalle">
                    <div class="etiqueta-detalle">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Mes Pagado</span>
                    </div>
                    <div class="valor-detalle">
                        ${pago.mes_pagado}
                    </div>
                </div>
                ` : ''}
                
                ${pago.referencia ? `
                <div class="item-detalle">
                    <div class="etiqueta-detalle">
                        <i class="fas fa-hashtag"></i>
                        <span>Referencia</span>
                    </div>
                    <div class="valor-detalle">
                        ${pago.referencia}
                    </div>
                </div>
                ` : ''}
                
                ${pago.metodo_nombre ? `
                <div class="item-detalle">
                    <div class="etiqueta-detalle">
                        <i class="fas fa-credit-card"></i>
                        <span>Método</span>
                    </div>
                    <div class="valor-detalle">
                        ${pago.metodo_nombre}
                    </div>
                </div>
                ` : ''}
            </div>
            
            <!-- Información adicional -->
            <div class="info-extra-pago">
                ${pago.observaciones ? `
                <div class="contenedor-observaciones">
                    <h4><i class="fas fa-comment"></i> Nombre y Apellido</h4>
                    <p class="texto-observaciones">${pago.observaciones}</p>
                </div>
                ` : ''}
                
                ${pago.razon_rechazo ? `
                <div class="contenedor-motivo-rechazo">
                    <h4><i class="fas fa-exclamation-triangle"></i> Motivo de Rechazo</h4>
                    <p class="texto-motivo-rechazo">${pago.razon_rechazo}</p>
                </div>
                ` : ''}
            </div>
            
            <!-- Acciones -->
            ${pago.comprobante ? `
            <div class="contenedor-acciones">
                <a href="../../../uploads/comprobantes/${pago.comprobante}" 
                   target="_blank" 
                   class="boton-comprobante">
                    <i class="fas fa-eye"></i> Ver Comprobante
                </a>
            </div>
            ` : ''}
        </div>
    `;
}

// Funciones auxiliares (mantener las mismas)
function getIconoEstado(estado) {
    switch(estado) {
        case 'aprobado': return 'fa-check-circle';
        case 'rechazado': return 'fa-times-circle';
        case 'pendiente': return 'fa-clock';
        default: return 'fa-file-invoice-dollar';
    }
}

function obtenerFechaPago(pago) {
    if (pago.fecha_pago) return new Date(pago.fecha_pago);
    if (pago.fecha_aprobacion) return new Date(pago.fecha_aprobacion);
    if (pago.fecha_rechazo) return new Date(pago.fecha_rechazo);
    if (pago.fecha_registro) return new Date(pago.fecha_registro);
    return new Date(0);
}

function obtenerTipoFecha(pago) {
    if (pago.fecha_pago) return 'Fecha Pago';
    if (pago.fecha_aprobacion) return 'Fecha Aprobación';
    if (pago.fecha_rechazo) return 'Fecha Rechazo';
    if (pago.fecha_registro) return 'Fecha Registro';
    return 'Fecha';
}

function obtenerFechaFormateada(pago) {
    const fecha = obtenerFechaPago(pago);
    
    if (fecha.getTime() === 0) {
        return 'Fecha no disponible';
    }
    
    return fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getMensajeVacio(estado) {
    switch(estado) {
        case 'pendiente': return 'No tienes pagos pendientes de revisión.';
        case 'aprobado': return 'No tienes pagos aprobados todavía.';
        case 'rechazado': return 'No tienes pagos rechazados.';
        default: return 'Aún no has realizado ningún pago mensual.';
    }
}

// Inicializar pestañas con animaciones
function inicializarPestanas() {
    const pestanas = document.querySelectorAll('.pestana');
    const contenedorPagos = document.getElementById('formulario-pago');
    
    pestanas.forEach(pestana => {
        pestana.addEventListener('click', function() {
            // Obtener el estado de la pestaña clickeada
            const nuevoEstado = this.getAttribute('data-estado');
            
            // Si ya está activa, no hacer nada
            if (this.classList.contains('activa') && estadoFiltro === nuevoEstado) {
                return;
            }
            
            // Efecto visual en el contenedor
            contenedorPagos.classList.add('cambiando');
            
            // Remover clase activa de todas con animación
            pestanas.forEach(p => {
                p.classList.remove('activa');
                p.style.transform = 'scale(1)';
            });
            
            // Agregar clase activa a la actual con efecto
            this.classList.add('activa');
            this.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            // Restaurar contenedor
            setTimeout(() => {
                contenedorPagos.classList.remove('cambiando');
            }, 300);
            
            // Cargar pagos según estado
            cargarPagos(nuevoEstado);
        });
    });
    
    // Efecto hover para pestañas
    pestanas.forEach(pestana => {
        pestana.addEventListener('mouseenter', function() {
            if (!this.classList.contains('activa')) {
                this.style.transform = 'translateY(-3px)';
            }
        });
        
        pestana.addEventListener('mouseleave', function() {
            if (!this.classList.contains('activa')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado - Iniciando módulo de pagos');
    
    // Inicializar pestañas
    inicializarPestanas();
    
    // Pequeña animación inicial
    const contenedor = document.getElementById('formulario-pago');
    contenedor.style.opacity = '0';
    
    setTimeout(() => {
        contenedor.style.transition = 'opacity 0.5s ease';
        contenedor.style.opacity = '1';
        
        // Cargar todos los pagos por defecto
        cargarPagos('todos');
    }, 100);
});

// Si el DOM ya está cargado
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    setTimeout(() => {
        inicializarPestanas();
        
        const contenedor = document.getElementById('formulario-pago');
        if (contenedor) {
            contenedor.style.transition = 'opacity 0.5s ease';
            contenedor.style.opacity = '1';
        }
        
        cargarPagos('todos');
    }, 100);
}