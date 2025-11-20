<?php

namespace App\Controller;

use App\Entity\RequestPosition;
use App\Model\PageRequest;
use App\Filter\RequestFilter;
use App\Repository\RequestPositionRepository;
use App\Service\FileStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class RequestPositionController extends AbstractController
{
    #[Route('/request/position/all', methods: ['GET'], name: 'request_position_all', format: 'json')]
    public function getRequestsAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $requests = $entityManager->getRepository(RequestPosition::class)->findAll();

        return $this->json($requests);
    }


    /**
     * Получает список позиций заявок на закупку (с фильтрацией, паджинацией и сортировкой)
     */
    #[Route('/request/position/filter', methods: ['GET'], name: 'request_position_filter', format: 'json')]
    public function getRequestsByFilter(
        #[MapQueryString] RequestFilter $filter, 
        #[MapQueryString] PageRequest $pageRequest, 
        RequestPositionRepository $repository): JsonResponse
    {
        $data = $repository->findAllByFilter($filter, $pageRequest);

        return $this->json($data);
    }


    #[Route('/request/position/{id}', methods: ['GET'], name: 'request_position_get', format: 'json')]
    public function getRequest(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($id);

        if (!$requestPosition) {
            throw $this->createNotFoundException('Не найдена запись с id='.$id);
        }

        return $this->json($requestPosition);
    }


    #[Route('/request/position', methods: ['POST'], name: 'request_position_create', format: 'json')]
    public function createRequest(#[MapRequestPayload] RequestPosition $requestPositionDto, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestPosition = new RequestPosition();
        $requestPosition->setRequestId($requestPositionDto->getRequestId());
        $requestPosition->setName($requestPositionDto->getName());
        $requestPosition->setDeliveryDate($requestPositionDto->getDeliveryDate());
        $requestPosition->setPrice($requestPositionDto->getPrice());
        $requestPosition->setQuantity($requestPositionDto->getQuantity());
        // TODO Вычислять totalPrice
        $requestPosition->setTotalPrice($requestPositionDto->getTotalPrice());

        // сообщаем Doctrine, что мы хотим (в итоге) сохранить Позицию заявки на закупку (пока без SQL-запросов)
        $entityManager->persist($requestPosition);

        // выполняем SQL-запросы (например, запрос INSERT)
        $entityManager->flush();

        return $this->json($requestPosition);
    }


    #[Route('/request/position', methods: ['PUT'], name: 'request_position_change', format: 'json')]
    public function changeRequest(#[MapRequestPayload] RequestPosition $requestPositionDto, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($requestPositionDto->getId());

        if (!$requestPosition) {
            throw $this->createNotFoundException('Не найдена запись с id='.$requestPositionDto->getId());
        }

        $requestPosition->setName($requestPositionDto->getName());
        $requestPosition->setDeliveryDate($requestPositionDto->getDeliveryDate());
        $requestPosition->setPrice($requestPositionDto->getPrice());
        $requestPosition->setQuantity($requestPositionDto->getQuantity());
        // TODO Вычислять totalPrice
        $requestPosition->setTotalPrice($requestPositionDto->getTotalPrice());

        // выполняем SQL-запрос (должен быть UPDATE)
        $entityManager->flush();

        return $this->json($requestPosition);
    }


    #[Route('/request/position/{id}', methods: ['DELETE'], name: 'request_position_delete', format: 'json')]
    public function deleteRequest(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($id);

        if (!$requestPosition) {
            throw $this->createNotFoundException('Не найдена запись с id='.$id);
        }

        // сообщаем Doctrine, что мы хотим удалить Позицию заявки на закупку (пока без SQL-запросов)
        $entityManager->remove($requestPosition);

        // выполняем SQL-запрос (должен быть DELETE)
        $entityManager->flush();

        return $this->json(['message' => 'Удаление выполнено']);
    }


    /**
     * Сохраняет файл позиции заявки в файловую систему
     */
    #[Route('/request/position/{id}/file', methods: ['POST'], name: 'request_position_file_save', format: 'json')]
    public function saveFile(
        int $id,
        #[MapUploadedFile] ?UploadedFile $file, 
        EntityManagerInterface $entityManager,
        FileStorage $fileStorage
    ): JsonResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($id);

        if (!$requestPosition) {
            throw $this->createNotFoundException('Не найдена запись с id='.$id);
        }

        // Запоминаем ссылку на старый файл
        $oldFileLink = $requestPosition->getFlFile();

        // Сохраняем новый файл в файловую систему
        $fileLink = $fileStorage->saveFile($file);

        $requestPosition->setFnFile($file->getClientOriginalName());
        $requestPosition->setFtFile($file->getClientMimeType());
        $requestPosition->setFlFile($fileLink);

        // выполняем SQL-запрос (должен быть UPDATE)
        $entityManager->flush();

        // Удаляем старый файл из файловой системы
        $fileStorage->deleteFile($oldFileLink);

        return $this->json(['message' => 'Файл загружен']);
    }


    /**
     * Удаляет файл позиции заявки из файловой системы
     */
    #[Route('/request/position/{id}/file', methods: ['DELETE'], name: 'request_position_file_delete', format: 'json')]
    public function deleteFile(
        int $id,
        EntityManagerInterface $entityManager,
        FileStorage $fileStorage
    ): JsonResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($id);

        if (!$requestPosition) {
            throw $this->createNotFoundException('Не найдена запись с id='.$id);
        }

        // Запоминаем ссылку на старый файл
        $oldFileLink = $requestPosition->getFlFile();

        $requestPosition->setFnFile(null);
        $requestPosition->setFtFile(null);
        $requestPosition->setFlFile(null);

        // выполняем SQL-запрос (должен быть UPDATE)
        $entityManager->flush();

        // Удаляем старый файл из файловой системы
        $fileStorage->deleteFile($oldFileLink);

        return $this->json(['message' => 'Файл удален']);
    }


    /**
     * Выгружает файл позиции заявки из файловой системы
     */
    #[Route('/request/position/{id}/file', methods: ['GET'], name: 'request_position_file_get', format: 'json')]
    public function readFile(
        int $id,
        EntityManagerInterface $entityManager,
        FileStorage $fileStorage
    ): BinaryFileResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($id);

        if (!$requestPosition) {
            throw $this->createNotFoundException('Не найдена запись с id='.$id);
        }

        if (!$requestPosition->getFlFile()) {
            throw $this->createNotFoundException('Запись с id='.$id.' не содержит файл');
        }

        return $fileStorage->getFile($requestPosition->getFlFile(),  $requestPosition->getFnFile());
    }
}
