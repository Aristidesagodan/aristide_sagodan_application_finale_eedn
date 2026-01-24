<?php

namespace App\Controller\Api;

use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/review')]
#[IsGranted('ROLE_USER')]
class ReviewController extends AbstractController
{
    private DocumentManager $dm;

    // ⚠️ INJECTION EXPLICITE (pas en propriété PHP 8)
    public function __construct(DocumentManager $documentManager)
    {
        $this->dm = $documentManager;
    }

    // -------------------------
    // Ajouter un avis
    // -------------------------
    #[Route('/add', name: 'api_review_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            empty($data['poi_id']) ||
            empty($data['rating']) ||
            empty($data['comment'])
        ) {
            return $this->json(['success' => false, 'error' => 'Champs manquants'], 400);
        }

        $review = new Review();
        $review
            ->setPoiId((int) $data['poi_id'])
            ->setUserId($this->getUser()->getId())
            ->setRating((float) $data['rating'])
            ->setComment($data['comment'])
            ->setCreatedAt(new \DateTime());

        $this->dm->persist($review);
        $this->dm->flush();

        return $this->json(['success' => true]);
    }

    // -------------------------
    // Lister les avis d’un POI
    // -------------------------
    #[Route('/all', name: 'api_review_all', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        $poiId = $request->query->get('poi_id');
        if (!$poiId) {
            return $this->json([]);
        }

        $reviews = $this->dm
            ->getRepository(Review::class)
            ->findBy(['poi_id' => (int) $poiId], ['created_at' => 'DESC']);

        $currentUserId = $this->getUser()->getId();

        return $this->json(array_map(
            fn (Review $r) => [
                'id' => (string) $r->getId(),
                'rating' => $r->getRating(),
                'comment' => $r->getComment(),
                'is_owner' => $r->getUserId() === $currentUserId,
            ],
            $reviews
        ));
    }
}
