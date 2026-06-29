<?php
session_start();
require "../Model/conexao.php";

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header("Location: listarProjeto.html");
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
                <h2>LOGIN</h2>
                <?php if ($erro): ?>
                    <p class="erro"><?= htmlspecialchars ($erro) ?></p>
                <?php endif; ?>
                <form method="POST">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="email@example.com">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="senha">
                    <a href="">Esqueceu a senha?</a>
                    <button type="submit" href="vulnerabilidade.html">Entrar</button>
                </form>
            </div>
        </section>
        </div>
    </main>
</body>

</html>
