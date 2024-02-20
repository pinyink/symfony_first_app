<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilPasswordType;
use App\Form\ProfilSummaryType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil', methods:['GET']), IsGranted('IS_AUTHENTICATED')]
    public function index(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $dataUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user]);
        
        $formUser = $formFactory->createNamed('formprofil', ProfilSummaryType::class, $dataUser);

        $formpass = $formFactory->createNamed('formpassword', ProfilPasswordType::class);
        return $this->render('profil/index.html.twig', [
            'formuser' => $formUser,
            'formpass' => $formpass
        ]);
    }

    #[Route(path: '/profil_summary', name: 'app_profil_summary', methods: ['POST'])]
    public function profilSummary(Request $request, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, FileUploader $fileUploader) : Response
    {
        $username = $this->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        $formUser = $formFactory->createNamed('formprofil', ProfilSummaryType::class, $user);
        $formUser->handleRequest($request);
        if ($formUser->isValid()) {
            $foto = $formUser->get('foto')->getData();
            if ($foto) {
                $dir = $this->getParameter('foto_profil_directory');
                $fileUploader->setTargetDirectory($dir);
                $fotoFileName = $fileUploader->upload($foto);
                $user->setFoto($fotoFileName);
            }
            $entityManager->flush();
            return $this->json([
                'info' => 'success',
                'message' => 'Update Data Success'
            ]);
        }
        return $this->json([
            'info' => 'error',
            'message' => 'Form Tidak Valid'
        ]);
    }

    #[Route(path: '/profil_password', name: 'app_profil_password', methods: ['POST'])]
    public function profilPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, FormFactoryInterface $formFactory) : Response
    {
        $data = $request->request->all();
        $username = $this->getUser()->getUserIdentifier();

        $formpass = $formFactory->createNamed('formpassword', ProfilPasswordType::class);
        $formpass->handleRequest($request);
        if ($formpass->isValid()) {
            $userId = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
            $user = $entityManager->getRepository(User::class)->find($userId->getId());
            if (!$passwordHasher->isPasswordValid($user, $data['formpassword']['oldpassword'])) {
                return $this->json([
                    'info' => 'warning',
                    'message' => 'Password Lama Salah'
                ]);
            }
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['formpassword']['newpassword']['first']
            );
            $user->setPassword($hashedPassword);
            $entityManager->flush();
            return $this->json([
                'info' => 'success',
                'message' => 'Update Password Success, Silahkan Login Kembali'
            ]);
        } else {
            return $this->json([
                'info' => 'warning',
                'message' => 'Entry Password Tidak Sama'
            ]);
        }
    }
}
