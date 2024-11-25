<?php
// Conectar ao banco de dados
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
    $stmt = $conn->prepare("SELECT titulo, arquivo_pdf FROM beneficios_fiscais WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($titulo, $arquivo_pdf);
    $stmt->fetch();

    if ($arquivo_pdf) {
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$titulo.pdf\"");
        echo $arquivo_pdf;
    } else {
        echo "Arquivo não encontrado.";
    }

    $stmt->close();
}

$conn->close();
?>
