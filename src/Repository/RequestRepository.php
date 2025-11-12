<?php

namespace App\Repository;

use App\Entity\Request;
use App\Model\PageRequest;
use App\Model\PageResult;
use App\Filter\RequestFilter;
use App\Service\DataPaginator;
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
     * @return PageResult<Request> Возвращает список заявок на закупку (с учетом фильтра, паджинации и сортировки)
     */
    public function findAllByFilter(RequestFilter $filter, PageRequest $pageRequest): PageResult
    {
        // автоматически знает, что надо выбирать Заявки на закупку
        // "p" - это псевдоним, который вы будете использовать до конца запроса
        $qb = $this->createQueryBuilder('r');

        if (isset($filter->code)) {
            $qb->andWhere('r.code like :code')
            ->setParameter('code', '%'.$filter->getCode().'%');
        }

        if (isset($filter->name)) {
            $qb->andWhere('r.name like :name')
            ->setParameter('name', '%'.$filter->getName().'%');
        }

        if (isset($filter->priceFrom)) {
            $qb->andWhere('r.price >= :priceFrom')
            ->setParameter('priceFrom', $filter->getPriceFrom());
        }

        if (isset($filter->priceTo)) {
            $qb->andWhere('r.price <= :priceTo')
            ->setParameter('priceTo', $filter->getPriceTo());
        }

        // TODO Сделать, чтобы конструктор сразу возвращал нужный объект (без вызова getPageResult())
        $paginator = new DataPaginator($qb, $pageRequest);
        return $paginator->getPageResult();
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
