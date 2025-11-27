<?php
namespace App\Service;

use App\Entity\Request;
use Doctrine\ORM\EntityManagerInterface;

class RequestService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function saveRequest(Request $request): Request
    {
        // сообщаем Doctrine, что мы хотим (в итоге) сохранить Заявку на закупку (пока без SQL-запросов)
        $this->entityManager->persist($request);

        // выполняем SQL-запросы (в данном случае INSERT)
        $this->entityManager->flush();

        return $request;
    }

}