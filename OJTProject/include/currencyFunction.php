<?php
$d_rate = 0;
$y_rate = 0;
$peso = 0;

$selectedMonth = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$selectedYear = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_dollar'])) {
        $new_rate = (float)$_POST['new_dollar_rate'];
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'dollar_rate'");
        $stmt->bind_param("d", $new_rate);
        $stmt->execute();
        $stmt->close();
    }
    if (isset($_POST['update_yen'])) {
        $new_rate = (float)$_POST['new_yen_rate'];
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'yen_rate'");
        $stmt->bind_param("d", $new_rate);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$settingsResult = $conn->query("SELECT * FROM settings");
if ($settingsResult && $settingsResult->num_rows > 0) {
    while($row = $settingsResult->fetch_assoc()) {
        if ($row['setting_key'] === 'dollar_rate') $d_rate = (float)$row['setting_value'];
        if ($row['setting_key'] === 'yen_rate') $y_rate = (float)$row['setting_value'];
    }
}

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory WHERE MONTH(date_created) = $selectedMonth AND YEAR(date_created) = $selectedYear");
if ($totalQuery) {
    $invRow = $totalQuery->fetch_assoc();
    $peso = (float)($invRow['grand_total'] ?? 0);
}

$dollarTotal = ($d_rate > 0) ? ($peso / $d_rate) : 0;
$yenTotal    = ($y_rate > 0) ? ($peso / $y_rate) : 0; 
?>