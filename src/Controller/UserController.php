<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

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


    #[Route('/admin/user/create', name: 'app_user_create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('prenom', TextType::class)
            ->add('nom', TextType::class)
            ->add('telephone', TextType::class)
            ->add('login', TextType::class)
            ->add('password', PasswordType::class)
            ->add('role', TextType::class) // Ou un select pour les rôles
            ->add('save', SubmitType::class, ['label' => 'Create'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_user_edit')]
    public function edit($id, UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->find($id);
        $form = $this->createFormBuilder($user)
            ->add('prenom', TextType::class)
            ->add('nom', TextType::class)
            ->add('telephone', TextType::class)
            ->add('login', TextType::class)
            ->add('password', PasswordType::class)
            ->add('role', TextType::class) // Ou un select pour les rôles
            ->add('save', SubmitType::class, ['label' => 'Update'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

