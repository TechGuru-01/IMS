<?php 
include "inventoryFunction.php"; 
require_once "../../auth_checker.php";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="inventory.css" />
    <link rel="stylesheet" href="../../style.css" /> 
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="inventoryModal.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />   
    <title>Inventory</title>
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"?>
    
    <div class="inventory-container">
        <div class="inv-header">
            <h2>Inventory List</h2>
            <p style="font-size: 1.1rem; font-weight: bold; color: #333; margin: 0;">
                Grand Total: <span style="color: #1d1d1d;">₱<?= number_format($grandTotal, 2) ?></span>
            </p>
            <div class="btn-container">
                <a href="inventoryAlertExport.php" style="text-decoration: none;">
                    <button class="excel-btn">
                        <span class="material-symbols-outlined">download</span>Purchase Request
                    </button>
                </a>
                <button id="openBtn" class="opnbtn">Edit</button>
            </div>
        </div>

        <?php include "inventoryModal.php"; ?>

        <hr style="margin: 20px 0;">

        <table class="inventory-table" id="inventoryTable">
            <thead>
                <tr>
                    <?php foreach($cols as $col): ?>
                        <th><?= ucfirst($col) ?></th>
                    <?php endforeach; ?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()) { 
                $qty = (float)($row['quantity'] ?? 0);
                $prc = (float)($row['price'] ?? 0);
                $rowTotal = $qty * $prc;
            ?>
                <tr data-id="<?= $row['id'] ?>">
                    <?php foreach($cols as $col): ?>
                        <td data-field="<?= $col ?>">
                            <?php 
                                if ($col === 'price') {
                                    echo "₱" . number_format($prc, 2);
                                } else {
                                    echo htmlspecialchars($row[$col] ?? '');
                                }
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <td class="total-val">₱<?= number_format($rowTotal, 2) ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
  
    <script src="inventory.js" defer></script>
</body>
</html>