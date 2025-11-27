<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * Получает полный путь к файлу по его имени
     */
    public function getFilePath(string $filename): string
    {
        return $this->getDirectory() . '/' . $filename;
    }

    /**
     * Сохраняет файл в файловую систему.
     * Возвращает системное имя файла (относительный путь)
     */
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

    
    /**
     * Возвращает файл в виде HTTP-ответа
     */
    public function getFileResponse(string $filename, string $originalFileName): BinaryFileResponse
    {
        $response = new BinaryFileResponse($this->getFilePath($filename));

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $originalFileName
        );

        return $response;
    }

    /**
     * Удаляет файл из файловой системы
     */
    public function deleteFile(?string $filename): void
    {
        if (isset($filename)) {
            $filesystem = new Filesystem();

            $filePath = $this->getFilePath($filename);

            if ($filesystem->exists($filePath)) {
                $filesystem->remove($filePath);
            }
        }
    }

}