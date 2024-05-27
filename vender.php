<?php
session_start();
$host = "localhost";
$username = "root";
$password = "mysql";
$database = "psidb";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Define addItem function
function addItem($pdo, $nome, $data, $preco, $combustivel, $cavalos, $cilindrada, $imagem) {
    try {
        $query = "INSERT INTO veiculos (nome, data, preco, combustivel, cavalos, cilindrada, imagem)
                  VALUES (:nome, :data, :preco, :combustivel, :cavalos, :cilindrada, :imagem)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':nome', $nome);
        $statement->bindParam(':data', $data);
        $statement->bindParam(':preco', $preco);
        $statement->bindParam(':combustivel', $combustivel);
        $statement->bindParam(':cavalos', $cavalos);
        $statement->bindParam(':cilindrada', $cilindrada);
        $statement->bindParam(':imagem', $imagem);
        $statement->execute();
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $data = $_POST['data'];
    $preco = $_POST['preco'];
    $combustivel = $_POST['combustivel'];
    $cavalos = $_POST['cavalos'];
    $cilindrada = $_POST['cilindrada'];
    $imagem = $_FILES['imagem']['name'] ?? null;
    $targetDir = "uploads/";
    $errorMessage = '';

    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($imagem);

    // Move the uploaded file to the target directory
    if ($imagem && move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFile)) {
        $result = addItem($pdo, $nome, $data, $preco, $combustivel, $cavalos, $cilindrada, $imagem);
    } else {
        $result = addItem($pdo, $nome, $data, $preco, $combustivel, $cavalos, $cilindrada, null);
        if ($result === true) {
            echo "Item added without image.";
        } else {
            $errorMessage = $result;
        }
    }
    
    if ($result === true) {
        // Clear form data
        $_POST = array();
        $_FILES['imagem']['name'] = null;

        // Refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<p>Error: $errorMessage</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="vender.css">
<title>Formulário de Veículo</title>
</head>
<body>

<header>
    <h1>Meu Site de Carros</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="#">Sobre</a>
    <a href="#">Contato</a>
</nav>

<section>
    <h2>Formulário de Veículo</h2>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES) ?>">
        
        <label for="data">Data:</label>
        <input type="date" id="data" name="data" required value="<?= htmlspecialchars($_POST['data'] ?? '', ENT_QUOTES) ?>">

        <label for="preco">Preço:</label>
        <input type="number" id="preco" name="preco" step="0.01" required value="<?= htmlspecialchars($_POST['preco'] ?? '', ENT_QUOTES) ?>">

        <label for="combustivel">Combustível:</label>
        <select id="combustivel" name="combustivel" required>
            <option value="gasolina" <?= (isset($_POST['combustivel']) && $_POST['combustivel'] == 'gasolina') ? 'selected' : '' ?>>Gasolina</option>
            <option value="diesel" <?= (isset($_POST['combustivel']) && $_POST['combustivel'] == 'diesel') ? 'selected' : '' ?>>Diesel</option>
            <option value="eletrico" <?= (isset($_POST['combustivel']) && $_POST['combustivel'] == 'eletrico') ? 'selected' : '' ?>>Elétrico</option>
        </select>

        <label for="cavalos">Cavalos:</label>
        <input type="number" id="cavalos" name="cavalos" required value="<?= htmlspecialchars($_POST['cavalos'] ?? '', ENT_QUOTES) ?>">

        <label for="cilindrada">Cilindrada:</label>
        <input type="number" id="cilindrada" name="cilindrada" required value="<?= htmlspecialchars($_POST['cilindrada'] ?? '', ENT_QUOTES) ?>">

        <label for="imagem">Imagem:</label>
        <input type="file" id="imagem" name="imagem" accept="image/*">

        <input type="submit" name="submit" value="Enviar">
    </form>
</section>
</body>
</html>
