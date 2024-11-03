<?php

namespace App\Service;

class CrudServicer
{
    public function controller($dir, $data = []) : static
    {
        $arrayUse = [];
        foreach ($data['fields'] as $key => $value) {
            // jika type image maka store use
            if ($value['type'] == 3 || $value['type'] == 5) {
                array_push($arrayUse, 'use App\Service\FileUploader;');
            }
        }
        $arrayUseUnique = array_unique($arrayUse);
        $stringUse = implode("\n", $arrayUseUnique);
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
".$stringUse."

class ".$data['crud']['entity']."Controller extends AbstractController
{
    #[Route('/".strtolower($data['crud']['route'])."/index', name: 'app_".strtolower($data['crud']['route'])."')]
    public function index(): Response
    {
        \$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Not Allowed Access');

        return \$this->render('".strtolower($data['crud']['route'])."/index.html.twig', [
        ]);
    }";

    $fieldDatatable = '';
    $searchDatatable = "['id'";
    $orderDatatable = "[null, null";
    foreach ($data['fields'] as $key => $value) {
        if ($value['datatable'] == 1) {
            if ($value['type'] == 4) {
                $fieldDatatable .= "\n\t\t\t\$row[] = number_format(\$value['".$value['name']."'], 0, ',', '.');";
            } else {
                $fieldDatatable .= "\n\t\t\t\$row[] = \$value['".$value['name']."'];";
            }
            $searchDatatable .= ", '".$value['name']."'";
            $orderDatatable .= ", '".$value['name']."'";
        }
    }
    $searchDatatable .= "]";
    $orderDatatable .= "]";
$string .= "\n\n\t#[Route(path: '/".strtolower($data['crud']['route'])."/ajax', name: 'app_".strtolower($data['crud']['route'])."_ajax', methods: ['POST'])]
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
            \$row[] = \"<a href='\".\$this->generateUrl('app_".strtolower($data['crud']['route'])."_show', ['id' => \$value['id']]).\"' class='btn btn-sm btn-primary mr-1'><i class='fa fa-search'></i></a><a href='\".\$this->generateUrl('app_".strtolower($data['crud']['route'])."_edit', ['id' => \$value['id']]).\"' class='btn btn-sm btn-info'><i class='fa fa-edit'></i></a>\";".$fieldDatatable."
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

$string .= "\n\n\t#[Route('/".strtolower($data['crud']['route'])."/{id}/show', name: 'app_".strtolower($data['crud']['route'])."_show', methods: ['GET'])]
    public function show(".$data['crud']['entity']." \$".strtolower($data['crud']['entity'])."): Response
    {
        return \$this->render('".strtolower($data['crud']['route'])."/show.html.twig', [
            '".strtolower($data['crud']['entity'])."' => \$".strtolower($data['crud']['entity']).",
        ]);
    }";

    $arrayParams = [];

    // set Data Before
    $setBeforeData = "";

    // set Data After Submit Form
    $setData = "";
    foreach ($data['fields'] as $key => $value) {
        // jika type image
        if ($value['type'] == 3) {
            // set param di function
            array_push($arrayParams, 'FileUploader $fileUploader');

            // get data dari form dan memproses
            $setData .= "\$".$value['name']." = \$form->get('".$value['name']."')->getData();
            if (\$".$value['name'].") {
                \$fileUploader->setDir('');
                \$dir = \$this->getParameter('image_directory');
                \$fileUploader->setTargetDirectory(\$dir.'/".strtolower($data['crud']['entity'])."');
                \$".$value['name']."FileName = \$fileUploader->upload(\$".$value['name'].");
                \$".strtolower($data['crud']['entity'])."->set".ucwords($value['name'])."(\$".$value['name']."FileName);
            }";
        }

        // jika Rupiah 
        if ($value['type'] == 4) {
            $setBeforeData .= "\$".strtolower($data['crud']['entity'])."->set".ucwords(strtolower($value['name']))."(number_format(\$".strtolower($data['crud']['entity'])."->get".ucwords(strtolower($value['name']))."(), 0, ',', '.'));";

            $setData .= "\$".$value['name']." = \$form->get('".$value['name']."')->getData();
            \$".strtolower($data['crud']['entity'])."->set".ucwords(strtolower($value['name']))."(str_replace('.', '', \$".$value['name']."));";
        }

        // jika type pdf
        if ($value['type'] == 5) {
            // set param di function
            array_push($arrayParams, 'FileUploader $fileUploader');

            // get data dari form dan memproses
            $setData .= "\$".$value['name']." = \$form->get('".$value['name']."')->getData();
            if (\$".$value['name'].") {
                \$fileUploader->setDir('');
                \$dir = \$this->getParameter('pdf_directory');
                \$fileUploader->setTargetDirectory(\$dir.'/".strtolower($data['crud']['entity'])."');
                \$".$value['name']."FileName = \$fileUploader->upload(\$".$value['name'].");
                \$".strtolower($data['crud']['entity'])."->set".ucwords($value['name'])."(\$".$value['name']."FileName);
            }";
        }
    }
    $arrayParamsUnique = array_unique($arrayParams);
    $stringParams = implode(", ", $arrayParamsUnique);
$string .= "\n\n\t#[Route(path: '/".strtolower($data['crud']['route'])."/new', name: 'app_".strtolower($data['crud']['route'])."_new', methods: ['GET', 'POST'])]
    public function new(Request \$request, EntityManagerInterface \$entityManager, ".$stringParams."): Response
    {
        \$".strtolower($data['crud']['entity'])." = new ".$data['crud']['entity']."();
        ".$setBeforeData."\n
        \$form = \$this->createForm(".$data['crud']['form']."::class, \$".strtolower($data['crud']['entity']).");
        \$form->handleRequest(\$request);
        if (\$form->isSubmitted() && \$form->isValid()) {
            ".$setData."
            \$entityManager->persist(\$".strtolower($data['crud']['entity']).");
            \$entityManager->flush();
            \$this->addFlash('success', 'Simpan Data Berhasil');
            return \$this->redirectToRoute('app_".strtolower($data['crud']['route'])."_edit', ['id' => \$".strtolower($data['crud']['entity'])."->getId()], Response::HTTP_SEE_OTHER);
        }
        return \$this->render('".strtolower($data['crud']['route'])."/new.html.twig', [
            '".strtolower($data['crud']['entity'])."' => \$".strtolower($data['crud']['entity']).",
            'form' => \$form
        ]);
    }";
$string .= "\n\n\t#[Route('/".strtolower($data['crud']['route'])."/{id}/edit', name: 'app_".strtolower($data['crud']['route'])."_edit', methods: ['GET', 'POST'])]
    public function edit(Request \$request, ".$data['crud']['entity']." \$".strtolower($data['crud']['entity']).", int \$id, EntityManagerInterface \$entityManager, ".$stringParams."): Response
    {
        if (!\$".strtolower($data['crud']['entity']).") {
            throw \$this->createNotFoundException(
                'No ".$data['crud']['entity']." found for id '.\$id
            );
        }
        ".$setBeforeData."\n
        \$form = \$this->createForm(".strtolower($data['crud']['form'])."::class, \$".strtolower($data['crud']['entity']).");
        \$form->handleRequest(\$request);

        if (\$form->isSubmitted() && \$form->isValid()) {
            ".$setData."
            \$entityManager->flush();
            \$this->addFlash('success', 'Edit Data Berhasil');
            return \$this->redirectToRoute('app_".strtolower($data['crud']['route'])."_edit', ['id' => \$".strtolower($data['crud']['entity'])."->getId()], Response::HTTP_SEE_OTHER);
        }

        return \$this->render('".strtolower($data['crud']['route'])."/edit.html.twig', [
            '".strtolower($data['crud']['route'])."' => \$".strtolower($data['crud']['route']).",
            'form' => \$form
        ]);
    }";

    $string .= "\n\n\t#[Route('/".strtolower($data['crud']['route'])."/{id}/delete', name: 'app_".strtolower($data['crud']['route'])."_delete', methods: ['POST'])]
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
            // jika type textarea
            if ($value['type'] == 2) {
                $form .= "\n\t\t\t->add('".$value['name']."', TextareaType::class, [
                'label' => '".$value['label']."'
            ])";
                array_push($arrayUse, 'use Symfony\Component\Form\Extension\Core\Type\TextareaType;');
            }
            // jika type gambar
            if ($value['type'] == 3) {
                $form .= "\n\t\t\t->add('".$value['name']."', FileTypeType::class, [
                'label' => '".$value['label']."',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image'
                    ])
                ],
                'attr' => [
                    'accept' => '.jpg,.jpeg,.img,.png',
                ]
            ])";
                array_push($arrayUse, 'use Symfony\Component\Form\Extension\Core\Type\FileType as FileTypeType;');
                array_push($arrayUse, 'use Symfony\Component\Validator\Constraints\File as FileConstraints;');
            }

            // if Rupiah
            if ($value['type'] == 4) {
                $form .= "\n\t\t\t->add('".$value['name']."', TextType::class, [
                'label' => '".$value['label']."',
                'attr' => [
                    'onkeyup' => 'formatRupiah(\"".strtolower($data['crud']['entity'])."_".strtolower($value['name'])."\")',
                    'onkeydown' => 'formatRupiah(\"".strtolower($data['crud']['entity'])."_".strtolower($value['name'])."\")'
                ]
            ])";
            }

            // jika pdf
            if ($value['type'] == 5) {
                $form .= "\n\t\t\t->add('".$value['name']."', FileTypeType::class, [
                'label' => '".$value['label']."',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF'
                    ])
                ],
                'attr' => [
                    'accept' => '.pdf',
                ]
            ])";
                array_push($arrayUse, 'use Symfony\Component\Form\Extension\Core\Type\FileType as FileTypeType;');
                array_push($arrayUse, 'use Symfony\Component\Validator\Constraints\File as FileConstraints;');
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