<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escribir Mensaje - Fitcamp Admin</title>
    <link rel="stylesheet" href="../../../styles/escribir_mensaje.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="container">
        <!-- Botón de regreso -->
        <a href="../../../dashboard/admin/index_admin.php" class="btn-regresar">
            <i class="fas fa-arrow-left"></i> Regresar al Panel
        </a>

        <!-- Caja de mensajes -->
        <div class="caja-de-mensajes">
            <h2><i class="fas fa-edit"></i> Redactar El Mensaje</h2>
            
            <form id="form-mensaje" action="../../procesos/procesar_mensaje.php" method="POST">
                <div class="form-group">
                    <input type="text" 
                           name="titulo" 
                           id="titulo" 
                           class="form-input"
                           placeholder="Título del mensaje"
                           required>
                </div>
                
                <div class="form-group">
                    <textarea name="mensaje" 
                              id="mensaje" 
                              class="form-textarea"
                              placeholder="Escribe tu mensaje aquí..."
                              required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Tipo de mensaje:</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="tipo" value="general" checked>
                            <span class="radio-label">General (para todos)</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tipo" value="privado">
                            <span class="radio-label">Privado</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group" id="grupo-destinatario" style="display: none;">
                    <input type="text" 
                           name="destinatario" 
                           class="form-input"
                           placeholder="Usuario del destinatario">
                </div>
                
                <button type="submit" class="btn-enviar" id="btn-enviar">
                    <i class="fas fa-paper-plane"></i> Enviar Mensaje
                </button>
            </form>
        </div>
    </div>

    <!-- Modal emergente -->
    <div id="modal-emergente" class="modal-overlay">
        <div class="emergente">
            <div class="emergente-header">
                <i class="fas fa-exclamation-circle"></i>
                <h3>Importante</h3>
            </div>
            
            <div class="emergente-body">
                <p>Este mensaje se enviará de forma general a <strong>TODOS</strong> los usuarios.</p>
                <p id="detalles-mensaje"></p>
            </div>
            
            <div class="emergente-footer">
                <button class="btn-cancelar" id="btn-cancelar-modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="btn-confirmar" id="btn-confirmar-modal">
                    <i class="fas fa-check"></i> Confirmar y Enviar
                </button>
            </div>
        </div>
    </div>

        <!-- Modal para seleccionar usuario (PRIVADO) -->
    <div id="modal-seleccion-usuario" class="modal-overlay">
        <div class="emergente emergente-grande">
            <div class="emergente-header">
                <div class="header-icon">
                    <i class="fas fa-user-friends"></i>
                    <h3>Seleccionar Usuario Destinatario</h3>
                </div>
                <button class="btn-cerrar-modal" id="btn-cerrar-usuario-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="emergente-body">
                <!-- Barra de búsqueda -->
                <div class="search-container">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                            id="buscar-usuario-input" 
                            class="form-input-search"
                            placeholder="Buscar por nombre, usuario o email..."
                            autocomplete="off">
                        <div class="search-count" id="contador-resultados">
                            <span id="total-resultados">0</span> usuarios encontrados
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de usuarios -->
                <div class="tabla-usuarios-container">
                    <table class="tabla-usuarios">
                        <thead>
                            <tr>
                                <th width="20%">Usuario</th>
                                <th width="35%">Nombre Completo</th>
                                <th width="35%">Email</th>
                                <th width="10%">Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody id="lista-usuarios-body">
                            <!-- Estado inicial: cargando -->
                            <tr id="estado-cargando">
                                <td colspan="4" class="text-center estado-mensaje">
                                    <div class="spinner pequeno"></div>
                                    <span>Cargando usuarios...</span>
                                </td>
                            </tr>
                            <!-- Estado: sin resultados -->
                            <tr id="estado-sin-resultados" style="display: none;">
                                <td colspan="4" class="text-center estado-mensaje">
                                    <i class="fas fa-users-slash"></i>
                                    <span>No se encontraron usuarios. Intenta con otra búsqueda.</span>
                                </td>
                            </tr>
                            <!-- Estado: error -->
                            <tr id="estado-error" style="display: none;">
                                <td colspan="4" class="text-center estado-mensaje error">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Error al cargar usuarios. Intenta nuevamente.</span>
                                </td>
                            </tr>
                            <!-- Aquí se insertarán los usuarios dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Usuario seleccionado -->
                <div class="usuario-seleccionado-container" id="usuario-seleccionado-container" style="display: none;">
                    <div class="seleccion-header">
                        <i class="fas fa-user-check"></i>
                        <strong>Destinatario seleccionado:</strong>
                    </div>
                    <div class="seleccion-info">
                        <div class="info-item">
                            <span class="info-label">Usuario:</span>
                            <span class="info-value" id="usuario-seleccionado-username"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nombre:</span>
                            <span class="info-value" id="usuario-seleccionado-nombre"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value" id="usuario-seleccionado-email"></span>
                        </div>
                    </div>
                    <input type="hidden" id="usuario-seleccionado-id" name="destinatario_id">
                </div>
            </div>
            
            <div class="emergente-footer">
                <button type="button" class="btn-cancelar" id="btn-cancelar-seleccion">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-confirmar" id="btn-confirmar-seleccion" disabled>
                    <i class="fas fa-check"></i> Usar este Destinatario
                </button>
            </div>
        </div>
    </div>


    <script src="../../../scripts/escribir_mensaje.js"></script>
</body>
</html>