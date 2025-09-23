<?php
$host = "localhost";   
$user = "root";       
$pass = "";            
$db   = "parque_veicular"; 

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){
    die("Error de conexión:hola soy yo" . $conn->connect_error);
}
?>