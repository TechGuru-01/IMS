<?php if ($result && $result->num_rows > 0): ?>
    <div class="inv-header">
        <div class="header-left">
            <p class="grand-total-card">
                <span class="label">Monthly Grand Total</span>
                <span class="amount">₱<?= number_format($grandTotal, 2) ?></span>
            </p>
        </div>
        <div class="header-right-actions">
            <div class="btn-container">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="inventorySearch" placeholder="Search..." onkeyup="filterTable()">
                </div>
                <a href="inventoryAlertExport.php" style="text-decoration: none;">
                    <button class="excel-btn">
                        <span class="material-symbols-outlined">download</span>Purchase Request
                    </button>
                </a>
                <button id="openBtn" class="opnbtn">
                    <span class="material-symbols-outlined">edit</span>Edit / Add
                </button>
            </div>
        </div>
    </div>

    <hr>

    <div class="table-wrapper">
        <table class="inventory-table" id="inventoryTable">
            <thead>
                <tr>
                    <?php foreach($cols as $index => $col): ?>
                        <th onclick="sortTable(<?= $index ?>)" data-column="<?= $col ?>" style="cursor: pointer;">
                            <div class="th-content">
                                <?php 
                                    $displayHeader = str_replace('_', ' ', $col);
                                    echo ucfirst($displayHeader); 
                                ?> 
                                <span class="material-symbols-outlined" style="font-size: 16px;">unfold_more</span>
                            </div>
                        </th>
                    <?php endforeach; ?>
                    <th data-column="total_value">Total Value</th>
                </tr>
            </thead>
<tbody>
    <?php 
    $result->data_seek(0); 
    while($row = $result->fetch_assoc()): 
        $rowTotal = (float)$row['quantity'] * (float)$row['price'];
        $isCritical = ($row['quantity'] <= $row['min_quantity']);
    ?>
        <tr data-id="<?= $row['id'] ?>" class="<?= $isCritical ? 'critical-row' : '' ?>">
            <?php foreach($cols as $col): ?>
                <td>
                    <?php 
                        if ($col === 'price') {
                            echo "₱" . number_format($row[$col], 2);
                        } 
                        elseif ($col === 'beginning_inventory') {
                            echo "<span style='color: #4a90e2; font-weight: 500;'>" . htmlspecialchars($row[$col] ?? '0') . "</span>";
                        } 
                        elseif ($col === 'received_qty') {
                            echo "<span style='color: #27ae60;'>+" . htmlspecialchars($row[$col] ?? '0') . "</span>";
                        } 
                        elseif ($col === 'quantity') {
                            echo "<strong style='" . ($isCritical ? "color: #e74c3c;" : "") . "'>" . htmlspecialchars($row[$col] ?? '0') . "</strong>";
                        }
                        else {
                            echo htmlspecialchars($row[$col] ?? '');
                        }
                    ?>
                </td>
            <?php endforeach; ?>
            <td class="total-val" style="font-weight: bold; background-color: #fcfcfc;">
                ₱<?= number_format($rowTotal, 2) ?>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
        </table>
    </div>

<?php else: ?>
    <div class="no-data-wrapper" style="text-align:center; padding:80px; background:#fff; border-radius:12px; border:1px dashed #ccc; margin-top:20px;">
        <span class="material-symbols-outlined" style="font-size:80px; color:#ddd;">inventory_2</span>
        <h3>No records found for <?= date("F", mktime(0, 0, 0, $selectedMonth, 1)) ?> <?= $selectedYear ?></h3>
        
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; align-items: center;">
            
            <button id="openBtnEmpty" class="opnbtn" style="margin: 0; display: flex; align-items: center; justify-content: center; height: 40px; padding: 0 20px;">

                Add First Item
            </button>

            <form method="POST" style="display: inline; margin: 0;">
                <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                <input type="hidden" name="year" value="<?= $selectedYear ?>">
                <button type="submit" name="carryOverAction" style="background-color: #f39c12; color: white; border: none; border-radius: 4px; cursor: pointer; height: 40px; padding: 0 20px; display: flex; align-items: center; justify-content: center; font-family: inherit; font-size: 14px; font-weight: 500;">
                    Carry Over from Last Month
                </button>
            </form>
            
        </div>
    </div>
<?php endif; ?>

<?php include "../../component/inventoryModal/inventoryModal.php"; ?>

<script>
    if (document.getElementById('openBtn')) {
        document.getElementById('openBtn').addEventListener('click', () => {
            document.getElementById('modal').classList.add('show');
        });
    }
    if (document.getElementById('openBtnEmpty')) {
        document.getElementById('openBtnEmpty').addEventListener('click', () => {
            document.getElementById('modal').classList.add('show');
        });
    }
</script>