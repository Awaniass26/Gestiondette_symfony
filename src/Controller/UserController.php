<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/admin/user/{page}', name: 'app_user', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function index(UserRepository $userRepository, int $page): Response
    {
        $limit = 6;  
        $offset = ($page - 1) * $limit;  

        $users = $userRepository->findBy([], null, $limit, $offset);

        $totalUsers = count($userRepository->findAll());
        $totalPages = ceil($totalUsers / $limit);  

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }



    #[Route('/admin/user/create', name: 'app_create_user', methods: ['POST'])]
public function createUser(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = new User();
    $user->setPrenom($request->request->get('Prenom'))
         ->setNom($request->request->get('Nom'))
         ->setAdresse($request->request->get('Adresse'))
         ->setLogin($request->request->get('Login'))
         ->setPassword($request->request->get('Password'));

    // Récupération des rôles, ou 'ROLE_CLIENT' par défaut si aucun rôle n'est spécifié
    $roles = $request->request->get('roleInput'); // Utilisez 'roleInput' ici, pas 'roles'
    if (!$roles) {
        $roles = ['ROLE_CLIENT'];
    }
    $user->setRoles($roles);

    $file = $request->files->get('fileInput');
    if ($file instanceof UploadedFile) {
        // Définir le chemin d'enregistrement et déplacer le fichier
        $uploadsDir = $this->getParameter('uploads_directory'); // Définir le paramètre dans services.yaml
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadsDir, $filename);

        // Sauvegarder le nom du fichier dans l'entité utilisateur
        $user->setPhoto($filename);
    }

    $telephone = $request->request->get('Tel');
    if ($telephone !== null) {
        $user->setTelephone($telephone);
    }

    $entityManager->persist($user);
    $entityManager->flush();

    return $this->redirectToRoute('app_user');
}

#[Route('/admin/user/{id}/edit', name: 'app_edit_user', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
public function editUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, $id = null): Response
{
    // Si on modifie un utilisateur existant, on récupère l'utilisateur par son id
    $user = $id ? $userRepository->find($id) : new User();

    if ($request->isMethod('POST')) {
        // On met à jour l'utilisateur
        $user->setPrenom($request->request->get('Prenom'))
             ->setNom($request->request->get('Nom'))
             ->setAdresse($request->request->get('Adresse'))
             ->setLogin($request->request->get('Login'))
             ->setPassword($request->request->get('Password'));

        // Récupération des rôles
        $roles = $request->request->get('roleInput');
        if (!$roles) {
            $roles = ['ROLE_CLIENT'];
        }
        $user->setRoles($roles);

        $telephone = $request->request->get('Tel');
        if ($telephone !== null) {
            $user->setTelephone($telephone);
        }

        // Si un fichier est uploadé, on le gère ici
        $file = $request->files->get('fileInput');
        if ($file) {
            $uploadsDir = $this->getParameter('uploads_directory');
            $filename = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadsDir, $filename);
            $user->setPhoto($filename);
        }

        // Enregistrer ou modifier dans la base
        $entityManager->persist($user);
        $entityManager->flush();

        // Rediriger vers la liste des utilisateurs
        return $this->redirectToRoute('app_user');
    }
    dd('isEdit');
    // On envoie l'utilisateur au formulaire pour modification ou ajout
    return $this->render('user/index.html.twig', [
        'user' => $user, // On passe l'utilisateur pour remplir les champs en cas de modification
        'isEdit' => $id !== null, // Si id est passé, c'est un formulaire de modification
    ]);
}



   
}

