<?php
namespace App\Service;

use App\Entity\Request;
use App\Entity\RequestPosition;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class RequestImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function loadRequestFromExcel(string $filename): Request
    {
        $spreadsheet = IOFactory::load($filename);

        // Первый лист excel-файла
        $sheet = $spreadsheet->getSheet(0);

        $request = new Request();
        $request->setCode($sheet->getCell([2, 2])->getValue());
        $request->setName($sheet->getCell([2, 3])->getValue());

        // $this->entityManager->persist($request);
        $this->saveRequest($request);

        foreach ($sheet->getRowIterator(6) as $row) {
            $rowNum = $row->getRowIndex();
            
            $position = new RequestPosition();

            // ID заявки заполняется автоматически с помощью ORM, но заполняется неправильно
            // (берется из единственной последовательности в схеме, а нужно брать из поля ID)
            $position->setRequestId($request->getId().'011');
            
            $position->setName($sheet->getCell([2, $rowNum])->getValue());
            $position->setDeliveryDate($this->getDateTime($sheet->getCell([3, $rowNum])));
            $position->setPrice($sheet->getCell([4, $rowNum])->getValue());
            $position->setQuantity($sheet->getCell([5, $rowNum])->getValue());
            $position->calcTotalPrice();

            // сообщаем Doctrine, что мы хотим (в итоге) сохранить Позицию заявки на закупку (пока без SQL-запросов)
            $this->entityManager->persist($position);
        }

        // выполняем SQL-запросы (в данном случае INSERT-ы)
        $this->entityManager->flush();

        return $request;
    }

    /**
     * Получает дату из ячейки Excel
     */
    public function getDateTime(Cell $cell): ?DateTime
    {
        if (Date::isDateTime($cell))
            return Date::excelToDateTimeObject($cell->getValue());
        else
            return null;
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