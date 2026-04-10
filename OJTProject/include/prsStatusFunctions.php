<?php
require_once "../../include/config.php"; 
require_once "../../include/auth_checker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status_manual'])) {
    $ref = $_POST['ref_number'];
    $new_status = $_POST['new_status'];

    $update_stmt = $conn->prepare("UPDATE pr_reports SET status = ? WHERE ref_number = ?");
    $update_stmt->bind_param("ss", $new_status, $ref);
    
    if ($update_stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


if (isset($_GET['filter_month'])) {
    $_SESSION['selected_month'] = date('n', strtotime($_GET['filter_month']));
    $_SESSION['selected_year'] = date('Y', strtotime($_GET['filter_month']));
}

$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');
$status_filter = $_GET['status_filter'] ?? 'all';


function getStatusClass($status) {
    $s = strtolower(trim($status ?? ''));
    switch ($s) {
        case 'cancelled': return 'status-cancelled';
        case 'follow up': return 'status-follow-up';
        case 'hold': return 'status-hold';
        case 'on process': return 'status-on-process';
        case 'ready for reporting': return 'status-done';
        case 'production office': return 'status-production';
        case 'received': return 'status-received';
        default: return 'status-default';
    }
}


$count_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'On Process' THEN 1 ELSE 0 END) as on_process,
    SUM(CASE WHEN status = 'Hold' THEN 1 ELSE 0 END) as hold,
    SUM(CASE WHEN status = 'Follow Up' THEN 1 ELSE 0 END) as follow_up,
    SUM(CASE WHEN status = 'Production Office' THEN 1 ELSE 0 END) as production,
    SUM(CASE WHEN status = 'Ready for Reporting' THEN 1 ELSE 0 END) as ready_reporting,
    SUM(CASE WHEN status = 'Received' THEN 1 ELSE 0 END) as received,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM pr_reports 
    WHERE MONTH(pr_date) = $m AND YEAR(pr_date) = $y";
$counts_res = $conn->query($count_sql);
$counts = $counts_res ? $counts_res->fetch_assoc() : [];


$where_clause = "WHERE MONTH(r.pr_date) = $m AND YEAR(r.pr_date) = $y";
if ($status_filter !== 'all') {
    $where_clause .= " AND r.status = '" . $conn->real_escape_string($status_filter) . "'";
}

$where_clause = "WHERE MONTH(r.pr_date) = $m AND YEAR(r.pr_date) = $y";

if ($status_filter !== 'all') {
    $where_clause .= " AND r.status = '" . $conn->real_escape_string($status_filter) . "'";
}
$sql = "SELECT r.*, 
        GROUP_CONCAT(i.material_name SEPARATOR ', ') as all_materials,
        GROUP_CONCAT(i.description SEPARATOR ', ') as all_descriptions
        FROM pr_reports r
        LEFT JOIN pr_items i ON r.ref_number = i.pr_ref_number
        $where_clause
        GROUP BY r.pr_id
        ORDER BY r.pr_id DESC";

$result = $conn->query($sql); 

?>