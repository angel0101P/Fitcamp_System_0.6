<?php

/*archivo de conexion al servidor y a la bd, NO TOCAR */

 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "usuarios_general";

 $conexion = new mysqli ($servername, $username, $password, $dbname);

 if ($conexion->connect_error){
    
    die ("la conexion ha fallado: ".$conexion->connect_error);


  }


?>