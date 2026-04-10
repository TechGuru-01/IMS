<?php
require_once './config.php'; 
require '../../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

// --- 1. DATA FETCHING LOGIC ---
$selectedIds = isset($_GET['ids']) ? $_GET['ids'] : '';

if (!empty($selectedIds)) {
    $idArray = explode(',', $selectedIds);
    $cleanIds = implode(',', array_fill(0, count($idArray), '?'));
    $query = "SELECT * FROM history WHERE id IN ($cleanIds) ORDER BY description ASC, date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($idArray)), ...array_map('intval', $idArray));
} else {
    $m = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
    $y = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $query = "SELECT * FROM history WHERE MONTH(date) = ? AND YEAR(date) = ? ORDER BY description ASC, date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $m, $y);
}

$stmt->execute();
$result = $stmt->get_result();

// --- 2. SPREADSHEET INITIALIZATION ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Inventory History');

// --- 3. STYLES DEFINITION ---
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B22222']], // Firebrick Red
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]]
];

// Group Header (Light Red / Pinkish Background)
$groupHeaderStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '8B0000']], // Dark Red Text
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCEAEA']], // Very Light Red
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    'borders' => [
        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'B22222']],
        'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'B22222']]
    ]
];

// Standard Row Borders
$rowBorderStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E6EB']]]
];

// --- 4. REPORT TITLES ---
$sheet->setCellValue('A1', 'HEPC JIG IMS - TRANSACTION HISTORY REPORT');
$sheet->mergeCells('A1:I1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->setColor(new Color('B22222'));
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

$sheet->setCellValue('A2', 'Generated on: ' . date('F d, Y h:i A'));
$sheet->mergeCells('A2:I2');
$sheet->getStyle('A2')->getFont()->setSize(10)->setColor(new Color('65676B'));

// --- 5. TABLE HEADERS ---
$headers = ['DATE', 'ITEM NAME', 'USER', 'IN', 'OUT', 'CUSTOMER', 'EQUIPMENT', 'REMAINING', 'REMARKS'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '4', $h);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}
$sheet->getRowDimension('4')->setRowHeight(25); // Mas makapal na header
$sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

// --- 6. DATA POPULATION ---
$rowNum = 5;
$lastDesc = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        // Group Header (ITEM DESCRIPTION)
        if ($lastDesc !== $row['description']) {
            $lastDesc = $row['description'];
            $sheet->setCellValue('A' . $rowNum, '  ITEM: ' . strtoupper($lastDesc)); // May space para hindi dikit sa border
            $sheet->mergeCells("A$rowNum:I$rowNum");
            $sheet->getStyle("A$rowNum:I$rowNum")->applyFromArray($groupHeaderStyle);
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
        }

        // Row Data
        $sheet->setCellValue('A' . $rowNum, $row['date']);
        $sheet->setCellValue('B' . $rowNum, $row['item']);
        $sheet->setCellValue('C' . $rowNum, $row['name']);
        
        // IN (Green - standard para sa IN)
        $sheet->setCellValue('D' . $rowNum, (int)$row['quantity_in']);
        if ((int)$row['quantity_in'] > 0) {
            $sheet->getStyle('D' . $rowNum)->getFont()->setColor(new Color('228B22'))->setBold(true);
        }

        // OUT (Red - matching the theme)
        $sheet->setCellValue('E' . $rowNum, (int)$row['quantity_out']);
        if ((int)$row['quantity_out'] > 0) {
            $sheet->getStyle('E' . $rowNum)->getFont()->setColor(new Color('B22222'))->setBold(true);
        }

        $sheet->setCellValue('F' . $rowNum, $row['customer'] ?? 'N/A');
        $sheet->setCellValue('G' . $rowNum, $row['equipment'] ?? 'N/A');
        $sheet->setCellValue('H' . $rowNum, (int)$row['remaining']);
        $sheet->getStyle('H' . $rowNum)->getFont()->setBold(true);
        $sheet->setCellValue('I' . $rowNum, $row['remarks']);

        // Zebra Stripes (Light gray/white style)
        if ($rowNum % 2 == 0) {
            $sheet->getStyle("A$rowNum:I$rowNum")->getFill()
                  ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FAFAFA');
        }
        
        // Apply Borders
        $sheet->getStyle("A$rowNum:I$rowNum")->applyFromArray($rowBorderStyle);
        $sheet->getRowDimension($rowNum)->setRowHeight(18);

        $rowNum++;
    }
}
// --- 7. EXPORT ---
$filename = "Wide_Inventory_Report_" . date('Ymd_His') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();