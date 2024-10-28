<?php
namespace App\Controller;

use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublisherController extends AbstractController
{
    #[Route('/publisher/index', name: 'app_publisher')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Not Allowed Access');

        return $this->render('publisher/index.html.twig', [
        ]);
    }

	#[Route(path: '/publisher/ajax', name: 'app_publisher_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Not Allowed Access');
        $dataTable->setColumnOrder([null, null, 'name', 'address']);
        $dataTable->setColumnSearch(['id', 'name', 'address']);
        $dataTable->setTable('publisher');
        $dataTable->setQuery('select * from publisher');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_publisher_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_publisher_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['name'];
			$row[] = $value['address'];
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

	#[Route('/publisher/{id}/show', name: 'app_publisher_show', methods: ['GET'])]
    public function show(Publisher $publisher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Not Allowed Access');
        return $this->render('publisher/show.html.twig', [
            'publisher' => $publisher,
        ]);
    }

	#[Route(path: '/publisher/new', name: 'app_publisher_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Not Allowed Access');

        $publisher = new Publisher();

        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($publisher);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_publisher_edit', ['id' => $publisher->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('publisher/new.html.twig', [
            'publisher' => $publisher,
            'form' => $form
        ]);
    }

	#[Route('/publisher/{id}/edit', name: 'app_publisher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publisher $publisher, int $id, EntityManagerInterface $entityManager, ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Not Allowed Access');

        if (!$publisher) {
            throw $this->createNotFoundException(
                'No Publisher found for id '.$id
            );
        }

        $form = $this->createForm(publishertype::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_publisher_edit', ['id' => $publisher->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publisher/edit.html.twig', [
            'publisher' => $publisher,
            'form' => $form
        ]);
    }

	#[Route('/publisher/{id}/delete', name: 'app_publisher_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Publisher $publisher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Not Allowed Access');
        
        if ($this->isCsrfTokenValid('delete'.$publisher->getId(), $request->request->get('_token'))) {
            $entityManager->remove($publisher);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }
}