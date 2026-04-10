<?php
include "config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'borrow') {
    $uuid = $_POST['item_uuid']; 
    $qtyToBorrow = (int)$_POST['qty'];
    
    // Server-side validation
    $technician = !empty($_POST['technician']) ? $_POST['technician'] : null; 
    $customer   = !empty($_POST['customer']) ? $_POST['customer'] : null; 
    $jig_eq     = !empty($_POST['equipment']) ? $_POST['equipment'] : null; 
    $remarks    = $_POST['remarks'] ?? '';

    if (!$technician || !$customer || !$jig_eq || $qtyToBorrow <= 0 || empty($uuid)) {
        echo json_encode(["status" => "error", "message" => "Lahat ng fields ay required!"]);
        exit;
    }

    // Start Transaction
    $conn->begin_transaction();

    try {
        // 1. Check Inventory
        $check = $conn->prepare("SELECT item, quantity, cabinet, description FROM inventory WHERE item_uuid = ? FOR UPDATE");
        $check->bind_param("s", $uuid);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();

        if (!$row || $row['quantity'] < $qtyToBorrow) {
            throw new Exception("Not enough stock!");
        }

        $itemName     = $row['item'];
        $itemCabinet  = (int)$row['cabinet']; 
        $itemDesc     = $row['description'] ?? '';
        $remainingQty = $row['quantity'] - $qtyToBorrow;

        // 2. Update Inventory
        $update = $conn->prepare("UPDATE inventory SET quantity = ? WHERE item_uuid = ?");
        $update->bind_param("is", $remainingQty, $uuid);
        if (!$update->execute()) throw new Exception("Inventory update failed");

        // 3. Insert to History
        // Bilangin natin: May 9 na '?' dito para sa 9 na variables sa bind_param
        $sql = "INSERT INTO history (
            `date`, `quantity_out`, `quantity_in`, `min_quantity`, 
            `name`, `item`, `description`, 
            `technician`, `customer`, `equipment`, `remaining`, `remarks`
        ) VALUES (NOW(), ?, 0, 0, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        // Mayroong 9 na '?' sa itaas, kaya dapat 9 din ang variables dito.
        // "issssssis" -> i (int), s (string), s, s, s, s, s, i (int), s
        $stmt->bind_param("issssssis", 
            $qtyToBorrow,   // quantity_out
            $technician,    // name (o kung sino ang nag-out)
            $itemName,      // item
            $itemDesc,      // description
            $technician,    // technician column
            $customer,      // customer column
            $jig_eq,        // equipment column
            $remainingQty,  // remaining
            $remarks        // remarks
        );

        if (!$stmt->execute()) throw new Exception("History failed: " . $stmt->error);

        // Commit transaction
        $conn->commit();
        echo json_encode(["status" => "success"]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

// Lists for dropdowns
$technicianList = $conn->query("SELECT name FROM technicians ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$jigList = $conn->query("SELECT name FROM jig_equipment ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$customerList = $conn->query("SELECT name FROM customers ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch item details
$itemUuid = $_GET['item_uuid'] ?? '';
$stmt = $conn->prepare("SELECT item_uuid, item, description, quantity, cabinet FROM inventory WHERE item_uuid = ?");
$stmt->bind_param("s", $itemUuid);
$stmt->execute();
$itemData = $stmt->get_result()->fetch_assoc();

$display_uuid    = $itemData['item_uuid'] ?? '';
$display_name    = $itemData['item'] ?? "Item Not Found";
$display_qty     = (int)($itemData['quantity'] ?? 0);
$display_desc    = $itemData['description'] ?? "No description available";
$display_cabinet = $itemData['cabinet'] ?? "N/A";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Pull Out Request</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="style.css">
    <style>
        .required-label::after { content: " *"; color: red; }
    </style>
</head>
<body>
    <header class="app-header" style="display: flex; align-items: center; gap: 15px; padding: 10px 20px;">
    <a href="javascript:history.back()" style="color: white; text-decoration: none; display: flex; align-items: center;">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <h1 style="margin: 0; font-size: 1.2rem;">
        <span class="material-symbols-outlined" style="vertical-align: middle;">shopping_cart_checkout</span> Pull Out Form
    </h1>
</header>

    <div class="container">
        <input type="hidden" id="item-uuid" value="<?= $display_uuid ?>">

        <div class="item-card">
            <p class="item-name"><?= htmlspecialchars($display_name) ?></p>
            <p class="item-desc"><?= htmlspecialchars($display_desc) ?></p>
            <p class="item-cabinet" style="font-size: 0.95rem; color: #001f3f; font-weight: bold; margin-top: 5px;">
                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px;">inventory_2</span> 
                Cabinet: <?= htmlspecialchars($display_cabinet) ?>
            </p>

            <span class="qty-badge <?= $display_qty > 0 ? '' : 'out-of-stock' ?>">
                Available: <?= $display_qty ?> units
            </span>
        </div>

        <div class="form-section">
            <div class="input-group">
                <label class="required-label">Technician Name</label>
                <select id="technician-name">
                    <option value="" disabled selected>Select technician...</option>
                    <?php foreach ($technicianList as $t): ?>
                        <option value="<?= htmlspecialchars($t['name']) ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label class="required-label">JIG / Machine</label>
                <select id="jig-equipment">
                    <option value="" disabled selected>Select equipment...</option>
                    <?php foreach ($jigList as $j): ?>
                        <option value="<?= htmlspecialchars($j['name']) ?>"><?= htmlspecialchars($j['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label class="required-label">Customer / Department</label>
                <select id="customer-name">
                    <option value="" disabled selected>Select customer...</option>
                    <?php foreach ($customerList as $c): ?>
                        <option value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="input-group">
                <label class="required-label">Quantity to Out</label>
                <input type="number" 
                    id="borrow-qty" 
                    class="qty-input-mobile" 
                    value="<?= $display_qty > 0 ? 1 : 0 ?>" 
                    min="<?= $display_qty > 0 ? 1 : 0 ?>" 
                    max="<?= $display_qty ?>">
            </div>

            <div class="input-group">
                <label>Remarks / Reason (Optional)</label>
                <textarea id="borrow-reason" rows="3"></textarea>
            </div>

            <button class="submit-btn" onclick="showModal()" <?= $display_qty <= 0 ? 'disabled' : '' ?>>
                <?= $display_qty > 0 ? 'Submit Request' : 'OUT OF STOCK' ?>
            </button>
        </div>
    </div>

    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <h3>Confirm Transaction</h3>
            <p>Please review the details before confirming.</p>
            <div class="modal-btns">
                <button class="btn-cancel" onclick="hideModal()">Back</button>
                <button class="btn-confirm" onclick="proceedSubmit()">Confirm</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>