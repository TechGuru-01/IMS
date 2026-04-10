<?php
session_start();
require_once '../../include/config.php';
require_once '../../include/function.php';
require_once './manageUserAction.php';

$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? 1; 

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();


$users_sql = "SELECT id, username, position, full_name FROM users WHERE id != ? ORDER BY id DESC";
$users_stmt = $conn->prepare($users_sql);
$users_stmt->bind_param("i", $user_id);
$users_stmt->execute();
$users_result = $users_stmt->get_result();


$current_full_name = $user['full_name']; 
$personal_sql = "SELECT item, description, date, quantity_in, quantity_out 
                 FROM history 
                 WHERE name = ? 
                 ORDER BY date DESC";
$personal_stmt = $conn->prepare($personal_sql);
$personal_stmt->bind_param("s", $current_full_name);
$personal_stmt->execute();
$history_logs = $personal_stmt->get_result(); 


$admin_sql = "SELECT 
                h.name, 
                h.item, 
                h.description, 
                h.date, 
                h.quantity_in, 
                h.quantity_out, 
                u.profile_pic 
              FROM history h
              LEFT JOIN users u ON h.name = u.full_name 
              ORDER BY h.date DESC, h.id DESC 
              LIMIT 20";

$admin_stmt = $conn->prepare($admin_sql);
$admin_stmt->execute();
$admin_history_logs = $admin_stmt->get_result();
?>