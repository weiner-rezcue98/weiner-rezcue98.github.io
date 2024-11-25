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

// Consulta SQL para pegar as secretarias
$sql = "SELECT * FROM secretarias";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretarias Municipais | Município de Modelo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        /* Estilo para a barra de pesquisa */
        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            width: 50%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Estilo para o botão de exportação */
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

        /* Estilo para os botões de colapso */
        .collapsible {
            cursor: pointer;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            text-align: left;
            width: 100%;
            outline: none;
        }

        .collapsible:hover {
            background-color: #f1f1f1;
        }

        .collapsible::before {
            content: '+';
            margin-right: 10px;
            font-size: 18px;
        }

        .collapsible.active::before {
            content: '-';
        }

        /* Conteúdo oculto do colapso */
        .content {
            display: none;
            padding: 10px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }

        .content p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <!-- Barra de Pesquisa -->
    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Pesquisar Secretaria...">
    </div>

    <!-- Botão para imprimir ou salvar como PDF -->
    <button class="print-button" onclick="printPage()">Exportar como PDF</button>

    <!-- Lista de Secretarias -->
    <div id="secretariasContainer">
        <?php
        if ($result->num_rows > 0) {
            // Exibe os dados das secretarias
            while($row = $result->fetch_assoc()) {
                echo "<button class='collapsible'>" . $row['nome_secretaria'] . "</button>";
                echo "<div class='content'>";
                echo "<p><strong>Cargo:</strong> " . $row['cargo'] . "</p>";
                echo "<p><strong>Endereço:</strong> " . $row['endereco'] . "</p>";
                echo "<p><strong>Telefone:</strong> " . $row['telefone'] . "</p>";
                echo "<p><strong>E-mail:</strong> " . $row['email'] . "</p>";
                echo "<p><strong>Horário de Funcionamento:</strong> " . $row['horario_funcionamento'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhuma secretaria encontrada.</p>";
        }
        ?>
    </div>

    <script>
        // Função de colapso
        var coll = document.getElementsByClassName("collapsible");
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                } else {
                    content.style.display = "block";
                }
            });
        }

        // Função de busca
        function searchTable() {
            var input, filter, container, buttons, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            container = document.getElementById("secretariasContainer");
            buttons = container.getElementsByClassName("collapsible");

            // Loop pelos botões de colapso
            for (i = 0; i < buttons.length; i++) {
                txtValue = buttons[i].textContent || buttons[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    buttons[i].style.display = "";
                    buttons[i].nextElementSibling.style.display = "none"; // Oculta o conteúdo
                } else {
                    buttons[i].style.display = "none";
                }
            }
        }

        // Função para exportar como PDF
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>
