<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la ville est obligatoire.")]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Length(
        max: 10,
        maxMessage: "Le code postal ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $postalCode = null;

    #[ORM\OneToMany(targetEntity: Poi::class, mappedBy: 'city')]
    private Collection $pois;

    public function __construct()
    {
        $this->pois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return Collection<int, Poi>
     */
    public function getPois(): Collection
    {
        return $this->pois;
    }

    public function addPoi(Poi $poi): static
    {
        if (!$this->pois->contains($poi)) {
            $this->pois->add($poi);
            $poi->setCity($this);
        }
        return $this;
    }

    public function removePoi(Poi $poi): static
    {
        if ($this->pois->removeElement($poi)) {
            if ($poi->getCity() === $this) {
                $poi->setCity(null);
            }
        }
        return $this;
    }
}
