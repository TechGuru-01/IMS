<?php
// 1. DATABASE CONNECTION & AUTH
require_once "../../include/config.php"; 
require_once "../../include/auth_checker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- BAGONG DAGDAG: MANUAL STATUS UPDATE LISTENER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status_manual'])) {
    $ref = $_POST['ref_number'];
    $new_status = $_POST['new_status'];

    $update_stmt = $conn->prepare("UPDATE pr_reports SET status = ? WHERE ref_number = ?");
    $update_stmt->bind_param("ss", $new_status, $ref);
    
    if ($update_stmt->execute()) {
        // Refresh para makita agad ang pagbabago
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// 2. DATE FILTERING
$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');

// Status Helper Function (Para sa CSS classes)
function getStatusClass($status) {
    $s = strtolower(trim($status ?? ''));
    switch ($s) {
        case 'cancelled': return 'status-cancelled';
        case 'follow up': return 'status-follow-up';
        case 'hold': return 'status-hold';
        case 'on process': return 'status-on-process';
        case 'ready for reporting': return 'status-done';
        case 'production office': return 'status-production';
        case 'received': return 'status-received';
        default: return 'status-default';
    }
}

/** * 3. MAIN SQL QUERY */
$sql = "SELECT r.*, 
        GROUP_CONCAT(i.material_name SEPARATOR ', ') as all_materials
        FROM pr_reports r
        LEFT JOIN pr_items i ON r.ref_number = i.pr_ref_number
        WHERE MONTH(r.pr_date) = $m 
        AND YEAR(r.pr_date) = $y
        GROUP BY r.pr_id
        ORDER BY r.pr_id DESC";

$result = $conn->query($sql); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Purchase Request Status</title>
    
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="PRSStatus.css">
    <link rel="stylesheet" href="../../pages/inventory/inventory.css"> 
    <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <div class="inventory-container list-mode" id="mainContainer">
        <div class="inv-header">
            <div>
                <h2>PRS Status</h2>
                <p style="color: #666; font-size: 0.9rem;">
                    Showing records for: <strong><?= date('F', mktime(0, 0, 0, $m, 1)) ?> <?= $y ?></strong>
                </p>
            </div>
            
            <div class="btn-container">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="inventorySearch" placeholder="Search Reference or Company..." onkeyup="filterTable()">
                </div>
                
                <div class="view-toggle">
                    <a href="#" id="listView" class="toggle-btn active">
                        <span class="material-symbols-outlined">format_list_bulleted</span> List
                    </a>
                    <a href="#" id="gridView" class="toggle-btn">
                        <span class="material-symbols-outlined">grid_view</span> Grid
                    </a>
                </div>
            </div>
        </div>

        <hr>

        <div class="status-content">
            <?php include __DIR__ . "/PRSStatusListView.php"; ?>
        </div>

        <?php include __DIR__ . "/../../component/settings/settings.php"; ?>
    </div>

    <script src="../../component/settings/settings.js"></script>
    <script src="../../component/search.js"></script>
    <script src="./PRSStatus.js"></script>
    
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