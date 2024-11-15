<?php
$servername = "localhost";   // Servidor MariaDB
$username = "root";          // Usuario root
$password = "";              // Generalmente vacío en XAMPP por defecto
$dbname = "rx_huilalaplata"; // El nombre de tu base de datos

// Crear conexión usando mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Conexión exitosa, puedes continuarr
?>
