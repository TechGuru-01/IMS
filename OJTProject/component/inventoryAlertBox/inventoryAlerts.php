<?php

$m = $_SESSION['selected_month'] ?? null;
$y = $_SESSION['selected_year'] ?? (int)date('Y');


if ($m !== null) {
    $alertSql = "SELECT item, description, cabinet, quantity, min_quantity
                 FROM inventory 
                 WHERE quantity <= min_quantity
                 AND MONTH(date_created) = $m 
                 AND YEAR(date_created) = $y
                 ORDER BY quantity ASC";
} else {

    $alertSql = "SELECT * FROM inventory WHERE 1=0";
}

$alertResult = $conn->query($alertSql);
if (!$alertResult) { die("Query Failed: " . $conn->error); }
$pendingCount = $alertResult->num_rows;
?>
<div class="box-content box-3" id="history">
    <div class="content-container" id="history-content">
        <div class="history-header">
            <div>
                <h2>
                    <span>Inventory Alerts</span>
                    <?php if ($pendingCount > 0): ?>
                        <span class="status-badge pending-badge"><?= $pendingCount ?> Pending</span>
                    <?php else: ?>
                        <span class="status-badge clear-badge">Updated</span>
                    <?php endif; ?>
                </h2>
                <p style="font-size: 0.85rem; color: #666; margin-top: 5px;">Items below minimum quantity threshold</p>
            </div>
            <a href="inventoryAlertExport.php" style="text-decoration: none;">
                <button class="excel-btn">
                    <span class="material-symbols-outlined">download</span>Purchase Request
                </button>
            </a>
        </div>
        <hr>
        <table class="history-table" id="historyTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Cabinet</th>
                    <th>Current Qty</th>
                    <th>Min. Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pendingCount > 0): 
                    $alertResult->data_seek(0); 
                    while($row = $alertResult->fetch_assoc()): ?>
                        <tr class="low-stock-row">
                            <td><?= htmlspecialchars($row['item']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['cabinet']) ?></td>
                            <td class="critical-text"><?= $row['quantity'] ?></td>
                            <td style="color: #666;"><?= $row['min_quantity'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-alert">
                            <span class="material-symbols-outlined" style="vertical-align: middle;">check_circle</span> 
                            All items are currently above minimum stock levels.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>    
    </div>
</div>