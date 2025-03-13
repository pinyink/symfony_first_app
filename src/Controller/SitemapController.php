<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap')]
    public function index(PostRepository $postRepository): Response
    {
        // return $this->render('sitemap/index.html.twig', [
        //     'controller_name' => 'SitemapController',
        // ]);

        $query = $postRepository->findAll();

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $query]),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
