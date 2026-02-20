<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/app', name: 'app_home')]
    public function index(): Response
    {
        // Le front consomme l'API /api/cities/{name}/pois
        return $this->render('home/index.html.twig');
    }
}
