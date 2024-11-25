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

// Verifica se um ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID inválido ou não fornecido.");
}

$id = $_GET['id'];

// Busca o registro pelo ID
$stmt = $conn->prepare("SELECT * FROM compras_inexigibilidade_dispensa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Registro não encontrado.");
}

$registro = $result->fetch_assoc();
$stmt->close();

// Lógica para salvar a edição
if (isset($_POST['update'])) {
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

    // Lógica para tratar o upload de arquivo PDF
    $arquivo_pdf = null; // Valor padrão
    if (isset($_FILES['arquivo_pdf']) && !empty($_FILES['arquivo_pdf']['tmp_name'])) {
        $arquivo_pdf = file_get_contents($_FILES['arquivo_pdf']['tmp_name']);
    }

    // Atualizar o registro com ou sem PDF
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        form {
            margin: 20px auto;
            width: 50%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form input, form select, form textarea {
            margin: 10px 0;
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Editar Compra</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $registro['id'] ?>">
        <input type="number" name="ano" placeholder="Ano" value="<?= $registro['ano'] ?>" required>
        <input type="text" name="processo" placeholder="Processo" value="<?= $registro['processo'] ?>" required>
        <input type="datetime-local" name="dispensa" placeholder="Dispensa" value="<?= $registro['dispensa'] ?>" required>
        <input type="datetime-local" name="ratificada" placeholder="Ratificada" value="<?= $registro['ratificada'] ?>" required>
        <input type="text" name="valor" placeholder="Valor (R$)" value="R$ <?= number_format($registro['valor'], 2, ',', '.') ?>" required onkeyup="formatarValor(this)">
        <script>
        function formatarValor(campo) {
            let valor = campo.value.replace(/[^\d]/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            campo.value = "R$ " + valor;
        }
        </script>
        <input type="text" name="modalidade" placeholder="Modalidade" value="<?= $registro['modalidade'] ?>" required>
        <input type="text" name="artigo" placeholder="Artigo" value="<?= $registro['artigo'] ?>">
        <input type="text" name="inciso" placeholder="Inciso" value="<?= $registro['inciso'] ?>">
        <input type="text" name="cpf_cnpj_fornecedor" placeholder="CPF/CNPJ do Fornecedor" value="<?= $registro['cpf_cnpj_fornecedor'] ?>" required>
        <input type="text" name="nome_fornecedor" placeholder="Nome do Fornecedor" value="<?= $registro['nome_fornecedor'] ?>" required>
        <input type="text" name="unidade" placeholder="Unidade" value="<?= $registro['unidade'] ?>" required>
        <textarea name="objeto" placeholder="Objeto" required><?= $registro['objeto'] ?></textarea>
        <input type="text" name="mes" placeholder="Mês" value="<?= $registro['mes'] ?>" required>
        <input type="file" name="arquivo_pdf" accept="application/pdf">
        <button type="submit" name="update">Atualizar</button>
    </form>
</body>
</html>
<?php
$conn->close();
?>
