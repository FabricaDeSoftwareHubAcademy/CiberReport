<?php
// require_once __DIR__ . '/../vendor/autoload.php';

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// function enviarEmail($destinatario, $assunto, $corpo) {
//     $config = require __DIR__ . '/mail.php';
//     $mail = new PHPMailer(true);

//     try {
//         $mail->isSMTP();
//         $mail->Host       = $config['host'];
//         $mail->SMTPAuth   = true;
//         $mail->Username   = $config['username'];
//         $mail->Password   = $config['password'];
//         $mail->SMTPSecure = $config['encryption'];
//         $mail->Port       = $config['port'];
//         $mail->CharSet    = 'UTF-8';
//         $mail->Timeout    = 10;

//         $mail->setFrom($config['from_email'], $config['from_name']);
//         $mail->addAddress($destinatario);

//         $mail->isHTML(true);
//         $mail->Subject = $assunto;
//         $mail->Body    = $corpo;

//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         error_log("Falha ao enviar e-mail para {$destinatario}: " . $mail->ErrorInfo);
//         return false;
//     }
// }