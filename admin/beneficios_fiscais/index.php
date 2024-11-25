<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transparencia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Excluir dados (se o ID for fornecido via GET)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepara a consulta SQL para excluir o registro
    $stmt = $conn->prepare("DELETE FROM beneficios_fiscais WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "Registro excluído com sucesso!";
    } else {
        echo "Erro ao excluir o registro: " . $stmt->error;
    }

    // Fecha a declaração
    $stmt->close();

    // Redireciona de volta para a página (para evitar reenvio de formulário ao atualizar a página)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Inserir dados (se enviado via POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $data = $_POST['data'];

    // Verifica se o arquivo foi enviado corretamente
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] == 0) {
        
        // Lê o conteúdo do arquivo PDF
        $pdfData = file_get_contents($_FILES['arquivo_pdf']['tmp_name']);

        // Verifica se o arquivo PDF foi lido corretamente
        if ($pdfData === false) {
            echo "Erro ao ler o arquivo PDF.";
            exit;
        }

        // Prepara a consulta SQL para inserir os dados no banco de dados
        $stmt = $conn->prepare("INSERT INTO beneficios_fiscais (titulo, data, arquivo_pdf) VALUES (?, ?, ?)");

        // Verifica se a preparação da consulta foi bem-sucedida
        if ($stmt === false) {
            echo "Erro ao preparar a consulta: " . $conn->error;
            exit;
        }

        // Associa os parâmetros e executa a consulta
        $null = NULL;
        $stmt->bind_param("ssb", $titulo, $data, $null);
        $stmt->send_long_data(2, $pdfData); // Envia os dados do PDF para o parâmetro 'arquivo_pdf'

        // Executa a consulta
        if ($stmt->execute()) {
            echo "Registro inserido com sucesso!";
        } else {
            echo "Erro ao inserir no banco de dados: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();
    } else {
        echo "Erro ao carregar o arquivo PDF. Código do erro: " . $_FILES['arquivo_pdf']['error'];
    }
}

// Consulta SQL para exibir os dados da tabela benefícios fiscais
$sql = "SELECT id, titulo, data FROM beneficios_fiscais";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefícios Fiscais</title>
    <style>
        body {
            font-family: Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .form-container {
            margin: 20px auto;
            width: 50%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin: 10px 0 5px;
        }

        form input, form button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .table-container {
            margin: 20px auto;
            width: 90%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .delete-btn {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Formulário de Inserção -->
    <div class="form-container">
        <h3>Adicionar Benefício Fiscal</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>

            <label for="arquivo_pdf">Arquivo PDF:</label>
            <input type="file" id="arquivo_pdf" name="arquivo_pdf" accept="application/pdf" required>

            <button type="submit">Adicionar</button>
        </form>
    </div>

    <!-- Tabela de Dados -->
    <div class="table-container">
        <h3>Lista de Benefícios Fiscais</h3>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['titulo']) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                        echo "<td>
                                  <a href='download.php?id=" . $row['id'] . "'>Download PDF</a> | 
                                  <a href='?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>Excluir</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum benefício cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
