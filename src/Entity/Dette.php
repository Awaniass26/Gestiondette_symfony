<?php

namespace App\Entity;

use App\Repository\DetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetteRepository::class)]
class Dette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?float $montantVerse = null;

    #[ORM\Column]
    private ?float $montantRestant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAt = null;

    #[ORM\ManyToOne(inversedBy: 'dettes')]
    private ?Client $client = null;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'dette')]
    private Collection $paiements;

    /**
     * @var Collection<int, DetteArticle>
     */
    #[ORM\OneToMany(targetEntity: DetteArticle::class, mappedBy: 'dette', cascade: ['persist'])]
    private Collection $detteArticles;    

    #[ORM\ManyToOne(inversedBy: 'dettes')]
    private ?Article $article = null;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->detteArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantVerse(): ?float
    {
        return $this->montantVerse;
    }

    public function setMontantVerse(float $montantVerse): self
{
    $this->montantVerse = $montantVerse;
    $this->montantRestant = $this->montant - $montantVerse; 
    return $this;
}


    public function getMontantRestant(): ?float
    {
        return $this->montantRestant;
    }

    public function setMontantRestant(float $montantRestant): static
    {
        $this->montantRestant = $montantRestant;

        return $this;
    }

    public function getDateAt(): ?\DateTimeImmutable
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeImmutable $dateAt): static
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setDette($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getDette() === $this) {
                $paiement->setDette(null);
            }
        }

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
            $detteArticle->setDette($this);
        }

        return $this;
    }

    public function removeDetteArticle(DetteArticle $detteArticle): static
    {
        if ($this->detteArticles->removeElement($detteArticle)) {
            // set the owning side to null (unless already changed)
            if ($detteArticle->getDette() === $this) {
                $detteArticle->setDette(null);
            }
        }

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function addArticle(Article $article): static
{
    $detteArticle = new DetteArticle();
    $detteArticle->setDette($this);
    $detteArticle->setArticle($article);

    $this->addDetteArticle($detteArticle);

    return $this;
}


}
