<?php

$conexao = require __DIR__ . "/../Model/conexao.php";
require_once __DIR__ . "/../Controller/LoginController.php";
require_once __DIR__ . "/../Controller/RecuperarSenhaController.php";
require_once __DIR__ . "/../Controller/RedefinirSenhaController.php";

$erro = null;
$mensagem = null;
$modoRecuperar = isset($_GET['recuperar']);
$tokenUrl = $_GET['token'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {

    $mensagem = processarRecuperarSenha($conexao, trim($_POST['email'] ?? ''));
    $modoRecuperar = true;

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {

    if (processarRedefinirSenha($conexao, $_POST['token'], $_POST['senha'] ?? '')) {
        $mensagem = "Senha redefinida! Faça login com a nova senha.";
    } else {
        $erro = "Link inválido ou expirado.";
        $modoRecuperar = false;
        $tokenUrl = null;
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $resultado = processarLogin($conexao, trim($_POST['email'] ?? ''), $_POST['senha'] ?? '');

    if ($resultado['sucesso']) {
        $_SESSION['usuario_id']   = $resultado['usuario']['id'];
        $_SESSION['usuario_nome'] = $resultado['usuario']['nome'];
        header("Location: " . BASE_URL . "gerenciar-pentest");
        exit;
    }

    $erro = $resultado['erro'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CYBER REPORT') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/login.css">
</head>

<body>
    <!-- <h1>CYBER REPORT</h1> -->
    <main>
        <div class="geral">
        <section class="lado-esq">
            <div class="cyber-report">
                <img src="<?= BASE_URL ?>app/assets/img/Gemini_Generated_Image_rh5rtirh5rtirh5r-removebg-preview 1.png" alt="" class="logo-report">
            </div>
            <div class="hackers">
                <img src="<?= BASE_URL ?>app/assets/img/img.png" class="sombra-esq">
                <img src="<?= BASE_URL ?>app/assets/img/img.png" class="sombra-dir">
            </div>
            <div class="principal">
                <img src="<?= BASE_URL ?>app/assets/img/img.png" alt="">
            </div>
        </section>
        <section class="lado-dir">
            <div class="login-topo">
                <img src="<?= BASE_URL ?>app/assets/img/Logo Direito.png" alt="" class="logo-baikal">
            </div>    
            <div class="login-box">
                <?php if ($tokenUrl): ?>

                    <h2>Nova senha</h2>
                    <form method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($tokenUrl) ?>">
                        <label>Nova senha</label>
                        <input type="password" name="senha" placeholder="senha">
                        <?php if ($erro): ?><p class="erro-login"><?= htmlspecialchars($erro) ?></p><?php endif; ?>
                        <button type="submit">Redefinir senha</button>
                    </form>

                <?php elseif ($modoRecuperar): ?>

                    <h2>Recuperar senha</h2>
                    <form method="POST">
                        <label>E-mail</label>
                        <input type="email" name="email" placeholder="email@example.com">
                        <?php if ($mensagem): ?><p class="erro-login"><?= htmlspecialchars($mensagem) ?></p><?php endif; ?>
                        <button type="submit" name="recuperar" value="1"class="btn-enviar-link-login">Enviar link</button>
                    </form>
                    <a href="<?= BASE_URL ?>" class="btn-voltar-login">Voltar ao login</a>

                <?php else: ?>

                    <h2>LOGIN</h2>
                    <form method="POST">
                        <label>E-mail</label>
                        <input type="email" name="email" placeholder="email@example.com">
                        <label>Senha</label>
                        <input type="password" name="senha" placeholder="senha">
                        <?php if ($erro): ?>
                            <p class="erro-login"><?= htmlspecialchars ($erro) ?></p>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>?recuperar" class="esqueceu-senha-login">Esqueceu a senha?</a>
                        <button type="submit" class="btn-entrar-login">Entrar</button>
                    </form>

                <?php endif; ?>
            </div>
        </section>
        </div>
    </main>
</body>

</html>
