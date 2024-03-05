<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class CrudTwigServicer 
{
    public function index($data = []) : static
    {
        $this->createFolder($data['crud']['route']);
        return $this;
    }

    private function createFolder($dir) : static
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir('templates/'.$dir);
        return $this;
    }
}