<?php

namespace App\Controller;

use App\Entity\Laman;
use App\Entity\Post;
use App\Entity\PostStatistic;
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

        $search = $request->query->get('search');
        
        $post = $entityManagerInterface->getRepository(Post::class);
        $kategori = $entityManagerInterface->getRepository(PostToCategories::class);

        $where = [
            'p.publish = :publish'
        ];
        $param = [
            'publish' => '1'
        ];
        if ($search != null) {
            $where[] = "p.title like :search";
            $param['search'] = '%'.$search.'%';
        }

        $data = $post->data($where, $param, $perPage, $offset);
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
            'totalPage' => $totalPage,
            'search' => $search
        ]);
    }

    #[Route('/article/{slug}', name: 'blog', methods: ['GET'])]
    public function blog(string $slug, EntityManagerInterface $entityManagerInterface) : Response
    {
        $post = $entityManagerInterface->getRepository(Post::class);
        $dataPost = $post->findOneBy(['url' => $slug]);
        if (!$dataPost) {
            throw $this->createNotFoundException('Page Not Found');
        }

        $request = Request::createFromGlobals();
        $statistic = new PostStatistic();
        $statistic->setPost($dataPost);
        $statistic->setIpAddress($request->getClientIp());
        $statistic->setDatetime(new \DateTime());
        $statistic->setAgent($request->headers->get('User-Agent'));
        $statistic->setAgentDetail($request->headers->get('User-Agent'));
        $statistic->setPlatform('no platform');

        $dataPost->setViews(intval($dataPost->getViews()) + 1);
        $entityManagerInterface->persist($statistic);
        $entityManagerInterface->flush();

        $recentPost = $post->data([], [], 3);
        return $this->render('main/blog.html.twig', [
            'post' => $dataPost,
            'recents' => $recentPost
        ]);
    }

    #[Route('/page/{slug}', name: 'page', methods: ['GET'])]
    public function page(string $slug, EntityManagerInterface $entityManagerInterface): Response
    {
        $laman = $entityManagerInterface->getRepository(Laman::class);
        $dataLaman = $laman->findOneBy(['url' => $slug]);
        if (!$dataLaman) {
            throw $this->createNotFoundException('Page Not Found');
        }
        return $this->render('main/laman.html.twig', [
            'laman' => $dataLaman
        ]);
    }
}
