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

// Consulta SQL para buscar os dados da tabela
$sql = "SELECT * FROM compras_inexigibilidade_dispensa";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras (Inexigibilidade e Dispensa de Licitação)</title>
    <style>
         .print-button {
            margin: 20px auto;
            display: block;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

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

          /* Seus estilos */
          th {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        th.sortable:after {
            content: " ▼"; /* Seta padrão */
            font-size: 12px;
        }

        th.asc:after {
            content: " ▲"; /* Seta crescente */
        }

        th.desc:after {
            content: " ▼"; /* Seta decrescente */
        }

        /* Mantém a tabela responsiva e com visualização ajustada */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

    <!-- Barra de Pesquisa -->
    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Pesquisar...">
    </div>

    <!-- Botão para imprimir ou exportar -->
    <button class="print-button" onclick="exportTableToPDF()">Exportar como PDF</button>


<!-- Tabela -->
<table id="dataTable">
    <thead>
        <tr>
            <th class="sortable" onclick="sortTable(0)">Ano</th>
            <th>Processo</th>
            <th>Dispensa</th>
            <th>Ratificada</th>
            <th>Valor</th>
            <th>Modalidade</th>
            <th>Fornecedor</th>
            <th>Objeto</th>
            <th>Mês</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['ano'] . "</td>";
                echo "<td>" . $row['processo'] . "</td>";
                echo "<td>" . (new DateTime($row['dispensa']))->format('d/m/Y') . "</td>";
                echo "<td>" . (new DateTime($row['ratificada']))->format('d/m/Y') . "</td>";
                echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                echo "<td>" . $row['modalidade'] . "</td>";
                echo "<td>" . $row['nome_fornecedor'] . "</td>";
                echo "<td>" . $row['objeto'] . "</td>";
                echo "<td>" . $row['mes'] . "</td>";
                echo "<td><a href='../admin/compras_inexigibilidade_dispensa/download.php?id=" . $row['id'] . "'>Baixar PDF</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Nenhum registro encontrado.</td></tr>";
        }
        ?>
    </tbody>
</table>

<script>
    // Ordenar tabela por coluna
    function sortTable(columnIndex) {
        const table = document.getElementById("dataTable");
        const rows = Array.from(table.rows).slice(1); // Ignorar cabeçalho
        let sortedRows;

        // Determinar ordem atual (ascendente ou descendente)
        const isAscending = table.querySelectorAll("th")[columnIndex].classList.toggle("asc");

        // Remover classes de outras colunas
        table.querySelectorAll("th").forEach(th => th.classList.remove("asc", "desc"));

        if (isAscending) {
            table.querySelectorAll("th")[columnIndex].classList.add("asc");
            sortedRows = rows.sort((a, b) => a.cells[columnIndex].innerText.localeCompare(b.cells[columnIndex].innerText, 'pt-BR', { numeric: true }));
        } else {
            table.querySelectorAll("th")[columnIndex].classList.add("desc");
            sortedRows = rows.sort((a, b) => b.cells[columnIndex].innerText.localeCompare(a.cells[columnIndex].innerText, 'pt-BR', { numeric: true }));
        }

        // Atualizar a tabela
        const tbody = table.tBodies[0];
        sortedRows.forEach(row => tbody.appendChild(row));
    }
</script>
 <!-- Incluindo jsPDF -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script>
        async function exportTableToPDF() {
            const { jsPDF } = window.jspdf; // Obtém a referência ao jsPDF
            const doc = new jsPDF();

            // Configurações do título
            const title = "Compras - Inexigibilidade e Dispensa de Licitação";
            doc.setFontSize(16);
            doc.text(title, 14, 10);

            // Configurações da tabela
            doc.autoTable({
                html: '#dataTable', // Seleciona a tabela para exportação
                startY: 20,         // Posição inicial
                theme: 'grid',      // Estilo da tabela
                headStyles: { fillColor: [0, 123, 255] }, // Cor do cabeçalho
            });

            // Salva o arquivo PDF
            doc.save('compras_inexigibilidade_dispensa.pdf');
        }
    </script>
</body>
</html>