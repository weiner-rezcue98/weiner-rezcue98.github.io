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
$sql = "SELECT * FROM api_de_dados";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API de Dados</title>
    <style>
        body {
            font-family: Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin: 20px;
        }

        .search-container {
            text-align: center;
            margin: 20px 0;
        }

        .search-container input {
            width: 50%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .table-container {
            width: 95%;
            margin: 20px auto;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
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
            border-radius: 5px;
            cursor: pointer;
        }

        .export-container button:hover {
            background-color: #0056b3;
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

    <!-- Exibição da Tabela -->
    <div class="table-container">
        <table id="apiTable">
            <thead>
                <tr>
                    <th>Documentação</th>
                    <th>Descrição</th>
                    <th>Serviço</th>
                    <th>End Point</th>
                    <th>Parâmetro de Entrada</th>
                    <th>Resultado de Erro</th>
                    <th>Resultado Esperado</th>
                    <th>Última Atualização</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><a href='../admin/api_de_dados/download.php?id=" . $row['id'] . "'>Download PDF</a></td>";
                        echo "<td>" . $row['descricao'] . "</td>";
                        echo "<td>" . $row['servico'] . "</td>";
                        echo "<td>" . $row['end_point'] . "</td>";
                        echo "<td>" . $row['parametro_entrada'] . "</td>";
                        echo "<td>" . $row['resultado_erro'] . "</td>";
                        echo "<td>" . $row['resultado_esperado'] . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['ultima_atualizacao'])) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Nenhum dado encontrado.</td></tr>";
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
            table = document.getElementById("apiTable");
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

        // Detectar Ctrl + P para impressão
        document.addEventListener("keydown", function (event) {
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
