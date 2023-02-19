<?php
namespace App\Service;

use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{


    public function __construct( private string $toyDirectory, private SluggerInterface $slugger  )
    {

    }

    public function upload(UploadedFile $file): string
    {
        $toyFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($toyFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        }catch (FileException $e) {
        // ... handle exception if something happens during file upload
        }

    return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->toyDirectory;
    }
}