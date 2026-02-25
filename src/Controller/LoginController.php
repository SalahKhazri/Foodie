<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route('/login/client', name: 'app_login_client')]
    public function login_client(): Response
    {
        return $this->render('login/login_client.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route('/login/admin', name: 'app_login_admin')]
    public function compte(): Response
    {
        return $this->render('login/login_admin.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route('/add_compte', name: 'login_process')]
    public function login(Request $request): Response{
            return $this->render('login/index.html.twig', [
                'error' => 'Tous les champs doivent être remplis.'
            ]);


    }

    #[Route('/admin_iterface', name: 'login_admin', methods: ['POST'])]
    public function admin_login(Request $request,EntityManagerInterface $entityManager): Response{
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);
        if($user){
            if(password_verify($request->request->get('password'), $user->getPassword())){
                return $this->redirectToRoute('app_home');
            }
            else{
                return $this->redirectToRoute('login_admin');
            }
        }
        return $this->render('login/login_admin.html.twig', []);

    }
}
