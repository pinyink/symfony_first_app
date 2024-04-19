<?php
// src/Service/FileUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $targetDirectory;
    private string $dir;

    public function __construct(
        private SluggerInterface $slugger
    ) {
        
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $path = date('Y/m/d/');
        try {
            if ($this->getDir() == null) {
                $file->move($this->getTargetDirectory().'/'.$path, $fileName);
            } else {
                $file->move($this->getTargetDirectory().'/'.$this->getDir().'/'.$path, $fileName);
            }
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            return $e->getMessage();
        }

        return $path.$fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function setTargetDirectory(String $parameter): static
    {
        $this->targetDirectory = $parameter;
        return $this;
    }

    /**
     * Get the value of dir
     */ 
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set the value of dir
     *
     * @return  self
     */ 
    public function setDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }
}