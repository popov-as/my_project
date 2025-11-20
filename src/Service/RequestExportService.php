<?php
namespace App\Service;

use App\Entity\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RequestExportService
{
    public function __construct(
    ) {
    }

    public function generateExcel(Request $request, array $positions): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Стиль для заголовков
        $headerStyle = $this->getHeaderStyle();

        $row = 1;

        $sheet->setCellValue([1, $row], 'Id');
        $sheet->getStyle([1, $row])->getFont()->setBold(true);
        $row = $row + 1;

        $sheet->setCellValue([1, $row], 'Код');
        $sheet->setCellValue([2, $row], $request->getCode());
        $row = $row + 1;

        $sheet->setCellValue([1, $row], 'Наименование');
        $sheet->setCellValue([2, $row], $request->getName());
        $row = $row + 1;
        // Стиль для первого столбца с именами полей
        $sheet->getStyle([1, $row-3, 1, $row-1])->applyFromArray($headerStyle);

        $row = $row + 1;

        // Шапка таблицы с позициями заявки
        $sheet->setCellValue([1, $row], 'Id');
        $sheet->setCellValue([2, $row], 'Наименование');
        $sheet->setCellValue([3, $row], 'Дата поставки');
        $sheet->setCellValue([4, $row], 'Цена');
        $sheet->setCellValue([5, $row], 'Количество');
        $sheet->setCellValue([6, $row], 'Сумма');
        // Стиль для всей шапки
        $sheet->getStyle([1, $row, 6, $row])->applyFromArray($headerStyle);
        $row = $row + 1;

        // Печать позиций заявки
        foreach ($positions as $position) {
            $sheet->setCellValue([1, $row], $position->getId());
            $sheet->setCellValue([2, $row], $position->getName());
            $sheet->setCellValue([3, $row], $position->getDeliveryDate());
            $sheet->setCellValue([4, $row], $position->getPrice());
            $sheet->setCellValue([5, $row], $position->getQuantity());
            $sheet->setCellValue([6, $row], $position->getTotalPrice());
            $row = $row + 1;
        }

        return $spreadsheet;
    }


    /**
     * Стиль для шапки таблицы
     */
    private function getHeaderStyle(): array
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'cccccc'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'eeeeee',
                ],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '444444'],
            ],
        ];
    }
}