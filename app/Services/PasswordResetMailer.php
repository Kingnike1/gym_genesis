<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

final class PasswordResetMailer
{
    public function send(string $email, string $resetUrl): void
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = (string) ($_ENV['MAIL_HOST'] ?? '');
        $mailer->Port = (int) ($_ENV['MAIL_PORT'] ?? 587);
        $mailer->SMTPAuth = true;
        $mailer->Username = (string) ($_ENV['MAIL_USERNAME'] ?? '');
        $mailer->Password = (string) ($_ENV['MAIL_PASSWORD'] ?? '');
        $mailer->SMTPSecure = (string) ($_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS);
        $from = (string) ($_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.invalid');
        $fromName = (string) ($_ENV['MAIL_FROM_NAME'] ?? 'Gym Genesis');
        $mailer->setFrom($from, $fromName);
        $mailer->addAddress($email);
        $mailer->isHTML(true);
        $mailer->Subject = 'Redefinição de senha - Gym Genesis';
        $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $mailer->Body = '<p>Recebemos uma solicitação de redefinição de senha.</p><p><a href="' . $safeUrl . '">Redefinir senha</a></p><p>O link expira em 30 minutos e só pode ser usado uma vez.</p>';
        $mailer->AltBody = "Redefina sua senha em: {$resetUrl}\nO link expira em 30 minutos e só pode ser usado uma vez.";
        $mailer->send();
    }
}
