<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../include/config.php';

// SECURITY: Position Gatekeeper
$current_position = strtoupper(trim($_SESSION['position'] ?? 'GUEST'));

if ($current_position === 'ADMIN' || $current_position === 'SUPER ADMIN') {
    header("Location: /OJTProject/pages/profile/adminProfile.php");
    exit;
}

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../login.php");
    exit;
}

// GLOBAL FILTERS
$m = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$y = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');

// FETCH USER DATA

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) { 
    die("User account not found in database."); 
}

$full_name = $user['full_name'];

// STATS: Total Items Issued (Filtered by Session Month/Year)
$issued_sql = "SELECT SUM(quantity_out) as total_issued 
               FROM history 
               WHERE name = ? 
               AND MONTH(date) = ? 
               AND YEAR(date) = ?";
$stmt_issued = $conn->prepare($issued_sql);
$stmt_issued->bind_param("sii", $full_name, $m, $y);
$stmt_issued->execute();
$issued_count = $stmt_issued->get_result()->fetch_assoc()['total_issued'] ?? 0;

// STATS: Total Transactions (Filtered by Session Month/Year)
$trans_sql = "SELECT COUNT(*) as total_trans 
              FROM history 
              WHERE name = ? 
              AND MONTH(date) = ? 
              AND YEAR(date) = ?";
$stmt_trans = $conn->prepare($trans_sql);
$stmt_trans->bind_param("sii", $full_name, $m, $y);
$stmt_trans->execute();
$trans_count = $stmt_trans->get_result()->fetch_assoc()['total_trans'] ?? 0;

// ACTIVITY LOGS: Last 20 Activities (Filtered by Session Month/Year)
$logs_sql = "SELECT * FROM history 
             WHERE name = ? 
             AND MONTH(date) = ? 
             AND YEAR(date) = ? 
             ORDER BY date DESC LIMIT 20";
$stmt_logs = $conn->prepare($logs_sql);
$stmt_logs->bind_param("sii", $full_name, $m, $y);
$stmt_logs->execute();
$logs_result = $stmt_logs->get_result();

// UI HELPER
$display_date = date('F Y', mktime(0, 0, 0, $m, 1, $y));
?>