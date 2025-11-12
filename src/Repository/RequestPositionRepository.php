<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\RequestPosition;
use App\Model\PageRequest;
use App\Model\PageResult;
use App\Filter\RequestFilter;
use App\Service\DataPaginator;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestPosition>
 */
class RequestPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestPosition::class);
    }


    /**
     * Получает список позиций заявок на закупку
     * @return PageResult<RequestPosition> 
     */
    public function findAllByFilterPagination(RequestFilter $filter, PageRequest $pageRequest): PageResult
    {
        $qb =  $this->getEntityManager()->createQueryBuilder();

        $qb->select('p.id', 'r.code as requestCode', 'r.name as requestName', 'p.name', 'p.quantity', 'p.price')
            ->from(RequestPosition::class, 'p')
            ->innerJoin(Request::class, 'r', Join::WITH, 'p.requestId = r.id')
        ;

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

        // TODO Сделать, чтобы конструктор сразу возвращал нужный объект (без вызова getPageResult())
        $paginator = new DataPaginator($qb, $pageRequest);
        return $paginator->getPageResult();
    }

}
