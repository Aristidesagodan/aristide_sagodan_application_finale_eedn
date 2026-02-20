<?php

namespace App\Controller\Api;

use App\Document\Review;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/review', name: 'api_review_')]
#[IsGranted('ROLE_USER')] // Toutes les actions nécessitent un utilisateur connecté
class ReviewController extends AbstractController
{
    public function __construct(private DocumentManager $dm, private ValidatorInterface $validator) {}

    // =====================
    // AJOUTER UN AVIS
    // =====================
    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $poiId = $data['poi_id'] ?? null;
        $userId = $this->getUser()?->getId();
        $rating = $data['rating'] ?? null;
        $comment = $data['comment'] ?? null;

        if (!$poiId || !$userId || $rating === null || !$comment) {
            return $this->json(['success' => false, 'error' => 'Champs manquants']);
        }

        $review = new Review();
        $review->setPoiId((int) $poiId)
               ->setUserId($userId)
               ->setRating((float) $rating)
               ->setComment($comment);

        // =====================
        // VALIDATION SYMFONY
        // =====================
        $errors = $this->validator->validate($review);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages]);
        }

        $this->dm->persist($review);
        $this->dm->flush();

        return $this->json([
            'success' => true,
            'id' => (string) $review->getId()
        ]);
    }

    // =====================
    // LISTER TOUS LES AVIS D’UN POI
    // =====================
    #[Route('/all', name: 'all', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        $poiId = $request->query->get('poi_id');
        if (!$poiId) return $this->json([]);

        $reviews = $this->dm->getRepository(Review::class)
            ->findBy(['poi_id' => (int)$poiId], ['created_at' => 'DESC']);

        $currentUserId = (string) $this->getUser()?->getId();

        $data = array_map(fn(Review $r) => [
            'id' => (string) $r->getId(),
            'poi_id' => $r->getPoiId(),
            'user_id' => (string) $r->getUserId(),
            'is_owner' => $currentUserId === (string) $r->getUserId(),
            'rating' => $r->getRating(),
            'comment' => $r->getComment(),
            'created_at' => $r->getCreatedAt()->format('Y-m-d H:i'),
        ], $reviews);

        return $this->json($data);
    }

    // =====================
    // MODIFIER UN AVIS
    // =====================
    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(string $id, Request $request): JsonResponse
    {
        $review = $this->dm->getRepository(Review::class)->find($id);
        if (!$review) {
            return $this->json(['success' => false, 'error' => 'Avis introuvable']);
        }

        if ((string)$review->getUserId() !== (string)$this->getUser()?->getId()) {
            return $this->json(['success' => false, 'error' => 'Accès refusé']);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['rating'])) $review->setRating((float) $data['rating']);
        if (isset($data['comment'])) $review->setComment($data['comment']);

        // Validation avant mise à jour
        $errors = $this->validator->validate($review);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages]);
        }

        $this->dm->flush();

        return $this->json(['success' => true]);
    }

    // =====================
    // SUPPRIMER UN AVIS
    // =====================
    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $review = $this->dm->getRepository(Review::class)->find($id);
        if (!$review) {
            return $this->json(['success' => false, 'error' => 'Avis introuvable']);
        }

        if ((string)$review->getUserId() !== (string)$this->getUser()?->getId()) {
            return $this->json(['success' => false, 'error' => 'Accès refusé']);
        }

        $this->dm->remove($review);
        $this->dm->flush();

        return $this->json(['success' => true]);
    }
}
