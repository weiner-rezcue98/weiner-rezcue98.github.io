<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transparencia";

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consultar o banco de dados para obter o arquivo PDF
    $sql = "SELECT arquivo_pdf, titulo FROM carta_de_servicos_ao_cidadao WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($pdfData, $titulo);
    
    // Verificar se o registro foi encontrado
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        
        // Definir os cabeçalhos para o download do arquivo PDF
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"" . $titulo . ".pdf\"");
        header("Content-Length: " . strlen($pdfData));

        // Garantir que os dados binários sejam enviados corretamente
        echo $pdfData;
    } else {
        echo "Arquivo não encontrado.";
    }

    $stmt->close();
} else {
    echo "ID não fornecido.";
}

$conn->close();
?>
