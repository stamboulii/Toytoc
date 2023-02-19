<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final  class FileUploader
{
    public function __construct(private readonly string $toyDirectory, private readonly SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file): string
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
}
