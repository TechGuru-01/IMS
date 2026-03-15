<?php
// 1. LAHAT NG LOGIC AT AUTH SA TAAS
require_once '../../include/auth_checker.php';
include "../../include/config.php"; 

// --- AUTO-GENERATION LOGIC PARA SA MODAL ---
$check_max = "SELECT MAX(pr_id) as last_id FROM pr_reports";
$res_max = $conn->query($check_max);
$row_max = $res_max->fetch_assoc();

// Increment logic (Gagamitin ito sa display ng Modal)
$next_number = ($row_max['last_id'] ?? 0) + 1;
$report_num = str_pad($next_number, 4, '0', STR_PAD_LEFT);

// Buuin ang Reference Number (JIG-YYYYMMDD-0001)
$datePart = date('Ymd');
$generated_ref = "JIG-{$datePart}-{$report_num}";

// --- INSERT & ACKNOWLEDGE LOGIC ---
if (isset($_POST['bulk_resolve'])) {
    $ref_number   = $_POST['ref_number'];
    $pr_date      = $_POST['pr_date'];
    $company      = $_POST['company'];
    $total_amount = $_POST['total_amount'];
    $remarks      = $_POST['remarks'];
    
    // Arrays mula sa dynamic items at checkboxes
    $material_names = $_POST['material_names'] ?? []; 
    $item_nums      = $_POST['item_nums'] ?? [];
    $ids_to_resolve = $_POST['acknowledge_ids'] ?? []; 

    // Magsimula ng Transaction para sigurado
    $conn->begin_transaction();

    try {
        // A. I-save ang Main Report (Dikit-dikit na ang "sssds")
        $stmt = $conn->prepare("INSERT INTO pr_reports (ref_number, pr_date, company, total_amount, remarks) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $ref_number, $pr_date, $company, $total_amount, $remarks);
        $stmt->execute();

        // B. I-save ang Items
        $item_stmt = $conn->prepare("INSERT INTO pr_items (pr_ref_number, material_name, item_number) VALUES (?, ?, ?)");
        foreach ($material_names as $index => $name) {
            $m_name = $name;
            $i_num  = $item_nums[$index];
            $item_stmt->bind_param("ssi", $ref_number, $m_name, $i_num);
            $item_stmt->execute();
        }

        // C. UPDATE INVENTORY STATUS (Para mawala sa Dashboard list)
        if (!empty($ids_to_resolve)) {
            $update_stmt = $conn->prepare("UPDATE inventory SET is_acknowledged = 1 WHERE id = ?");
            foreach ($ids_to_resolve as $id) {
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
            }
        }

        // Pag lahat okay, i-save na sa DB
        $conn->commit();
        
        echo "<script>alert('Report #$report_num Saved and Items Acknowledged!'); window.location.href='dashBoard.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | OJT Project</title>
    <link rel="stylesheet" href="../../style.css" /> 
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="dashBoard.css" />
    <link rel="stylesheet" href="../../component/currency/currency.css">
    <link rel="stylesheet" href="../../component/inventoryAlertBox/inventoryAlertsModal.css" />
    <link rel="stylesheet" href="../../component/inventoryAlertBox/inventoryAlerts.css">
    <link rel="stylesheet" href="../../component/graphBox/graph.css">
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <section class="main">
        <div class="box">
            <div class="box-1">
                <?php include __DIR__ . "/../../component/currency/currency.php"; ?>
            </div>
             
            <?php include __DIR__ . "/../../component/inventoryAlertBox/inventoryAlerts.php"; ?>
            <?php include "../../component/inventoryAlertBox/inventoryAlertsModal.php"; ?> 
            
            <?php include __DIR__ . "/../../component/graphBox/graph.php"; ?> 
        </div>

        <?php include __DIR__ . "/../../component/settings/settings.php"; ?>
    </section>

    <script src="../../component/inventoryAlertBox/inventoryAlerts.js"></script>
    <script src="../../component/inventoryAlertBox/inventoryAlertsModal.js"></script>
    <script src="../../component/settings/settings.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        var serverChartData = <?php echo json_encode($chartData ?? []); ?>;
        var serverView = "<?php echo $view ?? 'weekly'; ?>";
    </script>

    <script src="../../component/graphBox/chart.js"></script>
</body>
</html>