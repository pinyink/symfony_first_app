<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Entity\CrudDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CrudDetailController extends AbstractController
{
    #[Route('/crud/detail/{id}', name: 'app_crud_detail')]
    public function index(int $id, EntityManagerInterface $entitiyManager): Response
    {
        $crud = $entitiyManager->getRepository(Crud::class)->find($id);
        if (!$crud) {
            throw $this->createNotFoundException(
                'Id Not Found For Id: '.$id
            );
        }
        return $this->render('crud_detail/index.html.twig', [
            'crud' => $crud,
        ]);
    }

    #[Route('/crud/detail/{id}/fields', name:'app_crud_detail_fields', methods:['GET'])]
    public function field(int $id, EntityManagerInterface $em)
    {
        $field = $em->getRepository(CrudDetail::class);
        $fields = $field->findBy(['crud' => $id]);
        if (!$fields) {
            $data = [];
            return $this->json($data);
        }
        return $this->json($fields);
    }

    #[Route(path: '/crud/detail/{id}/entity', name: 'app_crud_detail_entity', methods: ['GET'])]
    public function entity(int $id, EntityManagerInterface $entitiyManager) : Response
    {
        $con = $entitiyManager->getConnection();
        $sm = $con->getSchemaManager();
        // $columns = $sm->listTableColumns('siswa');
        // $columnNames = [];
        // foreach($columns as $column){
        //     $columnNames[] = $column->getName();
        // }

        $table = $sm->listTableDetails('siswa');
        return $this->json($table);
    }
}
