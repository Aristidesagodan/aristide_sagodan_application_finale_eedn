<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator
    ) {
        $errors = [];
        $old = [];

        if ($request->isMethod('POST')) {

            // ✅ Vérification du token CSRF
            if (!$this->isCsrfTokenValid('register', $request->request->get('_csrf_token'))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            $old['email'] = $email;

            // ✅ Vérification confirm password
            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password); // temporaire pour validation

            // ✅ Validation Symfony (Email, longueur mot de passe, etc.)
            $violations = $validator->validate($user);

            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
            }

            // ✅ Si erreurs → on renvoie le formulaire
            if (!empty($errors)) {
                return $this->render('auth/register.html.twig', [
                    'errors' => $errors,
                    'old' => $old
                ]);
            }

            // ✅ Hash seulement si tout est valide
            $hash = $hasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/register.html.twig', [
            'errors' => $errors
        ]);
    }
}
