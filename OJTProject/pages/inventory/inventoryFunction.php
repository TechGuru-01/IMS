<?php
include_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. SESSION & FILTER LOGIC
if (isset($_GET['month'])) {
    $selectedMonth = (int)$_GET['month'];
    $_SESSION['selected_month'] = $selectedMonth;
} elseif (isset($_SESSION['selected_month'])) {
    $selectedMonth = $_SESSION['selected_month'];
} else {
    $selectedMonth = (int)date('n');
}

// Year Logic: Kung walang year sa URL, current year ang default
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// 2. WHITELISTED COLUMNS
$cols = ['id', 'category', 'cabinet', 'item', 'description', 'quantity', 'min_quantity', 'price'];

function verifyColumn($conn, $colName){
    $colName = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($colName));
    $protected = ['id', 'total', 'action', 'select', 'unfold_more'];
    if(empty($colName) || in_array($colName, $protected) || strpos($colName, 'unfold') !== false) return null;

    $check = $conn->query("SHOW COLUMNS FROM inventory LIKE '$colName'");
    if($check->num_rows == 0){
        $conn->query("ALTER TABLE inventory ADD COLUMN `$colName` VARCHAR(255) DEFAULT NULL");
    }
    return $colName;
}

// ADD ITEM (Incremental/Targeted)
if (isset($_POST['addItem'])){
    $category = $_POST['category']; 
    $item = $_POST['item'];
    $description = $_POST['description']; 
    $cabinet = $_POST['cabinet'];         
    $quantity = (int)$_POST['quantity'];
    $min_quantity = (int)$_POST['min_quantity']; 
    $price = (float)$_POST['price'];
    
    // Dito nagiging incremental: Isasave ang record sa specific na buwan at taon
    $targetMonth = isset($_POST['selected_month']) ? (int)$_POST['selected_month'] : $selectedMonth;
    $targetDate = "$selectedYear-$targetMonth-01 00:00:00";

    $stmt = $conn->prepare("INSERT INTO inventory (category, item, description, cabinet, quantity, min_quantity, price, date_created) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), min_quantity = VALUES(min_quantity), description = VALUES(description)");
    $stmt->bind_param("sssiiids", $category, $item, $description, $cabinet, $quantity, $min_quantity, $price, $targetDate);
    $stmt->execute();

    header("Location: inventory.php?keepOpen=1");
    exit();
}

// SYNC/UPDATE LOGIC
$input = file_get_contents('php://input');
$json = json_decode($input, true);
if (isset($json['updateData'])){
    foreach($json['updateData'] as $row){
        $id = (int)$row['id'];
        unset($row['id']);
        $updateParts = [];
        foreach($row as $key => $val){
            $cleanCol = verifyColumn($conn, $key);
            if ($cleanCol){
                $cleanVal = $conn->real_escape_string($val);
                $updateParts[] = "`$cleanCol` = '$cleanVal'";
            }
        }
        if(!empty($updateParts)){
            $conn->query("UPDATE inventory SET " . implode(', ', $updateParts) . " WHERE id = $id");
        }
    }
    echo json_encode(["status" => "success"]);
    exit();
}

// BULK DELETE
if(isset($_POST['bulkDelete'])){
    if(!empty($_POST['selectedItems'])){
        $ids = implode(',', array_map('intval', $_POST['selectedItems']));
        $conn->query("DELETE FROM inventory WHERE id IN ($ids)");
    }
    header("Location: inventory.php?keepOpen=1");
    exit();
}

// FILTERED FETCH: Dito nangyayari ang "Month-to-Month" view
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'ASC' : 'DESC';

$sql = "SELECT * FROM inventory WHERE 
        (item LIKE '%$search%' OR category LIKE '%$search%') 
        AND MONTH(date_created) = $selectedMonth 
        AND YEAR(date_created) = $selectedYear
        ORDER BY $sort $order";
$result = $conn->query($sql);

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory WHERE MONTH(date_created) = $selectedMonth AND YEAR(date_created) = $selectedYear");
$grandTotal = ($totalQuery) ? (float)($totalQuery->fetch_assoc()['grand_total'] ?? 0) : 0;