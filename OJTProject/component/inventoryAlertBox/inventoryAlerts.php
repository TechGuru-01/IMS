<?php
// 1. ACTION HANDLER
if (isset($_POST['acknowledge_id'])) {
    $id_to_update = intval($_POST['acknowledge_id']);
    $updateQuery = "UPDATE inventory SET is_acknowledged = 1 WHERE id = $id_to_update";
    if ($conn->query($updateQuery)) {
        echo "<script>window.location.href=window.location.href;</script>";
        exit();
    }
}

// 2. DATA QUERY
$m = $_SESSION['selected_month'] ?? null;
$y = $_SESSION['selected_year'] ?? (int)date('Y');

$alertSql = "SELECT id, item, description, cabinet, quantity, min_quantity 
             FROM inventory 
             WHERE quantity <= min_quantity 
             AND is_acknowledged = 0 " . 
             ($m !== null ? "AND MONTH(date_created) = $m AND YEAR(date_created) = $y " : "") . 
             "ORDER BY quantity ASC";

$alertResult = $conn->query($alertSql);
$pendingCount = ($alertResult) ? $alertResult->num_rows : 0;
?>

<div class="box-content box-3 <?php echo ($pendingCount > 0) ? 'has-pending' : ''; ?>" id="history">
    <div class="content-container" id="history-content">
        <div class="history-header">
            <h2 style="display: flex; align-items: center; gap: 10px; margin: 0;">
                <?php if ($pendingCount > 0): ?>
                    <span class="material-symbols-outlined bell-shake">notifications_active</span>
                <?php endif; ?>
                
                <span>Inventory Alerts</span>

                <?php if ($pendingCount > 0): ?>
                    <span class="status-badge pending-badge"><?= $pendingCount ?> PENDING</span>
                <?php else: ?>
                    <span class="status-badge clear-badge">HEALTHY</span>
                <?php endif; ?>
            </h2>
            <p style="font-size: 0.85rem; color: #666; margin-top: 5px;">
                <?= ($pendingCount > 0) ? '<strong>ATTENTION:</strong> Critical stock levels detected!' : 'All items are above minimum levels.' ?>
            </p>
        </div>
        <hr>
        
        <table class="history-table" id="historyTable">
            <thead>
                <tr>
                    <th style="text-align: center; width: 80px;">Action</th>
                    <th>Item Name</th>
                    <th>Cabinet</th>
                    <th style="text-align: center;">Qty Left</th>
                    <th style="text-align: center;">Min</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pendingCount > 0): 
                    while($row = $alertResult->fetch_assoc()): 
                        $isUrgent = ($row['quantity'] <= 0);
                    ?>
                        <tr class="low-stock-row <?= $isUrgent ? 'critical-row-urgent' : '' ?>">
                            <td style="text-align: center;">
                                <form method="POST" onsubmit="return confirm('Mark as resolved?');" style="margin:0;">
                                    <input type="hidden" name="acknowledge_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-resolve">DONE</button>
                                </form>
                            </td>
                            <td style="font-weight: 600;">
                                <span class="alert-dot"></span>
                                <?= htmlspecialchars($row['item']) ?>
                                <?php if($isUrgent): ?>
                                    <span class="urgent-badge">OUT OF STOCK</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; color: #475569;">
                                    <?= htmlspecialchars($row['cabinet']) ?>
                                </span>
                            </td>
                            <td class="critical-text" style="text-align: center; font-size: 1.1rem;">
                                <?= $row['quantity'] ?>
                            </td>
                            <td style="color: #94a3b8; text-align: center; font-weight: 500;">
                                <?= $row['min_quantity'] ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-alert">
                            <div style="margin: 30px 0; text-align: center;">
                                <span class="material-symbols-outlined" style="font-size: 3.5rem; color: #28a745; display: block; margin-bottom: 10px;">check_circle</span>
                                <p style="font-weight: 600; color: #2d3748;">All stock levels are optimal.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>    
    </div>
</div>