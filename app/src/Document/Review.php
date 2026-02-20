<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: "reviews")]
#[MongoDB\Index(keys: ['poi_id' => 1])]
#[MongoDB\Index(keys: ['user_id' => 1])]
#[MongoDB\Index(keys: ['poi_id' => 1, 'created_at' => -1])]
class Review
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "int")]
    private int $poi_id;

    #[MongoDB\Field(type: "int")]
    private int $user_id;

    #[MongoDB\Field(type: "float")]
    #[Assert\NotBlank(message: "La note est obligatoire.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}."
    )]
    private float $rating;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le commentaire ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 500,
        minMessage: "Le commentaire doit faire au moins {{ limit }} caractères.",
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $comment;

    #[MongoDB\Field(type: "date")]
    private \DateTime $created_at;

    public function __construct()
    {
        // Initialise automatiquement la date à la création
        $this->created_at = new \DateTime();
    }

    // =====================
    // GETTERS
    // =====================

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPoiId(): int
    {
        return $this->poi_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    // =====================
    // SETTERS
    // =====================

    public function setPoiId(int $poi_id): self
    {
        $this->poi_id = $poi_id;
        return $this;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}
