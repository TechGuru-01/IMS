<?php
include_once __DIR__ . '/../../config.php';


function verifyColumn($conn, $colName){
    $colName = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($colName));
    $protected = ['id', 'total', 'action', 'select'];

    if(empty($colName)|| in_array($colName, $protected)) return null;

    $check = $conn->query("SHOW COLUMNS FROM inventory LIKE '$colName'");

    if($check->num_rows == 0){
        $conn->query("ALTER TABLE inventory ADD COLUMN `$colName` VARCHAR(255) DEFAULT NULL");
    }
    return $colName;
}

verifyColumn($conn, 'min_quantity');

if (isset($_POST['addItem'])){
    $category = $_POST['category']; 
    $item = $_POST['item'];
    $description = $_POST['description']; 
    $cabinet = $_POST['cabinet'];         
    $quantity = (int)$_POST['quantity'];
    $min_quantity = (int)$_POST['min_quantity']; 
    $price = (float)$_POST['price'];
    
    $stmt = $conn->prepare("INSERT INTO inventory (category, item, description, cabinet, quantity, min_quantity, price) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), min_quantity = VALUES(min_quantity), description = VALUES(description)");
    $stmt->bind_param("sssiiid", $category, $item, $description, $cabinet, $quantity, $min_quantity, $price);
    $stmt->execute();

    header("Location: inventory.php?keepOpen=1");
    exit();
}

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
            $query = "UPDATE inventory SET " . implode(', ', $updateParts) . " WHERE id = $id";
            $conn->query($query);
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

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'ASC' : 'DESC';

$allowedSort = ['id', 'category', 'item', 'cabinet', 'quantity', 'min_quantity', 'price'];
if (!in_array($sort, $allowedSort)) { $sort = 'id'; }

$sql = "SELECT * FROM inventory WHERE 
        (item LIKE '%$search%' OR 
        category LIKE '%$search%' OR 
        description LIKE '%$search%') 
        ORDER BY $sort $order";

$result = $conn->query($sql);

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory");
$totalRow = $totalQuery->fetch_assoc();
$grandTotal = (float)($totalRow['grand_total'] ?? 0);

$columnResult = $conn->query("SHOW COLUMNS FROM inventory");
$cols = [];
while($c = $columnResult->fetch_assoc()){
    $cols[] = $c['Field'];
}
