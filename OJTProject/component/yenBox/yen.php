<?php
include_once __DIR__ . '/../../config.php';
// $peso is already calculated if dollar is included first, but this keeps it independent
$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory");
$row = $totalQuery->fetch_assoc();
$peso = (float)($row['grand_total'] ?? 0);
$yenTotal = $peso / 0.37;
?>
<div class="box-content" id="box-yen">
    <div class="inner-box">
        <h1>¥<?= number_format($yenTotal, 2) ?></h1>
    </div>
</div>