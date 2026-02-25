// =============================================
// DASHBOARD FITCAMP SYSTEM MANAGER
// =============================================

const CONFIG = {
    api: {
        notificaciones: {
            privadas: '../php/procesos/obtener_notificaciones.php',
            generales: '../php/procesos/obtener_notificaciones_generales.php'
        },
        usuario: '../php/procesos/obtener_perfil.php',
        rutinas: '../php/procesos/obtener_rutinas.php',
        pagos: '../php/modulos/pagos/',
        cerrarSesion: '../php/auth/cerrar_sesion.php'
    },
    
    secciones: {
        'inicio': {
            titulo: 'Inicio',
            icono: 'fa-home',
            cargar: mostrarInicio
        },
        'notificaciones': {
            titulo: 'Notificaciones',
            icono: 'fa-bell',
            cargar: cargarModuloNotificaciones
        },
        'rutinas': {
            titulo: 'Rutinas',
            icono: 'fa-calendar-alt',
            cargar: cargarModuloRutinas
        },
        'pagos': {
            titulo: 'Pagos',
            icono: 'fa-credit-card',
            cargar: cargarModuloPagos
        },
        'nutricion': {
            titulo: 'Nutrición',
            icono: 'fa-apple-whole',
            cargar: cargarModuloNutricion
        },
        'progreso': {
            titulo: 'Progreso',
            icono: 'fa-chart-line',
            cargar: cargarModuloProgreso
        },
        'perfil': {
            titulo: 'Perfil',
            icono: 'fa-user-circle',
            cargar: cargarModuloPerfil
        }
    }
};

const ESTADO = {
    usuario: {
        id: null,
        nombre: 'Usuario',
        foto: '../images/Fitcamp_Logo.png'
    },
    seccionActual: 'inicio',
    submenuAbierto: null,
    cache: {},
    notificacionesLeidas: JSON.parse(localStorage.getItem('notificacionesLeidas') || '[]'),
    inicializado: false
};

// =============================================
// SISTEMA DE NAVEGACIÓN
// =============================================

function toggleSubmenu(event, id) {
    event.stopPropagation();
    
    if (ESTADO.submenuAbierto && ESTADO.submenuAbierto !== id) {
        const submenuAnterior = document.getElementById(ESTADO.submenuAbierto);
        const arrowAnterior = document.querySelector(`[onclick*="${ESTADO.submenuAbierto}"] .arrow-icon`);
        if (submenuAnterior) submenuAnterior.classList.remove('show');
        if (arrowAnterior) arrowAnterior.classList.remove('rotate');
    }
    
    const submenu = document.getElementById(id);
    const arrow = event.currentTarget.querySelector('.arrow-icon');
    
    submenu.classList.toggle('show');
    if (arrow) arrow.classList.toggle('rotate');
    
    ESTADO.submenuAbierto = submenu.classList.contains('show') ? id : null;
}

async function cargarSeccion(seccionId, elemento = null) {
    console.log(`Cargando sección: ${seccionId}`);
    
    ESTADO.seccionActual = seccionId;
    actualizarNavegacionActiva(seccionId, elemento);
    mostrarLoading(seccionId);
    
    // Cargar la sección inmediatamente
    try {
        const configSeccion = CONFIG.secciones[seccionId];
        if (!configSeccion) {
            mostrarError('Sección no encontrada');
            return;
        }
        
        document.title = `${configSeccion.titulo} - Fitcamp`;
        await configSeccion.cargar();
        
    } catch (error) {
        console.error(`Error al cargar sección ${seccionId}:`, error);
        mostrarError(`Error al cargar ${seccionId}`);
    }
}

function actualizarNavegacionActiva(seccionId, elemento) {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    if (elemento) {
        const navItem = elemento.closest('.nav-item') || elemento;
        navItem.classList.add('active');
    } else {
        const elementoNav = document.querySelector(`[data-seccion="${seccionId}"]`);
        if (elementoNav) elementoNav.classList.add('active');
    }
    
    if (ESTADO.submenuAbierto) {
        document.getElementById(ESTADO.submenuAbierto).classList.remove('show');
        ESTADO.submenuAbierto = null;
    }
}

// =============================================
// INTERFAZ Y ESTADOS
// =============================================

function mostrarLoading(seccion) {
    const contenido = document.getElementById('contenido-dinamico');
    const configSeccion = CONFIG.secciones[seccion];
    
    contenido.innerHTML = `
        <div class="estado-carga">
            <div class="spinner-grande"></div>
            <h3>Cargando ${configSeccion?.titulo || seccion}</h3>
            <p>Por favor, espera un momento...</p>
        </div>
    `;
}

function mostrarError(mensaje) {
    const contenido = document.getElementById('contenido-dinamico');
    contenido.innerHTML = `
        <div class="estado-error">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Error</h3>
            <p>${mensaje}</p>
            <button class="btn-reintentar" onclick="cargarSeccion('inicio')">
                <i class="fas fa-home"></i> Volver al inicio
            </button>
        </div>
    `;
}

// =============================================
// SISTEMA DE NOTIFICACIONES
// =============================================

function actualizarBadgeSidebar(cantidad) {
    const badgeContainer = document.getElementById('badge-notificaciones');
    const badgeNumber = document.getElementById('contador-notificaciones');
    
    if (badgeContainer && badgeNumber) {
        if (cantidad > 0) {
            badgeNumber.textContent = cantidad;
            badgeContainer.style.display = 'flex';
        } else {
            badgeContainer.style.display = 'none';
        }
    }
}

function marcarComoLeida(idNotificacion) {
    if (!ESTADO.notificacionesLeidas.includes(idNotificacion)) {
        ESTADO.notificacionesLeidas.push(idNotificacion);
        localStorage.setItem('notificacionesLeidas', JSON.stringify(ESTADO.notificacionesLeidas));
    }
    actualizarBadges();
}

function marcarTodasComoLeidas() {
    if (ESTADO.cache.notificaciones) {
        ESTADO.cache.notificaciones.forEach(notif => {
            if (!ESTADO.notificacionesLeidas.includes(notif.id)) {
                ESTADO.notificacionesLeidas.push(notif.id);
            }
        });
        localStorage.setItem('notificacionesLeidas', JSON.stringify(ESTADO.notificacionesLeidas));
        actualizarBadges();
        cargarModuloNotificaciones();
    }
}

function actualizarBadges() {
    if (ESTADO.cache.notificaciones) {
        const noLeidas = ESTADO.cache.notificaciones.filter(
            notif => !ESTADO.notificacionesLeidas.includes(notif.id)
        ).length;
        
        actualizarBadgeSidebar(noLeidas);
        
        const badgeInicio = document.getElementById('badge-inicio-notificaciones');
        if (badgeInicio) {
            if (noLeidas > 0) {
                badgeInicio.textContent = noLeidas;
                badgeInicio.style.display = 'flex';
            } else {
                badgeInicio.style.display = 'none';
            }
        }
    }
}

// =============================================
// MÓDULOS DE CONTENIDO
// =============================================

function mostrarInicio() {
    const contenido = document.getElementById('contenido-dinamico');
    
    contenido.innerHTML = `
        <div class="inicio-container">
            <div class="inicio-header">
                <h1><i class="fas fa-home"></i> Bienvenido a Fitcamp</h1>
                <p>Gestiona tu entrenamiento desde un solo lugar.</p>
            </div>
            
            <div class="inicio-grid">
                <div class="tarjeta-inicio" onclick="cargarSeccion('notificaciones')">
                    <div class="tarjeta-icono">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Notificaciones</h3>
                    <p>Revisa tus mensajes</p>
                    <div class="tarjeta-badge" id="badge-inicio-notificaciones">0</div>
                </div>
                
                <div class="tarjeta-inicio" onclick="cargarSeccion('rutinas')">
                    <div class="tarjeta-icono">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Rutinas</h3>
                    <p>Plan de entrenamiento</p>
                </div>
                
                <div class="tarjeta-inicio" onclick="cargarSeccion('nutricion')">
                    <div class="tarjeta-icono">
                        <i class="fas fa-apple-whole"></i>
                    </div>
                    <h3>Nutrición</h3>
                    <p>Plan alimenticio</p>
                </div>
                
                <div class="tarjeta-inicio" onclick="cargarSeccion('progreso')">
                    <div class="tarjeta-icono">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Progreso</h3>
                    <p>Sigue tu evolución</p>
                </div>
            </div>
        </div>
    `;
    
    // Actualizar badge en inicio
    const badgeInicio = document.getElementById('badge-inicio-notificaciones');
    if (badgeInicio && ESTADO.cache.notificaciones) {
        const noLeidas = ESTADO.cache.notificaciones.filter(
            notif => !ESTADO.notificacionesLeidas.includes(notif.id)
        ).length;
        if (noLeidas > 0) {
            badgeInicio.textContent = noLeidas;
            badgeInicio.style.display = 'flex';
        } else {
            badgeInicio.style.display = 'none';
        }
    }
}

// Módulo de Notificaciones
async function cargarModuloNotificaciones() {
    try {
        mostrarLoading('notificaciones');
        
        let notificaciones = [];
        
        try {
            const responsePrivadas = await fetch(CONFIG.api.notificaciones.privadas);
            if (responsePrivadas.ok) {
                const data = await responsePrivadas.json();
                if (data.success && data.notificaciones) {
                    notificaciones = notificaciones.concat(data.notificaciones.map(n => ({ 
                        ...n, 
                        tipo: 'privada',
                        id: n.id || Date.now() + Math.random()
                    })));
                }
            }
        } catch (error) {
            console.error('Error en notificaciones privadas:', error);
        }
        
        try {
            const responseGenerales = await fetch(CONFIG.api.notificaciones.generales);
            if (responseGenerales.ok) {
                const data = await responseGenerales.json();
                if (data.success && data.notificaciones) {
                    notificaciones = notificaciones.concat(data.notificaciones.map(n => ({ 
                        ...n, 
                        tipo: 'general',
                        id: n.id || Date.now() + Math.random()
                    })));
                }
            }
        } catch (error) {
            console.error('Error en notificaciones generales:', error);
        }
        
        ESTADO.cache.notificaciones = notificaciones;
        actualizarBadges();
        
        mostrarContenidoNotificaciones(notificaciones);
        
    } catch (error) {
        console.error('Error en módulo notificaciones:', error);
        mostrarError('Error al cargar notificaciones');
    }
}

function mostrarContenidoNotificaciones(notificaciones) {
    const contenido = document.getElementById('contenido-dinamico');
    
    if (!notificaciones || notificaciones.length === 0) {
        contenido.innerHTML = `
            <div class="modulo-container">
                <div class="modulo-header">
                    <h2><i class="fas fa-bell"></i> Mis Notificaciones</h2>
                </div>
                <div class="modulo-contenido">
                    <div class="modulo-vacio">
                        <i class="fas fa-bell-slash"></i>
                        <h3>No hay notificaciones</h3>
                        <p>¡Todo está al día!</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    const notificacionesFiltradas = notificaciones
        .map(notif => ({
            ...notif,
            leida: ESTADO.notificacionesLeidas.includes(notif.id)
        }));
    
    const privadas = notificacionesFiltradas.filter(n => n.tipo === 'privada');
    const generales = notificacionesFiltradas.filter(n => n.tipo === 'general');
    const noLeidas = notificacionesFiltradas.filter(n => !n.leida);
    
    contenido.innerHTML = `
        <div class="modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-bell"></i> Mis Notificaciones</h2>
                <div class="modulo-acciones">
                    <button class="btn-actualizar" onclick="cargarModuloNotificaciones()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
            
            <div class="modulo-estadisticas">
                <div class="estadistica ${noLeidas.length > 0 ? 'destacada' : ''}">
                    <span class="estadistica-valor">${noLeidas.length}</span>
                    <span class="estadistica-texto">Nuevas</span>
                </div>
                <div class="estadistica">
                    <span class="estadistica-valor">${notificacionesFiltradas.length}</span>
                    <span class="estadistica-texto">Total</span>
                </div>
                <div class="estadistica">
                    <span class="estadistica-valor">${generales.length}</span>
                    <span class="estadistica-texto">Generales</span>
                </div>
                <div class="estadistica">
                    <span class="estadistica-valor">${privadas.length}</span>
                    <span class="estadistica-texto">Privadas</span>
                </div>
            </div>
            
            ${noLeidas.length > 0 ? `
            <div class="modulo-accion-multiple">
                <button class="btn-marcar-todas" onclick="marcarTodasComoLeidas()">
                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                </button>
            </div>
            ` : ''}
            
            <div class="lista-modulo">
                ${notificacionesFiltradas.map((notif, index) => `
                    <div class="item-modulo ${notif.leida ? 'leida' : 'no-leida'} ${notif.tipo}">
                        <div class="item-icono">
                            <i class="fas ${notif.tipo === 'privada' ? 'fa-user-lock' : 'fa-bullhorn'}"></i>
                        </div>
                        <div class="item-contenido">
                            <div class="item-header">
                                <h3>${notif.titulo || 'Notificación'}</h3>
                                <div class="item-acciones">
                                    ${!notif.leida ? `
                                    <button class="btn-marcar-leida" onclick="marcarComoLeida(${notif.id}); this.closest('.item-modulo').classList.add('leida'); actualizarBadges(); cargarModuloNotificaciones();">
                                        <i class="fas fa-check"></i> Marcar como leída
                                    </button>
                                    ` : `
                                    <span class="badge-leida">
                                        <i class="fas fa-check-circle"></i> Leída
                                    </span>
                                    `}
                                </div>
                            </div>
                            <div class="item-mensaje">
                                <p>${notif.mensaje || 'Sin mensaje'}</p>
                            </div>
                            <div class="item-detalles">
                                ${notif.remitente ? `
                                    <span class="item-remitente">
                                        <i class="fas fa-user"></i> ${notif.remitente}
                                    </span>
                                ` : ''}
                                <span class="item-tipo">${notif.tipo === 'privada' ? 'Privada' : 'General'}</span>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="modulo-footer">
                <p>Mostrando ${notificacionesFiltradas.length} notificaciones</p>
            </div>
        </div>
    `;
}

// Módulos restantes
async function cargarModuloRutinas() {
    const contenido = document.getElementById('contenido-dinamico');
    contenido.innerHTML = `
        <div class="modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-calendar-alt"></i> Rutinas y Actividades</h2>
            </div>
            <div class="modulo-contenido">
                <div class="modulo-en-construccion">
                    <i class="fas fa-dumbbell"></i>
                    <h3>Módulo en desarrollo</h3>
                    <p>Próximamente podrás gestionar tus rutinas de entrenamiento aquí.</p>
                </div>
            </div>
        </div>
    `;
}

async function cargarModuloNutricion() {
    const contenido = document.getElementById('contenido-dinamico');
    contenido.innerHTML = `
        <div class="modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-apple-whole"></i> Nutrición</h2>
            </div>
            <div class="modulo-contenido">
                <div class="modulo-en-construccion">
                    <i class="fas fa-utensils"></i>
                    <h3>Módulo en desarrollo</h3>
                    <p>Próximamente podrás gestionar tu plan nutricional aquí.</p>
                </div>
            </div>
        </div>
    `;
}

async function cargarModuloProgreso() {
    const contenido = document.getElementById('contenido-dinamico');
    contenido.innerHTML = `
        <div class="modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-chart-line"></i> Mi Progreso</h2>
            </div>
            <div class="modulo-contenido">
                <div class="modulo-en-construccion">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Módulo en desarrollo</h3>
                    <p>Próximamente podrás ver tu progreso y estadísticas aquí.</p>
                </div>
            </div>
        </div>
    `;
}

async function cargarModuloPerfil() {
    const contenido = document.getElementById('contenido-dinamico');
    contenido.innerHTML = `
        <div class="modulo-container">
            <div class="modulo-header">
                <h2><i class="fas fa-user-circle"></i> Mi Perfil</h2>
            </div>
            <div class="modulo-contenido">
                <div class="modulo-en-construccion">
                    <i class="fas fa-user-edit"></i>
                    <h3>Módulo en desarrollo</h3>
                    <p>Próximamente podrás gestionar tu perfil aquí.</p>
                </div>
            </div>
        </div>
    `;
}

function cargarModuloPagos() {
    window.location.href = CONFIG.api.pagos;
}

function cerrarSesion() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = CONFIG.api.cerrarSesion;
    }
}

// =============================================
// INICIALIZACIÓN CORREGIDA
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Fitcamp inicializado');
    
    // Cerrar submenús al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.has-submenu') && ESTADO.submenuAbierto) {
            document.getElementById(ESTADO.submenuAbierto).classList.remove('show');
            ESTADO.submenuAbierto = null;
        }
    });
    
    // Ocultar badge al inicio
    const badgeContainer = document.getElementById('badge-notificaciones');
    if (badgeContainer) {
        badgeContainer.style.display = 'none';
    }
    
    // Marcar como inicializado
    ESTADO.inicializado = true;
    
    // Mostrar inicio directamente
    mostrarInicio();
    
    // Cargar notificaciones en segundo plano (sin afectar la UI)
    setTimeout(() => {
        cargarNotificacionesEnSegundoPlano();
    }, 100);
});

// Función para cargar notificaciones sin afectar la UI
async function cargarNotificacionesEnSegundoPlano() {
    try {
        let notificaciones = [];
        
        const responsePrivadas = await fetch(CONFIG.api.notificaciones.privadas);
        if (responsePrivadas.ok) {
            const data = await responsePrivadas.json();
            if (data.success && data.notificaciones) {
                notificaciones = notificaciones.concat(data.notificaciones.map(n => ({ 
                    ...n, 
                    tipo: 'privada',
                    id: n.id || Date.now() + Math.random()
                })));
            }
        }
        
        const responseGenerales = await fetch(CONFIG.api.notificaciones.generales);
        if (responseGenerales.ok) {
            const data = await responseGenerales.json();
            if (data.success && data.notificaciones) {
                notificaciones = notificaciones.concat(data.notificaciones.map(n => ({ 
                    ...n, 
                    tipo: 'general',
                    id: n.id || Date.now() + Math.random()
                })));
            }
        }
        
        ESTADO.cache.notificaciones = notificaciones;
        actualizarBadges();
        
    } catch (error) {
        console.error('Error al cargar notificaciones en segundo plano:', error);
    }
}

// Función para HTML (eliminé la API pública por seguridad)
function cargarNotificaciones() {
    cargarSeccion('notificaciones');
}