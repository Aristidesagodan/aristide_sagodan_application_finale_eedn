<?php
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require __DIR__ . '/../vendor/autoload.php';

$transport = Transport::fromDsn(getenv('MAILER_DSN'));
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('aristidesagodan48@gmail.com')    // ton Gmail
    ->to('aristidesagodan48@gmail.com')      // même email pour recevoir
    ->subject('Test Symfony Mailer')
    ->text("Ceci est un test d'envoi depuis Symfony + Docker");

try {
    $mailer->send($email);
    echo "Mail envoyé ✅\n";
} catch (\Throwable $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
