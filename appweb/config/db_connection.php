<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "jatco_copia";

    date_default_timezone_set('America/Mexico_City');
    
    try {
        $conn = new mysqli($host, $user, $password, $database);
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }
    } catch (Exception $e) {
        die("Excepción al conectar a la base de datos: " . $e->getMessage());
    }
?>

