<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transparencia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o ID foi passado via URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Consulta para buscar o documento (arquivo PDF)
    $sql = "SELECT documentacao FROM api_de_dados WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    // Verificar se o arquivo foi encontrado
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($documentacao);
        $stmt->fetch();

        // Definir os cabeçalhos para download do arquivo PDF
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=documentacao.pdf");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . strlen($documentacao));

        // Evitar que qualquer outro conteúdo seja enviado
        ob_clean();
        flush();

        // Exibir o conteúdo do PDF
        echo $documentacao;
    } else {
        echo "Arquivo não encontrado!";
    }

    $stmt->close();
} else {
    echo "ID inválido!";
}

$conn->close();
?>
