<?php

namespace App\Entity;

use App\Repository\PoiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PoiRepository::class)]
#[ORM\Table(name: 'poi')]
class Poi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * URI unique de Datatourisme
     */
    #[ORM\Column(length: 191, unique: true)]
    #[Assert\NotBlank(message: "L'ID externe est obligatoire.")]
    private ?string $externalId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du lieu est obligatoire.")]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'pois')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La ville est obligatoire.")]
    private ?City $city = null;

    #[ORM\ManyToOne(inversedBy: 'pois')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La catégorie est obligatoire.")]
    private ?PoiCategory $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contacts = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $classements = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): static
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getCategory(): ?PoiCategory
    {
        return $this->category;
    }

    public function setCategory(?PoiCategory $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getContacts(): ?string
    {
        return $this->contacts;
    }

    public function setContacts(?string $contacts): static
    {
        $this->contacts = $contacts;
        return $this;
    }

    public function getClassements(): ?string
    {
        return $this->classements;
    }

    public function setClassements(?string $classements): static
    {
        $this->classements = $classements;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
