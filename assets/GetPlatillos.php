<?php
$servername = "localhost"; 
$username = "root"; 
$password = "0612"; 
$database = "restaurante";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM platillos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $platillos = array();
    while($row = $result->fetch_assoc()) {
        $platillos[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($platillos);
} else {
    echo "No hay platillos disponibles.";
}

$conn->close();
?>
