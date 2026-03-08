<?php
include_once __DIR__ . '/../../config.php';

function verifyColumn($conn, $colName) {
    $colName = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($colName));
    $protected = ['id', 'total', 'action', 'select'];
    if (empty($colName) || in_array($colName, $protected)) return null;

    $check = $conn->query("SHOW COLUMNS FROM inventory LIKE '$colName'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE inventory ADD COLUMN `$colName` VARCHAR(255) DEFAULT NULL");
    }
    return $colName;
}

if(isset($_POST['addItem'])){
    $category = $_POST['category'];
    $item = $_POST['item'];
    $desc = $_POST['description'];
    $cabinet = $_POST['cabinet'];
    $qty = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];


    $sql = "INSERT INTO inventory (category, item, description, cabinet, quantity, price) 
            VALUES (?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            quantity = quantity + ?, 
            description = VALUES(description)";

    $stmt = $conn->prepare($sql);

    // 2. Data types: 
    // s s s i i d (para sa INSERT) + i (para sa quantity update) = "sssiidi"
    $stmt->bind_param("sssiidi", $category, $item, $desc, $cabinet, $qty, $price, $qty);
    
    $stmt->execute();
    
    header("Location: inventory.php?keepOpen=1");
    exit();
}

$input = file_get_contents('php://input');
$json = json_decode($input, true);
if (isset($json['updateData'])) {
    foreach($json['updateData'] as $row) {
        $id = (int)$row['id'];
        unset($row['id']);
        $updateParts = [];
        foreach($row as $key => $val) {
            $cleanCol = verifyColumn($conn, $key);
            if ($cleanCol) {
                $cleanVal = $conn->real_escape_string($val);
                $updateParts[] = "`$cleanCol` = '$cleanVal'";
            }
        }
        if (!empty($updateParts)) {
            $conn->query("UPDATE inventory SET " . implode(', ', $updateParts) . " WHERE id = $id");
        }
    }
    echo json_encode(["status" => "success"]);
    exit();
}

if(isset($_POST['bulkDelete'])){
    if(!empty($_POST['selectedItems'])){
        $ids = implode(',', array_map('intval', $_POST['selectedItems']));
        $conn->query("DELETE FROM inventory WHERE id IN ($ids)");
    }
    header("Location: inventory.php?keepOpen=1");
    exit();
}

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory");
$totalRow = $totalQuery->fetch_assoc();
$grandTotal = (float)($totalRow['grand_total'] ?? 0);

$columnsResult = $conn->query("SHOW COLUMNS FROM inventory");
$cols = [];
while($c = $columnsResult->fetch_assoc()) { 
    $cols[] = $c['Field'];
}
$result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");