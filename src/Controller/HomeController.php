<?php

namespace App\Controller;

use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/menu/interface', name: 'app_menu_interface')]
    public function afficher_menu(EntityManagerInterface $entityManager): Response{
        $statut = $entityManager->getRepository(Menu::class)->findBy(['statut' => 'Plat Principale']);
        foreach ($statut as $menu) {
            if ($menu->getImage()) {
                $menu->base64Image = 'data:image/jpeg;base64,' . base64_encode(stream_get_contents($menu->getImage()));
            } else {
                $menu->base64Image = null;
            }

        }
        $types = ["Plat Principale", "Plat d'entrée", "Dessert", "Boisson", "Plat du Jour"];
        $plat = ["tacos", "pattes"];
        return $this->render('home/MenuInterface.html.twig', [
            'plat' => $plat,
            'types' => $types,
            'statut' => $statut,
        ]);
    }


}
