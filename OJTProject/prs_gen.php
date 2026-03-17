<?php
require './include/config.php'; 
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ref_number'])) {
    // --- 1. CAPTURE HEADER DATA ---
    $ref_no      = $_POST['ref_number'];
    $company     = $_POST['company'];
    $currency    = $_POST['currency'];
    $grandTotal  = floatval($_POST['total_amount']);
    $remarks     = $_POST['remarks'];
    $pr_date     = $_POST['pr_date'] ?? date('Y-m-d'); 
    
    $uom         = $_POST['uom'] ?? '';
    $rm_fg       = $_POST['rm_fg'] ?? ''; 
    $tor         = $_POST['ToR'] ?? '';   

    // TUGMAAN NATIN SA JAVASCRIPT NAMES
    $names       = $_POST['item_names'] ?? []; 
    $descs       = $_POST['item_descs'] ?? [];
    $makers      = $_POST['item_makers'] ?? [];
    $qtys        = $_POST['item_qtys'] ?? [];
    $prices      = $_POST['item_prices'] ?? [];
    $ids         = $_POST['acknowledge_ids'] ?? [];

    // --- 2. DATABASE TRANSACTION ---
    $conn->begin_transaction();
    try {
        // I-save ang Main Report (Dito aakyat yung ID para maging 18 next time)
        $stmt = $conn->prepare("INSERT INTO pr_reports (ref_number, pr_date, company, currency, total_amount, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $ref_no, $pr_date, $company, $currency, $grandTotal, $remarks);
        $stmt->execute();

        // I-save ang bawat Item
        $item_stmt = $conn->prepare("INSERT INTO pr_items (pr_ref_number, material_name, description, maker, quantity, unit_price) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($names as $index => $name) {
            $curr_desc  = $descs[$index] ?? '';
            $curr_maker = $makers[$index] ?? '';
            $curr_qty   = floatval($qtys[$index] ?? 0);
            $curr_price = floatval($prices[$index] ?? 0);

            $item_stmt->bind_param("ssssdd", $ref_no, $name, $curr_desc, $curr_maker, $curr_qty, $curr_price);
            $item_stmt->execute();
        }

        // I-update ang Inventory Alerts
        if (!empty($ids)) {
            $update_stmt = $conn->prepare("UPDATE inventory SET is_acknowledged = 1 WHERE id = ?");
            foreach ($ids as $id) {
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("DB Error: " . $e->getMessage());
    }

    // --- 3. EXCEL GENERATION ---
    try {
        $spreadsheet = IOFactory::load('PRS_Template.xlsx');
        $baseSheet = $spreadsheet->getActiveSheet();
        
        $itemsPerPage = 19; 
        $totalItems = count($names);
        $pageCount = ceil($totalItems / $itemsPerPage);

        for ($p = 0; $p < $pageCount; $p++) {
            if ($p > 0) {
                $newSheet = clone $baseSheet;
                $newSheet->setTitle("Page " . ($p + 1));
                $spreadsheet->addSheet($newSheet);
                $sheet = $newSheet;
            } else {
                $sheet = $baseSheet;
                $sheet->setTitle("Page 1");
            }

            // HEADER
            $sheet->setCellValue('F6', $company);
            $sheet->setCellValue('D7', $ref_no);
            $sheet->setCellValue('AL7', $pr_date);

            // BODY MAPPING (Looping through items per page)
            for ($i = 0; $i < $itemsPerPage; $i++) {
                $idx = ($p * $itemsPerPage) + $i;
                if ($idx >= $totalItems) break;

                $row = 17 + $i;
                
                $sheet->setCellValue('A' . $row, $idx + 1); 
                $sheet->setCellValue('B' . $row, $names[$idx]);   
                $sheet->setCellValue('G' . $row, $descs[$idx] ?? ''); // Pasok na ang Description!
                $sheet->setCellValue('O' . $row, $tor);    
                $sheet->setCellValue('R' . $row, $rm_fg);  
                $sheet->setCellValue('V' . $row, $makers[$idx] ?? '');  
                $sheet->setCellValue('Z' . $row, $qtys[$idx]);    
                $sheet->setCellValue('AB' . $row, $uom);   
                $sheet->setCellValue('AE' . $row, $currency); 
                $sheet->setCellValue('AF' . $row, $prices[$idx]); 
                
                $row_total = floatval($qtys[$idx]) * floatval($prices[$idx]);
                $sheet->setCellValue('AI' . $row, $row_total); 
            }

            // FOOTER
            $sheet->setCellValue('A37', $remarks);
            if ($p == ($pageCount - 1)) {
                $sheet->setCellValue('AL39', $grandTotal); 
            }
        }

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PRS_'.$ref_no.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        die("Excel Error: " . $e->getMessage());
    }
}