<?php
namespace App\Service;

use App\Model\PageRequest;
use App\Model\PageResult;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

class DataPaginator
{
    public function __construct(
        public QueryBuilder $qb, 
        public PageRequest $pageRequest, 
    ) {
    }

    public function getPageResult(): PageResult
    {
        // Номер страницы
        $page = $this->pageRequest->getPage();

        // Количество строк на странице
        $pageSize = $this->pageRequest->getPageSize();

        // Выбираем строки для текущей страницы
        $data = $this->qb
            ->orderBy('p.id', 'ASC')
            ->setFirstResult($pageSize * ($page-1))  // Смещение для текущей страницы
            ->setMaxResults($pageSize)                // Количество строк на странице
            ->getQuery()
            ->getResult();

        // Всего строк
        $totalItems = $this->qb
            ->select('count(p)')
            ->resetDQLPart('orderBy')
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        // Всего страниц
        $pagesCount = ceil($totalItems / $pageSize);

        return new PageResult($data, $page, $pageSize, $totalItems, $pagesCount);
    }
}