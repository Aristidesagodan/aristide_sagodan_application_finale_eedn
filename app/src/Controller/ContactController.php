<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/contact', name: 'contact_index')]
class ContactController extends AbstractController
{
    #[Route('/', name: '')] // URL finale : /contact
    public function index(
        Request $request, 
        MailerInterface $mailer,
        ValidatorInterface $validator
    ): Response
    {
        if ($request->isMethod('POST')) {

            // 🔐 Vérification CSRF
            $submittedToken = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('contact_form', $submittedToken)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('contact_index');
            }

            $name = trim($request->request->get('name'));
            $email = trim($request->request->get('email'));
            $messageContent = trim($request->request->get('message'));

            // ✅ Validation avec Symfony Validator
            $constraints = new Assert\Collection([
                'name' => [
                    new Assert\NotBlank(message: "Le nom est obligatoire."),
                    new Assert\Length(
                        min: 2,
                        minMessage:"Le nom doit contenir au moins 2 caractères."
                    )
                ],
                'email' => [
                    new Assert\NotBlank(message: "L'email est obligatoire."),
                    new Assert\Email(message: "Adresse email invalide.")
                ],
                'message' => [
                    new Assert\NotBlank(message: "Le message est obligatoire."),
                    new Assert\Length(
                        min: 10,
                        minMessage:"Le message doit contenir au moins 10 caractères."
                    )
                ],
            ]);

            $input = [
                'name' => $name,
                'email' => $email,
                'message' => $messageContent
            ];

            $violations = $validator->validate($input, $constraints);

            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }

                return $this->redirectToRoute('contact_index');
            }

            try {
                // 📧 Création de l'email
                $emailMessage = (new Email())
                    ->from('aristidesagodan48@gmail.com') 
                    ->replyTo($email)
                    ->to('aristidesagodan48@gmail.com')
                    ->subject('Message depuis le formulaire de contact')
                    ->text(
                        "Nom : $name\n" .
                        "Email : $email\n\n" .
                        $messageContent
                    );

                // 🚀 Envoi
                $mailer->send($emailMessage);

                $this->addFlash('success', 'Merci ! Votre message a bien été envoyé.');
                return $this->redirectToRoute('contact_index');

            } catch (\Throwable $e) {
                $this->addFlash('error', 'Erreur lors de l’envoi du message.');
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('contact_index');
            }
        }

        // 📍 Coordonnées de la carte
        return $this->render('contact/index.html.twig', [
            'officeLat' => 46.5,
            'officeLng' => 2.5,
        ]);
    }
}
