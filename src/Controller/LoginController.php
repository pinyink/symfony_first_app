<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route(path: '/login_aksi', name: 'login_aksi', methods: ['POST'])]
    public function loginAksi()
    {
        $this->addFlash(
            'info',
            'Login Gagal'
        );
        return $this->redirectToRoute('login');
    }
}
