<?php

namespace App\Service;

class CrudServicer
{
    public function controller($dir, $data = []) : static
    {
        $path = $dir.'/Controller/'.$data['crud']['entity'].'Controller.php';
        $string = "<?='Hello World'?>";
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    public function type($dir, $data = []) : static
    {
        $path = $dir.'/Form/'.$data['crud']['form'].'.php';

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
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }


}