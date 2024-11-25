<?php
// Conectar ao banco de dados (substitua com suas credenciais)
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

// Consulta SQL para exibir os dados da tabela
$sql = "SELECT * FROM acompanhamento_do_orcamento";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento do Orçamento (Saldos)</title>
    <style>
        body {
            font-family: Verdana, sans-serif; 
            background-color: #f4f6f9;
            color: #333;
            font-size: 14px; 
        }

        h2 {
            color: #333;
            text-align: center;
            font-size: 18px; 
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            width: 50%;
            padding: 8px;
            font-size: 14px; 
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .export-container {
            text-align: center;
            margin: 20px 0;
        }

        .export-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
        }

        .export-container button:hover {
            background-color: #0056b3;
        }

        .table-container {
            margin: 20px auto;
            width: 95%;
            border-collapse: collapse;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px; 
            text-align: left;
            font-size: 14px; 
        }

        th {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h2>Acompanhamento do Orçamento (Saldos)</h2>

    <!-- Barra de Pesquisa -->
    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Pesquisar na tabela...">
    </div>

    <!-- Botão de Exportar PDF -->
    <div class="export-container">
        <button onclick="window.print()">Exportar como PDF</button>
    </div>

    <!-- Exibição da tabela -->
    <div class="table-container">
        <table id="orcamentoTable">
            <thead>
                <tr>
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
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['ano'] . "</td>";
                        echo "<td>" . $row['mes'] . "</td>";
                        echo "<td>" . $row['orgao'] . "</td>";
                        echo "<td>" . $row['secretaria'] . "</td>";
                        echo "<td>" . $row['unidade'] . "</td>";
                        echo "<td>" . $row['acao'] . "</td>";
                        echo "<td>" . $row['elemento_despesa'] . "</td>";
                        echo "<td>" . $row['fonte_recurso'] . "</td>";
                        echo "<td>R$ " . number_format($row['inicial'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['suplementado'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['anulado'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['bloqueado'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['empenhado_ano'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['liquidado_ano'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['pago_ano'], 2, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($row['saldo_atual'], 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='16'>Nenhum dado encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("orcamentoTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }

        // Detectar Ctrl + P e ativar impressão
        document.addEventListener("keydown", function(event) {
            if (event.ctrlKey && event.key === "p") {
                event.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
