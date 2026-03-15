<?php
// 1. SETTINGS & RATES
$usd_rate = 1;
$jpy_rate = 1;

$query_settings = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('dollar_rate', 'yen_rate')";
$res_settings = $conn->query($query_settings);

if ($res_settings && $res_settings->num_rows > 0) {
    while($row = $res_settings->fetch_assoc()) {
        if ($row['setting_key'] == 'dollar_rate') $usd_rate = $row['setting_value'];
        if ($row['setting_key'] == 'yen_rate') $jpy_rate = $row['setting_value'];
    }
}

// 2. REFERENCE NUMBER GENERATOR
$check_max = "SELECT MAX(pr_id) as last_id FROM pr_reports";
$res_max = $conn->query($check_max);
$row_max = $res_max->fetch_assoc();
$next_number = ($row_max['last_id'] ?? 0) + 1;
$report_num = str_pad($next_number, 4, '0', STR_PAD_LEFT);
$generated_ref = "JIG-" . date('Ymd') . "-" . $report_num;

// 3. MAIN INSERT LOGIC
if (isset($_POST['bulk_resolve'])) {
    // 1. Kunin ang lahat ng inputs mula sa form
    $ref_number   = $_POST['ref_number'] ?? '';
    $pr_date      = $_POST['pr_date'] ?? date('Y-m-d');
    $company      = $_POST['company'] ?? '';
    $currency     = $_POST['currency'] ?? 'PHP'; 
    $quantity     = (float)($_POST['quantity_req'] ?? 0); 
    $unit_price   = (float)($_POST['price'] ?? 0);        
    $total_amount = (float)($_POST['total_amount'] ?? 0); 
    $remarks      = $_POST['remarks'] ?? '';

    // 2. SQL INSERT - Siguraduhin na kasama lahat ng columns na hinahanap mo
    $sql = "INSERT INTO pr_reports 
            (ref_number, pr_date, company, currency, quantity, unit_price, total_amount, remarks) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare Error: " . $conn->error);
    }

    // 3. Bind Parameters (8 values)
    // s = string, d = double/decimal
    // Order: ref(s), date(s), company(s), currency(s), qty(d), price(d), total(d), remarks(s)
    $stmt->bind_param("ssssddds", 
        $ref_number, 
        $pr_date, 
        $company, 
        $currency, 
        $quantity, 
        $unit_price, 
        $total_amount, 
        $remarks
    );

    if ($stmt->execute()) {
        // --- LOGIC PARA SA PR_ITEMS (Materials) ---
        if (!empty($_POST['material_names'])) {
            $material_names = $_POST['material_names'];
            $item_nums      = $_POST['item_nums'];
            
            $item_stmt = $conn->prepare("INSERT INTO pr_items (pr_ref_number, material_name, item_number) VALUES (?, ?, ?)");
            
            foreach ($material_names as $index => $name) {
                $i_ref  = $ref_number;
                $i_name = $name;
                $i_num  = (int)($item_nums[$index] ?? ($index + 1));
                
                $item_stmt->bind_param("ssi", $i_ref, $i_name, $i_num);
                $item_stmt->execute();
            }
        }

        echo "<script>alert('Report Saved Successfully!'); window.location.href='dashBoard.php';</script>";
        exit();
    } else {
        die("Execute Error sa pr_reports: " . $stmt->error);
    }
}
?>
<div id="prModal" class="modal-overlay">
    <div class="pr-modal">
        <h3 id="prModalTitle" style="color:#072d7a; margin-top:0;">Prepare Purchase Request #<?= $report_num ?></h3>
        
        <form id="exportForm" action="" method="POST">
            <div id="hidden_id_container" style="display:none;"></div>
            <input type="hidden" name="ref_number" id="final_ref">

            <div class="pr-grid">
                <div class="pr-form-group full-row">
                    <label>Reference Number</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" id="gen_ref" class="pr-input-style" style="background:#edf2f7; width:60%;" value="<?= $generated_ref ?>" readonly>
                        <input type="text" id="admin_suffix" class="pr-input-style" placeholder="Suffix" style="width:35%;">
                    </div>
                </div>

                <div class="pr-form-group full-row">
                    <label>Materials to Process</label>
                    <div id="dynamic_items_container" class="isolated-box"></div>
                </div>

                <div class="pr-form-group">
                    <label>Date</label>
                    <input type="date" name="pr_date" class="pr-input-style" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="pr-form-group">
                    <label>Attention to</label>
                    <input list="company_options" name="company" class="pr-input-style" required>
                    <datalist id="company_options">
                        <option value="Samsung">
                        <option value="TDK Philippines">
                    </datalist>
                </div>

                <div class="pr-form-group">
                    <label>Currency</label>
                    <select name="currency" id="currency_type" class="pr-input-style" onchange="calculateTotal()">
                        <option value="PHP" data-rate="1">PHP (₱)</option>
                        <option value="USD" data-rate="<?= $usd_rate ?>">USD ($)</option>
                        <option value="JPY" data-rate="<?= $jpy_rate ?>">JPY (¥)</option>
                    </select>
                </div>

                <div class="pr-form-group">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" id="pr_price" name="price" class="pr-input-style" required oninput="calculateTotal()">
                </div>

                <div class="pr-form-group">
                    <label>Quantity</label>
                    <input type="number" id="pr_qty" name="quantity_req" class="pr-input-style" required oninput="calculateTotal()">
                </div>

                <div class="pr-form-group full-row">
                    <label>Total Amount (<span id="currency_label">PHP</span>)</label>
                    <input type="number" step="0.01" id="pr_total" name="total_amount" class="pr-input-style" readonly style="background:#f8fafc; font-weight:bold;">
                </div>

                <div class="pr-form-group full-row">
                    <label>Remarks</label>
                    <textarea name="remarks" class="pr-input-style" style="height:60px; resize:none;"></textarea>
                </div>

               <div class="form-actions full-row" style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px;">
                    <button type="button" onclick="closePRModal()" style="padding:10px 20px; border-radius:8px; border:1px solid #ccc; background:white;">Cancel</button>
                    <button type="submit" name="bulk_resolve" onclick="return prepareSubmission();" style="padding:10px 25px; background:#072d7a; color:white; border:none; border-radius:8px; font-weight:bold;">
                        Resolve & Export
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>