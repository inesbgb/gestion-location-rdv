<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailService
{
    private $mailer;
    private $logger;
    private $params;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->mailer = new PHPMailer(true);
        $this->logger = $logger;
        $this->params = $params;
        $this->configureMailer();
    }

    private function configureMailer(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->params->get('mailer_host');
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->params->get('mailer_user');
        $this->mailer->Password = $this->params->get('mailer_password');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $this->params->get('mailer_port');
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

    public function sendNewsletterEmail(string $to, string $subject, string $htmlContent): void
    {
        $this->sendEmail($to, $subject, $htmlContent, 'newsletter@exemple.com', 'Newsletter');
    }
}