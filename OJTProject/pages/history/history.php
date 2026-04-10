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

$groupedItems = [];
$sql = "SELECT * FROM history 
        WHERE MONTH(date) = $m 
        AND YEAR(date) = $y 
        ORDER BY item ASC, description ASC, date DESC, id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $itemName = $row['item'] ?? 'Unknown Item';
        $desc = $row['description'] ?? 'No Description';
        $groupKey = $itemName . " | " . $desc;
        $groupedItems[$groupKey][] = $row;
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
    <link rel="stylesheet" href="../../component/utils/utils.css">
    <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />

    <title>HEPC JIG IMS | History </title>
    
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
            background: #e9ecef;
        }
        .td-date {
            color: #2c3e50;
            font-weight: 500;
            white-space: nowrap;
        }
        .inventory-table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 5;
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
        }
        .btnContainer {
            display: flex;
            align-items: center; 
            justify-content: center; 
            gap: 15px; 
            flex-wrap: wrap; 
        }
        .search-box {
            margin-bottom: 0 !important; 
            display: flex;
            align-items: center;
            background: #f1f3f4;
            padding: 5px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
        }
        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            padding: 5px;
            margin-left: 5px;
            width: 200px;
        }
        .history-checkbox, .group-select-all {
            width: 11px;
            height: 11px;
            cursor: pointer;
            accent-color: #007bff;
        }
        .inventory-table th:nth-child(4), 
        .inventory-table td:nth-child(4) {
            display: none !important;
        }
        @media (max-width: 768px) {
    .item-group-wrapper {
        overflow: auto; 
        max-height: 500px; 
        width: 100%;
        -webkit-overflow-scrolling: touch; 
        padding: 15px; 
    }
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
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="historySearch" placeholder="Search history..." onkeyup="historyLiveSearch()">
                </div>

                <button type="button" class="excel-btn hide-on-mobile" onclick="exportSelectedHistory()" style="display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined">download</span> Export to Excel
                </button>
            </div>
        </div>

        <div class="table-wrapper" style="max-height: 80vh; overflow-y: auto; border-radius: 8px;">
            <div id="historyTableContainer">
                <?php if (!empty($groupedItems)): ?>
                    <?php foreach ($groupedItems as $groupKey => $transactions): ?>
                        <div class="item-group-wrapper">
                            <h3 class="item-title"><?= htmlspecialchars($groupKey) ?></h3>
                            <table class="inventory-table">
                                <thead>
                                    <tr>
                                        <?php foreach($cols as $col): ?>
                                            <?php if (in_array(strtolower($col), ['id', 'description', 'item'])) continue; ?> 
                                            <th><?= ucfirst(str_replace('_', ' ', $col)) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $row): ?>
                                        <tr>
                                            <?php foreach ($cols as $col): ?>
                                                <?php if (in_array(strtolower($col), ['id', 'description', 'item'])) continue; ?> 
                                                <td class="<?= ($col == 'date') ? 'td-date' : '' ?>">
                                                    <?php 
                                                        $val = $row[$col];
                                                        
                                                        
                                                        if ($val === null || trim((string)$val) === '') {
                                                            $numericCols = ['quantity_in', 'quantity_out', 'remaining'];
                                                            echo in_array($col, $numericCols) ? '0' : '<span style="color:#ccc;">N/A</span>';
                                                        } else {
                                                            $cleanVal = htmlspecialchars($val);
                                                            if ($col == 'action') {
                                                                echo "<span class='status-badge'>$cleanVal</span>";
                                                            } else {
                                                                echo $cleanVal;
                                                            }
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
    </div>
        <?php include "../../component/utils/utils.php"; ?>
         <script src="../../component/utils/utils.js"></script></script>

    <script src="../../component/search.js"></script>
    <script>
      

        function exportSelectedHistory() {
            const selected = document.querySelectorAll('.history-checkbox:checked');
            const ids = Array.from(selected).map(cb => cb.value);
            const m = "<?= $m ?>";
            const y = "<?= $y ?>";

            let exportUrl = `./historyExport.php?month=${m}&year=${y}`;
            if (ids.length > 0) {
                exportUrl += `&ids=${ids.join(',')}`;
            }
            window.location.href = exportUrl;
        }
    </script>
</body>
</html>