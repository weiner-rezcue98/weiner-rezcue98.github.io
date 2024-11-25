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

// Lógica para inserção
if (isset($_POST['add'])) {
    $ano = $_POST['ano'];
    $processo = $_POST['processo'];
    $dispensa = $_POST['dispensa'];
    $ratificada = $_POST['ratificada'];
    $valor = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']); // Formata valor para salvar no banco
    $modalidade = $_POST['modalidade'];
    $artigo = $_POST['artigo'];
    $inciso = $_POST['inciso'];
    $cpf_cnpj_fornecedor = $_POST['cpf_cnpj_fornecedor'];
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $unidade = $_POST['unidade'];
    $objeto = $_POST['objeto'];
    $mes = $_POST['mes'];
    $arquivo_pdf = isset($_FILES['arquivo_pdf']) ? file_get_contents($_FILES['arquivo_pdf']['tmp_name']) : null;

    $stmt = $conn->prepare("INSERT INTO compras_inexigibilidade_dispensa 
        (ano, processo, dispensa, ratificada, valor, modalidade, artigo, inciso, cpf_cnpj_fornecedor, nome_fornecedor, unidade, objeto, mes, arquivo_pdf) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdsssssssss", $ano, $processo, $dispensa, $ratificada, $valor, $modalidade, $artigo, $inciso, $cpf_cnpj_fornecedor, $nome_fornecedor, $unidade, $objeto, $mes, $arquivo_pdf);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}

// Lógica para exclusão
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM compras_inexigibilidade_dispensa WHERE id = $id");
    header("Location: index.php");
}

// Lógica para edição
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $ano = $_POST['ano'];
    $processo = $_POST['processo'];
    $dispensa = $_POST['dispensa'];
    $ratificada = $_POST['ratificada'];
    $valor = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']); // Formata valor para salvar no banco
    $modalidade = $_POST['modalidade'];
    $artigo = $_POST['artigo'];
    $inciso = $_POST['inciso'];
    $cpf_cnpj_fornecedor = $_POST['cpf_cnpj_fornecedor'];
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $unidade = $_POST['unidade'];
    $objeto = $_POST['objeto'];
    $mes = $_POST['mes'];
    $arquivo_pdf = isset($_FILES['arquivo_pdf']) ? file_get_contents($_FILES['arquivo_pdf']['tmp_name']) : null;

    if ($arquivo_pdf) {
        $stmt = $conn->prepare("UPDATE compras_inexigibilidade_dispensa 
            SET ano = ?, processo = ?, dispensa = ?, ratificada = ?, valor = ?, modalidade = ?, artigo = ?, inciso = ?, cpf_cnpj_fornecedor = ?, nome_fornecedor = ?, unidade = ?, objeto = ?, mes = ?, arquivo_pdf = ? 
            WHERE id = ?");
        $stmt->bind_param("isssdsssssssssi", $ano, $processo, $dispensa, $ratificada, $valor, $modalidade, $artigo, $inciso, $cpf_cnpj_fornecedor, $nome_fornecedor, $unidade, $objeto, $mes, $arquivo_pdf, $id);
    } else {
        $stmt = $conn->prepare("UPDATE compras_inexigibilidade_dispensa 
            SET ano = ?, processo = ?, dispensa = ?, ratificada = ?, valor = ?, modalidade = ?, artigo = ?, inciso = ?, cpf_cnpj_fornecedor = ?, nome_fornecedor = ?, unidade = ?, objeto = ?, mes = ? 
            WHERE id = ?");
        $stmt->bind_param("isssdssssssssi", $ano, $processo, $dispensa, $ratificada, $valor, $modalidade, $artigo, $inciso, $cpf_cnpj_fornecedor, $nome_fornecedor, $unidade, $objeto, $mes, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}

// Buscar registros
$result = $conn->query("SELECT * FROM compras_inexigibilidade_dispensa");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Compras</title>
    <style>
/* Reset básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    line-height: 1.6;
    padding: 10px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}

/* Formulários */
form {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

form input,
form select,
form textarea {
    margin: 10px 0;
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

form button {
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #0056b3;
}

/* Tabela */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

th,
td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}

tr:hover {
    background-color: #f1f1f1;
}

a {
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
}

a:hover {
    color: #0056b3;
}

/* Responsividade */
@media (max-width: 768px) {
    body {
        padding: 5px;
    }

    form {
        padding: 15px;
    }

    th,
    td {
        padding: 10px;
    }

    table {
        font-size: 0.9rem;
    }

    form button {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    table,
    thead,
    tbody,
    th,
    td,
    tr {
        display: block;
        width: 100%;
    }

    th {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    tr {
        border-bottom: 1px solid #ddd;
        margin-bottom: 15px;
    }

    td {
        border: none;
        position: relative;
        padding-left: 50%;
        text-align: right;
    }

    td:before {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 45%;
        white-space: nowrap;
        font-weight: bold;
        color: #555;
    }

    td:nth-of-type(1):before {
        content: "Ano";
    }

    td:nth-of-type(2):before {
        content: "Processo";
    }

    td:nth-of-type(3):before {
        content: "Valor";
    }

    td:nth-of-type(4):before {
        content: "Fornecedor";
    }

    td:nth-of-type(5):before {
        content: "Objeto";
    }

    td:nth-of-type(6):before {
        content: "Ações";
    }
}

    </style>
</head>
<body>

    <h1>Administração de Compras</h1>

    <!-- Formulário de Inserção -->
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id">
        <input type="number" name="ano" placeholder="Ano" required>
        <input type="text" name="processo" placeholder="Processo" required>
        <input type="datetime-local" name="dispensa" placeholder="Dispensa" required>
        <input type="datetime-local" name="ratificada" placeholder="Ratificada" required>
        <input type="text" name="valor" placeholder="Valor (R$)" required onkeyup="formatarValor(this)">
        <script>
        function formatarValor(campo) {
            let valor = campo.value.replace(/[^\d]/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            campo.value = "R$ " + valor;
        }
        </script>
        <input type="text" name="modalidade" placeholder="Modalidade" required>
        <input type="text" name="artigo" placeholder="Artigo">
        <input type="text" name="inciso" placeholder="Inciso">
        <input type="text" name="cpf_cnpj_fornecedor" placeholder="CPF/CNPJ do Fornecedor" required>
        <input type="text" name="nome_fornecedor" placeholder="Nome do Fornecedor" required>
        <input type="text" name="unidade" placeholder="Unidade" required>
        <textarea name="objeto" placeholder="Objeto" required></textarea>
        <input type="text" name="mes" placeholder="Mês" required>
        <input type="file" name="arquivo_pdf" accept="application/pdf">
        <button type="submit" name="add">Adicionar</button>
    </form>

    <!-- Tabela de Dados -->
    <table>
        <thead>
            <tr>
                <th>Ano</th>
                <th>Processo</th>
                <th>Valor</th>
                <th>Fornecedor</th>
                <th>Objeto</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ano'] ?></td>
                    <td><?= $row['processo'] ?></td>
                    <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                    <td><?= $row['nome_fornecedor'] ?></td>
                    <td><?= $row['objeto'] ?></td>
                    <td>
                        <a href="edit_compras.php?id=<?= $row['id'] ?>">Editar</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>
