<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function loginAksi(EntityManagerInterface $entityManager, Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $entityManager->getRepository(User::class);
        $queryUser = $user->findOneBy(['username' => $username]);
        if (!$queryUser) {
            $this->addFlash(
                'danger',
                'Login Gagal'
            );
            return $this->redirectToRoute('login');
        }
        if (!password_verify($password, $queryUser->getPassword())) {
            $this->addFlash(
                'danger',
                'Login Gagal'
            );
            return $this->redirectToRoute('login');
        }
        return $this->redirectToRoute('dashboard');
    }
}
