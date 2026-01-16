<?php

namespace App\Controller\Admin;

use App\Repository\CityRepository;
use App\Repository\PoiRepository;
use App\Repository\PoiCategoryRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        CityRepository $cityRepo,
        PoiRepository $poiRepo,
        PoiCategoryRepository $catRepo,
        UserRepository $userRepo
    ): Response
    {
        // On récupère les derniers éléments pour le dashboard
        $cities = $cityRepo->findBy([], ['id' => 'DESC'], 5);
        $pois = $poiRepo->findBy([], ['id' => 'DESC'], 5);
        $categories = $catRepo->findBy([], ['id' => 'DESC'], 5);
        $users = $userRepo->findBy([], ['id' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'cities' => $cities,
            'pois' => $pois,
            'categories' => $categories,
            'users' => $users,
        ]);
    }
}
