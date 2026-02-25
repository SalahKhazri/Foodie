<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/page/add/compte', name: 'app_add_compte')]
    public function add_compte_page(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'AddCompteController',
        ]);
    }

    #[Route('/add/client', name: 'add_client', methods: ['POST'])]
    public function add_client(Request $request, EntityManagerInterface $entityManager): Response{
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $sexe = $request->request->get('sexe');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $password_confirm = $request->request->get('password_confirm');

            if ($password == $password_confirm) {
                $client = new Client();
                $client->setNom($nom);
                $client->setPrenom($prenom);
                $client->setSexe($sexe);
                $client->setEmail($email);
                $client->setPassword(password_hash($password, PASSWORD_DEFAULT));

                $entityManager->persist($client);
                $entityManager->flush();

                return $this->redirectToRoute('app_home');
            }else {
                return new Response('Erreur : Aucune image reçue.');
            }

        }
        return $this->render('client/index.html.twig', []);
        //return $this->render('user_interface/add_user.html.twig', []);


    }
}
