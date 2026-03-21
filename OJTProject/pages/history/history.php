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
        ORDER BY description ASC, date DESC, id DESC";

$result = $conn->query($sql);
$groupedItems = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $desc = $row['description'] ?? 'No Description';
        $groupedItems[$desc][] = $row;
    }
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
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <title>HEPC JIG IMS | History</title>
    
    <style>
        .item-group-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .item-title {
            color: #007bff;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-size: 1.2rem;
            text-transform: uppercase;
        }
        .inventory-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .td-date {
            color: #2c3e50;
            font-weight: 500;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <div class="inventory-container">
        <div class="inv-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="margin: 0; color: #2c3e50;">Transaction History</h2>
                <p style="color: #666; font-size: 0.95rem; margin-top: 5px;">
                    Records for: <span style="color: #007bff; font-weight: 600;"><?= date('F', mktime(0, 0, 0, $m, 1)) ?> <?= $y ?></span>
                </p>
            </div>
            <div class="btnContainer">
                <a href="./historyExport.php?month=<?= $m ?>&year=<?= $y ?>" style="text-decoration: none;">
                    <button class="excel-btn" style="display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined">download</span> Export to Excel
                    </button>
                </a>
            </div>
        </div>
    
        <div id="historyTableContainer">
            <?php if (!empty($groupedItems)): ?>
                <?php foreach ($groupedItems as $description => $transactions): ?>
                    
                    <div class="item-group-wrapper">
                        <h3 class="item-title"><?= htmlspecialchars($description) ?></h3>
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <?php foreach($cols as $col): ?>
                                        <?php if (strtolower($col) == 'id' || strtolower($col) == 'description') continue; ?> 
                                        <th><?= ucfirst(str_replace('_', ' ', $col)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $row): ?>
                                    <tr>
                                        <?php foreach ($cols as $col): ?>
                                            <?php if (strtolower($col) == 'id' || strtolower($col) == 'description') continue; ?> 
                                            
                                            <td class="<?= ($col == 'date') ? 'td-date' : '' ?>">
                                                <?php 
                                                    $val = htmlspecialchars($row[$col] ?? '');
                                                    if ($col == 'action') {
                                                        echo "<span class='status-badge'>$val</span>";
                                                    } else {
                                                        echo $val;
                                                    }
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="item-group-wrapper" style="text-align:center; padding: 60px 0;">
                    <span class="material-symbols-outlined" style="font-size: 64px; color: #ccc;">history_toggle_off</span>
                    <h3 style="margin-top: 15px; font-weight: 400;">No history records found</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filterHistory() {
            let input = document.getElementById('historySearch');
            let filter = input.value.toUpperCase();
            let wrappers = document.querySelectorAll(".item-group-wrapper");

            wrappers.forEach(wrapper => {
                let text = wrapper.textContent.toUpperCase();
                wrapper.style.display = text.includes(filter) ? "" : "none";
            });
        }
    </script>
</body>
</html>