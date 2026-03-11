<?php
include_once __DIR__ . '/../../include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- FILTER LOGIC (Month & Year) ---
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (isset($_SESSION['selected_month']) ? $_SESSION['selected_month'] : (int)date('n'));
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (isset($_SESSION['selected_year']) ? $_SESSION['selected_year'] : (int)date('Y'));

$_SESSION['selected_month'] = $selectedMonth;
$_SESSION['selected_year'] = $selectedYear;

// --- DATABASE HELPER ---
/** * Tinanggal ang 'is_acknowledged' dito para hindi siya lumabas sa dynamic table display.
 */
$cols = ['id', 'category', 'cabinet', 'item', 'description', 'beginning_inventory', 'received_qty', 'quantity', 'min_quantity', 'price'];

function verifyColumn($conn, $colName){
    $colName = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($colName));
    $protected = ['id', 'total', 'action', 'select', 'unfold_more'];
    
    if(empty($colName) || in_array($colName, $protected)) return null;

    $check = $conn->query("SHOW COLUMNS FROM inventory LIKE '$colName'");
    if($check->num_rows == 0){
        if($colName === 'is_acknowledged'){
            $conn->query("ALTER TABLE inventory ADD COLUMN `is_acknowledged` TINYINT(1) DEFAULT 0");
        } else {
            $type = (strpos($colName, 'qty') !== false || strpos($colName, 'inventory') !== false) ? "INT DEFAULT 0" : "VARCHAR(255) DEFAULT NULL";
            $conn->query("ALTER TABLE inventory ADD COLUMN `$colName` $type");
        }
    }
    return $colName;
}

// Siguraduhin na exist ang column para sa logic pero 'hidden' sa UI
verifyColumn($conn, 'is_acknowledged');

// --- FORM ACTIONS ---

// ADD ITEM / RESTOCK
if (isset($_POST['addItem'])){
    $category = $_POST['category']; 
    $item = $_POST['item'];
    $description = $_POST['description']; 
    $cabinet = $_POST['cabinet'];         
    $input_qty = (int)$_POST['quantity'];
    $min_quantity = (int)$_POST['min_quantity']; 
    $price = (float)$_POST['price'];
    $targetDate = "$selectedYear-$selectedMonth-01 00:00:00";

    /**
     * LOGIC: Kapag nag-restock, kailangan mag-reset ang is_acknowledged sa 0
     * para lumabas ulit sa Inventory Alerts box.
     */
    $stmt = $conn->prepare("INSERT INTO inventory 
        (category, item, description, cabinet, beginning_inventory, received_qty, quantity, min_quantity, price, date_created, is_acknowledged) 
        VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?, 0) 
        ON DUPLICATE KEY UPDATE 
            received_qty = received_qty + VALUES(beginning_inventory), 
            quantity = quantity + VALUES(beginning_inventory),
            min_quantity = VALUES(min_quantity), 
            description = VALUES(description),
            is_acknowledged = 0"); 
    
    $stmt->bind_param("ssssiiids", $category, $item, $description, $cabinet, $input_qty, $input_qty, $min_quantity, $price, $targetDate);
    $stmt->execute();

    header("Location: inventory.php?keepOpen=1");
    exit();
}

// JSON BULK/INLINE EDIT (Modal Sync)
$input = file_get_contents('php://input');
$json = json_decode($input, true);
if (isset($json['updateData'])){
    foreach($json['updateData'] as $row){
        $id = (int)$row['id'];
        $newQty = isset($row['quantity']) ? (int)$row['quantity'] : null; 
        
        $current = $conn->query("SELECT quantity FROM inventory WHERE id = $id")->fetch_assoc();
        $oldQty = (int)($current['quantity'] ?? 0);

        $updateParts = [];
        foreach($row as $key => $val){
            $cleanCol = verifyColumn($conn, $key);
            // double check para hindi talaga ma-update via JSON edit ang is_acknowledged nang aksidente
            if ($cleanCol && $cleanCol !== 'is_acknowledged'){
                $cleanVal = $conn->real_escape_string($val);
                $updateParts[] = "`$cleanCol` = '$cleanVal'";
            }
        }

        if(!empty($updateParts)){
            // Kung tumaas ang quantity, i-reset ang alert status
            if($newQty !== null && $newQty > $oldQty){
                $updateParts[] = "`is_acknowledged` = 0";
            }

            $conn->query("UPDATE inventory SET " . implode(', ', $updateParts) . " WHERE id = $id");
            
            if ($newQty !== null && $newQty > $oldQty) {
                $addedAmount = $newQty - $oldQty;
                $conn->query("UPDATE inventory SET `received_qty` = `received_qty` + $addedAmount WHERE id = $id");
            }
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
    header("Location: inventory.php");
    exit();
}

// --- DATA RETRIEVAL ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'id';
$order = (isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'ASC' : 'DESC';

$sql = "SELECT * FROM inventory WHERE 
        (item LIKE '%$search%' OR category LIKE '%$search%') 
        AND MONTH(date_created) = $selectedMonth 
        AND YEAR(date_created) = $selectedYear
        ORDER BY $sort $order";
$result = $conn->query($sql);

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory WHERE MONTH(date_created) = $selectedMonth AND YEAR(date_created) = $selectedYear");
$grandTotal = ($totalQuery) ? (float)($totalQuery->fetch_assoc()['grand_total'] ?? 0) : 0;

// --- AUTO-PURGE OLD DATA ---
$YearsAgo = date('Y-m-d', strtotime('-5 years'));
$purgeSql = "DELETE FROM inventory WHERE date_created < '$YearsAgo'";
$conn->query($purgeSql);
?>