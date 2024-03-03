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


}