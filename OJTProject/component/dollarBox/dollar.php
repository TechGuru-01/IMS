<?php
include_once __DIR__ . '/../../config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_dollar'])) {
        $new_rate = (float)$_POST['new_dollar_rate'];
       
        $_SESSION['dollar_rate'] = $new_rate; 
    }
}
$d_rate = $_SESSION['dollar_rate'] ?? 57.74;
$y_rate = $_SESSION['yen_rate'] ?? 0.37;

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory");
$row = $totalQuery->fetch_assoc();
$peso = (float)($row['grand_total'] ?? 0);

$dollarTotal = $peso / $d_rate;
$yenTotal = $peso / $y_rate;
?>
<div class="box-content" id="box-dollar">
    <div class="inner-box" id="inner-dollar">
        <div class="box-header">
            <span>Rate Settings</span>
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
            <span>Rate Settings</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_yen_rate" placeholder="rate" required>
                <button type="submit" name="update_yen">Set</button>
            </form> 
        </div>
        <h1>¥<?= number_format($yenTotal, 2) ?></h1>
    </div>
</div>