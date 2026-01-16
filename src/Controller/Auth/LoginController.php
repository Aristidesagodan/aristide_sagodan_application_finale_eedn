<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $auth): Response
    {
        return $this->render('auth/login.html.twig', [
            'error' => $auth->getLastAuthenticationError(),
            'last_username' => $auth->getLastUsername(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void {}
}
