<?php

namespace App\Controller;

use App\Entity\File;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;

class MediaController extends AbstractController
{
    #[Route('/media/list', name: 'media_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');
        
        $files = $em->getRepository(File::class)->findAll();
        $data = [];
        foreach ($files as $file) {
            $ext = pathinfo($file->getPath(), PATHINFO_EXTENSION);
            $data[] = [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'url' => '/uploads/image/file/' . $file->getPath(),
                'size' => $file->getSize(),
                'type' => $this->getFileType($ext),
                'extension' => $ext
            ];
        }

        return $this->json($data);
    }

    private function getFileType(string $ext): string
    {
        $ext = strtolower($ext);
        $video = ['mp4', 'webm', 'ogg'];
        $pdf = ['pdf'];
        $doc = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        $image = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];

        if (in_array($ext, $video)) {
            return 'video';
        }
        if (in_array($ext, $pdf)) {
            return 'pdf';
        }
        if (in_array($ext, $doc)) {
            return 'document';
        }
        if (in_array($ext, $image)) {
            return 'image';
        }
        return 'file';
    }

    #[Route('/media/upload', name: 'media_upload', methods: ['POST'])]
    public function upload(Request $request, FileUploader $fileUploader, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');
        
        $uploadedFile = $request->files->get('file');
        if ($uploadedFile) {
            try {
                $dir = $this->getParameter('image_directory');
                $fileUploader->setTargetDirectory($dir . '/file');
                $fileUploader->setDir('');
                $pathFileName = $fileUploader->upload($uploadedFile);

                $fileEntity = new File();
                $fileEntity->setName($uploadedFile->getClientOriginalName());
                $fileEntity->setPath($pathFileName);
                $fileEntity->setSize(filesize($dir . '/file/' . $pathFileName));

                $em->persist($fileEntity);
                $em->flush();

                return $this->json([
                    'success' => true,
                    'id' => $fileEntity->getId(),
                    'url' => '/uploads/image/file/' . $pathFileName
                ]);
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }
        return $this->json(['success' => false], 400);
    }

    #[Route('/media/rename', name: 'media_rename', methods: ['POST'])]
    public function rename(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');
        
        $id = $request->request->get('id');
        $newName = $request->request->get('newName');
        
        $file = $em->getRepository(File::class)->find($id);
        if ($file) {
            $file->setName($newName);
            $em->flush();
            return $this->json(['success' => true]);
        }
        
        return $this->json(['success' => false], 404);
    }

    #[Route('/media/delete', name: 'media_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');
        
        $id = $request->request->get('id');
        $file = $em->getRepository(File::class)->find($id);
        
        if ($file) {
            $filesystem = new Filesystem();
            $dir = $this->getParameter('image_directory');
            $path = $dir . '/file/' . $file->getPath();
            
            if ($filesystem->exists($path)) {
                $filesystem->remove($path);
            }

            $em->remove($file);
            $em->flush();
            
            return $this->json(['success' => true]);
        }
        
        return $this->json(['success' => false], 404);
    }
}