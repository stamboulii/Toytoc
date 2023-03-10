<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final  class FileUploader
{

    public function __construct(private readonly string $toyDirectory, private readonly SluggerInterface $slugger)
    {

    }

    public function upload(UploadedFile $file,UploadedFile $picture): string
    {
        $toyFilename  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($toyFilename);
        $fileName     = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->toyDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;


    }


//    public function delete(string $file, ?string $folder = '', ?int $width = 250, ?int $height = 250)
//    {
//        if($file !== 'default.webp'){
//            $success = false;
//            $path = $this->params->get('images_directory') . $folder;
//
//            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $file;
//
//            if(file_exists($mini)){
//                unlink($mini);
//                $success = true;
//            }
//
//            $original = $path . '/' . $file;
//
//            if(file_exists($original)){
//                unlink($original);
//                $success = true;
//            }
//            return $success;
//        }
//        return false;
//    }
}
