<?php
require_once "../../include/config.php"; 


$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

$table_map = [
    'users' => 'technicians',
    'equipment' => 'jig_equipment',
    'customers' => 'customers'
];

$label_map = [
    'users' => 'User Full Name',
    'equipment' => 'Equipment Name',
    'customers' => 'Customer Name'
];


$target_table = isset($table_map[$current_tab]) ? $table_map[$current_tab] : 'technicians';
$current_label = isset($label_map[$current_tab]) ? $label_map[$current_tab] : 'Name';


if (isset($_POST['add_entry'])) {
    $name = $_POST['itemName'];
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO $target_table (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=" . $current_tab);
        exit;
    }
}


if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM $target_table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=" . $current_tab);
    exit;
}


$result = $conn->query("SELECT * FROM $target_table ORDER BY name ASC");
?>