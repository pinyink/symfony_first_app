<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostToCategories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $perPage = 6;
        $page = $request->get('page') == null || $request->get('page') == 0 ? 1 : $request->get('page');
        $offset = $page != 1 ? ($page - 1) * $perPage : 0;
        
        $post = $entityManagerInterface->getRepository(Post::class);
        $kategori = $entityManagerInterface->getRepository(PostToCategories::class);

        $data = $post->data([], [], $perPage, $offset);
        foreach ($data as $key => $value) {
            $data[$key]['categories'] = [];
            $postToCategories = $kategori->FindBy(['post' => $value['id']]);
            foreach ($postToCategories as $k => $v) {
                $data[$key]['categories'][] = [
                    'name' => $v->getCategories()->getName(),
                    'url' => $v->getCategories()->getUrl()
                ];
            }
        }
        $totalPage = $post->total() / $perPage;

        return $this->render('main/index.html.twig', [
            'data' => $data,
            'page' => $page,
            'pageBefore3' => $page - 3,
            'pageBefore1' => $page - 1,
            'pageAfter3' => $page + 3,
            'pageAfter1' => $page + 1,
            'totalPage' => $totalPage
        ]);
        // return $this->json([
        //     'data' => $data,
        //     'offset' => $offset
        // ]);
    }
}
