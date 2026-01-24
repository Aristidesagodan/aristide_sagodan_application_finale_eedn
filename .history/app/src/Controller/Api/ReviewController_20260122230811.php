<?php
namespace App\Controller\Api;

use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/review')]
#[IsGranted('ROLE_USER')]
class ReviewController extends AbstractController
{
    public function __construct(private DocumentManager $dm) {}

    // -------------------------------
    // Ajouter un avis
    // -------------------------------
    #[Route('/add', name: 'api_review_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $poiId = $data['poi_id'] ?? null;
        $rating = $data['rating'] ?? null;
        $comment = $data['comment'] ?? null;
        $userId = $this->getUser()?->getId();

        if (!$poiId || !$userId || $rating === null || !$comment) {
            return $this->json(['success' => false, 'error' => 'Champs manquants']);
        }

        $review = new Review();
        $review->setPoiId((int)$poiId)
               ->setUserId($userId)
               ->setRating((float)$rating)
               ->setComment($comment)
               ->setCreatedAt(new \DateTime());

        $this->dm->persist($review);
        $this->dm->flush();

        return $this->json(['success' => true, 'id' => (string)$review->getId()]);
    }

    // -------------------------------
    // Lister les avis d’un POI
    // -------------------------------
    #[Route('/all', name: 'api_review_all', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        $poiId = $request->query->get('poi_id');
        if (!$poiId) return $this->json([]);

        $reviews = $this->dm->getRepository(Review::class)
            ->findBy(['poi_id' => (int)$poiId], ['created_at' => 'DESC']);

        $currentUserId = $this->getUser()?->getId();

        $data = array_map(fn(Review $r) => [
            'id' => (string)$r->getId(),
            'poi_id' => $r->getPoiId(),
            'user_id' => $r->getUserId(),
            'is_owner' => $currentUserId === $r->getUserId(),
            'rating' => $r->getRating(),
            'comment' => $r->getComment(),
            'created_at' => $r->getCreatedAt()->format('Y-m-d H:i'),
        ], $reviews);

        return $this->json($data);
    }

    // -------------------------------
    // Modifier un avis
    // -------------------------------
    #[Route('/edit/{id}', name: 'api_review_edit', methods: ['PUT'])]
    public function edit(string $id, Request $request): JsonResponse
    {
        $review = $this->dm->getRepository(Review::class)->find($id);
        if (!$review) return $this->json(['success' => false, 'error' => 'Avis introuvable']);
        if ($review->getUserId() !== $this->getUser()?->getId()) {
            return $this->json(['success' => false, 'error' => 'Accès refusé']);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['rating'])) $review->setRating((float)$data['rating']);
        if (isset($data['comment'])) $review->setComment($data['comment']);

        $this->dm->flush();

        return $this->json(['success' => true]);
    }

    // -------------------------------
    // Supprimer un avis
    // -------------------------------
    #[Route('/delete/{id}', name: 'api_review_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $review = $this->dm->getRepository(Review::class)->find($id);
        if (!$review) return $this->json(['success' => false, 'error' => 'Avis introuvable']);
        if ($review->getUserId() !== $this->getUser()?->getId()) {
            return $this->json(['success' => false, 'error' => 'Accès refusé']);
        }

        $this->dm->remove($review);
        $this->dm->flush();

        return $this->json(['success' => true]);
    }
}
