<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transparencia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o ID foi passado
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Busca o arquivo no banco de dados
    $stmt = $conn->prepare("SELECT arquivo_pdf FROM compras_inexigibilidade_dispensa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($arquivo_pdf);
    $stmt->fetch();
    $stmt->close();

    // Verifica se o arquivo existe
    if ($arquivo_pdf) {
        // Define os headers para download
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=arquivo_$id.pdf");
        echo $arquivo_pdf;
    } else {
        echo "Arquivo não encontrado.";
    }
} else {
    echo "ID inválido.";
}

$conn->close();
?>
