<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Article;
use App\Entity\Dette;
use App\Entity\Paiement;
use App\Entity\Demande; 
use App\Entity\DetteArticle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\DemandeArticle;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $photoDirectory = __DIR__ . '/../../public/uploads/photos';
        $defaultPhotoPath = $photoDirectory . '/default.jpg';

        if (!file_exists($photoDirectory)) {
            mkdir($photoDirectory, 0777, true);
        }

        $articlesDisponibles = [];
$articlesRuptureDeStock = [];


for ($i = 1; $i <= 5; $i++) {
    $article = new Article();
    $article->setNomArticle("Article" . $i);
    $article->setQuantiteRestante(0); 
    $article->setPrix(rand(500, 20000));

    $manager->persist($article);

    $articlesRuptureDeStock[] = $article;
}

for ($i = 1; $i <= 15; $i++) {
    $article = new Article();
    $article->setNomArticle("Article" . $i);
    $article->setQuantiteRestante(rand(1, 10)); 
    $article->setPrix(rand(500, 20000));

    $manager->persist($article);

    $articlesDisponibles[] = $article;

    $this->addReference('article_' . $i, $article);

}

        $manager->flush(); 

        for ($i = 1; $i <= 20; $i++) {
            $client = new Client();
            $client->setNom("Nom" . $i);
            $client->setPrenom("Prenom" . $i);
            $client->setAdresse("Adresse" . $i);
            $client->setTelephone("77" . str_pad($i, 7, '0', STR_PAD_LEFT));
            $client->setMontantDette(rand(10000, 500000));

            $email = strtolower("prenom{$i}.nom{$i}@exemple.com");
            $client->setEmail($email);

            $photoName = 'client_' . $i . '.jpg';
            $photoPath = $photoDirectory . '/' . $photoName;

            if (!file_exists($photoPath)) {
                copy($defaultPhotoPath, $photoPath);
            }

            $client->setPhoto('uploads/photos/' . $photoName);

            $manager->persist($client);
            $this->addReference('client_' . $i, $client);
        }

        $manager->flush();  


            for ($j = 0; $j < 10; $j++) {

                $detteNonSoldee = new Dette();
                $detteNonSoldee->setDateAt(new \DateTimeImmutable());
                $montantDette = rand(1000, 100000);
                $detteNonSoldee->setMontant($montantDette);
                $montantVerse = rand(0, $montantDette - 1);
                $detteNonSoldee->setMontantVerse($montantVerse);
                $detteNonSoldee->setMontantRestant($montantDette - $montantVerse);

                $detteNonSoldee->setClient($client);

                $nombreArticles = rand(1, 20); 
                for ($k = 0; $k < $nombreArticles; $k++) {
                    $article = $articlesDisponibles[array_rand($articlesDisponibles)];
                    $quantite = rand(1, 5);
                
                    $detteArticle = new DetteArticle();
                    $detteArticle->setDette($detteNonSoldee);
                    $detteArticle->setArticle($article);
                    $detteArticle->setQuantite($quantite);
                
                    $manager->persist($detteArticle);
                }               
                    $manager->persist($detteNonSoldee);

                for ($k = 1; $k <= 10; $k++) {
                    $paiementNonSoldee = new Paiement();
                    $paiementNonSoldee->setDateAt(new \DateTimeImmutable());
                    $montantPaiement = rand(100, $detteNonSoldee->getMontantRestant());
                    $paiementNonSoldee->setMontant($montantPaiement);
                    $paiementNonSoldee->setDette($detteNonSoldee);
                    $manager->persist($paiementNonSoldee);
                }
            }

            for ($j = 10; $j < 20; $j++) {
                $detteSoldee = new Dette();
                $detteSoldee->setDateAt(new \DateTimeImmutable());
                $montantTotal = rand(1000, 100000);
                $detteSoldee->setMontant($montantTotal);
                $detteSoldee->setMontantVerse($montantTotal); 
                $detteSoldee->setMontantRestant(0); 
                $detteSoldee->setClient($client);

                $article = $articlesDisponibles[array_rand($articlesDisponibles)];
                $detteSoldee->setArticle($article);

                $manager->persist($detteSoldee);

                for ($k = 0; $k < 10; $k++) {
                    $paiement = new Paiement();
                    $paiement->setDateAt(new \DateTimeImmutable());
                    $montantPaiement = $montantTotal / 3; 
                    $paiement->setMontant($montantPaiement);
                    $paiement->setDette($detteSoldee);

                    $manager->persist($paiement);
                }
            }


            for ($j = 0; $j < 28; $j++) {
                $demande = new Demande();
                $demande->setDateAt(new \DateTimeImmutable());
                $demande->setMontant(rand(1000, 100000));
            
                $statut = rand(0, 2);
                $statutDemande = $statut == 0 ? 'En cours' : ($statut == 1 ? 'Annulé' : 'Accepté');
                $demande->setStatut($statutDemande);
            
                $client = $this->getReference('client_' . rand(1, 20));
                $nomComplet = $client->getNom() . ' ' . $client->getPrenom();
                $demande->setNomComplet($nomComplet);
                $demande->setTelephone($client->getTelephone());
                $demande->setClient($client);
    
                for ($k = 0; $k < 15; $k++) {
                    $demandeArticle = new DemandeArticle();
                    $demandeArticle->setDemande($demande);
                    $demandeArticle->setArticle($articlesDisponibles[array_rand($articlesDisponibles)]);
                    $demandeArticle->setQuantite(rand(1, 10));
    
                    $manager->persist($demandeArticle);
                }
            
        }

        $manager->flush(); 
    }
}
