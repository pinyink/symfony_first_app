<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Entity\CrudDetail;
use App\Repository\CrudDetailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function fields(int $id, CrudDetailRepository $crudDetail)
    {
        $crud = $crudDetail->findBy(['crud' => $id]);
        $data = [];
        foreach ($crud as $key => $value) {
            $array = [
                'id' => $value->getId(),
                'name' => $value->getName(),
                'crudName' => $value->getCrud()->getEntityName(),
                'type' => $value->getType(),
                'setting' => $value->getSetting()
            ];
            array_push($data, $array);
        }
        return $this->json($data);
    }

    #[Route('/crud/detail/{id}/edit', name: 'app_crud_detail_edit', methods: ['GET'])]
    public function edit(CrudDetail $crudDetail, int $id) 
    {
        $data = [
            'id' => $crudDetail->getId(),
            'name' => $crudDetail->getName(),
            'crudName' => $crudDetail->getCrud()->getEntityName(),
            'type' => $crudDetail->getType(),
            'setting' => $crudDetail->getSetting(),
            'datatable' => $crudDetail->getDatatable()
        ];
        return $this->json($data);
    }

    #[Route('/crud/detail/{id}/save', name:'app_crud_detail_save', methods:['POST'])]
    public function save(Request $request, EntityManagerInterface $em, int $id)
    {
        $data = $request->request->all();

        $crud = $em->getRepository(Crud::class)->find($id);
        if ($data['saveMethod'] == 'save') {
            $crudDetail = new CrudDetail();
        } else {
            $crudDetail = new CrudDetail();
            $crudDetail = $em->getRepository(CrudDetail::class)->find($data['id']);
        }
        $crudDetail->setName($data['nameField']);
        $crudDetail->setType($data['typeField']);
        $crudDetail->setCrud($crud);
        $crudDetail->setDatatable($data['datatableField']);
        if ($data['saveMethod'] == 'save') {
            $em->persist($crudDetail);
        }
        $em->flush();
        return $this->json([
            'info' => 'success',
            'message' => 'Save Data Success'
        ]);
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
