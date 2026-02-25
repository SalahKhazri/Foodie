<?php

namespace App\Controller;

use App\Entity\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TableInterfaceController extends AbstractController
{
    #[Route('/table/interface', name: 'app_table_interface')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $table = $entityManager->getRepository(Table::class)->findAll();
        return $this->render('table_interface/index.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/table/add', name: 'app_table_add')]
    public function add(): Response{

        return $this->render('table_interface/add_table.html.twig', []);
    }

    #[Route('/add/table', name: 'add_table', methods: ['POST'])]
    public function addTable(Request $request, EntityManagerInterface $entityManager): Response{
        if($request->isMethod('POST')){
            $nomTable = $request->request->get('nom');
            $nbrPlace = $request->request->get('nbr');
            $statut = $request->request->get('statut');
            $table = new Table();
            $table->setNom($nomTable);
            $table->setNbrPlace($nbrPlace);
            $table->setStatut($statut);
            $entityManager->persist($table);
            $entityManager->flush();
            return $this->redirectToRoute('app_table_interface');

        }
        return $this->render('table_interface/add_table.html.twig', []);
    }

    #[Route('/modifier/table/{id}', name: 'modifier_table', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function modifierTable(Request $request, EntityManagerInterface $entityManager, int $id): Response{
        $table = $entityManager->getRepository(Table::class)->find($id);
        return $this->render('table_interface/update_table.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/update/table/{id}', name: 'update_table', methods: ['POST'])]
    public function updateTable(Request $request, EntityManagerInterface $entityManager, int $id): Response{
        $table = $entityManager->getRepository(Table::class)->find($id);
        if($request->isMethod('POST')){
            $table->setNom($request->request->get('nom'));
            $table->setNbrPlace($request->request->get('nbr'));
            $table->setStatut($request->request->get('statut'));
            $entityManager->persist($table);
            $entityManager->flush();
            return $this->redirectToRoute('app_table_interface');
        }
        return $this->render('table_interface/update_table.html.twig', []);
    }

    #[Route('/delete/table/{id}', name: 'delete_table', methods: ['GET'])]
    public function deleteTable(Request $request, EntityManagerInterface $entityManager, int $id): Response{
        $table = $entityManager->getRepository(Table::class)->find($id);
        $entityManager->remove($table);
        $entityManager->flush();
        return $this->redirectToRoute('app_table_interface');
    }
}
