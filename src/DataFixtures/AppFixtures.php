<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Article;
use App\Entity\Dette;
use App\Entity\Paiement;
use App\Entity\DetteArticle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
    $article->setQuantiteRestante(0); // En rupture
    $article->setPrix(rand(500, 20000));

    $manager->persist($article);

    $articlesRuptureDeStock[] = $article;
}

// Articles disponibles
for ($i = 1; $i <= 15; $i++) {
    $article = new Article();
    $article->setNomArticle("Article" . $i);
    $article->setQuantiteRestante(rand(1, 10)); // Disponible
    $article->setPrix(rand(500, 20000));

    $manager->persist($article);

    $articlesDisponibles[] = $article;
}

        $manager->flush(); 

        for ($i = 1; $i <= 20; $i++) {
            $client = new Client();
            $client->setNom("Nom" . $i);
            $client->setPrenom("Prenom" . $i);
            $client->setAdresse("adresses" . $i);
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
        }

        $manager->flush(); 
    }
}
