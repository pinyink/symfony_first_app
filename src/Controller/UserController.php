<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\DataTableService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user'), IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/index', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            
        ]);
    }

    #[Route(path: '/ajax', name: 'app_user_ajax', methods: ['GET', 'POST'])]
    public function ajax(DataTableService $dataTable, UserRepository $userRepository, Request $request) : Response
    {        
        $perPage = $request->get('row');
        $page = $request->get('page') == null || $request->get('page') == 0 ? 1 : $request->get('page');
        $offset = $page != 1 ? ($page - 1) * $perPage : 0;

        $username = $request->get('username');
        
        $where = [];
        $param = [];

        if ($username != null) {
            $where[] = "f.username like :username";
            $param['username'] = '%'.$username.'%';
        }

        $data = $userRepository->data($where, $param, $perPage, $offset);
        
        return $this->json([
            'data' => $data,
            'currentPage' => $page,
            'where' => $where
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['user']['pass']['first']
            );
            $user->setPassword($hashedPassword);
            // handle upload foto
            $foto = $form->get('foto')->getData();
            if ($foto) {
                $dir = $this->getParameter('foto_profil_directory');
                $fileUploader->setDir('');
                $fileUploader->setTargetDirectory($dir);
                $fotoFileName = $fileUploader->upload($foto);
                $user->setFoto($fotoFileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Tambah Data Berhasil');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $user->setRoles([$data['user']['admin']]);
            if ($data['user']['pass']['first'] != null) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $data['user']['pass']['first']
                );
                $user->setPassword($hashedPassword);
            }
            // handle upload foto
            $foto = $form->get('foto')->getData();
            if ($foto) {
                $dir = $this->getParameter('foto_profil_directory');
                $fileUploader->setDir('');
                $fileUploader->setTargetDirectory($dir);
                $fotoFileName = $fileUploader->upload($foto);
                $user->setFoto($fotoFileName);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        $privilages = $user->getRoles();
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'priv' => $privilages[0]
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
