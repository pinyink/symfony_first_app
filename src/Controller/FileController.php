<?php
namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use App\Service\FormatSize;

class FileController extends AbstractController
{
    #[Route('/file', name: 'app_file')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return $this->render('file/index.html.twig', [
        ]);
    }

	#[Route(path: '/file_ajax', name: 'app_file_ajax', methods: ['POST'])]
    public function ajax(DataTableService $dataTable, EntityManagerInterface $entityManager, Request $request, FormatSize $formatSize) : Response
    {
        $dataTable->setColumnOrder([null, null, 'name', 'size']);
        $dataTable->setColumnSearch(['id', 'name', 'size']);
        $dataTable->setTable('file');
        $dataTable->setQuery('select * from file');
        $queryResult = $dataTable->getData($entityManager, $request);
        $data = [];
        $params = $request->request->all();
        $no = isset($params['start']) ? $params['start'] : 1;
        foreach ($queryResult['data'] as $key => $value) {
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".$this->generateUrl('app_file_show', ['id' => $value['id']])."' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='".$this->generateUrl('app_file_edit', ['id' => $value['id']])."' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>";
			$row[] = $value['name'];
			$row[] = $formatSize->formatSizeUnits($value['size']);
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

	#[Route('/file/{id}/show', name: 'app_file_show', methods: ['GET'])]
    public function show(File $file): Response
    {
        return $this->render('file/show.html.twig', [
            'file' => $file,
        ]);
    }

	#[Route(path: '/file/new', name: 'app_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $path = $form->get('path')->getData();
            if ($path) {
                $dir = $this->getParameter('image_directory');
                $fileUploader->setTargetDirectory($dir.'/file');
                $pathFileName = $fileUploader->upload($path);
                $file->setPath($pathFileName);
                $size = filesize($dir.'/file/'.$pathFileName);
                $file->setSize($size);
            }
            $entityManager->persist($file);
            $entityManager->flush();
            $this->addFlash('success', 'Simpan Data Berhasil');
            return $this->redirectToRoute('app_file_edit', ['id' => $file->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('file/new.html.twig', [
            'file' => $file,
            'form' => $form
        ]);
    }

	#[Route('/file/{id}/edit', name: 'app_file_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, File $file, int $id, EntityManagerInterface $entityManager, FileUploader $fileUploader, FormatSize $formatSize): Response
    {
        if (!$file) {
            throw $this->createNotFoundException(
                'No File found for id '.$id
            );
        }
        $file->setSize($formatSize->formatSizeUnits($file->getSize()));
        $form = $this->createForm(filetype::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $path = $form->get('path')->getData();
            if ($path) {
                $dir = $this->getParameter('image_directory');
                $fileUploader->setTargetDirectory($dir.'/file');
                $pathFileName = $fileUploader->upload($path);
                $file->setPath($pathFileName);
                $size = filesize($dir.'/file/'.$pathFileName);
                $file->setSize($size);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Edit Data Berhasil');
            return $this->redirectToRoute('app_file_edit', ['id' => $file->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/edit.html.twig', [
            'file' => $file,
            'form' => $form
        ]);
    }

	#[Route('/file/{id}/delete', name: 'app_file_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, File $file): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->json([
            "info" => "success",
            "message" => "Delete Data Berhasil"
        ]);
    }

    #[Route('/file/data', name: 'app_file_data', methods: ['GET'])]
    public function data(Request $request, EntityManagerInterface $entityManagerInterface) : Response
    {
        $page = $request->get('page') == null || $request->get('page') == 0 ? 1 : $request->get('page');
        $offset = $page * 10;
        $file = $entityManagerInterface->getRepository(File::class);
        $dataFile = $file->data([], [], 10, $offset);
        $total = $file->total() / 10;

        // percobaan
        // $page = 3;
        // $total = 10;
        return $this->json([
            'currentPage' => $page,
            'totalPage' => $total,
            'baseUrl' => 'uploads/image/file',
            'data' => $dataFile,
        ]);
    }

    #[Route('file/{id}/detail', name: 'app_file_detail', methods: ['GET'])]
    public function detail(File $file, int $id) : Response
    {
        if (!$file) {
            throw $this->createNotFoundException();
        }
        $data = [
            'id' => $file->getId(),
            'name' => $file->getName(),
            'path' => '/uploads/image/file/'.$file->getPath()
        ];
        return $this->json($data);
    }
}