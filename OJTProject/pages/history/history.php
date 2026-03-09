<?php

require_once "../../config.php";
require_once "../../auth_checker.php";

$columnResult = $conn->query("SHOW COLUMNS FROM history");
$cols = [];
if ($columnResult) {
    while($c = $columnResult->fetch_assoc()){
        $cols[] = $c['Field'];
    }
}

$result = $conn->query("SELECT * FROM history ORDER BY id DESC");
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../../style.css">
     <link rel="stylesheet" href="../inventory/inventory.css"> 
     <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <title>HEPC JIG IMS | History</title>
</head>
<body>
    <?php 
     
        include "../../component/navbar/nav-bar.php"; 
    ?>

    <div class="inventory-container">
        <div class="inv-header">
            <h2>Transaction History</h2>
            <div class="btnContainer">
                <a href="./historyExport.php" style="text-decoration: none;">
                    <button class="excel-btn">
                        <span class="material-symbols-outlined">download</span>Transaction History
                    </button>
                </a>
            </div>
        </div>
    
        <table class="inventory-table" id="inventoryTable">
            <thead>
                <tr>
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
                            <?php foreach ($cols as $col): ?>
                                <?php if (strtolower($col) == 'id') continue; ?> 
                                <td data-field="<?= $col ?>"><?= htmlspecialchars($row[$col] ?? '') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($cols) - 1 ?>" style="text-align:center; padding: 20px;">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>