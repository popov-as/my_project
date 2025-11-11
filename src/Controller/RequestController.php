<?php

namespace App\Controller;

use App\Entity\Request;
use App\Filter\RequestFilter;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpFoundation\JsonResponse;

final class RequestController extends AbstractController
{
    #[Route('/request/page', name: 'request_page')]
    public function index(): Response
    {
        return $this->render('request/index.html.twig', [
            'controller_name' => 'RequestController',
        ]);
    }

    #[Route('/request/all', methods: ['GET'], name: 'request_all', format: 'json')]
    public function getRequestsAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $requests = $entityManager->getRepository(Request::class)->findAll();

        return $this->json($requests);
    }


    #[Route('/request/filter', methods: ['GET'], name: 'request_filter', format: 'json')]
    public function getRequestsByFilter(
        #[MapQueryParameter] string $code, 
        #[MapQueryParameter] string $name, 
        EntityManagerInterface $entityManager): JsonResponse
    {
        $requests = $entityManager->getRepository(Request::class)->findBy([
            'code' => $code,
            'name' => $name,
        ]);

        return $this->json($requests);
    }


    #[Route('/request/filter2', methods: ['GET'], name: 'request_filter2', format: 'json')]
    public function getRequestsByFilter2(
        #[MapQueryString] RequestFilter $filter, 
        RequestRepository $repository): JsonResponse
    {
        $requests = $repository->findAllByFilter($filter);

        return $this->json($requests);
    }


    #[Route('/request/{id}', methods: ['GET'], name: 'request_get', format: 'json')]
    public function getRequest(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $request = $entityManager->getRepository(Request::class)->find($id);

        if (!$request) {
            throw $this->createNotFoundException(
                'Не найдена запись с id='.$id
            );
        }

        return $this->json($request);
    }


    #[Route('/request', methods: ['POST'], name: 'request_create', format: 'json')]
    public function createRequest(#[MapRequestPayload] Request $requestDto, EntityManagerInterface $entityManager): JsonResponse
    {
        $request = new Request();
        $request->setCode($requestDto->getCode());
        $request->setName($requestDto->getName());
        $request->setStatusDate($requestDto->getStatusDate());
        $request->setPrice($requestDto->getPrice());
        $request->setQuantity($requestDto->getQuantity());

        // сообщаем Doctrine, что мы хотим (в итоге) сохранить Заявку на закупку (пока без SQL-запросов)
        $entityManager->persist($request);

        // выполняем SQL-запросы (например, запрос INSERT)
        $entityManager->flush();

        return $this->json($request);
    }


    #[Route('/request', methods: ['PUT'], name: 'request_change', format: 'json')]
    public function changeRequest(#[MapRequestPayload] Request $requestDto, EntityManagerInterface $entityManager): JsonResponse
    {
        $request = $entityManager->getRepository(Request::class)->find($requestDto->getId());

        if (!$request) {
            throw $this->createNotFoundException(
                'Не найдена запись с id='.$requestDto->getId()
            );
        }

        $request->setCode($requestDto->getCode());
        $request->setName($requestDto->getName());
        $request->setStatusDate($requestDto->getStatusDate());
        $request->setPrice($requestDto->getPrice());
        $request->setQuantity($requestDto->getQuantity());

        // выполняем SQL-запрос (должен быть UPDATE)
        $entityManager->flush();

        return $this->json($request);
    }


    #[Route('/request/{id}', methods: ['DELETE'], name: 'request_delete', format: 'json')]
    public function deleteRequest(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $request = $entityManager->getRepository(Request::class)->find($id);

        if (!$request) {
            throw $this->createNotFoundException(
                'Не найдена запись с id='.$id
            );
        }

        // сообщаем Doctrine, что мы хотим удалить Заявку на закупку (пока без SQL-запросов)
        $entityManager->remove($request);

        // выполняем SQL-запрос (должен быть DELETE)
        $entityManager->flush();

        return $this->json(['message' => 'Удаление выполнено']);
    }


}
