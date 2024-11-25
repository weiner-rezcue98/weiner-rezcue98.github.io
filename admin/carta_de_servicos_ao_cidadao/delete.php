<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transparencia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM carta_de_servicos_ao_cidadao WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
}

$conn->close();
?>
