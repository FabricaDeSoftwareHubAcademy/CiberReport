<?php

$conexao = require __DIR__ . "/../Model/conexao.php";

$erro = null;
$mensagem = null;
$modoRecuperar = isset($_GET['recuperar']);
$tokenUrl = $_GET['token'] ?? null;

function gerarLinkRecuperacao($conexao, $email) {
    // if ($email === 'caiovv1@outlook.com') {          
    //     $token = bin2hex(random_bytes(32));       
    //     return BASE_URL . "login?token=$token";          
    // } 

    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $conexao->prepare("UPDATE usuario SET reset_token = ?, reset_token_expira = ? WHERE email = ?");
    $stmt->execute([$token, $expira, $email]);

    if ($stmt->rowCount() > 0) {
        return BASE_URL . "login?token=$token";
    }
    return null;
}

function redefinirSenha($conexao, $token, $novaSenha) {
    $stmt = $conexao->prepare("SELECT id FROM usuario WHERE reset_token = ? AND reset_token_expira > NOW()");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        return false;
    }

    $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
    $conexao->prepare("UPDATE usuario SET senha = ?, reset_token = NULL, reset_token_expira = NULL WHERE id = ?")
            ->execute([$hash, $usuario['id']]);
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {

    $link = gerarLinkRecuperacao($conexao, trim($_POST['email'] ?? ''));
    $mensagem = $link ? "Link gerado: $link" : "E-mail não encontrado.";
    $modoRecuperar = true;

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {

    if (redefinirSenha($conexao, $_POST['token'], $_POST['senha'] ?? '')) {
        $mensagem = "Senha redefinida! Faça login com a nova senha.";
    } else {
        $erro = "Link inválido ou expirado.";
        $modoRecuperar = false;
        $tokenUrl = null;
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); 

        if ($usuario) {

            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header("Location: " . BASE_URL . "gerenciar-pentest");
                exit;
            }
        }

        $erro = "E-mail ou senha inválidos.";
    }
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
