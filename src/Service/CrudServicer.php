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
            \$row[] = \"<a href='\".\$this->generateUrl('app_".$data['crud']['route']."_show', ['id' => \$value['id']]).\"' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='\".\$this->generateUrl('app_".$data['crud']['route']."_edit', ['id' => \$value['id']]).\"' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>\";".$fieldDatatable."
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

$string .= "\n\n\t#[Route('/".$data['crud']['route']."/{id}/show', name: 'app_".$data['crud']['route']."_show', methods: ['GET'])]
    public function show(".$data['crud']['entity']." \$".strtolower($data['crud']['entity'])."): Response
    {
        return \$this->render('".$data['crud']['route']."/show.html.twig', [
            '".strtolower($data['crud']['entity'])."' => \$".strtolower($data['crud']['entity']).",
        ]);
    }";

$string .= "\n\n\t#[Route(path: '/".$data['crud']['route']."/new', name: 'app_".$data['crud']['route']."_new', methods: ['GET', 'POST'])]
    public function new(Request \$request, EntityManagerInterface \$entityManager): Response
    {
        \$".strtolower($data['crud']['entity'])." = new ".$data['crud']['entity']."();
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

    $string .= "\n\n\t#[Route('/".$data['crud']['route']."/{id}/delete', name: 'app_".$data['crud']['route']."_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface \$entityManager, Request \$request, ".$data['crud']['entity']." \$".strtolower($data['crud']['entity'])."): Response
    {
        if (\$this->isCsrfTokenValid('delete'.\$".strtolower($data['crud']['entity'])."->getId(), \$request->request->get('_token'))) {
            \$entityManager->remove(\$".strtolower($data['crud']['entity']).");
            \$entityManager->flush();
        }

        return \$this->json([
            \"info\" => \"success\",
            \"message\" => \"Delete Data Berhasil\"
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
        $arrayUse = [];
        foreach ($data['fields'] as $key => $value) {
            if ($value['type'] == 1) {
                $form .= "\n\t\t\t->add('".$value['name']."', TextType::class, [
                'label' => '".$value['label']."'
            ])";
                array_push($arrayUse, 'use Symfony\Component\Form\Extension\Core\Type\TextType;');
            }
            if ($value['type'] == 2) {
                $form .= "\n\t\t\t->add('".$value['name']."', TextareaType::class, [
                'label' => '".$value['label']."'
            ])";
                array_push($arrayUse, 'use Symfony\Component\Form\Extension\Core\Type\TextareaType;');
            }
        }
        $arrayUseUnique = array_unique($arrayUse);
        $stringUse = implode("\n", $arrayUseUnique);

        $string = "<?php

namespace App\Form;

use App\Entity\\".$data['crud']['entity'].";
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
".$stringUse."

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