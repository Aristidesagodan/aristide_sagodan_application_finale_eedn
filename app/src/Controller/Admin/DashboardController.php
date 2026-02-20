<?php

namespace App\Controller\Admin;

use App\Repository\CityRepository;
use App\Repository\PoiRepository;
use App\Repository\PoiCategoryRepository;
use App\Repository\UserRepository;
use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AbstractController
{
    public function __construct(private DocumentManager $dm) {}

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

        // -----------------------
        // Statistiques globales
        // -----------------------
        $totalCities = $cityRepo->count([]);
        $totalPois = $poiRepo->count([]);
        $totalCategories = $catRepo->count([]);
        $totalUsers = $userRepo->count([]);

        // -----------------------
        // Statistiques et derniers avis (MongoDB)
        // -----------------------
        $reviewRepo = $this->dm->getRepository(Review::class);

        // Total des avis
        $totalReviews = $reviewRepo->createQueryBuilder()
            ->getQuery()
            ->execute()
            ->count();

        // 5 derniers avis
        $lastReviews = $reviewRepo->createQueryBuilder()
            ->sort('created_at', 'DESC')
            ->limit(5)
            ->getQuery()
            ->execute();

        return $this->render('admin/dashboard.html.twig', [
            'cities' => $cities,
            'pois' => $pois,
            'categories' => $categories,
            'users' => $users,
            'totalCities' => $totalCities,
            'totalPois' => $totalPois,
            'totalCategories' => $totalCategories,
            'totalUsers' => $totalUsers,
            'totalReviews' => $totalReviews,
            'lastReviews' => $lastReviews,
        ]);
    }
}
