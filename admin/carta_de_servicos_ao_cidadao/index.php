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

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter dados do formulário
    $data = $_POST['data'];
    $titulo = $_POST['titulo'];

    // Verificar se um arquivo foi enviado
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] == 0) {
        // Obter o conteúdo do arquivo PDF
        $pdfData = file_get_contents($_FILES['arquivo_pdf']['tmp_name']);

        // Inserir os dados na tabela
        $stmt = $conn->prepare("INSERT INTO carta_de_servicos_ao_cidadao (data, titulo, arquivo_pdf) VALUES (?, ?, ?)");
        $stmt->bind_param("ssb", $data, $titulo, $null);
        $stmt->send_long_data(2, $pdfData); // Enviar o conteúdo do PDF
        $stmt->execute();

        // Mensagem de sucesso
        if ($stmt->affected_rows > 0) {
            echo "Novo registro inserido com sucesso!";
        } else {
            echo "Erro ao inserir dados.";
        }

        $stmt->close();
    } else {
        echo "Erro ao fazer upload do arquivo PDF.";
    }
}

// Consulta SQL para exibir os dados da tabela
$sql = "SELECT * FROM carta_de_servicos_ao_cidadao";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Carta de Serviços ao Cidadão</title>
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

        .action-links {
            color: #007bff;
        }

        .action-links:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Formulário de Inserção -->
    <div class="form-container">
        <h3>Adicionar Carta de Serviços ao Cidadão</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="arquivo_pdf">Arquivo PDF:</label>
            <input type="file" id="arquivo_pdf" name="arquivo_pdf" accept="application/pdf" required>

            <button type="submit">Salvar</button>
        </form>
    </div>

    <!-- Tabela de Dados -->
    <div class="table-container">
        <h3>Lista de Cartas de Serviços ao Cidadão</h3>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Título</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                        echo "<td>" . $row['titulo'] . "</td>";
                        echo "<td><a class='action-links' href='?edit=" . $row['id'] . "'>Editar</a> | 
                                  <a class='action-links' href='delete.php?id=" . $row['id'] . "'>Excluir</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhuma carta cadastrada.</td></tr>";
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
