<?php

namespace App\Entity;

use App\Repository\PoiCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PoiCategoryRepository::class)]
class PoiCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la catégorie est obligatoire.")]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Poi::class, mappedBy: 'category')]
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
            $poi->setCategory($this);
        }
        return $this;
    }

    public function removePoi(Poi $poi): static
    {
        if ($this->pois->removeElement($poi)) {
            if ($poi->getCategory() === $this) {
                $poi->setCategory(null);
            }
        }
        return $this;
    }
}
