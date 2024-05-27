<?php 
    session_start();
    $host = "localhost";
    $username = "root";
    $password = "mysql";
    $database = "psidb";
    $found = false;
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);

    // Verificar se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['new_username'])) {
            // Obter o novo nome de usuário do formulário
            $new_username = $_POST['new_username'];

            // Atualizar o nome de usuário no banco de dados
            $sql_update = "UPDATE utilizador SET username = :new_username WHERE username = :username";
            $stmt = $pdo->prepare($sql_update);
            $stmt->execute(array(':new_username' => $new_username, ':username' => $_SESSION['username']));

            // Atualizar a variável de sessão com o novo nome de usuário
            $_SESSION['username'] = $new_username;
        }
    }

    $sql = 'SELECT * FROM utilizador';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Página de utilizador</h1>
            <?php 
                if(isset($_SESSION['username'])) {
                    echo "<p>Olá " . $_SESSION['username'] . "</p>";
                } else {
                    echo "<a href='login.php'>Iniciar Sessão</a>";
                }
            ?>
        </div>
    </header>
    <nav>
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <!-- Formulário para mudar o nome de utilizador -->
        <h2>Mudar Nome de Utilizador</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="new_username">Novo Nome de Utilizador:</label>
            <input type="text" id="new_username" name="new_username" required>
            <button type="submit">Alterar</button>
        </form>
    </div>
</body>
</html>
