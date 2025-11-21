<?php

namespace App\Service;

use App\Entity\Data;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    public function generateProductsExcel(array $products): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Nagłówki
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Product');
        $sheet->setCellValue('D1', 'Color');
        $sheet->setCellValue('E1', 'Amount');

        // Dane
        $row = 2;
        foreach ($products as $p) {
            $sheet->setCellValue("A{$row}", $p->getId());
            $sheet->setCellValue("B{$row}", $p->getDate()?->format('Y-m-d H:i'));
            $sheet->setCellValue("C{$row}", $p->getProduct());
            $sheet->setCellValue("D{$row}", $p->getColor());
            $sheet->setCellValue("E{$row}", $p->getAmount());
            $row++;
        }

        return $spreadsheet;
    }

    public function getWriter(Spreadsheet $spreadsheet): Xlsx
    {
        return new Xlsx($spreadsheet);
    }
}
