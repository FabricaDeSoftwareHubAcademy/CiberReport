<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($destinatario, $assunto, $corpo) {
    $config = require __DIR__ . '/mailconfig.php';
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 10;

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $corpo;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Falha ao enviar e-mail para {$destinatario}: " . $mail->ErrorInfo);
        return false;
    }
}

function enviarEmailRecuperacao($destinatario, $nome, $link) {
    $assunto = 'Recuperação de senha - CiberReport';
    $corpo = "
        <p>Olá, {$nome}!</p>
        <p>Recebemos uma solicitação para redefinir sua senha no CiberReport.</p>
        <p>Clique no link abaixo para criar uma nova senha:</p>
        <p><a href='{$link}'>{$link}</a></p>
        <p>Se você não solicitou isso, ignore este e-mail.</p>
        <p>Este link expira em 1 hora.</p>
    ";

    return enviarEmail($destinatario, $assunto, $corpo);
}