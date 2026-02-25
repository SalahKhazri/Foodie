<?php

namespace App\Controller;

use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //$menus = $entityManager->getRepository(Menu::class)->findBy(['statut' => 'Menu Pricipale']);
        $menus = $entityManager->getRepository(Menu::class)->findAll();
        foreach ($menus as $menu) {
            if ($menu->getImage()) {
                $menu->base64Image = 'data:image/jpeg;base64,' . base64_encode(stream_get_contents($menu->getImage()));
            } else {
                $menu->base64Image = null;
            }

        }

        return $this->render('menu/index.html.twig', [
            'menus' => $menus
        ]);
    }
    #[Route('/menu/add', name: 'app_menu_add')]
    public function menu_add(): Response
    {
        return $this->render('menu/add_menu.html.twig');
    }

    #[Route('/add/menu', name: 'add_menu', methods: ['POST', 'GET'])]
    public function add_menu(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $prix = $request->request->get('prix');
            $type_de_plat = $request->request->get('type_plat');
            $statut = $request->request->get('statut');
            $imageFile = $request->files->get('image');

            if ($imageFile) {
                $imageData = file_get_contents($imageFile->getPathname()); // Lire l'image en binaire

                $menu = new Menu();
                $menu->setNom($nom);
                $menu->setDescription($description);
                $menu->setPrix($prix);

                $menu->setImage($imageData);
                $menu->setStatut($statut);
                $menu->setTypeDePlat($type_de_plat);

                $entityManager->persist($menu);
                $entityManager->flush();

//                return new Response('Image enregistrée avec succès dans la base de données !');
                return $this->redirectToRoute('app_menu_list');
            }else {
                return new Response('Erreur : Aucune image reçue.');
            }

        }
        return $this->render('menu/add_menu.html.twig', []);
    }

    #[Route('/offre/list', name: 'app_offre_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $menus = $entityManager->getRepository(Menu::class)->findBy(['statut' => 'Offre']);
        foreach ($menus as $menu) {
            if ($menu->getImage()) {
                $menu->base64Image = 'data:image/jpeg;base64,' . base64_encode(stream_get_contents($menu->getImage()));
            } else {
                $menu->base64Image = null;
            }

        }

        return $this->render('offre/index.html.twig', [
            'menus' => $menus
        ]);
    }

    #[Route('/menu/page/edit/{id}', name:'page_edit', methods: ['GET'])]
    public function page_edit(int $id, EntityManagerInterface $entityManager): Response
    {
        $menu = $entityManager->getRepository(Menu::class)->find($id);
        return $this->render('menu/update_menu.html.twig', [
        'menu' => $menu
        ]);
    }

    #[Route('/menu/edit/{id}', name: 'app_menu_edit', methods: ['POST'])]
    public function menu_edit(Request $request, EntityManagerInterface $entityManager, int $id): Response{
        $menu = $entityManager->getRepository(Menu::class)->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Le plat demandé n\'existe pas');
        }

        if ($request->isMethod('POST')) {
            $menu->setNom($request->request->get('nom'));
            $menu->setDescription($request->request->get('description'));
            $menu->setPrix($request->request->get('prix'));
            $menu->setTypeDePlat($request->request->get('type_plat'));
            $menu->setStatut($request->request->get('statut'));

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $imageData = file_get_contents($imageFile->getPathname());
                $menu->setImage($imageData);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_menu_list');
        }

        return $this->render('menu/update_menu.html.twig', [
            'menu' => $menu
        ]);
    }

    #[Route('/menu/delete/{id}', name: 'menu_delete', methods: ['GET'])]
    public function menu_delete(int $id, EntityManagerInterface $entityManager): Response{
        $menu = $entityManager->getRepository(Menu::class)->find($id);
        if (!$menu) {
            throw $this->createNotFoundException('Le plat demandé n\'existe pas');
        }
        $entityManager->remove($menu);
        $entityManager->flush();
        return $this->redirectToRoute('app_menu_list');
    }
}
