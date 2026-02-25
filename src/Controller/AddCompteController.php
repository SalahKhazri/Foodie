<?php

namespace App\Controller;

use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddCompteController extends AbstractController
{
    #[Route('/add/compte', name: 'app_add_compte')]
    public function index(): Response
    {
        return $this->render('add_compte/index.html.twig', [
            'controller_name' => 'AddCompteController',
        ]);
    }

    #[Route('/add/compte', name: 'add_client',)]
    public function add_client(Request $request): Response{
        $dsn = 'mysql:host=localhost;dbname=foodie';
        $username = 'root';
        $passwd = '';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $sexe = $request->request->get('sexe');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $password_confirm = $request->request->get('password_confirm');
        if (empty($nom) || empty($prenom) || empty($sexe) || empty($email) || empty($password) || empty($password_confirm)) {

            return $this->render('add_compte/index.html.twig', [
                'error' => 'Tous les champs doivent être remplis.'
            ]);
        }

        try {
            if($password === $password_confirm){
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $pdo = new PDO($dsn, $username, $passwd, $options);
                $stmt = $pdo->prepare('INSERT INTO client (nom, prenom, sexe, email, password) VALUES (:nom, :prenom, :sexe, :email, :password)');
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':sexe', $sexe);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);

                $stmt->execute();

            }

        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
        return $this->redirectToRoute('app_home');


    }

//    #[Route('/add/user', name: 'add_user',)]
//    public function add_user(Request $request): Response{
//        $dsn = 'mysql:host=localhost;dbname=foodie';
//        $username = 'root';
//        $passwd = '';
//        $options = [
//            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//        ];
//        $nom = $request->request->get('nom');
//        $prenom = $request->request->get('prenom');
//        $sexe = $request->request->get('sexe');
//        $email = $request->request->get('email');
//        $poste = $request->request->get('poste');
//        $cin = $request->request->get('cin');
//        $telephone = $request->request->get('telephone');
//        $adresse = $request->request->get('adresse');
//        $password = $request->request->get('password');
//        $password_confirm = $request->request->get('password_confirm');
//        if (empty($nom) || empty($prenom) || empty($sexe) || empty($email) || empty($poste) || empty($cin) || empty($telephone) || empty($adresse) || empty($password) || empty($password_confirm)) {
//
//            return $this->render('add_compte/add_user.html.twig', [
//                'error' => 'Tous les champs doivent être remplis.'
//            ]);
//        }
//
//        try {
//            if($password === $password_confirm){
//                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
//
//                $pdo = new PDO($dsn, $username, $passwd, $options);
//                $stmt = $pdo->prepare('INSERT INTO user (nom, prenom, sexe, email, password, poste, cin, telephone, adresse) VALUES (:nom, :prenom, :sexe, :email, :password, :poste, :cin, :telephone, :adresse)');
//                $stmt->bindParam(':nom', $nom);
//                $stmt->bindParam(':prenom', $prenom);
//                $stmt->bindParam(':sexe', $sexe);
//                $stmt->bindParam(':email', $email);
//                $stmt->bindParam(':password', $hashedPassword);
//                $stmt->bindParam(':poste', $poste);
//                $stmt->bindParam(':cin', $cin);
//                $stmt->bindParam(':telephone', $telephone);
//                $stmt->bindParam(':adresse', $adresse);
//                $stmt->execute();
//            }
//
//        } catch (PDOException $e) {
//            die('Erreur de connexion : ' . $e->getMessage());
//        }
//        return $this->redirectToRoute('app_home');
//
//
//    }
}
