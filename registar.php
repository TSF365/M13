<?php
$pdo = new PDO("mysql:host=localhost;dbname=psidb", "root", "mysql");

session_start(); // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Insere o novo usuário no banco de dados
    $insert_user_sql = "INSERT INTO utilizador (username, password) VALUES (?, ?)";
    $insert_user_stmt = $pdo->prepare($insert_user_sql);
    if ($insert_user_stmt->execute([$username, $password])) {
        // Registo bem-sucedido, redireciona para a página inicial
        $_SESSION['username'] = $username;
        header("location: user.php");
        exit(); // Importante para evitar que o script continue a ser executado após o redirecionamento
    } else {
        $error = "Ocorreu um erro ao registar. Por favor, tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registar.css">
    <title>Registo</title>
</head>

<body>
    <div class="container">
        <h2>Registo</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input class="registo" type="submit" value="Registar">
            <!-- <input class="login" type="submit" value="Login" formaction="login.php"> -->
        </form>
        <a class="login" href="login.php"> Login</a>
        <div class="erro">
        <?php if (isset($error))
        echo "<p>$error</p>"; ?>
        </div>
    </div>
</body>

</html>