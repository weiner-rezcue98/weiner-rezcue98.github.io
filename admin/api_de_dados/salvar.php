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

// Receber dados do formulário
$id = $_POST['id'] ?? 0;
$descricao = $_POST['descricao'] ?? '';
$servico = $_POST['servico'] ?? '';
$end_point = $_POST['end_point'] ?? '';
$parametro_entrada = $_POST['parametro_entrada'] ?? '';
$resultado_erro = $_POST['resultado_erro'] ?? '';
$resultado_esperado = $_POST['resultado_esperado'] ?? '';
$ultima_atualizacao = $_POST['ultima_atualizacao'] ?? date('Y-m-d');

// Atualizar ou inserir
if ($id) {
    $sql = "UPDATE api_de_dados SET descricao=?, servico=?, end_point=?, parametro_entrada=?, resultado_erro=?, resultado_esperado=?, ultima_atualizacao=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $descricao, $servico, $end_point, $parametro_entrada, $resultado_erro, $resultado_esperado, $ultima_atualizacao, $id);
} else {
    $sql = "INSERT INTO api_de_dados (descricao, servico, end_point, parametro_entrada, resultado_erro, resultado_esperado, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $descricao, $servico, $end_point, $parametro_entrada, $resultado_erro, $resultado_esperado, $ultima_atualizacao);
}
$stmt->execute();

header("Location: index.php");
exit;
?>
