<?php
namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

class BookController extends AbstractController
{
    #[Route('/book/index', name: 'app_book')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return $this->render('book/index.html.twig', [
        ]);
    }

	#[Route(path: '/book/ajax', name: 'app_book_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $dataTable->setColumnOrder([null, null, 'name', 'cover']);
        $dataTable->setColumnSearch(['id', 'name', 'cover']);
        $dataTable->setTable('book');
        $dataTable->setQuery('select * from book');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_book_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_book_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['name'];
			$row[] = $value['cover'];
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

	#[Route('/book/{id}/show', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

	#[Route(path: '/book/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $book = new Book();
        

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('cover')->getData();
            if ($cover) {
                $fileUploader->setDir('');
                $dir = $this->getParameter('image_directory');
                $fileUploader->setTargetDirectory($dir.'/book');
                $coverFileName = $fileUploader->upload($cover);
                $book->setCover('uploads/image/book/'.$coverFileName);
            }
            $file = $form->get('file')->getData();
            if ($file) {
                $fileUploader->setDir('');
                $dir = $this->getParameter('pdf_directory');
                $fileUploader->setTargetDirectory($dir.'/book');
                $fileFileName = $fileUploader->upload($file);
                $book->setFile('uploads/pdf/book/'.$fileFileName);

                $size = filesize($dir.'/book/'.$fileFileName);
                $book->setSize($size);

                $ext = pathinfo($dir.'/book/'.$fileFileName, PATHINFO_EXTENSION);
                $book->setExtension($ext);
            }
            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form
        ]);
    }

	#[Route('/book/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, int $id, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        if (!$book) {
            throw $this->createNotFoundException(
                'No Book found for id '.$id
            );
        }
        

        $form = $this->createForm(booktype::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('cover')->getData();
            if ($cover) {
                $fileUploader->setDir('');
                $dir = $this->getParameter('image_directory');
                $fileUploader->setTargetDirectory($dir.'/book');
                $coverFileName = $fileUploader->upload($cover);
                $book->setCover($coverFileName);
            }$file = $form->get('file')->getData();
            if ($file) {
                $fileUploader->setDir('');
                $dir = $this->getParameter('upload_directory');
                $fileUploader->setTargetDirectory($dir.'/book');
                $fileFileName = $fileUploader->upload($file);
                $book->setFile($fileFileName);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form
        ]);
    }

	#[Route('/book/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }
}