<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'app_categories_index', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route(path: '/categories_ajax', name: 'app_categories_ajax', methods: ['GET', 'POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder(['id', 'url', 'name', 'summary']);
        $dataTable->setColumnSearch(['id', 'url', 'name', 'summary']);
        $dataTable->setTable('categories');
        $dataTable->setQuery('select * from categories');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $no = 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = $value['url'];
            $row[] = $value['name'];
            $row[] = $value['summary'];
            $row[] = "<a href='".$this->generateUrl('app_categories_edit', ['id' => $value['id']])."' class='btn btn-info btn-sm'>edit</a>";
            $data[] = $row;
            $no++;
        }
        $output = [
            "draw" => 0,
            "recordsTotal" => $queryResult['count'],
            "recordsFiltered" => $queryResult['filter'],
            "data" => $data,
        ];
        return $this->json($output);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Data Berhasil Di Simpan');
            return $this->redirectToRoute('app_categories_edit', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Data Berhasil Di Simpan');
            return $this->redirectToRoute('app_categories_edit', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
    }
}
