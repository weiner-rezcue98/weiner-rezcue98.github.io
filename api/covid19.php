<?php
// Conectar ao banco de dados (substitua com suas credenciais)
$servername = "localhost"; // Host do banco de dados
$username = "root"; // Usuário do banco de dados
$password = ""; // Senha do banco de dados
$dbname = "transparencia"; // Nome do banco de dados

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta SQL para exibir os dados da tabela Covid19
$sql = "SELECT * FROM Covid19";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Covid-19 | Município de Modelo</title>
    <style>
        body {
            font-family: Verdana, sans-serif; /* Fonte ajustada */
            background-color: #f4f6f9;
            color: #333;
            font-size: 14px; /* Diminui o tamanho da fonte */
        }

        h2 {
            color: #333;
            text-align: center;
            font-size: 18px; /* Tamanho ajustado */
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            width: 50%;
            padding: 8px;
            font-size: 14px; /* Tamanho ajustado */
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
            width: 90%;
            border-collapse: collapse;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px; /* Diminui o padding */
            text-align: left;
            font-size: 14px; /* Diminui o tamanho da fonte */
        }

        th {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        th.sortable:after {
            content: " ▼";
        }

        th.asc:after {
            content: " ▲";
        }

        th.desc:after {
            content: " ▼";
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

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
        <table id="covidTable">
            <thead>
                <tr>
                    <th class="sortable" onclick="sortTable(0)">Data</th> <!-- FILTRO DE DATA AQUI -->
                    <th>Descrição</th>
                    <th class="sortable" onclick="sortTable(2)">Ano</th> <!-- FILTRO DE ANO -->
                    <th>Tipo</th>
                    <th>Bairro</th>
                    <th>Solicitação</th>
                    <th>Atendimento</th>
                    <th>Unidade</th>
                    <th class="sortable" onclick="sortTable(8)">Última Atualização</th> <!-- FILTRO DE DATA AQUI -->
                    <th>Anexo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                        echo "<td>" . $row['descricao'] . "</td>";
                        echo "<td>" . $row['ano'] . "</td>";
                        echo "<td>" . $row['tipo'] . "</td>";
                        echo "<td>" . $row['bairro'] . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['solicitacao'])) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['atendimento'])) . "</td>";
                        echo "<td>" . $row['unidade'] . "</td>";
                        echo "<td>" . date('d/m/Y H:i:s', strtotime($row['ultima_atualizacao'])) . "</td>";
                        echo "<td><a href='../admin/covid/download.php?id=" . $row['id'] . "'>Download PDF</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Nenhum dado encontrado.</td></tr>";
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
            table = document.getElementById("covidTable");
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

        let sortOrder = [true, false, true, false, true, false, true, false, true];

        function sortTable(n) {
            let table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("covidTable");
            switching = true;
            dir = sortOrder[n] ? "asc" : "desc"; // A alternância da direção (crescente/decrescente)
            sortOrder[n] = !sortOrder[n]; // Alterna a ordem para a próxima vez

            // Trocar a classe para alternar a seta
            let th = table.getElementsByTagName("th")[n];
            if (dir === "asc") {
                th.classList.remove("desc");
                th.classList.add("asc");
            } else {
                th.classList.remove("asc");
                th.classList.add("desc");
            }

            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir == "asc") {
                        if (n == 0 || n == 8) {  // Para data e última atualização
                            if (new Date(x.innerHTML) > new Date(y.innerHTML)) {
                                shouldSwitch = true;
                                break;
                            }
                        } else {
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    } else {
                        if (n == 0 || n == 8) {  // Para data e última atualização
                            if (new Date(x.innerHTML) < new Date(y.innerHTML)) {
                                shouldSwitch = true;
                                break;
                            }
                        } else {
                            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                }
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
