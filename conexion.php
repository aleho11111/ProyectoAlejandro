<?php
$host = "localhost";        
$usuario = "root";          
$contraseña = "";          
$baseDeDatos = "Plataforma"; 


$conn = new mysqli($host, $usuario, $contraseña, $baseDeDatos);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>