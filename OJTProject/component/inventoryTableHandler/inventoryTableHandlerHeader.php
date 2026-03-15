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
                <a href="/OJTProject/component/inventoryTableHandler/inventoryExport.php" style="text-decoration: none;">
                    <button type="button" class="excel-btn">
                        <span class="material-symbols-outlined">download</span> Export Inventory
                    </button>
                </a>
                <button id="openBtn" class="opnbtn">
                    <span class="material-symbols-outlined">edit</span>Edit / Add
                </button>
            </div>
        </div>
    </div>