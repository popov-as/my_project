<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\RequestPosition;
use App\Model\PageRequest;
use App\Model\PageResult;
use App\Filter\RequestFilter;
use App\Service\DataPaginator;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

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
     * @return RequestPosition[] Returns an array of Product objects
     */
    public function findAllByFilter(EntityManagerInterface $entityManager, RequestFilter $filter, LoggerInterface $logger): array
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('p.id', 'r.code as requestCode', 'r.name as requestName', 'p.name', 'p.quantity', 'p.price')
            ->from(RequestPosition::class, 'p')
            ->innerJoin(Request::class, 'r', Join::WITH, 'p.requestId = r.id')
            ->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
        ;

        //$logger->info('---TEST---');
        //$logger->info($qb->getQuery());

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
     * @return PageResult<RequestPosition> Returns an array of Product objects
     */
    public function findAllByFilterPagination(RequestFilter $filter, PageRequest $pageRequest): PageResult
    {
        $qb =  $this->getEntityManager()->createQueryBuilder();

        $qb->select('p.id', 'r.code as requestCode', 'r.name as requestName', 'p.name', 'p.quantity', 'p.price')
            ->from(RequestPosition::class, 'p')
            ->innerJoin(Request::class, 'r', Join::WITH, 'p.requestId = r.id')
            //->orderBy('p.id', 'ASC')
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

        
        // // Количество строк на странице
        // $pageSize = 10;

        // // Создаем doctrine Paginator
        // //$paginator = new Paginator($qb, fetchJoinCollection: false);

        // // Всего строк
        // $totalItems = $qb->select('count(p)')->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        // $qb->select('p.id', 'r.code as requestCode', 'r.name as requestName', 'p.name', 'p.quantity', 'p.price');

        // // Всего страниц
        // $pagesCount = ceil($totalItems / $pageSize);

        // // Выбираем строки для текущей страницы
        // $qb
        //     ->setFirstResult($pageSize * ($page-1)) // Устанавливаем смещение для текущей страницы
        //     ->setMaxResults($pageSize); // Количество строк на странице
        //
        // new PageResult($qb->getQuery()->getResult(), $page, $pageSize, $totalItems, $pagesCount);

        
        // TODO Добавить сортировку в качестве параметра
        $paginator = new DataPaginator($qb, $pageRequest);
        return $paginator->getPageResult();
    }

}
