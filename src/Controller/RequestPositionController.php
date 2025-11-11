<?php

namespace App\Controller;

use App\Entity\RequestPosition;
use App\Model\PageRequest;
use App\Filter\RequestFilter;
use App\Repository\RequestPositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpFoundation\JsonResponse;

final class RequestPositionController extends AbstractController
{
    #[Route('/request/position/all', methods: ['GET'], name: 'request_position_all', format: 'json')]
    public function getRequestsAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $requests = $entityManager->getRepository(RequestPosition::class)->findAll();

        return $this->json($requests);
    }


    #[Route('/request/position/filter', methods: ['GET'], name: 'request_position_filter', format: 'json')]
    public function getRequestsByFilter(
        #[MapQueryParameter] string $code, 
        #[MapQueryParameter] string $name, 
        EntityManagerInterface $entityManager): JsonResponse
    {
        $requests = $entityManager->getRepository(RequestPosition::class)->findBy([
            'code' => $code,
            'name' => $name,
        ]);

        return $this->json($requests);
    }


    #[Route('/request/position/filter2', methods: ['GET'], name: 'request_position_filter2', format: 'json')]
    public function getRequestsByFilter2(
        #[MapQueryString] RequestFilter $filter, 
        EntityManagerInterface $entityManager,
        RequestPositionRepository $repository, 
        LoggerInterface $logger): JsonResponse
    {
        $requests = $repository->findAllByFilter($entityManager, $filter, $logger);

        return $this->json($requests);
    }


    #[Route('/request/position/filter_pagin', methods: ['GET'], name: 'request_position_filter_pagin', format: 'json')]
    public function getRequestsByFilterPagination(
        #[MapQueryString] RequestFilter $filter, 
        RequestPositionRepository $repository): JsonResponse
    {
        $requests = $repository->findAllByFilterPagination($filter, new PageRequest(1, 10));

        return $this->json($requests);
    }


    #[Route('/request/position/{id}', methods: ['GET'], name: 'request_position_get', format: 'json')]
    public function getRequest(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestPosition = $entityManager->getRepository(RequestPosition::class)->find($id);

        if (!$requestPosition) {
            throw $this->createNotFoundException(
                'Не найдена запись с id='.$id
            );
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
            throw $this->createNotFoundException(
                'Не найдена запись с id='.$requestPositionDto->getId()
            );
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
            throw $this->createNotFoundException(
                'Не найдена запись с id='.$id
            );
        }

        // сообщаем Doctrine, что мы хотим удалить Позицию заявки на закупку (пока без SQL-запросов)
        $entityManager->remove($requestPosition);

        // выполняем SQL-запрос (должен быть DELETE)
        $entityManager->flush();

        return $this->json(['message' => 'Удаление выполнено']);
    }

}
