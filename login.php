<?php
$pdo = new PDO("mysql:host=localhost;dbname=psidb", "root", "mysql");

session_start(); // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para verificar se o utilizador existe no banco de dados
    $sql = "SELECT * FROM utilizador WHERE username = :username AND password = :password";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();

    // Verifica se o login foi bem-sucedido
    if ($stmt->rowCount() == 1) {
        // Obtém os dados do utilizador
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Define as variáveis de sessão
        $_SESSION['username'] = $username;
        $_SESSION['IDuser'] = $user['IDuser'];

        // Redireciona para a página inicial
        header("location: user.php");
        exit(); // Termina o script após o redirecionamento
    } else {
        // Login falhou, exibe mensagem de erro
        $error = "Nome de utilizador ou palavra-pass incorreta";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>

<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input class="login" type="submit" value="Login">
        </form>
        <a class="registar" href="registar.php"> Registar</a>
        <div class="erro">
        <?php if (isset($error))
        echo "<p>$error</p>"; ?>
        </div>
    </div>
</body>

</html>
