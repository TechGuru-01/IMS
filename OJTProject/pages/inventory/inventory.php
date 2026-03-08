<?php include "inventoryFunction.php"; ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="inventory.css" />
    <link rel="stylesheet" href="../../style.css" /> 
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="inventoryModal.css">
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
            <button id="openBtn" class="opnbtn">Edit</button>
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
            <?php while($row = $result->fetch_assoc()) { ?>
                <tr data-id="<?= $row['id'] ?>">
                    <?php foreach($cols as $col): ?>
                        <td data-field="<?= $col ?>"><?= htmlspecialchars($row[$col] ?? '') ?></td>
                    <?php endforeach; ?>
                    <td class="total-val"><?= (float)($row['quantity'] ?? 0) * (float)($row['price'] ?? 0) ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
  
    <script src="inventory.js" defer></script>
</body>
</html>