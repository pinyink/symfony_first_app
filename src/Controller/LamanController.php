<?php
namespace App\Controller;

use App\Entity\Laman;
use App\Form\LamanType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LamanController extends AbstractController
{
    #[Route('/laman/index', name: 'app_laman')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return $this->render('laman/index.html.twig', [
        ]);
    }

	#[Route(path: '/laman/ajax', name: 'app_laman_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder([null, null, 'url', 'name']);
        $dataTable->setColumnSearch(['id', 'url', 'name']);
        $dataTable->setTable('laman');
        $dataTable->setQuery('select * from laman');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_laman_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_laman_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['url'];
			$row[] = $value['name'];
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

	#[Route('/laman/{id}/show', name: 'app_laman_show', methods: ['GET'])]
    public function show(Laman $laman): Response
    {
        return $this->render('laman/show.html.twig', [
            'laman' => $laman,
        ]);
    }

	#[Route(path: '/laman/new', name: 'app_laman_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ): Response
    {
        $laman = new Laman();

        

        $form = $this->createForm(LamanType::class, $laman);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($laman);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_laman_edit', ['id' => $laman->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('laman/new.html.twig', [
            'laman' => $laman,
            'form' => $form
        ]);
    }

	#[Route('/laman/{id}/edit', name: 'app_laman_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Laman $laman, int $id, EntityManagerInterface $entityManager, ): Response
    {
        if (!$laman) {
            throw $this->createNotFoundException(
                'No Laman found for id '.$id
            );
        }

        


        $form = $this->createForm(lamantype::class, $laman);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_laman_edit', ['id' => $laman->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('laman/edit.html.twig', [
            'laman' => $laman,
            'form' => $form
        ]);
    }

	#[Route('/laman/{id}/delete', name: 'app_laman_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Laman $laman): Response
    {
        if ($this->isCsrfTokenValid('delete'.$laman->getId(), $request->request->get('_token'))) {
            $entityManager->remove($laman);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }
}