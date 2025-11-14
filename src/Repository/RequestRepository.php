<?php

namespace App\Repository;

use App\Entity\Request;
use App\Model\PageRequest;
use App\Model\PageResult;
use App\Filter\RequestFilter;
use App\Service\DataPaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;


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
     * Возвращает заявку на закупку по id (все поля кроме файла, чтобы быстро работало)
     */
    public function findById($id): ?Request
    {
        // Во фразе select нельзя исключить одно поле из сущности. 
        // Можно только явно перечислить все поля кроме одного
        $fields = $this->createQueryBuilder('p')
            ->select('p.id', 'p.code', 'p.name', 'p.statusDate', 'p.price', 'p.quantity', 'p.ftFile', 'p.fnFile')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (isset($fields)) {
            // Функция getOneOrNullResult() возвращает массив полей вместо объекта, если в select() явно перечислены поля
            $request = new Request();
            $request->setId($fields['id']);
            $request->setCode($fields['code']);
            $request->setName($fields['name']);
            $request->setStatusDate($fields['statusDate']);
            $request->setPrice($fields['price']);
            $request->setQuantity($fields['quantity']);
            $request->setFtFile($fields['ftFile']);
            $request->setFnFile($fields['fnFile']);
            return $request;
        } else {
            return null;
        }
    }


    /**
     * Возвращает файл заявки на закупку по id
     */
    public function findFileById($id): ?string
    {
        // Во фразе select нельзя исключить одно поле из сущности. 
        // Можно только явно перечислить все поля кроме одного
        $fields = $this->createQueryBuilder('p')
            ->select('p.fdFile')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (isset($fields)) {
            $resource = $fields['fdFile'];
            if (is_resource($resource))
                return stream_get_contents($resource);
            else
                return null;            
        } else {
            return null;
        }
    }
}
