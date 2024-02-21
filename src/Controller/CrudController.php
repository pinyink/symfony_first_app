<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\Crud1Type;
use App\Repository\CrudRepository;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/crud')]
class CrudController extends AbstractController
{
    #[Route('/', name: 'app_crud_index', methods: ['GET'])]
    public function index(CrudRepository $crudRepository): Response
    {
        return $this->render('crud/index.html.twig', [
            'cruds' => $crudRepository->findAll(),
        ]);
    }

    #[Route(path: '/crud_ajax', name: 'app_crud_ajax', methods: ['GET', 'POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder(['id', 'entity_nama', 'form_name', 'route_name']);
        $dataTable->setColumnSearch(['id', 'entity_nama', 'form_name', 'route_name']);
        $dataTable->setTable('crud');
        $dataTable->setQuery('select * from crud');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $value['id'];
            $row[] = $value['entity_name'];
            $row[] = $value['form_name'];
            $row[] = $value['route_name'];
            $row[] = "<a href='".$this->generateUrl('app_crud_edit', ['id' => $value['id']])."' class='btn btn-info btn-sm'>edit</a>";
            $data[] = $row;
        }
        $output = [
            "draw" => 0,
            "recordsTotal" => $queryResult['count'],
            "recordsFiltered" => $queryResult['filter'],
            "data" => $data,
        ];
        return $this->json($output);
    }

    #[Route('/new', name: 'app_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $crud = new Crud();
        $form = $this->createForm(Crud1Type::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($crud);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Berhasil');
            return $this->redirectToRoute('app_crud_edit', ['id' => $crud->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud/new.html.twig', [
            'crud' => $crud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_show', methods: ['GET'])]
    public function show(Crud $crud): Response
    {
        return $this->render('crud/show.html.twig', [
            'crud' => $crud,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Crud $crud, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Crud1Type::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Berhasil');
        }

        return $this->render('crud/edit.html.twig', [
            'crud' => $crud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Crud $crud, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$crud->getId(), $request->request->get('_token'))) {
            $entityManager->remove($crud);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
