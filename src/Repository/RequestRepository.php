<?php

namespace App\Repository;

use App\Entity\Request;
use App\Filter\RequestFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    /**
     * @return Request[] Возвращает список заявок на закупку с учетом фильтра
     */
    public function findAllByFilter(RequestFilter $filter): array
    {
        // автоматически знает, что надо выбирать Заявки на закупку
        // "p" - это псевдоним, который вы будете использовать до конца запроса
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
        ;

        if (isset($filter->code)) {
            $qb->andWhere('p.code like :code')
            ->setParameter('code', '%'.$filter->getCode().'%');
        }

        if (isset($filter->name)) {
            $qb->andWhere('p.name like :name')
            ->setParameter('name', '%'.$filter->getName().'%');
        }

        if (isset($filter->priceFrom)) {
            $qb->andWhere('p.price >= :priceFrom')
            ->setParameter('priceFrom', $filter->getPriceFrom());
        }

        if (isset($filter->priceTo)) {
            $qb->andWhere('p.price <= :priceTo')
            ->setParameter('priceTo', $filter->getPriceTo());
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * @return Request Возвращает заявку на закупку по коду (просто ради упражнения)
     */
    public function findOneByCode($code): ?Request
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.code = :val')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
