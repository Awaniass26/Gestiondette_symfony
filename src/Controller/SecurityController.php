<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\ClientRepository;


class SecurityController extends AbstractController
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }


    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $userConnect=$this->getUser();
        if ($userConnect) {
            $roles=$userConnect-> getRoles();
            if (in_array('ROLE_ADMIN',$roles)) {
                // dd("ADMIN");
                return $this->redirectToRoute('app_dashboard');
            }elseif (in_array('ROLE_BOUTIQUIER', $roles)) {
                // dd("BOUTIQUIER");
                return $this->redirectToRoute('app_client'); 
            } elseif (in_array('ROLE_CLIENT', $roles)) {
                if ($userConnect) {
                    $client = $this->clientRepository->findOneBy(['compte' => $userConnect]); // Associez le client à l'utilisateur
                }
                $telephone = $client->getTelephone();
                return $this->redirectToRoute('app_dettelist', [
                    'telephone' => $telephone,
                ]);
                            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
