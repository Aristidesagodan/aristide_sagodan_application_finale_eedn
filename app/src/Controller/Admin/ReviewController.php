<?php

namespace App\Controller\Admin;

use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/review', name: 'admin_review_')]
class ReviewController extends AbstractController
{
    public function __construct(private DocumentManager $dm) {}

    // =====================
    // LISTE TOUS LES AVIS
    // =====================
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $reviews = $this->dm->getRepository(Review::class)
            ->findBy([], ['created_at' => 'DESC']);

        return $this->render('admin/review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    // =====================
    // SUPPRIMER UN AVIS
    // =====================
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(string $id): Response
    {
        $review = $this->dm->getRepository(Review::class)->find($id);

        if (!$review) {
            $this->addFlash('error', 'Avis introuvable.');
        } else {
            $this->dm->remove($review);
            $this->dm->flush();
            $this->addFlash('success', 'Avis supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_review_index');
    }
}

