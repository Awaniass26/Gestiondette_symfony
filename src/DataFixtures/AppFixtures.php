<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Article;
use App\Entity\Dette;
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

            for ($j = 1; $j <= 10; $j++) {
                $detteSoldee = new Dette();
                $detteSoldee->setDateAt(new \DateTimeImmutable());
                $detteSoldee->setMontant(rand(1000, 100000));
                $detteSoldee->setMontantVerse($detteSoldee->getMontant()); 
                $detteSoldee->setMontantRestant(0); 
            
                $detteSoldee->setClient($client);
                $detteSoldee->setArticle($manager->getRepository(Article::class)->find(rand(1, 10)));
                $manager->persist($detteSoldee);
            
                $detteNonSoldee = new Dette();
                $detteNonSoldee->setDateAt(new \DateTimeImmutable());
                $detteNonSoldee->setMontant(rand(1000, 100000));
                $montantVerse = rand(0, $detteNonSoldee->getMontant() - 1); 
                $detteNonSoldee->setMontantVerse($montantVerse);
                $detteNonSoldee->setMontantRestant($detteNonSoldee->getMontant() - $montantVerse); 
            
                $detteNonSoldee->setClient($client);
                $detteNonSoldee->setArticle($manager->getRepository(Article::class)->find(rand(1, 10)));
                $manager->persist($detteNonSoldee);
            }
            
            
            
        }

        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setNomArticle("Article" . $i);
            $article->setQuantiteRestante(rand(0, 50));
            $article->setPrix(rand(500, 20000));
            $manager->persist($article);
        }

        $manager->flush();
    }
}
