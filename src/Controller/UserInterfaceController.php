<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserInterfaceController extends AbstractController
{
    #[Route('/user/interface', name: 'app_user_interface')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('user_interface/index.html.twig', [
            'controller_name' => 'UserInterfaceController',
            'users' => $users

        ]);
    }

    #[Route('/user/add', name: 'app_user_add')]
    public function add(Request $request): Response{
        return $this->render('user_interface/add_user.html.twig', []);
    }

    #[Route('/add/user', name: 'add_user',)]
    public function add_user(Request $request, EntityManagerInterface $entityManager): Response{
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $sexe = $request->request->get('sexe');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $password_confirm = $request->request->get('password_confirm');
            $poste = $request->request->get('poste');
            $cin = $request->request->get('cin');
            $telephone = $request->request->get('telephone');
            $adresse = $request->request->get('adresse');

            if ($password == $password_confirm) {
                $user = new User();
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setSexe($sexe);
                $user->setEmail($email);
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
                $user->setPoste($poste);
                $user->setCin($cin);
                $user->setTelephone($telephone);
                $user->setAdresse($adresse);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_user_interface');
            }else {
                return new Response('Erreur : Aucune image reçue.');
            }

        }
        return $this->render('user_interface/add_user.html.twig', []);
        //return $this->render('user_interface/add_user.html.twig', []);


    }

    #[Route('/modifier/user/{id}', name: 'update_user', requirements: ['id' => '\d+'])]
    public function update_user(EntityManagerInterface $entityManager, int $id): Response{
        $user = $entityManager->getRepository(User::class)->find($id);
        return $this->render('user_interface/update.html.twig', [
            'controller_name' => 'UserInterfaceController',
            'user' => $user
        ]);
    }

    #[Route('/update/user/{id}', name: 'modifier_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function modifier_user(Request $request, EntityManagerInterface $entityManager, int $id): Response{
        $users = $entityManager->getRepository(User::class)->find($id);
        if(!$users){
            throw $this->createNotFoundException("L'utilisateur n'existe pas.");
        }
        if ($request->isMethod('POST') && $request->request->get('password') == $request->request->get('password_confirm')) {

            $users->setNom($request->request->get('nom'));
            $users->setPrenom($request->request->get('prenom'));
            $users->setSexe($request->request->get('sexe'));
            $users->setEmail($request->request->get('email'));
            $users->setPoste($request->request->get('poste'));
            $users->setCin($request->request->get('cin'));
            $users->setTelephone($request->request->get('telephone'));
            $users->setAdresse($request->request->get('adresse'));
            $users->setPassword(password_hash($request->request->get('password'), PASSWORD_DEFAULT));
            $entityManager->persist($users);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_interface');
        }

        return $this->render('user_interface/modifier.html.twig', []);


    }

    #[Route('/delete/user/{id}', name: 'delete_user', requirements: ['id' => '\d+'])]
    public function delete_user(Request $request,EntityManagerInterface $entityManager,  int $id): Response{
        $user = $entityManager->getRepository(User::class)->find($id);
        if(!$user){
            throw $this->createNotFoundException("L'utilisateur n'existe pas.");
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_interface');
        }
}
