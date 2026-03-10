<?php
// Fix: Nilagyan ng '/' pagkatapos ng __DIR__
include_once __DIR__ . '/../../include/config.php';

// Check kung pumasok ba talaga yung config
if (!isset($conn)) {
    die("Error: Database connection variable (\$conn) not found. Check your file path.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_dollar'])) {
        $new_rate = (float)$_POST['new_dollar_rate'];
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'dollar_rate'");
        $stmt->bind_param("d", $new_rate);
        $stmt->execute();
    }
    
    if (isset($_POST['update_yen'])) {
        $new_rate = (float)$_POST['new_yen_rate'];
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'yen_rate'");
        $stmt->bind_param("d", $new_rate);
        $stmt->execute();
    }
}

$settingsResult = $conn->query("SELECT * FROM settings");
$rates = [];
if ($settingsResult) {
    while($row = $settingsResult->fetch_assoc()) {
        $rates[$row['setting_key']] = $row['setting_value'];
    }
}

$d_rate = $rates['dollar_rate'] ?? 57.74;
$y_rate = $rates['yen_rate'] ?? 0.37;

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory");
$invRow = $totalQuery->fetch_assoc();
$peso = (float)($invRow['grand_total'] ?? 0);

$dollarTotal = ($d_rate > 0) ? ($peso / $d_rate) : 0;
$yenTotal    = ($y_rate > 0) ? ($peso / $y_rate) : 0; 
?>

<div class="box-content" id="box-dollar">
    <div class="inner-box" id="inner-dollar">
        <div class="box-header">
            <span>Dollar Rate ($1 = ₱<?= $d_rate ?>)</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_dollar_rate" placeholder="rate" required>
                <button type="submit" name="update_dollar">Set</button>
            </form> 
        </div>
        <h1>$<?= number_format($dollarTotal, 2) ?></h1>
    </div>
</div>

<div class="box-content" id="box-yen">
    <div class="inner-box" id="inner-yen">
        <div class="box-header">
            <span>Yen Rate (¥1 = ₱<?= $y_rate ?>)</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_yen_rate" placeholder="rate" required>
                <button type="submit" name="update_yen">Set</button>
            </form> 
        </div>
        <h1>¥<?= number_format($yenTotal, 2) ?></h1>
    </div>
</div>