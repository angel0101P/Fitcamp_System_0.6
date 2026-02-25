<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitcamp System Manager</title>
    <link rel="stylesheet" href="../../styles/styles.css">
    <link rel="icon" href="../../images/Fitcamp_Logo.png">
</head>

<body>

    <div class="contenedor-imagen">
        <img src="../../images/Fitcamp_Logo.png" alt="Logo Fitcamp" class="logo-main">
        <div id="mensaje-motivador" class="mensaje-txt">
            "EL ÉXITO COMIENZA CON LA DECISIÓN DE INTENTARLO"
        </div>
    </div>


    <div class="contenedor">
        <div class="tabs">
            <button class="tab active" onclick="openTab('login')">Login</button>
            <button class="tab" onclick="openTab('registro')">Registro</button>
        </div>

        <div id="login" class="form-contenedor active">
            <h2>Iniciar Sesión</h2>
            <form action="inicio_de_sesion.php" method="POST">

                <div class="select-container">
                    <select name="tipo_usuario" class="form-select" required>
                        <option value="cliente" selected>Cliente</option>
                        <option value="monitor">Monitor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>


                <input type="text" placeholder="Usuario" name="user_credencial" required>
                <input type="password" placeholder="Contraseña" name="password_credencial" required>
                <p>¿Aun no tienes una Cuenta? ¡Dale a Registro!</p>
                <button type="submit">Acceder</button>
            </form>
        </div>

        <div id="registro" class="form-contenedor">
            <h2>Registrarse</h2>
            <form action="registro_de_usuarios.php" method="POST">
                <input type="text" placeholder="Nombre" name="name" required>
                <input type="text" placeholder="Apellido" name="surname" required>
                <input type="email" placeholder="Correo Electrónico" name="email" required>
                <input type="text" placeholder="Usuario" name="user" required>
                <input type="password" placeholder="Contraseña" name="password" required>
                <input type="password" placeholder="Confirmar contraseña" name="confirm_password" required>
                <p>¿Ya tienes una Cuenta? ¡Dale a Login!</p>
                <button type="submit">Registrarse</button>
            </form>
        </div>
    </div>

    <script src="../../scripts/script_style_login.js"></script>
</body>

</html>