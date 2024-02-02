<?php

namespace App\Controller;

use App\Entity\Siswa;
use App\Form\SiswaType;
use App\Repository\SiswaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiswaController extends AbstractController
{
    #[Route('/siswa', name: 'app_siswa')]
    public function index(SiswaRepository $siswaRepository): Response
    {
        return $this->render('siswa/index.html.twig', [
            'controller_name' => 'SiswaController',
            'siswas' => $siswaRepository->findAll()
        ]);
    }

    #[Route(path: '/siswa_new', name: 'app_siswa_new', methods: ['GET'])]
    public function new(): Response
    {
        $form = $this->createForm(SiswaType::class, new Siswa());
        return $this->render('siswa/new.html.twig', [
            'form' => $form
        ]);
    }
}
