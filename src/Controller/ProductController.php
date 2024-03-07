<?php
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return $this->render('product/index.html.twig', [
        ]);
    }

	#[Route(path: '/product_ajax', name: 'app_product_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder([null, null, 'nama', 'harga']);
        $dataTable->setColumnSearch(['id', 'nama', 'harga']);
        $dataTable->setTable('product');
        $dataTable->setQuery('select * from product');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_product_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_product_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['nama'];
			$row[] = $value['harga'];
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

	#[Route('/product/{id}/show', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

	#[Route(path: '/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }

	#[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$product) {
            throw $this->createNotFoundException(
                'No Product found for id '.$id
            );
        }
        $form = $this->createForm(producttype::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }

	#[Route('/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }
}