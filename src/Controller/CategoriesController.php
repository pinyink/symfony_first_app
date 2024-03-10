<?php
namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return $this->render('categories/index.html.twig', [
        ]);
    }

	#[Route(path: '/categories_ajax', name: 'app_categories_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder([null, null, 'url', 'name', 'summary']);
        $dataTable->setColumnSearch(['id', 'url', 'name', 'summary']);
        $dataTable->setTable('categories');
        $dataTable->setQuery('select * from categories');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_categories_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_categories_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['url'];
			$row[] = $value['name'];
			$row[] = $value['summary'];
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

	#[Route('/categories/{id}/show', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $categories): Response
    {
        return $this->render('categories/show.html.twig', [
            'categories' => $categories,
        ]);
    }

	#[Route(path: '/categories/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categories = new Categories();
        $form = $this->createForm(CategoriesType::class, $categories);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categories);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_categories_edit', ['id' => $categories->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('categories/new.html.twig', [
            'categories' => $categories,
            'form' => $form
        ]);
    }

	#[Route('/categories/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $categories, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$categories) {
            throw $this->createNotFoundException(
                'No Categories found for id '.$id
            );
        }
        $form = $this->createForm(categoriestype::class, $categories);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_categories_edit', ['id' => $categories->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'categories' => $categories,
            'form' => $form
        ]);
    }

	#[Route('/categories/{id}/delete', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Categories $categories): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categories->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categories);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }
}