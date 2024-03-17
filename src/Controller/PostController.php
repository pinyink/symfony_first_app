<?php
namespace App\Controller;

use App\Entity\File;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return $this->render('post/index.html.twig', [
        ]);
    }

	#[Route(path: '/post_ajax', name: 'app_post_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder([null, null, 'title', 'summary']);
        $dataTable->setColumnSearch(['id', 'title', 'summary']);
        $dataTable->setTable('post');
        $dataTable->setQuery('select * from post');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_post_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_post_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['title'];
			$row[] = $value['summary'];
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

	#[Route('/post/{id}/show', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

	#[Route(path: '/post/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $this->getUser()->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
            $post->setUser($user);
            $post->setDate(new \Datetime());
            $post->setModified(new \DateTime());
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_post_edit', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form
        ]);
    }

	#[Route('/post/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$post) {
            throw $this->createNotFoundException(
                'No Post found for id '.$id
            );
        }
        $form = $this->createForm(posttype::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setModified(new \DateTime());
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_post_edit', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form
        ]);
    }

	#[Route('/post/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }

    #[Route('/post/{id}/sampul', name: 'app_post_sampul', methods: ['POST'])]
    public function sampul(int $id, Post $post, EntityManagerInterface $em, Request $request) : Response
    {
        if (!$post) {
            return $this->json([
                "info" => "danger",
                "message" => "ID Not Found"
            ]);
        }

        $data = $request->request->all();
        $sampul = $em->getRepository(File::class);
        $sampulId = $sampul->find($data['fileId']);
        $post->setSampul($sampulId);
        $em->flush();
        
        return $this->json([
            "info" => "success",
            "message" => "Update Sampul Berhasil"
        ]);
    }
}