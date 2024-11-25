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

// Variáveis iniciais
$id = 0;
$descricao = $servico = $end_point = $parametro_entrada = $resultado_erro = $resultado_esperado = "";
$documentacao = null;
$ultima_atualizacao = date('Y-m-d');

// Função para inserir ou atualizar dados
if (isset($_POST['submit'])) {
    $descricao = $_POST['descricao'];
    $servico = $_POST['servico'];
    $end_point = $_POST['end_point'];
    $parametro_entrada = $_POST['parametro_entrada'];
    $resultado_erro = $_POST['resultado_erro'];
    $resultado_esperado = $_POST['resultado_esperado'];
    $ultima_atualizacao = $_POST['ultima_atualizacao'];

    // Verifica se há um arquivo PDF enviado
    if (isset($_FILES['documentacao']) && $_FILES['documentacao']['error'] == 0) {
        $documentacao = file_get_contents($_FILES['documentacao']['tmp_name']); // Obtém o conteúdo binário do PDF
    }

    // Verifica se é um novo cadastro ou edição
    if ($id == 0) {
        // Inserir novo registro
        $stmt = $conn->prepare("INSERT INTO api_de_dados (descricao, servico, end_point, parametro_entrada, resultado_erro, resultado_esperado, documentacao, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $descricao, $servico, $end_point, $parametro_entrada, $resultado_erro, $resultado_esperado, $documentacao, $ultima_atualizacao);
    } else {
        // Atualizar um registro existente
        $stmt = $conn->prepare("UPDATE api_de_dados SET descricao = ?, servico = ?, end_point = ?, parametro_entrada = ?, resultado_erro = ?, resultado_esperado = ?, documentacao = ?, ultima_atualizacao = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $descricao, $servico, $end_point, $parametro_entrada, $resultado_erro, $resultado_esperado, $documentacao, $ultima_atualizacao, $id);
    }

    // Executa a consulta
    if ($stmt->execute()) {
        echo "Dados salvos com sucesso!";
    } else {
        echo "Erro ao salvar dados: " . $stmt->error;
    }
    $stmt->close();
}

// Função para excluir um registro
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM api_de_dados WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}

// Função para carregar dados para edição
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM api_de_dados WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $descricao = $row['descricao'];
        $servico = $row['servico'];
        $end_point = $row['end_point'];
        $parametro_entrada = $row['parametro_entrada'];
        $resultado_erro = $row['resultado_erro'];
        $resultado_esperado = $row['resultado_esperado'];
        $documentacao = $row['documentacao']; // Conteúdo binário do PDF
        $ultima_atualizacao = $row['ultima_atualizacao'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API de Dados - Administração</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fa;
    margin: 0;
    padding: 20px;
    color: #333;
}

h2, h3 {
    color: #007bff;
    text-align: center;
}

form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 60%;
    margin: 20px auto;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

textarea, input[type="text"], input[type="date"], input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    margin-top: 10px;
}

button:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #007bff;
    color: white;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

table td a {
    margin: 0 5px;
}

    </style>
</head>
<body>

    <h2><?php echo ($id == 0) ? 'Inserir Novo Registro' : 'Editar Registro'; ?></h2>
    
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label for="descricao">Descrição</label><br>
        <textarea name="descricao" required><?php echo $descricao; ?></textarea><br><br>

        <label for="servico">Serviço</label><br>
        <input type="text" name="servico" value="<?php echo $servico; ?>" required><br><br>

        <label for="end_point">End Point</label><br>
        <input type="text" name="end_point" value="<?php echo $end_point; ?>" required><br><br>

        <label for="parametro_entrada">Parâmetro de Entrada</label><br>
        <textarea name="parametro_entrada" required><?php echo $parametro_entrada; ?></textarea><br><br>

        <label for="resultado_erro">Resultado de Erro</label><br>
        <textarea name="resultado_erro" required><?php echo $resultado_erro; ?></textarea><br><br>

        <label for="resultado_esperado">Resultado Esperado</label><br>
        <textarea name="resultado_esperado" required><?php echo $resultado_esperado; ?></textarea><br><br>

        <label for="documentacao">Documentação (PDF)</label><br>
        <input type="file" name="documentacao" accept="application/pdf"><br><br>

        <label for="ultima_atualizacao">Última Atualização</label><br>
        <input type="date" name="ultima_atualizacao" value="<?php echo $ultima_atualizacao; ?>" required><br><br>

        <button type="submit" name="submit"><?php echo ($id == 0) ? 'Inserir' : 'Atualizar'; ?></button>
    </form>

    <h3>Registros</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Serviço</th>
                <th>End Point</th>
                <th>Última Atualização</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM api_de_dados";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['descricao'] . "</td>";
                    echo "<td>" . $row['servico'] . "</td>";
                    echo "<td>" . $row['end_point'] . "</td>";
                    echo "<td>" . $row['ultima_atualizacao'] . "</td>";
                    echo "<td><a href='index.php?id=" . $row['id'] . "'>Editar</a> | <a href='index.php?delete=" . $row['id'] . "'>Excluir</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum registro encontrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>
