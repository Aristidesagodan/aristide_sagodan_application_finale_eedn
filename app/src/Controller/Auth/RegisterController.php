<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        if ($request->isMethod('POST')) {

            // ✅ Vérification du token CSRF
            if (!$this->isCsrfTokenValid('register', $request->request->get('_csrf_token'))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $user = new User();
            $user->setEmail($request->request->get('email'));

            $hash = $hasher->hashPassword(
                $user,
                $request->request->get('password')
            );

            $user->setPassword($hash);
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/register.html.twig');
    }
}
