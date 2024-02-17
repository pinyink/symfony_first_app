<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil', methods:['GET']), IsGranted('IS_AUTHENTICATED')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $dataUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user]);
        $formUser = $this->createFormBuilder($dataUser)
                    ->add('username', TextType::class, [
                        'required' => false,
                        'attr' => [
                            'disabled' => 'disabled'
                        ]
                    ])
                    ->add('fullname', TextType::class, [
                        'label' => 'Nama Lengkap'
                    ])
                    ->getForm();
        
        $formpass = $this->createFormBuilder(null)
                    ->add('oldpassword', PasswordType::class, [
                        'label' => 'Password Lama'
                    ])
                    ->add('newpassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'first_options'  => ['label' => 'Password Baru', 'hash_property_path' => 'password'],
                        'second_options' => ['label' => 'Ulangi Password Baru'],
                        'mapped' => false,
                        'required' => false
                    ])
                    ->getForm();
        return $this->render('profil/index.html.twig', [
            'formuser' => $formUser,
            'formpass' => $formpass
        ]);
    }
}
