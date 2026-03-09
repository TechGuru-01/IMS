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
                            <button class="excel-btn"><span class="material-symbols-outlined">download</span>Purchase Request</button>
                        </a>
                        <button id="openBtn" class="opnbtn"><span class="material-symbols-outlined">edit</span>Edit / Add</button>
                    </div>
                </div>
            </div>

            <?php include "../../component/inventoryModal/inventoryModal.php"; ?>
             <hr>
            <div class="table-wrapper">
                <table class="inventory-table" id="inventoryTable">
                    <thead>
                        <tr>
                            <?php foreach($cols as $index => $col): ?>
                                <th onclick="sortTable(<?= $index ?>)" style="cursor: pointer;">
                                    <div class="th-content"><?= ucfirst($col) ?> <span class="material-symbols-outlined" style="font-size: 16px;">unfold_more</span></div>
                                </th>
                            <?php endforeach; ?>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                            $rowTotal = (float)$row['quantity'] * (float)$row['price'];
                        ?>
                            <tr data-id="<?= $row['id'] ?>">
                                <?php foreach($cols as $col): ?>
                                    <td><?= ($col === 'price') ? "₱".number_format($row[$col], 2) : htmlspecialchars($row[$col] ?? '') ?></td>
                                <?php endforeach; ?>
                                <td class="total-val">₱<?= number_format($rowTotal, 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data-wrapper" style="text-align:center; padding:80px; background:#fff; border-radius:12px; border:1px dashed #ccc; margin-top:20px;">
                <span class="material-symbols-outlined" style="font-size:80px; color:#ddd;">inventory_2</span>
                <h3>No records found for <?= date("F", mktime(0, 0, 0, $selectedMonth, 1)) ?> <?= $selectedYear ?></h3>
                <button id="openBtnEmpty" class="opnbtn" >
                    Add First Item
                </button>
            </div>
            <?php include "../../component/inventoryModal/inventoryModal.php"; ?>
            <script>
                document.getElementById('openBtnEmpty').addEventListener('click', function() {
                    document.getElementById('modal').classList.add('show');
                });
            </script>
        <?php endif; ?>