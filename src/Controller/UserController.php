<?php

namespace App\Controller;

use App\Entity\Level;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $level = $entityManager->getRepository(Level::class)->findAll();
        $form = $this->createFormBuilder($user)
                ->setAction($this->generateUrl('app_user_tambah'))
                ->setMethod('POST')
                ->add('username', TextType::class)
                ->add('password', PasswordType::class)
                ->add('level', ChoiceType::class, ['choices' => $level])
                ->add('save', SubmitType::class, ['label' => 'Simpan'])
                ->getForm();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'formuser' => $form
        ]);
    }

    #[Route(path: '/user_tambah', name: 'app_user_tambah', methods: ['POST'])]
    public function tambah(EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
        // $user->setLevel(1);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response('tambah data berhasil');
    }

    #[Route(path: '/user_data/{id}', name: 'app_user_data', methods: ['GET'])]
    public function data(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $entityManager->getRepository(User::class);
        $user = $entity->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No User found for id '.$id
            );
        }
        // return new Response('Check out this great product: '.$user->getUsername());
        return $this->json($user,200);
    }
}
