<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $nomArticle = null;

    #[ORM\Column]
    private ?float $prix = null;



    /**
     * @var Collection<int, DetteArticle>
     */
    #[ORM\OneToMany(targetEntity: DetteArticle::class, mappedBy: 'article')]
    private Collection $detteArticles;

    /**
     * @var Collection<int, DemandeArticle>
     */
    #[ORM\OneToMany(targetEntity: DemandeArticle::class, mappedBy: 'article')]
    private Collection $demandeArticles;

    #[ORM\Column]
    private ?int $quantiteRestante = null;

    /**
     * @var Collection<int, Dette>
     */
    #[ORM\OneToMany(targetEntity: Dette::class, mappedBy: 'article')]
    private Collection $dettes;

    public function __construct()
    {
        $this->detteArticles = new ArrayCollection();
        $this->demandeArticles = new ArrayCollection();
        $this->dettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomArticle(): ?string
    {
        return $this->nomArticle;
    }

    public function setNomArticle(string $nomArticle): static
    {
        $this->nomArticle = $nomArticle;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }


    /**
     * @return Collection<int, DetteArticle>
     */
    public function getDetteArticles(): Collection
    {
        return $this->detteArticles;
    }

    public function addDetteArticle(DetteArticle $detteArticle): static
    {
        if (!$this->detteArticles->contains($detteArticle)) {
            $this->detteArticles->add($detteArticle);
            $detteArticle->setArticle($this);
        }

        return $this;
    }

    public function removeDetteArticle(DetteArticle $detteArticle): static
    {
        if ($this->detteArticles->removeElement($detteArticle)) {
            // set the owning side to null (unless already changed)
            if ($detteArticle->getArticle() === $this) {
                $detteArticle->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeArticle>
     */
    public function getDemandeArticles(): Collection
    {
        return $this->demandeArticles;
    }

    public function addDemandeArticle(DemandeArticle $demandeArticle): static
    {
        if (!$this->demandeArticles->contains($demandeArticle)) {
            $this->demandeArticles->add($demandeArticle);
            $demandeArticle->setArticle($this);
        }

        return $this;
    }

    public function removeDemandeArticle(DemandeArticle $demandeArticle): static
    {
        if ($this->demandeArticles->removeElement($demandeArticle)) {
            // set the owning side to null (unless already changed)
            if ($demandeArticle->getArticle() === $this) {
                $demandeArticle->setArticle(null);
            }
        }

        return $this;
    }

    public function getQuantiteRestante(): ?int
    {
        return $this->quantiteRestante;
    }

    public function setQuantiteRestante(int $quantiteRestante): static
    {
        $this->quantiteRestante = $quantiteRestante;

        return $this;
    }

    /**
     * @return Collection<int, Dette>
     */
    public function getDettes(): Collection
    {
        return $this->dettes;
    }

    public function addDette(Dette $dette): static
    {
        if (!$this->dettes->contains($dette)) {
            $this->dettes->add($dette);
            $dette->setArticle($this);
        }

        return $this;
    }

    public function removeDette(Dette $dette): static
    {
        if ($this->dettes->removeElement($dette)) {
            // set the owning side to null (unless already changed)
            if ($dette->getArticle() === $this) {
                $dette->setArticle(null);
            }
        }

        return $this;
    }

}
