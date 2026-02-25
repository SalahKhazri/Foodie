<?php

namespace App\Controller;


use App\Entity\Offre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OffreController extends AbstractController
{
    #[Route('/offre', name: 'app_offre_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //$menus = $entityManager->getRepository(Menu::class)->findBy(['statut' => 'Menu Pricipale']);
        $offre = $entityManager->getRepository(Offre::class)->findAll();
        foreach ($offre as $menu) {
            if ($menu->getImage()) {
                $menu->base64Image = 'data:image/jpeg;base64,' . base64_encode(stream_get_contents($menu->getImage()));
            } else {
                $menu->base64Image = null;
            }

        }

        return $this->render('offre/index.html.twig', [
            'offre' => $offre
        ]);
    }
    #[Route('/offre/add', name: 'app_offre_add')]
    public function offre_add(): Response
    {
        return $this->render('offre/add_offre.html.twig');
    }

    #[Route('/add/offre', name: 'add_offre', methods: ['POST', 'GET'])]
    public function add_offre(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $prix = $request->request->get('prix');

            $imageFile = $request->files->get('image');

            if ($imageFile) {
                $imageData = file_get_contents($imageFile->getPathname()); // Lire l'image en binaire

                $offre = new Offre();
                $offre->setNom($nom);
                $offre->setDescription($description);
                $offre->setPrix($prix);

                $offre->setImage($imageData);


                $entityManager->persist($offre);
                $entityManager->flush();

//                return new Response('Image enregistrée avec succès dans la base de données !');
                return $this->redirectToRoute('app_offre_list');
            }else {
                return new Response('Erreur : Aucune image reçue.');
            }

        }
        return $this->render('offre/add_offre.html.twig', []);
    }



    #[Route('/offre/page/edit/{id}', name:'page_edit_offre', methods: ['GET'])]
    public function page_edit(int $id, EntityManagerInterface $entityManager): Response
    {
        $offre = $entityManager->getRepository(Offre::class)->find($id);
        return $this->render('offre/update_offre.html.twig', [
            'offre' => $offre
        ]);
    }

    #[Route('/offre/edit/{id}', name: 'app_offre_edit', methods: ['POST'])]
    public function menu_edit(Request $request, EntityManagerInterface $entityManager, int $id): Response{
        $menu = $entityManager->getRepository(Offre::class)->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Le plat demandé n\'existe pas');
        }

        if ($request->isMethod('POST')) {
            $menu->setNom($request->request->get('nom'));
            $menu->setDescription($request->request->get('description'));
            $menu->setPrix($request->request->get('prix'));

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $imageData = file_get_contents($imageFile->getPathname());
                $menu->setImage($imageData);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_offre_list');
        }

        return $this->render('offre/update_offre.html.twig', [
            'menu' => $menu
        ]);
    }

    #[Route('/offre/delete/{id}', name: 'offre_delete', methods: ['GET'])]
    public function menu_delete(int $id, EntityManagerInterface $entityManager): Response{
        $menu = $entityManager->getRepository(Offre::class)->find($id);
        if (!$menu) {
            throw $this->createNotFoundException('Le plat demandé n\'existe pas');
        }
        $entityManager->remove($menu);
        $entityManager->flush();
        return $this->redirectToRoute('app_offre_list');
    }
}
