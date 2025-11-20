<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileStorage
{
    const UPLOAD_DIRECTORY = '/public/uploads';
    private string $projectDir;

    public function __construct(
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ) {
        $this->projectDir = $projectDir;
    }

    private function getDirectory(): string
    {
        return $this->projectDir . FileStorage::UPLOAD_DIRECTORY;
    }


    public function saveFile(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($this->getDirectory(), $filename);

        //try {
        //    $file->move($uploadsDirectory, $filename);
        //} catch (FileException $e) {
        //    // Выдать свою ошибку
        //}
        
        return $filename;
    }

    
    public function getFile(string $fileLink, string $fileName): BinaryFileResponse
    {
        $response = new BinaryFileResponse($this->getDirectory() . '/' . $fileLink);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

}