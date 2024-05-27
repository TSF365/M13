<?php
session_start();

if (isset($_POST['logout'])) {
    // Destruir todas as variáveis de sessão
    $_SESSION = array();

    // Se você quiser destruir a sessão completamente, apague também o cookie de sessão.
    // Nota: Isso destruirá a sessão e não apenas os dados da sessão!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destrua a sessão
    session_destroy();

    // Redirecionar para a página de login ou qualquer outra página desejada após o logout
    header("Location: login.php");
    exit;
}

$host = "localhost";
$username = "root";
$password = "mysql";
$database = "psidb";
$pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);

if (isset($_POST['add_favorite'])) {
    if (isset($_SESSION['username'])) {
        $id_veiculo = $_POST['id_veiculo'];
        $user_id = $_SESSION['IDuser'];

        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar se o veículo já está nos favoritos do usuário
            $checkFavorite = $pdo->prepare("SELECT * FROM favoritos WHERE IDveiculo = :id_veiculo AND IDuser = :user_id");
            $checkFavorite->execute(['id_veiculo' => $id_veiculo, 'user_id' => $user_id]);

            if ($checkFavorite->rowCount() == 0) {
                // Adicionar o veículo aos favoritos com favorito = 1
                $addFavorite = $pdo->prepare("INSERT INTO favoritos (IDveiculo, IDuser, favorito) VALUES (:id_veiculo, :user_id, 1)");
                $addFavorite->execute(['id_veiculo' => $id_veiculo, 'user_id' => $user_id]);

                echo "Veículo adicionado aos favoritos!";
            } else {
                echo "Este veículo já está nos seus favoritos!";
            }
        } catch (PDOException $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
        }
    } else {
        echo "Você precisa estar logado para adicionar favoritos!";
    }
}
// Selecionar todos os veículos
$statement = $pdo->prepare("SELECT * FROM veiculos");
$statement->execute();
$dados = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement2 = $pdo->prepare("SELECT * FROM utilizador");
$statement2->execute();
$dados2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stand XYZ - Página Inicial</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <h1>Stand XYZ</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <form method="post">
                <button type="submit" name="logout">LogOut</button>
            </form>
        <?php endif; ?>
    </header>
    <section>
        <nav>
            <a href="user.php">Conta</a>
            <a href="vender.php">Vender</a>
        </nav>
        <h2>Veículos em Destaque</h2>
        <table class="tabela">
            <tr>
                <th>Nome</th>
                <th>Cavalos</th>
                <th>Imagem</th>
                <th>Favorito</th>
            </tr>
            <?php foreach ($dados as $linha): ?>
                <tr>
                    <td><?= htmlspecialchars($linha["nome"]) ?></td>
                    <td><?= htmlspecialchars($linha["cavalos"]) ?></td>
                    <td>
                        <?php if (!empty($linha["imagem"])): ?>
                            <img src='uploads/<?= htmlspecialchars($linha["imagem"]) ?>' alt='<?= htmlspecialchars($linha["nome"]) ?>' width='75%'>
                        <?php else: ?>
                           Sem Imagem
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method='post'>
                            <input type='hidden' name='id_veiculo' value='<?= $linha["IDveiculo"] ?>'>
                            <button type='submit' name='add_favorite'>Adicionar aos Favoritos</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</body>
</html>
