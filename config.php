<?php
// Configuración de conexión a MySQL de AlwaysData
$host = "jojoapp.alwaysdata.net";
$user = "jojoapp";
$pass = "3108787231Jc.";
$db   = "jojoapp_enviosdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Crear la tabla automáticamente si no existe.
$sql = "CREATE TABLE IF NOT EXISTS envios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destinatario VARCHAR(150) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("No se pudo crear/verificar la tabla envios: " . $conn->error);
}
?>