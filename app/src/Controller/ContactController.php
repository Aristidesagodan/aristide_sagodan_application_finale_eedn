<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/contact', name: 'contact_index')]
class ContactController extends AbstractController
{
    /**
     * Affiche le formulaire de contact et envoie l'email si le formulaire est soumis.
     */
    #[Route('/', name: '')] // la route finale sera /contact
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Vérifie si le formulaire est soumis
        if ($request->isMethod('POST')) {

            // ✅ Vérification du token CSRF
            $submittedToken = $request->request->get('_token');

            if (!$this->isCsrfTokenValid('contact_form', $submittedToken)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('contact_index');
            }

            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $messageContent = $request->request->get('message');

            // Vérifie que tous les champs sont remplis
            if ($name && $email && $messageContent) {
                // Création de l'email
                $emailMessage = (new Email())
                    ->from($email)
                    ->to('aristidesagodan@hotmail.fr') // changer avec ton email
                    ->subject('Message depuis Contact')
                    ->text("Nom: $name\nEmail: $email\n\n$messageContent");

                // Envoi de l'email
                $mailer->send($emailMessage);

                $this->addFlash('success', 'Merci ! Votre message a été envoyé.');
                return $this->redirectToRoute('contact_index');
            }

            $this->addFlash('error', 'Veuillez remplir tous les champs.');
        }

        // Coordonnées du marker sur la carte
        $officeLat = 46.5;
        $officeLng = 2.5;

        // Rend le template contact/index.html.twig
        return $this->render('contact/index.html.twig', [
            'officeLat' => $officeLat,
            'officeLng' => $officeLng,
        ]);
    }
}
