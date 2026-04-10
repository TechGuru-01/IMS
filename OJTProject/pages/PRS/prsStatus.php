<?php
require_once "../../include/prsStatusFunctions.php";
require_once "../../include/InventoryAlertsModalFunction.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Purchase Request Status</title>
    
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="prsStatus.css">
    <link rel="stylesheet" href="../../component/prsModal/prsModal.css">
    <link rel="stylesheet" href="../../pages/inventory/inventory.css"> 
    <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
    <link rel="stylesheet" href="../../component/utils/utils.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <div class="inventory-container list-mode" id="mainContainer">
        <div class="inv-header">
            <div>
                <h2 style="font-weight: 800; color: #072d7a;">PRS STATUS</h2>
                <p style="color: #64748b; font-size: 0.85rem;">
                    Monitoring records for <strong><?= date('F Y', mktime(0, 0, 0, $m, 1, $y)) ?></strong>
                </p>
            </div>
            
            <div class="btn-container">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="inventorySearch" placeholder="Search Reference..." onkeyup="filterTable()">
                </div>
                
                <div class="view-toggle">
                    <a href="#" id="listView" class="toggle-btn active"><span class="material-symbols-outlined">format_list_bulleted</span>List</a>
                    <a href="#" id="gridView" class="toggle-btn"><span class="material-symbols-outlined">grid_view</span>Grid</a>
                </div>
            </div>
        </div>

        <hr>

        <div class="summary-wrapper">
            <div class="stat-card card-total"><h4>Total</h4><div class="count"><?= $counts['total'] ?? 0 ?></div></div>
            <div class="stat-card card-on-process"><h4>On Process</h4><div class="count"><?= $counts['on_process'] ?? 0 ?></div></div>
            <div class="stat-card card-hold"><h4>Hold</h4><div class="count"><?= $counts['hold'] ?? 0 ?></div></div>
            <div class="stat-card card-follow-up"><h4>Follow Up</h4><div class="count"><?= $counts['follow_up'] ?? 0 ?></div></div>
            <div class="stat-card card-production"><h4>Production</h4><div class="count"><?= $counts['production'] ?? 0 ?></div></div>
            <div class="stat-card card-ready"><h4>Ready</h4><div class="count"><?= $counts['ready_reporting'] ?? 0 ?></div></div>
            <div class="stat-card card-received"><h4>Received</h4><div class="count"><?= $counts['received'] ?? 0 ?></div></div>
            <div class="stat-card card-cancelled"><h4>Cancelled</h4><div class="count"><?= $counts['cancelled'] ?? 0 ?></div></div>
        </div>

        <div class="control-bar">
            <form method="GET" class="filter-section">
                <div class="filter-item">
                    <label>Period</label>
                    <input type="month" name="filter_month" value="<?= $y ?>-<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>">
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status_filter">
                        <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Records</option>
                        <option value="On Process" <?= $status_filter == 'On Process' ? 'selected' : '' ?>>On Process</option>
                        <option value="Hold" <?= $status_filter == 'Hold' ? 'selected' : '' ?>>Hold</option>
                        <option value="Follow Up" <?= $status_filter == 'Follow Up' ? 'selected' : '' ?>>Follow Up</option>
                        <option value="Production Office" <?= $status_filter == 'Production Office' ? 'selected' : '' ?>>Production Office</option>
                        <option value="Ready for Reporting" <?= $status_filter == 'Ready for Reporting' ? 'selected' : '' ?>>Ready for Reporting</option>
                        <option value="Received" <?= $status_filter == 'Received' ? 'selected' : '' ?>>Received</option>
                        <option value="Cancelled" <?= $status_filter == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn-apply">Apply Filter</button>
                <a href="PRSStatus.php" class="btn-reset">Reset</a>
            </form>

            <div class="action-section">
            <button class="btn-action" id="reuseBtn" onclick="toggleReuseMode()" style="background-color: #28a745;">
                <span class="material-symbols-outlined" style="font-size: 18px;">history</span> 
                <span id="reuseText">Reuse</span>
            </button>
            <button type="button" onclick="openNewPRModal()" style="background:#072d7a; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                <span class="material-symbols-outlined">add_circle</span>
                Create New PR
            </button>
                </div>
            </div> <div class="status-content">
                <?php include __DIR__ . "/prsTableView.php"; ?>
            </div>

            <?php include __DIR__ . "/../../component/utils/utils.php"; ?>
            <?php include "../../component/prsModal/prsModal.php"; ?>  

        </div> 
        <script src="../../component/prsModal/prsModal.js"></script>
        <script src="../../component/utils/utils.js"></script>
        <script src="../../component/search.js"></script>
        <script src="./prsStatus.js"></script>

    <script>
        function filterTable() {
            let input = document.getElementById("inventorySearch").value.toLowerCase();
            let rows = document.querySelectorAll("#inventoryTable tbody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>