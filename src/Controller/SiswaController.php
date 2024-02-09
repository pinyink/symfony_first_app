<?php

namespace App\Controller;

use App\Entity\Siswa;
use App\Form\SiswaType;
use App\Repository\SiswaRepository;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiswaController extends AbstractController
{
    #[Route('/siswa', name: 'app_siswa')]
    public function index(SiswaRepository $siswaRepository): Response
    {
        return $this->render('siswa/index.html.twig', [
        ]);
    }

    #[Route(path: '/siswa_ajax', name: 'app_siswa_ajax', methods: ['GET', 'POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder(['id', 'nis', 'nama', 'alamat', 'umur']);
        $dataTable->setColumnSearch(['id', 'nis', 'nama', 'alamat', 'umur']);
        $dataTable->setTable('siswa');
        $dataTable->setQuery('select * from siswa');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $value['id'];
            $row[] = $value['nis'];
            $row[] = $value['nama'];
            $row[] = $value['alamat'];
            $row[] = $value['umur'];
            $row[] = "<a href='".$this->generateUrl('app_siswa_edit', ['id' => $value['id']])."' class='btn btn-info'>edit</a>";
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

    #[Route(path: '/siswa_new', name: 'app_siswa_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $siswa = new Siswa();
        $form = $this->createForm(SiswaType::class, $siswa);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($siswa);
            $entityManager->flush();

            return $this->redirectToRoute('app_siswa', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('siswa/new.html.twig', [
            'siswa' => $siswa,
            'form' => $form
        ]);
    }

    #[Route('/siswa/{id}/edit', name: 'app_siswa_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Siswa $siswa, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SiswaType::class, $siswa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_siswa', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('siswa/edit.html.twig', [
            'siswa' => $siswa,
            'form' => $form
        ]);
    }
}
