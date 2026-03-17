<?php

require_once "../../include/config.php";
require_once "../../include/auth_checker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');

$columnResult = $conn->query("SHOW COLUMNS FROM history");
$cols = [];
if ($columnResult) {
    while($c = $columnResult->fetch_assoc()){
        $cols[] = $c['Field'];
    }
}

$sql = "SELECT * FROM history 
        WHERE MONTH(date) = $m 
        AND YEAR(date) = $y 
        ORDER BY id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../../style.css">
     <link rel="stylesheet" href="../inventory/inventory.css"> 
     <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
     <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
     <link rel="stylesheet" href="../../component/settings/settings.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <title>HEPC JIG IMS | History</title>
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <div class="inventory-container">
        <div class="inv-header">
            <div>
                <h2 style="font-weight: 800; color: #072d7a;">TRANSACTION HISTORY</h2>
                <p style="color: #666; font-size: 0.9rem;">
                    Showing records for: <strong><?= date('F', mktime(0, 0, 0, $m, 1)) ?> <?= $y ?></strong>
                </p>
            </div>
            <div class="btn-container">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="inventorySearch" placeholder="Search..." onkeyup="filterTable()">
                </div>
                <a href="./historyExport.php?month=<?= $m ?>&year=<?= $y ?>" style="text-decoration: none;">
                    <button class="excel-btn">
                        <span class="material-symbols-outlined">download</span> Export History
                    </button>
                </a>
            </div>
        </div>
    
 <table class="inventory-table" id="inventoryTable">
    <thead>
        <tr>
            <th style="width: 40px; text-align: center;">
                <input type="checkbox" id="selectAllRows" style="cursor: pointer;">
            </th>

            <?php foreach($cols as $col): ?>
                <?php if (strtolower($col) == 'id') continue; ?> 
                <th><?= ucfirst(str_replace('_', ' ', $col)) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr data-id="<?= $row['id'] ?>"> 
                    <td style="text-align: center;">
                        <input type="checkbox" class="row-checkbox" value="<?= $row['id'] ?>" style="cursor: pointer;">
            
                    </td>

                    <?php foreach ($cols as $col): ?>
                        <?php if (strtolower($col) == 'id') continue; ?> 
                        <td data-field="<?= $col ?>"><?= htmlspecialchars($row[$col] ?? '') ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?= count($cols) ?>" style="text-align:center; padding: 40px; color: #999;">
                    <span class="material-symbols-outlined" style="font-size: 48px; display: block; margin-bottom: 10px;">history</span>
                    No history records found for this period.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

         <?php include __DIR__ . "/../../component/settings/settings.php"; ?>
    </div>
     <script src="../../component/settings/settings.js"></script>
    <script src="../../component/search.js"></script>
    <script src="../history/history.js"></script>
</body>
</html>