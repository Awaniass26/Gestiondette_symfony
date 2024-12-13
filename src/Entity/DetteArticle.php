<?php

namespace App\Entity;

use App\Repository\DetteArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetteArticleRepository::class)]
class DetteArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detteArticles')]
    private ?Dette $dette = null;

    #[ORM\ManyToOne(inversedBy: 'detteArticles')]
    private ?Article $article = null;

    #[ORM\Column]
    private ?float $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDette(): ?Dette
    {
        return $this->dette;
    }

    public function setDette(?Dette $dette): static
    {
        $this->dette = $dette;

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

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }
}
