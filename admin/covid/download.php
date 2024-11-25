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

// Verifica se o ID foi passado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta SQL para pegar o caminho do arquivo
    $sql = "SELECT anexo FROM Covid19 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($anexo);

    if ($stmt->fetch()) {
        // Verifica se o arquivo existe
        if (file_exists($anexo)) {
            // Definir cabeçalhos para forçar o download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($anexo) . '"');
            header('Content-Length: ' . filesize($anexo));
            header('Cache-Control: no-cache, no-store, must-revalidate'); // Não cachear o arquivo
            header('Pragma: no-cache'); // Para navegadores antigos
            header('Expires: 0'); // O conteúdo expira imediatamente

            // Limpa o buffer de saída
            ob_clean();
            flush();

            // Ler e enviar o conteúdo do arquivo PDF para o navegador
            readfile($anexo);
            exit;  // Garantir que nada mais será enviado após o arquivo
        } else {
            echo "Arquivo não encontrado.";
        }
    } else {
        echo "Nenhum arquivo encontrado para o ID fornecido.";
    }

    $stmt->close();
} else {
    echo "ID não especificado.";
}

$conn->close();
?>
