<?php
session_start();
//procesar mensajes

error_reporting(E_ALL);
ini_set('display_errors', 1);


include '../config/conexion.php';

if (!isset($_SESSION['usuario_user'])) {
    echo '<script>
        alert("Debe iniciar sesión primero");
        window.location = "../auth/index_login.php";
    </script>';
    exit();
}


$titulo = trim($_POST['titulo']);
$mensaje = trim($_POST['mensaje']);
$admin_remitente = $_SESSION['usuario_user'];
$tipo = trim($_POST['tipo']);
$destinatario = trim($_POST['destinatario']);



if($tipo == "general"){
    $query = "INSERT INTO notificaciones_generales ( titulo_mensaje, mensaje, admin_remitente) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "sss", $titulo, $mensaje, $admin_remitente);

        if(mysqli_stmt_execute($stmt)){
            echo '<script>
            alert("Mensaje Enviado")
            window.location = "../../dashboard/admin/index_admin.php";
            </script>';

        }else{
            echo '<script>
            alert("error al enviar")
            window.location = "../../dashboard/admin/index_admin.php";
            </script>';
        }
    } else{
            echo '<script>
            alert("Error de conexion")
            window.location = "../../dashboard/admin/index_admin.php";
            </script>';
    }


} elseif($tipo == "privado"){

    $query = "INSERT INTO notificaciones_privadas (titulo_mensaje, mensaje, usuario_receptor, admin_remitente) VALUES  (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "ssss", $titulo, $mensaje, $destinatario, $admin_remitente);

        if(mysqli_stmt_execute($stmt)){
            echo '<script>
            alert("Mensaje Enviado")
            window.location = "../../dashboard/admin/index_admin.php";
            </script>';
        }else{
            echo '<script>
            alert("error al enviar")
            window.location = "../../dashboard/admin/index_admin.php";
            </script>';
        }

    }else{
            echo '<script>
            alert("Error de conexion")
            window.location = "../../dashboard/admin/index_admin.php";
            </script>';
    }




}





?>