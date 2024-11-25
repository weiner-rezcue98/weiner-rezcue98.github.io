<?php
// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transparencia";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para converter valores para o formato do banco (de "5.000,00" para "5000.00")
function formatToDatabase($value) {
    $value = str_replace('.', '', $value); // Remove os pontos de milhar
    $value = str_replace(',', '.', $value); // Substitui a vírgula decimal por ponto
    return floatval($value);
}

// Função para converter valores do banco para o formato brasileiro (de "5000.00" para "5.000,00")
function formatToBrazilian($value) {
    return number_format($value, 2, ',', '.');
}

// Adicionar registro
if (isset($_POST['add'])) {
    $ano = $_POST['ano'];
    $mes = $_POST['mes'];
    $orgao = $_POST['orgao'];
    $secretaria = $_POST['secretaria'];
    $unidade = $_POST['unidade'];
    $acao = $_POST['acao'];
    $elemento_despesa = $_POST['elemento_despesa'];
    $fonte_recurso = $_POST['fonte_recurso'];
    $inicial = formatToDatabase($_POST['inicial']);
    $suplementado = formatToDatabase($_POST['suplementado']);
    $anulado = formatToDatabase($_POST['anulado']);
    $bloqueado = formatToDatabase($_POST['bloqueado']);
    $empenhado_ano = formatToDatabase($_POST['empenhado_ano']);
    $liquidado_ano = formatToDatabase($_POST['liquidado_ano']);
    $pago_ano = formatToDatabase($_POST['pago_ano']);
    $saldo_atual = formatToDatabase($_POST['saldo_atual']);

    $sql = "INSERT INTO acompanhamento_do_orcamento (ano, mes, orgao, secretaria, unidade, acao, elemento_despesa, fonte_recurso, inicial, suplementado, anulado, bloqueado, empenhado_ano, liquidado_ano, pago_ano, saldo_atual) 
            VALUES ('$ano', '$mes', '$orgao', '$secretaria', '$unidade', '$acao', '$elemento_despesa', '$fonte_recurso', '$inicial', '$suplementado', '$anulado', '$bloqueado', '$empenhado_ano', '$liquidado_ano', '$pago_ano', '$saldo_atual')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registro adicionado com sucesso!');</script>";
    } else {
        echo "Erro ao adicionar: " . $conn->error;
    }
}

// Editar registro
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $ano = $_POST['ano'];
    $mes = $_POST['mes'];
    $orgao = $_POST['orgao'];
    $secretaria = $_POST['secretaria'];
    $unidade = $_POST['unidade'];
    $acao = $_POST['acao'];
    $elemento_despesa = $_POST['elemento_despesa'];
    $fonte_recurso = $_POST['fonte_recurso'];
    $inicial = formatToDatabase($_POST['inicial']);
    $suplementado = formatToDatabase($_POST['suplementado']);
    $anulado = formatToDatabase($_POST['anulado']);
    $bloqueado = formatToDatabase($_POST['bloqueado']);
    $empenhado_ano = formatToDatabase($_POST['empenhado_ano']);
    $liquidado_ano = formatToDatabase($_POST['liquidado_ano']);
    $pago_ano = formatToDatabase($_POST['pago_ano']);
    $saldo_atual = formatToDatabase($_POST['saldo_atual']);

    $sql = "UPDATE acompanhamento_do_orcamento SET ano='$ano', mes='$mes', orgao='$orgao', secretaria='$secretaria', unidade='$unidade', acao='$acao', elemento_despesa='$elemento_despesa', fonte_recurso='$fonte_recurso', inicial='$inicial', suplementado='$suplementado', anulado='$anulado', bloqueado='$bloqueado', empenhado_ano='$empenhado_ano', liquidado_ano='$liquidado_ano', pago_ano='$pago_ano', saldo_atual='$saldo_atual' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registro atualizado com sucesso!');</script>";
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}

// Excluir registro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM acompanhamento_do_orcamento WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registro excluído com sucesso!');</script>";
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
}

// Consulta registros
$sql = "SELECT * FROM acompanhamento_do_orcamento";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Acompanhamento do Orçamento</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        color: #333;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
        margin: 20px 0;
        color: #007bff;
    }

    form {
        width: 80%;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    form input, form button {
        display: block;
        width: calc(100% - 20px);
        margin: 10px auto;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
    }

    form input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    form button {
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    form button:hover {
        background-color: #0056b3;
    }

    table {
        width: 90%;
        margin: 20px auto;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
        font-size: 14px;
    }

    th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #e9ecef;
    }

    td a {
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
    }

    td a:hover {
        text-decoration: underline;
    }

    .delete-link {
        color: red;
    }

    .delete-link:hover {
        color: darkred;
    }

    @media (max-width: 768px) {
        form, table {
            width: 95%;
        }

        form input, form button {
            font-size: 12px;
        }

        th, td {
            font-size: 12px;
        }
    }
</style>

</head>
<body>
    <h2>Administração - Acompanhamento do Orçamento</h2>

    <!-- Formulário -->
    <form method="POST">
        <input type="hidden" name="id" id="id">
        <input type="number" name="ano" id="ano" placeholder="Ano" required>
        <input type="text" name="mes" id="mes" placeholder="Mês" required>
        <input type="text" name="orgao" id="orgao" placeholder="Órgão" required>
        <input type="text" name="secretaria" id="secretaria" placeholder="Secretaria" required>
        <input type="text" name="unidade" id="unidade" placeholder="Unidade" required>
        <input type="text" name="acao" id="acao" placeholder="Ação" required>
        <input type="text" name="elemento_despesa" id="elemento_despesa" placeholder="Elemento de Despesa" required>
        <input type="text" name="fonte_recurso" id="fonte_recurso" placeholder="Fonte de Recurso" required>
        <input type="text" name="inicial" id="inicial" placeholder="Inicial (ex: 5.000,00)" required>
        <input type="text" name="suplementado" id="suplementado" placeholder="Suplementado (ex: 1.000,00)" required>
        <input type="text" name="anulado" id="anulado" placeholder="Anulado (ex: 500,00)" required>
        <input type="text" name="bloqueado" id="bloqueado" placeholder="Bloqueado (ex: 200,00)" required>
        <input type="text" name="empenhado_ano" id="empenhado_ano" placeholder="Empenhado Ano (ex: 2.000,00)" required>
        <input type="text" name="liquidado_ano" id="liquidado_ano" placeholder="Liquidado Ano (ex: 1.500,00)" required>
        <input type="text" name="pago_ano" id="pago_ano" placeholder="Pago Ano (ex: 1.200,00)" required>
        <input type="text" name="saldo_atual" id="saldo_atual" placeholder="Saldo Atual (ex: 3.000,00)" required>
        <button type="submit" name="add">Adicionar</button>
        <button type="submit" name="edit">Editar</button>
    </form>

    <!-- Tabela -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ano</th>
                <th>Mês</th>
                <th>Órgão</th>
                <th>Secretaria</th>
                <th>Unidade</th>
                <th>Ação</th>
                <th>Elemento de Despesa</th>
                <th>Fonte Recurso</th>
                <th>Inicial</th>
                <th>Suplementado</th>
                <th>Anulado</th>
                <th>Bloqueado</th>
                <th>Empenhado Ano</th>
                <th>Liquidado Ano</th>
                <th>Pago Ano</th>
                <th>Saldo Atual</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['ano'] . "</td>";
                    echo "<td>" . $row['mes'] . "</td>";
                    echo "<td>" . $row['orgao'] . "</td>";
                    echo "<td>" . $row['secretaria'] . "</td>";
                    echo "<td>" . $row['unidade'] . "</td>";
                    echo "<td>" . $row['acao'] . "</td>";
                    echo "<td>" . $row['elemento_despesa'] . "</td>";
                    echo "<td>" . $row['fonte_recurso'] . "</td>";
                    echo "<td>" . formatToBrazilian($row['inicial']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['suplementado']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['anulado']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['bloqueado']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['empenhado_ano']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['liquidado_ano']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['pago_ano']) . "</td>";
                    echo "<td>" . formatToBrazilian($row['saldo_atual']) . "</td>";
                    echo "<td>
                        <a href='?delete=" . $row['id'] . "' onclick=\"return confirm('Tem certeza que deseja excluir?')\">Excluir</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='17'>Nenhum dado encontrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
