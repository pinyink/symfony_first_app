<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    
    #[Route(path: '/tambah_user', name: 'tambah_user', methods: ['GET'])]
    public function tambahUser(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager) : Response
    {
        $user = new User();
        $user->setUsername('admin');
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            'admin'
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json(['success' => 'success']);
    }
}
