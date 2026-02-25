<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminInterfaceController extends AbstractController
{
    #[Route('/admin/interface', name: 'app_admin_interface')]
    public function index(): Response
    {
        return $this->render('admin_interface/index.html.twig', [
            'controller_name' => 'AdminInterfaceController',
        ]);
    }

    #[Route('/user', name: 'user_interface')]
    public function user(): Response{
            return $this->render('user_interface/index.html.twig', []);
        }
}
