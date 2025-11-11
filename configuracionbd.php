<?php
$host = "localhost";   
$user = "root";       
$pass = "";            
$db   = "parque_vehicular"; 

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_errno){
    die("Error de conexión:hola soy yo" . $conn->connect_errno);
}
?>