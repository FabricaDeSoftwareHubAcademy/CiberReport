<?php
if (!isset($viewCarregadaPeloController)) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    require_once __DIR__ . '/../Controller/LoginController.php';
    $controller = new LoginController();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $controller->index();
    } elseif (isset($_POST['recuperar'])) {
        $controller->solicitarRecuperacao();
    } elseif (isset($_POST['token'])) {
        $controller->redefinirSenha();
    } else {
        $controller->autenticar();
    }

    exit;
}

$erro = $erro ?? null;
$mensagem = $mensagem ?? null;
$modoRecuperar = $modoRecuperar ?? false;
$tokenUrl = $tokenUrl ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYBER REPORT</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/login.css">
</head>

<body>
    <!-- <h1>CYBER REPORT</h1> -->
    <main>
        <div class="geral">
        <section class="lado-esq">
            <div class="cyber-report">
                <img src="../assets/img/Gemini_Generated_Image_rh5rtirh5rtirh5r-removebg-preview 1.png" alt="" class="logo-report">
            </div>
            <div class="hackers">
                <img src="../assets/img/img.png" class="sombra-esq">
                <img src="../assets/img/img.png" class="sombra-dir">
            </div>
            <div class="principal">
                <img src="../assets/img/img.png" alt="">
            </div>
        </section>
        <section class="lado-dir">
            <div class="login-topo">
                <img src="../assets/img/Logo Direito.png" alt="" class="logo-baikal">
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
                        <button type="submit" name="recuperar" value="1">Enviar link</button>
                    </form>
                    <a href="login.php">Voltar ao login</a>

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
                        <a href="login.php?recuperar">Esqueceu a senha?</a>
                        <button type="submit">Entrar</button>
                    </form>

                <?php endif; ?>
            </div>
        </section>
        </div>
    </main>
</body>

</html>
