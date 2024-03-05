<?php

namespace App\Service;

class CrudServicer
{
    public function controller($dir, $data = []) : static
    {
        $string = "<?php
namespace App\Controller;

use App\Entity\\".$data['crud']['entity'].";
use App\Form\\".$data['crud']['form'].";
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ".$data['crud']['entity']."Controller extends AbstractController
{
    #[Route('/".$data['crud']['route']."', name: 'app_".$data['crud']['route']."')]
    public function index(): Response
    {
        \$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return \$this->render('".$data['crud']['route']."/index.html.twig', [
        ]);
    }";

    $fieldDatatable = '';
    $searchDatatable = "['id'";
    $orderDatatable = "[null, null";
    foreach ($data['fields'] as $key => $value) {
        if ($value['datatable'] == 1) {
            $fieldDatatable .= "\n\t\t\t\$row[] = \$value['".$value['name']."'];";
            $searchDatatable .= ", '".$value['name']."'";
            $orderDatatable .= ", '".$value['name']."'";
        }
    }
    $searchDatatable .= "]";
    $orderDatatable .= "]";
$string .= "\n\n\t#[Route(path: '/".$data['crud']['route']."_ajax', name: 'app_".$data['crud']['route']."_ajax', methods: ['POST'])]
    public function ajax(DataTableService \$dataTable, EntityManagerInterface \$entityManager, Request \$request) : Response
    {
        \$dataTable->setColumnOrder(".$orderDatatable.");
        \$dataTable->setColumnSearch(".$searchDatatable.");
        \$dataTable->setTable('".strtolower($data['crud']['entity'])."');
        \$dataTable->setQuery('select * from ".strtolower($data['crud']['entity'])."');
        \$queryResult = \$dataTable->getData(\$entityManager, \$request);
        \$data = [];
        \$params = \$request->request->all();
        \$no = isset(\$params['start']) ? \$params['start'] : 1;
        foreach (\$queryResult['data'] as \$key => \$value) {
            \$row = array();
            \$row[] = \$no;
            \$row[] = \"<a href='\".\$this->generateUrl('app_".$data['crud']['route']."_edit', ['id' => \$value['id']]).\"' class='btn btn-sm btn-info'>edit</a>\";".$fieldDatatable."
            \$data[] = \$row;
            \$no++;
        }
        \$output = [
            \"draw\" => 0,
            \"recordsTotal\" => \$queryResult['count'],
            \"recordsFiltered\" => \$queryResult['filter'],
            \"data\" => \$data,
        ];
        return \$this->json(\$output);
    }";
$string .= "\n\n\t#[Route(path: '/".$data['crud']['route']."/new', name: 'app_".$data['crud']['route']."_new', methods: ['GET', 'POST'])]
    public function new(Request \$request, EntityManagerInterface \$entityManager): Response
    {
        \$".strtolower($data['crud']['entity'])." = new ".$data['crud']['entity'].";
        \$form = \$this->createForm(".$data['crud']['form']."::class, \$".strtolower($data['crud']['entity']).");
        \$form->handleRequest(\$request);
        if (\$form->isSubmitted() && \$form->isValid()) {
            \$entityManager->persist(\$".strtolower($data['crud']['entity']).");
            \$entityManager->flush();
            \$this->addFlash('success', 'Simpan Data Berhasil');
            return \$this->redirectToRoute('app_".$data['crud']['route']."_edit', ['id' => \$".strtolower($data['crud']['entity'])."->getId()], Response::HTTP_SEE_OTHER);
        }
        return \$this->render('".$data['crud']['route']."/new.html.twig', [
            '".strtolower($data['crud']['entity'])."' => \$".strtolower($data['crud']['entity']).",
            'form' => \$form
        ]);
    }";
$string .= "\n\n\t#[Route('/".$data['crud']['route']."/{id}/edit', name: 'app_".$data['crud']['route']."_edit', methods: ['GET', 'POST'])]
    public function edit(Request \$request, ".$data['crud']['entity']." \$".strtolower($data['crud']['entity']).", int \$id, EntityManagerInterface \$entityManager): Response
    {
        if (!\$".strtolower($data['crud']['entity']).") {
            throw \$this->createNotFoundException(
                'No ".$data['crud']['entity']." found for id '.\$id
            );
        }
        \$form = \$this->createForm(".strtolower($data['crud']['form'])."::class, \$".strtolower($data['crud']['entity']).");
        \$form->handleRequest(\$request);

        if (\$form->isSubmitted() && \$form->isValid()) {
            \$entityManager->flush();
            \$this->addFlash('success', 'Edit Data Berhasil');
            return \$this->redirectToRoute('app_".$data['crud']['route']."_edit', ['id' => \$".strtolower($data['crud']['entity'])."->getId()], Response::HTTP_SEE_OTHER);
        }

        return \$this->render('".$data['crud']['route']."/edit.html.twig', [
            '".$data['crud']['route']."' => \$".$data['crud']['route'].",
            'form' => \$form
        ]);
    }";
$string .= "\n}";
        $path = $dir.'/Controller/'.$data['crud']['entity'].'Controller.php';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    public function type($dir, $data = []) : static
    {
        // generate form
        $form = "";
        foreach ($data['fields'] as $key => $value) {
            if ($value['type'] == 1) {
                $form .= "\n\t\t\t->add('".$value['name']."')";
            }
        }
        $string = "<?php

namespace App\Form;

use App\Entity\\".$data['crud']['entity'].";
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ".$data['crud']['form']." extends AbstractType
{
    public function buildForm(FormBuilderInterface \$builder, array \$options): void
    {
        \$builder".$form."
        ;
    }

    public function configureOptions(OptionsResolver \$resolver): void
    {
        \$resolver->setDefaults([
            'data_class' => ".$data['crud']['entity']."::class,
        ]);
    }
}
        ";
        $path = $dir.'/Form/'.$data['crud']['form'].'.php';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }


}