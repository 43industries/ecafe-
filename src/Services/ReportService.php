<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderModel;
use App\Models\MenuModel;
use App\Models\StudentModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    public function salesPdf(): string
    {
        $orders = new OrderModel();
        $stats = $orders->getStats();
        $sales = $orders->getSalesByDay(30);

        $html = '<h1>Sales Report</h1>';
        $html .= '<p>Total Orders: ' . $stats['total'] . '</p>';
        $html .= '<p>Daily: KES ' . number_format($stats['daily'], 2) . '</p>';
        $html .= '<p>Weekly: KES ' . number_format($stats['weekly'], 2) . '</p>';
        $html .= '<p>Monthly: KES ' . number_format($stats['monthly'], 2) . '</p>';
        $html .= '<table border="1" cellpadding="5"><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr>';
        foreach ($sales as $row) {
            $html .= '<tr><td>' . $row['day'] . '</td><td>' . $row['orders'] . '</td><td>' . number_format((float)$row['revenue'], 2) . '</td></tr>';
        }
        $html .= '</table>';

        $dir = ECAFE_ROOT . '/storage/receipts';
        $path = $dir . '/sales_report_' . date('Ymd') . '.pdf';

        if (class_exists(Dompdf::class)) {
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            file_put_contents($path, $dompdf->output());
        } else {
            file_put_contents($path, strip_tags($html));
        }

        return $path;
    }

    public function salesExcel(): string
    {
        $orders = new OrderModel();
        $sales = $orders->getSalesByDay(30);

        $dir = ECAFE_ROOT . '/storage/receipts';
        $path = $dir . '/sales_report_' . date('Ymd') . '.xlsx';

        if (class_exists(Spreadsheet::class)) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray(['Date', 'Orders', 'Revenue'], null, 'A1');
            $row = 2;
            foreach ($sales as $s) {
                $sheet->setCellValue("A{$row}", $s['day']);
                $sheet->setCellValue("B{$row}", $s['orders']);
                $sheet->setCellValue("C{$row}", $s['revenue']);
                $row++;
            }
            (new Xlsx($spreadsheet))->save($path);
        }

        return $path;
    }

    public function getAnalyticsData(): array
    {
        $orders = new OrderModel();
        $menu = new MenuModel();
        $students = new StudentModel();

        return [
            'stats' => $orders->getStats(),
            'salesByDay' => $orders->getSalesByDay(7),
            'popularFoods' => $menu->getPopular(5),
            'studentCount' => $students->count(),
        ];
    }
}
