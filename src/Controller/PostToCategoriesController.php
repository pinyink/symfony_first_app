<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Post;
use App\Entity\PostToCategories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostToCategoriesController extends AbstractController
{
    #[Route('/post_to_categories/{id}', name: 'app_post_to_categories')]
    public function index(EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        $data = [];

        $emCategories = $entityManagerInterface->getRepository(Categories::class);
        $categories = $emCategories->findAll();

        $emPostToCategories = $entityManagerInterface->getRepository(PostToCategories::class);
        foreach ($categories as $key => $value) {
            $array = [
                'id' => $value->getId(),
                'name' => $value->getName(),
                'url' => $value->getUrl()
            ];
            $postToCategories = $emPostToCategories->findOneBy(['categories' => $value->getId(), 'post' => $id]);
            if (!$postToCategories) {
                $array['postToCategories'] = false;
            } else {
                $array['postToCategories'] = true;
            }
            array_push($data, $array);
        }
        return $this->json([
            'data' => $data
        ]);
    }

    #[Route(path: '/post_to_categories/{id}/save', name: 'app_post_to_categories_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em) : Response
    {
        $data = $request->request->all();

        $postToCategoriesEntity = new PostToCategories();
        $post = $em->getRepository(Post::class)->find($data['post']);
        if (!$post) {
            return $this->json([
                'info' => 'warning',
                'message' => 'Post Id Not Found',
            ]);
        }
        $categories = $em->getRepository(Categories::class)->find($data['categories']);
        if (!$categories) {
            return $this->json([
                'info' => 'warning',
                'message' => 'Categories Id Not Found',
            ]);
        }
        $postToCategoriesEntity->setPost($post);
        $postToCategoriesEntity->setCategories($categories);
        $em->persist($postToCategoriesEntity);
        $em->flush();

        return $this->json([
            'info' => 'success',
            'message' => 'Add Categories Success',
        ]);
    }

    #[Route(path: '/post_to_categories/{id}/remove', name: 'app_post_to_categories_remove', methods: ['POST'])]
    public function remove(EntityManagerInterface $em, Request $request) : Response
    {
        $data = $request->request->all();
        $postToCategories = $em->getRepository(PostToCategories::class)->findOneBy(['post' => $data['post'], 'categories' => $data['categories']]);
        if (!$postToCategories) {
            return $this->json([
                'info' => 'warning',
                'message' => 'No Data Found'
            ]);
        }
        $em->remove($postToCategories);
        $em->flush();
        return $this->json([
            'info' => 'success',
            'message' => 'Un-Check Categories Successfully'
        ]);
    }
}
