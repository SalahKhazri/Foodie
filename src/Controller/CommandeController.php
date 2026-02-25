<?php

namespace App\Controller;

use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/commande/interface', name: 'app_commande')]
    public function index(EntityManagerInterface $entityManager): Response
    {
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
        return $this->render('commande/index.html.twig', [
            'plat' => $plat,
            'types' => $types,
            'statut' => $statut,
        ]);
    }

    #[Route('/commande', name: 'commande')]
    public function commander(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $menusSelectionnes = $request->request->get('menus', []);
            $quantites = $request->request->get('quantites', []); // 🔥 Correction ici

            if (!is_array($quantites)) {
                $quantites = [];
            }

            if (empty($menusSelectionnes)) {
                $this->addFlash('error', 'Veuillez sélectionner au moins un plat.');
                return $this->redirectToRoute('commande');
            }

            $commande = new Commande();
            $quantitesFinales = [];
            $total = 0;

            foreach ($menusSelectionnes as $menuId) {
                $menu = $entityManager->getRepository(Menu::class)->find($menuId);
                if (!$menu) {
                    continue; // Ignore les menus non trouvés
                }

                $commande->getMenus()->add($menu);
                $quantite = isset($quantites[$menuId]) ? (int) $quantites[$menuId] : 1;
                $quantitesFinales[$menuId] = $quantite;

                // Calculer le total
                $total += $menu->getPrix() * $quantite;
            }

            // Stocker les quantités et le total
            $commande->setQuantites($quantitesFinales);
            $commande->setTotal($total);

            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commande a été enregistrée avec succès ! Montant total : ' . $total . ' DH');

            return $this->redirectToRoute('commande');
        }

        $menus = $entityManager->getRepository(Menu::class)->findAll();

        return $this->render('commande/index.html.twig', [
            'menus' => $menus
        ]);
    }
}
