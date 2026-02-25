<?php

/*GAY EL QUE EDITE EL ARCHIVO SIN MI PERMISO */


/*esto es para errores */
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/conexion.php';

$name = trim($_POST['name']);
$surname = trim($_POST['surname']);
$email = trim($_POST['email']);
$user = trim($_POST['user']);
$password = ($_POST['password']);
$confirm_password = ($_POST['confirm_password']);

/*array para los errores*/ 
 $error = [];


/*esto es para campos vacíos, aunque creo que es innecesario, ya que en el html esta el 'requeried'*/
 if(empty($name) || empty($surname) || empty($email) || empty($user) || empty($password)){
   $error[] = "Todos los campos son obligatorios";
 }

/*lo de arriba */
 if(empty($confirm_password)){
   $error[] = "Debe repetir su contraseña";
 }

/*comprobar que coincidan las contraseñas */ 
 if($password != $confirm_password){
   $error[] = "Las contraseñas no coinciden";
 }

/*comprobar el formato del correo */
 if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
   $error[] = "Formato de correo inválido";
 }

/*la longitud de la contraseña */
 if(strlen($password) <= 7){
   $error[] = "La contraseña es muy corta";
 }

 /*lo que contiene la contraseña (debo tambien comprobar que tenga al menos una letra y 
 un numero, pero lo hare luego) */
 if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)){
   $error[] = "La contraseña debe tener al menos un carácter especial (!@#$%^&* etc.)";
 } 

 /*hace que el error se muestre */
 if(!empty($error)) {
    echo '<script>alert("' . implode("\\n", $error) . '"); window.history.back();</script>';
    exit();
 }

 /*encripta la contraseña */
 $password_encrypted = password_hash($password, PASSWORD_DEFAULT);


 /*comprueba que el usuario y correo no existan */
 $query_check = "SELECT id FROM usuarios where user = ? OR email = ?";
 $stmt_check = mysqli_prepare($conexion, $query_check);
 mysqli_stmt_bind_param($stmt_check, "ss", $user, $email);
 mysqli_stmt_execute($stmt_check);
 mysqli_stmt_store_result($stmt_check);

 if(mysqli_stmt_num_rows($stmt_check) > 0){
   echo '<script>alert("El usuario o el correo ya están registrados")</script>';

   mysqli_stmt_close($stmt_check);
   mysqli_close($conexion);
   exit();
 }

 mysqli_stmt_close($stmt_check);


/*esto registra los usuarios */
 $query = "INSERT INTO usuarios (name_, surname, email, user, password_) VALUES (?, ?, ?, ?, ?)";
 $stmt = mysqli_prepare($conexion, $query);

 if($stmt){
   mysqli_stmt_bind_param($stmt, "sssss", $name, $surname, $email, $user, $password_encrypted);

   if(mysqli_stmt_execute($stmt)){
     echo '<script>
     alert ("USUARIO REGISTRADO");
     window.location = "../../dashboard/index.php";
    </script>
    ';

   } else {

        echo '<script>
            alert("Error al registrar");
            window.history.back();
        </script>';
    }

   mysqli_stmt_close($stmt);
 } else {
    echo '<script>
        alert("Error al registrar");
        window.history.back();
    </script>';
}

mysqli_close($conexion);

?>