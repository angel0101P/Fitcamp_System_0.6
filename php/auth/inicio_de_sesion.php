<?php

session_start();

include '../config/conexion.php';


/*aqui obtengo los datos */
$usuario_credencial = trim($_POST['user_credencial'] ?? '');
$password_credencial = $_POST['password_credencial'] ?? '';
$tipo_de_usuario = trim($_POST['tipo_usuario']);


if ($tipo_de_usuario == "cliente"){


    /*hago la consulta con esto */
    $query = "SELECT id, name_, surname, email, user, password_ FROM usuarios WHERE user = ? or email = ?";


    $stmt = mysqli_prepare($conexion, $query);

    if(!$stmt){
        '<script>
            alert("Error al iniciar sesion");
            window.history.back();
        </script>';
    }

    /*envio los parametros para la consulta */

    mysqli_stmt_bind_param($stmt, "ss", $usuario_credencial, $usuario_credencial);

    mysqli_stmt_execute($stmt);

    $resultado_de_la_consulta = mysqli_stmt_get_result($stmt);

    /*comprobaciones */
    if($row = mysqli_fetch_assoc($resultado_de_la_consulta)){

        /*en caso de que el usuario o correo exista pasa a consultar la contraseña */
        if(password_verify($password_credencial, $row['password_'])){
            session_regenerate_id(true);

            $_SESSION['id'] = $row['id'];
            $_SESSION['nombre_completo'] = $row['name_'] . ' ' . $row['surname'];
            $_SESSION['usuario_email'] = $row['email'];
            $_SESSION['usuario_user'] = $row['user'];
            $_SESSION['login_time'] = time(); // Tiempo de inicio de sesión, esto lo usare despues

            mysqli_stmt_close($stmt);
            mysqli_close($conexion);

            /*redirige al dashboard, aun en construccion xd */
            header("Location: ../../dashboard/index.php");
            exit();

        }else {
            // Contraseña incorrecta
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            echo '<script>
            alert("Contraseña incorrecta");
            window.history.back();
            </script>';
            exit();
        }

    } else {
        // Usuario no encontrado
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
            echo '<script>
            alert("Perfil de usuario no encontrado");
            window.history.back();
            </script>';
        exit();
    }

} elseif($tipo_de_usuario == "admin"){

    /*hago la consulta con esto */
    $query = "SELECT id, nombre_, apellido_, email, username, password_ FROM admin_lista WHERE username = ? or email = ?";


    $stmt = mysqli_prepare($conexion, $query);

    if(!$stmt){
        '<script>
            alert("Error al iniciar sesion");
            window.history.back();
        </script>';
    }

    /*envio los parametros para la consulta */

    mysqli_stmt_bind_param($stmt, "ss", $usuario_credencial, $usuario_credencial);

    mysqli_stmt_execute($stmt);

    $resultado_de_la_consulta = mysqli_stmt_get_result($stmt);

    /*comprobaciones */
    if($row = mysqli_fetch_assoc($resultado_de_la_consulta)){

        /*en caso de que el usuario o correo exista pasa a consultar la contraseña */
        if(password_verify($password_credencial, $row['password_'])){
            session_regenerate_id(true);

            $_SESSION['id'] = $row['id'];
            $_SESSION['nombre_completo'] = $row['nombre_'] . ' ' . $row['apellido_'];
            $_SESSION['usuario_email'] = $row['email'];
            $_SESSION['usuario_user'] = $row['username'];
            $_SESSION['login_time'] = time(); // Tiempo de inicio de sesión, esto lo usare despues

            mysqli_stmt_close($stmt);
            mysqli_close($conexion);

            /*redirige al dashboard, aun en construccion xd */
            header("Location: ../../dashboard/admin/index_admin.php");
            exit();

        }else {
            // Contraseña incorrecta
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            echo '<script>
            alert("Contraseña incorrecta");
            window.history.back();
            </script>';
            exit();
        }

    } else {
        // Usuario no encontrado
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
            echo '<script>
            alert("Perfil de usuario no encontrado");
            window.history.back();
            </script>';
        exit();
    }



}



?>