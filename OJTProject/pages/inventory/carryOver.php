<?php
include_once __DIR__ . '/../../include/config.php';

// Siguraduhin na may session para sa messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['carryOverAction'])) {
    $currentMonth = (int)$_POST['month'];
    $currentYear = (int)$_POST['year'];

    // 1. Alamin kung ano ang "Last Month"
    $lastMonth = ($currentMonth == 1) ? 12 : $currentMonth - 1;
    $lastYear = ($currentMonth == 1) ? $currentYear - 1 : $currentYear;

    // 2. Kunin lahat ng items mula sa Last Month (Dinagdag ang item_uuid sa SELECT)
    $sqlLastMonth = "SELECT category, item, description, cabinet, quantity, price, min_quantity, item_uuid 
                     FROM inventory 
                     WHERE MONTH(date_created) = $lastMonth AND YEAR(date_created) = $lastYear";
    
    $resultLastMonth = $conn->query($sqlLastMonth);

    if ($resultLastMonth && $resultLastMonth->num_rows > 0) {
        while ($row = $resultLastMonth->fetch_assoc()) {
            $category = $conn->real_escape_string($row['category']);
            $item = $conn->real_escape_string($row['item']);
            $desc = $conn->real_escape_string($row['description']);
            $cabinet = $conn->real_escape_string($row['cabinet']);
            $min_qty = (int)$row['min_quantity'];
            $price = (float)$row['price'];
            
            // --- UUID LOGIC ---
            // Kung NULL ang uuid sa last month record, gawan na natin agad ng bago para hindi na mag-NULL
            $item_uuid = (!empty($row['item_uuid'])) ? $row['item_uuid'] : uniqid('JIG-');
            
            // LOGIC: Ang remaining quantity ay nagiging simula ng bagong buwan
            $beginning = (int)$row['quantity']; 
            $received = 0; 
            $totalQty = $beginning + $received;
            
            $targetDate = "$currentYear-" . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . "-01 00:00:00";

            // 3. I-insert o I-update sa Current Month (Isinama ang item_uuid)
            $stmt = $conn->prepare("INSERT INTO inventory 
                (category, item, description, cabinet, beginning_inventory, received_qty, quantity, min_quantity, price, date_created, item_uuid) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                beginning_inventory = VALUES(beginning_inventory),
                quantity = VALUES(beginning_inventory) + received_qty,
                item_uuid = COALESCE(item_uuid, VALUES(item_uuid))");
            
            // Bind 11 parameters: ssssiiiidss
            $stmt->bind_param("ssssiiiidss", $category, $item, $desc, $cabinet, $beginning, $received, $totalQty, $min_qty, $price, $targetDate, $item_uuid);
            $stmt->execute();
        }
        $_SESSION['msg'] = "Carry over successful!";
    } else {
        $_SESSION['msg'] = "No records found from last month to carry over.";
    }

    // Siguraduhing walang echo o HTML bago itong line na 'to
    header("Location: inventory.php");
    exit();
}
?>