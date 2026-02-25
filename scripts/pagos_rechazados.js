// js/pagos_rechazados.js
document.addEventListener('DOMContentLoaded', function() {
    cargarPagosRechazados();
});

function cargarPagosRechazados() {
    const contenedor = document.getElementById('lista-rechazados');
    
    contenedor.innerHTML = `
        <div class="cargando-metodos">
            <div class="spinner"></div>
            <p>Cargando pagos rechazados...</p>
        </div>
    `;
    
    fetch('../../procesos/admin/pagos/obtener_rechazados.php')
        .then(response => {
            if (!response.ok) throw new Error(`Error ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarPagosRechazados(data.pagos, data.estadisticas);
            } else {
                mostrarErrorRechazados(data.error);
            }
        })
        .catch(error => {
            mostrarErrorRechazados('Error de conexión: ' + error.message);
        });
}

function mostrarPagosRechazados(pagos, estadisticas) {
    const contenedor = document.getElementById('lista-rechazados');
    
    if (!pagos || pagos.length === 0) {
        contenedor.innerHTML = `
            <div class="estado-vacio">
                <i class="fas fa-ban"></i>
                <h4>No hay pagos rechazados</h4>
                <p>El historial de pagos rechazados está vacío</p>
                <button class="btn-reintentar" onclick="cargarPagosRechazados()">
                    <i class="fas fa-redo"></i> Actualizar
                </button>
            </div>
        `;
        return;
    }
    
    // LIMPIAR CONTENEDOR
    contenedor.innerHTML = '';
    
    // ESTADÍSTICAS ARRIBA
    const statsContainer = document.createElement('div');
    statsContainer.className = 'estadisticas-pagos';
    statsContainer.innerHTML = `
        <div class="estadistica-item">
            <span class="estadistica-numero">${pagos.length}</span>
            <span class="estadistica-texto">Pagos Rechazados</span>
        </div>
        <div class="estadistica-item">
            <span class="estadistica-numero">$${estadisticas.total_rechazado.toFixed(2)}</span>
            <span class="estadistica-texto">Total Rechazado</span>
        </div>
        <button class="btn-actualizar" onclick="cargarPagosRechazados()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    `;
    
    // TABLA ABAJO
    const tablaContainer = document.createElement('div');
    tablaContainer.className = 'tabla-container';
    
    const tabla = document.createElement('table');
    tabla.className = 'tabla-pagos';
    
    // ENCABEZADO DE TABLA
    const thead = document.createElement('thead');
    thead.innerHTML = `
        <tr>
            <th><i class="fas fa-user"></i> Usuario</th>
            <th><i class="fas fa-calendar"></i> Fecha Pago</th>
            <th><i class="fas fa-times-circle"></i> Fecha Rechazo</th>
            <th><i class="fas fa-money-bill-wave"></i> Monto</th>
            <th><i class="fas fa-comment"></i> Razón del Rechazo</th>
            <th><i class="fas fa-user-times"></i> Rechazado por</th>
            <th><i class="fas fa-cogs"></i> Acciones</th>
        </tr>
    `;
    
    // CUERPO DE TABLA
    const tbody = document.createElement('tbody');
    
    pagos.forEach(pago => {
        const tr = document.createElement('tr');
        tr.className = 'pago-fila';
        tr.dataset.pagoId = pago.id;
        
        tr.innerHTML = `
            <td>
                <div class="usuario-info">
                    <div class="usuario-nombre">${escapeHtml(pago.usuario_nombre)}</div>
                    <small class="usuario-id">ID: ${pago.usuario_id} | Pago: #${pago.pago_id}</small>
                </div>
            </td>
            <td>
                <div class="fecha-info">
                    <i class="fas fa-clock"></i>
                    ${pago.fecha_pago_formateada}
                </div>
            </td>
            <td>
                <div class="fecha-info rechazo">
                    <i class="fas fa-times"></i>
                    ${pago.fecha_rechazo_formateada}
                </div>
            </td>
            <td>
                <div class="monto-info">
                    <strong>$${pago.monto.toFixed(2)}</strong>
                    ${pago.mes_pagado ? `<br><small>${escapeHtml(pago.mes_pagado)}</small>` : ''}
                </div>
            </td>
            <td>
                <div class="razon-rechazo">
                    <i class="fas fa-exclamation-triangle"></i>
                    ${pago.razon_rechazo ? escapeHtml(pago.razon_rechazo.substring(0, 50)) + (pago.razon_rechazo.length > 50 ? '...' : '') : 'Sin razón'}
                </div>
            </td>
            <td>
                <div class="rechazador-info">
                    <i class="fas fa-user-shield"></i>
                    ${escapeHtml(pago.rechazado_por)}
                </div>
            </td>
            <td>
                <div class="acciones-tabla">
                    ${pago.referencia ? `
                    <button class="btn-tabla btn-referencia" onclick="verReferenciaRechazado('${escapeHtml(pago.referencia)}')" 
                            title="Ver referencia">
                        <i class="fas fa-search-dollar"></i>
                    </button>` : ''}
                    
                    ${pago.comprobante ? `
                    <button class="btn-tabla btn-comprobante" onclick="verComprobanteRechazado('${escapeHtml(pago.comprobante)}')" 
                            title="Ver comprobante">
                        <i class="fas fa-file-invoice"></i>
                    </button>` : ''}
                    
                    <button class="btn-tabla btn-eliminar" onclick="eliminarPagoRechazado(${pago.id})" 
                            title="Eliminar del historial">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    
                    <button class="btn-tabla btn-detalles" onclick="verDetallesRechazado(${pago.id})" 
                            title="Ver detalles">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
    
    // ENSAMBLAR TABLA
    tabla.appendChild(thead);
    tabla.appendChild(tbody);
    tablaContainer.appendChild(tabla);
    
    // AGREGAR AL CONTENEDOR
    contenedor.appendChild(statsContainer);
    contenedor.appendChild(tablaContainer);
}

function verReferenciaRechazado(referencia) {
    if (referencia) {
        alert(`Referencia del pago rechazado:\n\n${referencia}`);
    } else {
        alert('No hay referencia disponible');
    }
}

function verComprobanteRechazado(comprobante) {
    if (comprobante) {
        window.open(`../../../uploads/comprobantes/${comprobante}`, '_blank');
    } else {
        alert('No hay comprobante disponible');
    }
}

function eliminarPagoRechazado(pagoId) {
    if (confirm(`¿Está seguro de ELIMINAR este pago del historial de rechazados?\n\nEsta acción no se puede deshacer.`)) {
        const datos = new URLSearchParams();
        datos.append('pago_id', pagoId);
        
        fetch('../../procesos/admin/pagos/eliminar_rechazado.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: datos
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`✅ Pago eliminado del historial de rechazados`);
                cargarPagosRechazados();
            } else {
                alert(`❌ Error: ${data.error || 'No se pudo eliminar'}`);
            }
        })
        .catch(() => {
            alert('Error de conexión');
        });
    }
}

function verDetallesRechazado(pagoId) {
    // Implementar modal o más detalles
    const fila = document.querySelector(`[data-pago-id="${pagoId}"]`);
    if (fila) {
        alert('Funcionalidad de detalles en desarrollo');
    }
}

function mostrarErrorRechazados(mensaje) {
    const contenedor = document.getElementById('lista-rechazados');
    contenedor.innerHTML = `
        <div class="estado-error">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>Error al cargar pagos rechazados</h4>
            <p>${escapeHtml(mensaje)}</p>
            <button class="btn-reintentar" onclick="cargarPagosRechazados()">
                <i class="fas fa-redo"></i> Reintentar
            </button>
        </div>
    `;
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}