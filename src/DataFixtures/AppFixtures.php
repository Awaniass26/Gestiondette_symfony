<?php

namespace App\DataFixtures;

use App\Entity\User;
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

            for ($j = 0; $j < 28; $j++) {
                $demande = new Demande();
                $demande->setDateAt(new \DateTimeImmutable());
                $montant = rand(1000, 100000);
                $demande->setMontant($montant);                $montantVerse = rand(0, $montant);  
                $montantRestant = $montant - $montantVerse;

                $demande->setMontantVerse($montantVerse);
                $demande->setMontantRestant($montantRestant);
            
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
                $manager->persist($demande);
            }
            
    


            for ($i = 1; $i <= 10; $i++) {
                $client = $this->getReference('client_' . $i);
    
                $user = new User();
                $user->setNom($client->getNom());
                $user->setPrenom($client->getPrenom());
                $user->setLogin(strtolower($client->getPrenom()) . '.' . strtolower($client->getNom()) . $i . '@exemple.com');
                $user->setTelephone($client->getTelephone());
                $user->setRoles(['ROLE_CLIENT']);
                $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
    
                $manager->persist($user);
                $client->setCompte($user);
            }
    
            $manager->flush(); 


            for ($i = 1; $i <= 20; $i++) { 
                $client = $this->getReference('client_' . $i);
            
                for ($j = 0; $j < 8; $j++) {
                    $detteNonSoldee = new Dette();
                    $detteNonSoldee->setDateAt(new \DateTimeImmutable());
            
                    $montantTotal = rand(1000, 100000);
                    $montantVerse = rand(0, $montantTotal - 1);
                    $detteNonSoldee->setMontant($montantTotal);
                    $detteNonSoldee->setMontantVerse($montantVerse);
                    $detteNonSoldee->setMontantRestant($montantTotal - $montantVerse);
            
                    $detteNonSoldee->setClient($client);
            
                    $nombreArticles = rand(1, 5);
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
                }
            
                for ($j = 0; $j < 4; $j++) {
                    $detteSoldee = new Dette();
                    $detteSoldee->setDateAt(new \DateTimeImmutable());
            
                    $montantTotal = rand(1000, 100000);
                    $detteSoldee->setMontant($montantTotal);
                    $detteSoldee->setMontantVerse($montantTotal);
                    $detteSoldee->setMontantRestant(0);
            
                    $detteSoldee->setClient($client);
            
                    $nombreArticles = rand(1, 5);
                    for ($k = 0; $k < $nombreArticles; $k++) {
                        $article = $articlesDisponibles[array_rand($articlesDisponibles)];
                        $quantite = rand(1, 5);
            
                        $detteArticle = new DetteArticle();
                        $detteArticle->setDette($detteSoldee);
                        $detteArticle->setArticle($article);
                        $detteArticle->setQuantite($quantite);
            
                        $manager->persist($detteArticle);
                    }
            
                    $manager->persist($detteSoldee);
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
                
                
            $roles = ['ROLE_CLIENT', 'ROLE_ADMIN', 'ROLE_BOUTIQUIER'];
                foreach ($roles as $role) {
                    for ($i = 1; $i <= 8; $i++) {
                        $user = new User();
                        $user->setNom('Nom' . $role . $i);
                        $user->setPrenom('Prenom' . $role . $i);
        
                        $user->setLogin(uniqid('user_') . '@exemple.com');
                        $user->setAdresse("Adresse" . $i);
                        $user->setTelephone('77' . str_pad($i, 7, '0', STR_PAD_LEFT));
                        $user->setRoles([$role]);                        
                        $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
                        $user->setPhoto('uploads/photos/tree.jpg');

        
                        $manager->persist($user);
                    }
                }
        
                $manager->flush();
                           
            }
        }