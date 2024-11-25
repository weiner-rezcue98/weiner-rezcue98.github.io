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

// Manipulação de inserção de dados
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['edit_id'])) {
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
    $ano = isset($_POST['ano']) ? $_POST['ano'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $bairro = isset($_POST['bairro']) ? $_POST['bairro'] : '';
    $solicitacao = isset($_POST['solicitacao']) ? $_POST['solicitacao'] : '';
    $atendimento = isset($_POST['atendimento']) ? $_POST['atendimento'] : '';
    $unidade = isset($_POST['unidade']) ? $_POST['unidade'] : '';
    $ultima_atualizacao = date("Y-m-d H:i:s");

    // Manipulação do upload do arquivo
    $anexo_path = "";
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $uploadFile = $uploadDir . basename($_FILES['anexo']['name']);

        if (strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION)) === 'pdf') {
            if (move_uploaded_file($_FILES['anexo']['tmp_name'], $uploadFile)) {
                $anexo_path = $uploadFile;
            } else {
                echo "Erro ao mover o arquivo.";
                exit;
            }
        } else {
            echo "Somente arquivos PDF são permitidos.";
            exit;
        }
    }

    // Inserção no banco de dados
    $stmt = $conn->prepare("INSERT INTO Covid19 (data, descricao, ano, tipo, bairro, solicitacao, atendimento, unidade, ultima_atualizacao, anexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssssss", $data, $descricao, $ano, $tipo, $bairro, $solicitacao, $atendimento, $unidade, $ultima_atualizacao, $anexo_path);

    if ($stmt->execute()) {
        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados: " . $stmt->error;
    }

    $stmt->close();
}

// Manipulação de edição de dados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
    $ano = isset($_POST['ano']) ? $_POST['ano'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $bairro = isset($_POST['bairro']) ? $_POST['bairro'] : '';
    $solicitacao = isset($_POST['solicitacao']) ? $_POST['solicitacao'] : '';
    $atendimento = isset($_POST['atendimento']) ? $_POST['atendimento'] : '';
    $unidade = isset($_POST['unidade']) ? $_POST['unidade'] : '';
    $ultima_atualizacao = date("Y-m-d H:i:s");

    // Manipulação do upload do arquivo
    $anexo_path = "";
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $uploadFile = $uploadDir . basename($_FILES['anexo']['name']);

        if (strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION)) === 'pdf') {
            if (move_uploaded_file($_FILES['anexo']['tmp_name'], $uploadFile)) {
                $anexo_path = $uploadFile;
            } else {
                echo "Erro ao mover o arquivo.";
                exit;
            }
        } else {
            echo "Somente arquivos PDF são permitidos.";
            exit;
        }
    }

    // Atualizar dados no banco de dados
    $stmt = $conn->prepare("UPDATE Covid19 SET data = ?, descricao = ?, ano = ?, tipo = ?, bairro = ?, solicitacao = ?, atendimento = ?, unidade = ?, ultima_atualizacao = ?, anexo = ? WHERE id = ?");
    $stmt->bind_param("ssisssssssi", $data, $descricao, $ano, $tipo, $bairro, $solicitacao, $atendimento, $unidade, $ultima_atualizacao, $anexo_path, $id);

    if ($stmt->execute()) {
        echo "Dados atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar dados: " . $stmt->error;
    }

    $stmt->close();
}

// Manipulação de exclusão de dados
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Covid19 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Registro excluído com sucesso!";
    } else {
        echo "Erro ao excluir registro: " . $stmt->error;
    }

    $stmt->close();
}

// Exibir dados
$sql = "SELECT * FROM Covid19";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Dados Covid19</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .action-buttons button:hover {
            background-color: #218838;
        }

        .action-buttons a {
            padding: 5px 10px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .action-buttons a:hover {
            background-color: #c82333;
        }
        /* Container do formulário */
form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Rótulos */
form label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

/* Campos de entrada */
form input[type="date"],
form input[type="number"],
form input[type="text"],
form textarea,
form input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

/* Textarea */
form textarea {
    height: 80px;
    resize: vertical;
}

/* Botão */
form button {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    font-weight: bold;
    color: #ffffff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #0056b3;
}

/* Hover nos campos */
form input:focus,
form textarea:focus,
form input[type="file"]:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
    </style>
</head>
<body>
<!-- Formulário de Inserção/Atualização -->
<center><h3>Adicionar/Atualizar Dados</h3></center>
    <form action="" method="POST" enctype="multipart/form-data">
        <?php
        if (isset($_GET['edit_id'])) {
            $edit_id = $_GET['edit_id'];
            $edit_sql = "SELECT * FROM Covid19 WHERE id = $edit_id";
            $edit_result = $conn->query($edit_sql);
            $edit_row = $edit_result->fetch_assoc();
        ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_row['id']; ?>">
        <?php } ?>

        <label for="data">Data:</label>
        <input type="date" id="data" name="data" value="<?php echo isset($edit_row) ? $edit_row['data'] : ''; ?>" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required><?php echo isset($edit_row) ? $edit_row['descricao'] : ''; ?></textarea>

        <label for="ano">Ano:</label>
        <input type="number" id="ano" name="ano" value="<?php echo isset($edit_row) ? $edit_row['ano'] : ''; ?>" required>

        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo" value="<?php echo isset($edit_row) ? $edit_row['tipo'] : ''; ?>" required>

        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" value="<?php echo isset($edit_row) ? $edit_row['bairro'] : ''; ?>" required>

        <label for="solicitacao">Data de Solicitação:</label>
        <input type="date" id="solicitacao" name="solicitacao" value="<?php echo isset($edit_row) ? $edit_row['solicitacao'] : ''; ?>" required>

        <label for="atendimento">Data de Atendimento:</label>
        <input type="date" id="atendimento" name="atendimento" value="<?php echo isset($edit_row) ? $edit_row['atendimento'] : ''; ?>" required>

        <label for="unidade">Unidade:</label>
        <input type="text" id="unidade" name="unidade" value="<?php echo isset($edit_row) ? $edit_row['unidade'] : ''; ?>" required>

        <label for="anexo">Anexo (PDF):</label>
        <input type="file" id="anexo" name="anexo" accept="application/pdf">

        <button type="submit"><?php echo isset($edit_row) ? 'Atualizar Dados' : 'Inserir Dados'; ?></button>
    </form>





    <h2>Gerenciar Dados Covid19</h2>

    <!-- Tabela de Dados -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Descrição</th>
                <th>Ano</th>
                <th>Tipo</th>
                <th>Bairro</th>
                <th>Solicitação</th>
                <th>Atendimento</th>
                <th>Unidade</th>
                <th>Última Atualização</th>
                <th>Anexo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                    echo "<td>" . $row['descricao'] . "</td>";
                    echo "<td>" . $row['ano'] . "</td>";
                    echo "<td>" . $row['tipo'] . "</td>";
                    echo "<td>" . $row['bairro'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['solicitacao'])) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['atendimento'])) . "</td>";
                    echo "<td>" . $row['unidade'] . "</td>";
                    echo "<td>" . date('d/m/Y H:i:s', strtotime($row['ultima_atualizacao'])) . "</td>";
                    echo "<td><a href='" . $row['anexo'] . "' target='_blank'>Visualizar</a></td>";
                    echo "<td class='action-buttons'>";
                    
                    echo "<a href='?delete_id=" . $row['id'] . "'>Excluir</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>

    
</body>
</html>

<?php
$conn->close();
?>
