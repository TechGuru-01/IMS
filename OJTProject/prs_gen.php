<?php
/**
 * SIGURADUHIN NA WALANG WHITESPACE SA TAAS NG <?php TAG
 */

// I-start ang output buffering para iwas sa "Header already sent" errors
if (ob_get_level()) ob_end_clean();
ob_start();

require './include/config.php'; 
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ref_number'])) {
    
    // --- 1. CAPTURE HEADER DATA ---
    $ref_no      = $_POST['ref_number'];
    $company     = $_POST['company'];
    $currency    = $_POST['currency'];
    $grandTotal  = floatval($_POST['total_amount']);
    $remarks     = $_POST['remarks'];
    $pr_date     = $_POST['pr_date'] ?? date('Y-m-d'); 
    
    $uoms = $_POST['item_uoms'] ?? [];
    $rm_fg       = $_POST['rm_fg'] ?? ''; 
    $tor         = $_POST['ToR'] ?? '';   

    $names       = $_POST['item_names'] ?? []; 
    $descs       = $_POST['item_descs'] ?? [];
    $makers      = $_POST['item_makers'] ?? [];
    $qtys        = $_POST['item_qtys'] ?? [];
    $prices      = $_POST['item_prices'] ?? [];
    $ids         = $_POST['acknowledge_ids'] ?? [];

    // --- 2. DATABASE TRANSACTION (PRESERVED) ---
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO pr_reports (ref_number, pr_date, company, currency, total_amount, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $ref_no, $pr_date, $company, $currency, $grandTotal, $remarks);
        $stmt->execute();

        $item_stmt = $conn->prepare("INSERT INTO pr_items (pr_ref_number, material_name, description) VALUES (?, ?, ?)");
        foreach ($names as $index => $name) {
            $curr_desc = $descs[$index] ?? '';
            $item_stmt->bind_param("sss", $ref_no, $name, $curr_desc);
            $item_stmt->execute();
        }

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
        ob_end_clean();
        die("DB Error: " . $e->getMessage());
    }

    // --- 3. EXCEL GENERATION (THE FULL FIX) ---
    try {
        // Load the template
        $spreadsheet = IOFactory::load('PRS_Template.xlsx');
        
        // Settings para iwas corruption
        $spreadsheet->getProperties()->setCreator("Inventory System");
        
        $itemsPerPage = 19; 
        $totalItems = count($names);
        $pageCount = ceil($totalItems / $itemsPerPage);

        for ($p = 0; $p < $pageCount; $p++) {
            if ($p == 0) {
                // Gamitin ang default sheet sa Page 1
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Page 1");
            } else {
                // FIX: Kopyahin ang first sheet (Template structure) bilang bagong sheet
                // Mas safe ito kaysa sa direct clone ng variable para sa XML internal references
                $clonedSheet = clone $spreadsheet->getSheet(0); 
                $clonedSheet->setTitle("Page " . ($p + 1));
                $spreadsheet->addSheet($clonedSheet);
                $sheet = $spreadsheet->getSheet($p);
            }

            // --- PAGE NUMBERING ---
            $sheet->setCellValue('AN4', "Page " . ($p + 1) . " / " . $pageCount);

            // --- HEADER DATA ---
            $sheet->setCellValue('F6', $company);
            $sheet->setCellValue('D7', $ref_no);
            $sheet->setCellValue('AL7', $pr_date);

            // --- BODY ITEMS LOOP ---
            for ($i = 0; $i < $itemsPerPage; $i++) {
                $idx = ($p * $itemsPerPage) + $i;
                if ($idx >= $totalItems) break;

                $row = 17 + $i;
                
                $sheet->setCellValue('A' . $row, $idx + 1); 
                $sheet->setCellValue('B' . $row, $names[$idx]);   
                $sheet->setCellValue('G' . $row, $descs[$idx] ?? ''); 
                $sheet->setCellValue('O' . $row, $tor);    
                $sheet->setCellValue('R' . $row, $rm_fg);  
                $sheet->setCellValue('V' . $row, $makers[$idx] ?? '');  
                $sheet->setCellValue('Z' . $row, $qtys[$idx]);    
                $sheet->setCellValue('AB' . $row, $uoms[$idx] ?? '');
                $sheet->setCellValue('AE' . $row, $currency); 
                $sheet->setCellValue('AF' . $row, $prices[$idx]); 
                
                // Manual calculation for row total (Better as a value for stability)
                $row_total = floatval($qtys[$idx]) * floatval($prices[$idx]);
                $sheet->setCellValue('AI' . $row, $row_total); 
            }

            // --- FOOTER & TOTALS ---
            $sheet->setCellValue('A37', $remarks);
            
            // Grand Total: Only show on the last page
            if ($p == ($pageCount - 1)) {
                $sheet->setCellValue('AL39', $grandTotal); 
            } else {
                $sheet->setCellValue('AL39', "SEE PAGE " . $pageCount); 
            }
        }

        // --- PREPARE FOR DOWNLOAD ---
        // I-set ang active sheet back to Page 1 para yun ang unang makikita ni user
        $spreadsheet->setActiveSheetIndex(0);

        // Clear any previous output buffers
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PRS_'.$ref_no.'.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        /** * ETO YUNG PINAKA-FIX: 
         * Pinapatay natin ang Pre-Calculation para hindi mag-error si Excel sa "Formula" part.
         * Hahayaan nating si Excel ang mag-compute pag-open ng file.
         */
        $writer->setPreCalculateFormulas(false);
        
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        if (ob_get_length()) ob_end_clean();
        die("Excel Error: " . $e->getMessage());
    }
}
?>