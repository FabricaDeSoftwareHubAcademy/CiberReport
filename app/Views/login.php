<?php
$modo = $modo ?? 'login';
$erro = $erro ?? null;
$mensagem = $mensagem ?? null;
$token = $token ?? null;
$sucessoRedefinicao = $sucessoRedefinicao ?? false;
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
    <main>
        <div class="geral">
            <section class="lado-esq">
                <video
                    class="video-login"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    aria-label="Apresentação visual do Cyber Report"
                >
                    <source
                        src="<?= BASE_URL ?>app/assets/videos/video-login.mp4"
                        type="video/mp4"
                    >
                    Seu navegador não suporta a reprodução de vídeos.
                </video>
            </section>

            <section class="lado-dir">
                <div class="login-topo">
                    <img
                        src="<?= BASE_URL ?>app/assets/img/Logo Direito.png"
                        alt=""
                        class="logo-baikal"
                    >
                </div>

                <div class="login-box">
                    <?php if ($modo === 'redefinir'): ?>
                        <h2>Nova senha</h2>

                        <form method="POST" action="<?= BASE_URL ?>redefinir-senha">
                            <input
                                type="hidden"
                                name="token"
                                value="<?= htmlspecialchars($token ?? '') ?>"
                            >

                            <label for="nova-senha">Nova senha</label>

                            <input
                                id="nova-senha"
                                type="password"
                                name="senha"
                                placeholder="Nova senha"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <label for="confirmar-senha">Confirme sua senha</label>

                            <input
                                id="confirmar-senha"
                                type="password"
                                name="confirmar_senha"
                                placeholder="Confirme sua senha"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <?php if ($erro): ?>
                                <p class="erro-login">
                                    <?= htmlspecialchars($erro) ?>
                                </p>
                            <?php endif; ?>

                            <button
                                class="btn-redefinir-senha-login"
                                type="submit"
                            >
                                Redefinir senha
                            </button>
                        </form>

                    <?php elseif ($modo === 'recuperar'): ?>
                        <h2>Recuperar senha</h2>

                        <form method="POST" action="<?= BASE_URL ?>recuperar-senha">
                            <label for="email-recuperacao">E-mail</label>

                            <input
                                id="email-recuperacao"
                                type="email"
                                name="email"
                                placeholder="email@example.com"
                                autocomplete="email"
                                required
                            >

                            <?php if ($mensagem): ?>
                                <p class="sucesso-login">
                                    <?= htmlspecialchars($mensagem) ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($erro): ?>
                                <p class="erro-login">
                                    <?= htmlspecialchars($erro) ?>
                                </p>
                            <?php endif; ?>

                            <button
                                type="submit"
                                class="btn-enviar-link-login"
                            >
                                Enviar link
                            </button>
                        </form>

                        <a
                            href="<?= BASE_URL ?>login"
                            class="btn-voltar-login"
                        >
                            Voltar ao login
                        </a>

                    <?php else: ?>
                        <h2>LOGIN</h2>

                        <?php if ($sucessoRedefinicao): ?>
                            <p class="sucesso-login">
                                Senha redefinida! Faça login com a nova senha.
                            </p>
                        <?php endif; ?>

                        <form method="POST" action="<?= BASE_URL ?>login">
                            <label for="email-login">E-mail</label>

                            <input
                                id="email-login"
                                type="email"
                                name="email"
                                placeholder="email@example.com"
                                autocomplete="email"
                                required
                            >

                            <label for="senha-login">Senha</label>

                            <input
                                id="senha-login"
                                type="password"
                                name="senha"
                                placeholder="senha"
                                autocomplete="current-password"
                                required
                            >

                            <?php if ($erro): ?>
                                <p class="erro-login">
                                    <?= htmlspecialchars($erro) ?>
                                </p>
                            <?php endif; ?>

                            <a
                                href="<?= BASE_URL ?>recuperar-senha"
                                class="esqueceu-senha-login"
                            >
                                Esqueceu a senha?
                            </a>

                            <button
                                type="submit"
                                class="btn-entrar-login"
                            >
                                Entrar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>

</html>
