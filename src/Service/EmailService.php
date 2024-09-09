<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;

class EmailService
{
    private $mailer;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->mailer = new PHPMailer(true);
        $this->logger = $logger;
        $this->configureMailer();
    }

    private function configureMailer(): void
    {
        // Configuration du serveur SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'ines.bougtaib@gmail.com';
        $this->mailer->Password = 'llvi fzfr tuvf jugq';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
    }

    public function sendEmail(string $to, string $subject, string $htmlContent, string $from = 'no-reply@exemple.com', string $fromName = 'CnK Couture'): void
    {
        try {
            // Configuration de l'email
            $this->mailer->setFrom($from, $fromName);
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $htmlContent;

            // Envoi de l'email
            $this->mailer->send();
        } catch (Exception $e) {
            // Gestion des erreurs
            $this->logger->error("Le message n'a pas pu être envoyé. Erreur de Mailer: {$this->mailer->ErrorInfo}");
        }
    }

    public function sendConfirmationEmail(string $to, string $subject, string $htmlContent): void
    {
        $this->sendEmail($to, $subject, $htmlContent);
    }

    // je peux ajouter d'autres méthodes pour différents types d'emails
    public function sendNewsletterEmail(string $to, string $subject, string $htmlContent): void
    {
        $this->sendEmail($to, $subject, $htmlContent, 'newsletter@exemple.com', 'Newsletter');
    }
}