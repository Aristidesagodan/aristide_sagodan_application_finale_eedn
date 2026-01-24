<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "reviews")]
class Review
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "int")]
    private int $poi_id;

    #[MongoDB\Field(type: "int")]
    private int $user_id;

    #[MongoDB\Field(type: "float")]
    private float $rating;

    #[MongoDB\Field(type: "string")]
    private string $comment;

    #[MongoDB\Field(type: "date")]
    private \DateTime $created_at;

    // ✅ Getters et setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPoiId(): int
    {
        return $this->poi_id;
    }

    public function setPoiId(int $poi_id): self
    {
        $this->poi_id = $poi_id;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}
