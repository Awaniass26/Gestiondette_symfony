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

    $roles = $request->request->get('roleInput');
    if (!$roles) {
        $roles = ['ROLE_CLIENT'];
    }
    $user->setRoles($roles);

    $file = $request->files->get('fileInput');
    if ($file instanceof UploadedFile) {
        $uploadsDir = $this->getParameter('uploads_directory'); 
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadsDir, $filename);

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
    $user = $id ? $userRepository->find($id) : new User();

    if ($request->isMethod('POST')) {
        $user->setPrenom($request->request->get('Prenom'))
             ->setNom($request->request->get('Nom'))
             ->setAdresse($request->request->get('Adresse'))
             ->setLogin($request->request->get('Login'))
             ->setPassword($request->request->get('Password'));

        $roles = $request->request->get('roleInput');
        if (!$roles) {
            $roles = ['ROLE_CLIENT'];
        }
        $user->setRoles($roles);

        $telephone = $request->request->get('Tel');
        if ($telephone !== null) {
            $user->setTelephone($telephone);
        }

        $file = $request->files->get('fileInput');
        if ($file) {
            $uploadsDir = $this->getParameter('uploads_directory');
            $filename = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadsDir, $filename);
            $user->setPhoto($filename);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user');
    }
    // dd('isEdit');
    return $this->render('user/index.html.twig', [
        'user' => $user, 
        'isEdit' => $id !== null, 
    ]);
}



   
}

