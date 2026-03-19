<?php
$usd_rate = 1; $jpy_rate = 1;
$res_settings = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('dollar_rate', 'yen_rate')");
if($res_settings) {
    while($row = $res_settings->fetch_assoc()) {
        if ($row['setting_key'] == 'dollar_rate') $usd_rate = $row['setting_value'];
        if ($row['setting_key'] == 'yen_rate') $jpy_rate = $row['setting_value'];
    }
}


$check_max = "SELECT MAX(pr_id) as last_id FROM pr_reports";
$res_max = $conn->query($check_max);
$row_max = $res_max->fetch_assoc();
$next_number = ($row_max['last_id'] ?? 0) + 1;
$report_num = str_pad($next_number, 4, '0', STR_PAD_LEFT);
$datePart = date('Ymd');
$generated_ref = "JIG-{$datePart}-{$report_num}";
?>

<style>
    .pr-input-style {
        width: 100%;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        box-sizing: border-box;
    }

.pr-items-grid {
    display: grid;
    grid-template-columns: 20px 1fr 1fr 0.8fr 70px 50px 80px 100px 35px !important;
    gap: 10px;
    align-items: center;
}


.pr-header-labels {
    padding: 10px 0;
    border-bottom: 2px solid #e2e8f0;
    font-weight: bold;
    font-size: 11px;
    color: #475569;
    text-transform: uppercase;
}
    .text-right { text-align: right; }
    .text-center { text-align: center; }
</style>
<div id="prModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; width:95%; max-width:900px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow-y:auto; max-height:90vh;">
        
        <h3 id="modal_title" style="color:#072d7a; margin:0 0 20px 0; border-bottom:2px solid #edf2f7; padding-bottom:10px;">
            📝 Prepare Purchase Request
        </h3>
        
        <form id="exportForm" method="POST" action="../../prs_gen.php" target="_blank">
            <input type="hidden" name="form_mode" id="form_mode" value="create">
            <input type="hidden" name="ref_number" id="final_ref">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                
                <div style="grid-column: span 2;">
                    <label style="font-weight:600; font-size:13px;">Reference Number & Suffix</label>
                    <div style="display:flex; gap:10px; margin-top:5px;">
                        <input type="text" id="gen_ref" class="pr-input-style" 
                               style="background:#edf2f7; flex:2;" 
                               value="<?= htmlspecialchars($generated_ref) ?>" readonly>
                        <input type="text" name="admin_suffix" id="admin_suffix" 
                               class="pr-input-style" placeholder="Suffix (e.g. URGENT, REUSE)" style="flex:1;">
                    </div>
                </div>
                <div>
                    <label style="font-weight:600; font-size:13px;">PR Date</label>
                    <input type="date" name="pr_date" id="modal_pr_date" class="pr-input-style" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div>
                    <label style="font-weight:600; font-size:13px;">Attention to</label>
                    <input list="company_options" name="company" id="modal_company" class="pr-input-style" required>
                </div>
                <div style="grid-column: span 2; display: flex; justify-content: space-between; align-items: flex-end; margin-top: 15px;">
                        <label style="font-weight:600; font-size:14px; color:#072d7a;">📦 Item Details</label>
                        <button type="button" onclick="addNewRow()" style="background:#10b981; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; transition: 0.2s; display: flex; align-items: center; gap: 5px;">
                            <span style="font-size:16px;">+</span> Add New Item
                        </button>
                    </div>

                    <div style="grid-column: span 2; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-top: 5px; background: #fcfcfc;">
                        <div class="pr-items-grid pr-header-labels" style="border-bottom: 2px solid #cbd5e1; padding-bottom: 8px;">
                            <div class="text-center">No.</div>
                            <div>Item Name</div>
                            <div>Description/Specs</div>
                            <div>Maker</div>
                            <div>UOM</div>
                            <div class="text-center">Qty</div>
                            <div>Unit Price</div>
                            <div class="text-right">Subtotal</div>
                            <div></div> </div>
                        
                        <div id="items_list_body" style="max-height:300px; overflow-y:auto; padding-top: 10px;">
                            </div>
                    </div>

               

                <div>
                    <label style="font-weight:600; font-size:13px;">Currency</label>
                    <select name="currency" id="currency_type" class="pr-input-style" onchange="calculateGrandTotal()">
                        <option value="PHP" data-rate="1">PHP (₱)</option>
                        <option value="USD" data-rate="<?= $usd_rate ?>">USD ($)</option>
                        <option value="JPY" data-rate="<?= $jpy_rate ?>">JPY (¥)</option>
                    </select>
                </div>

                <div>
                    <label style="font-weight:600; font-size:13px;">RM / FG</label>
                    <input list="RMFG_options" name="rm_fg" id="modal_rmfg" class="pr-input-style" required> 
                    <datalist id="RMFG_options"><option value="✔"><option value="✖"></datalist>
                </div>

                <div>
                    <label style="font-weight:600; font-size:13px;">Types of Requisition</label>
                    <input list="ToR_options" name="ToR" id="modal_tor" class="pr-input-style" required>
                    <datalist id="ToR_options">
                        <option value="Machinery"><option value="Maintenance Parts & Supplies">
                    </datalist>
                </div>

                <div style="grid-column: span 2;">
                    <label style="font-weight:600; font-size:13px;">Grand Total</label>
                    <input type="number" step="0.01" id="pr_total" name="total_amount" class="pr-input-style" readonly style="background:#f1f5f9; font-weight:700; color:#072d7a; font-size:16px;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="font-weight:600; font-size:13px;">Remarks</label>
                    <textarea name="remarks" id="modal_remarks" class="pr-input-style" style="height:50px; resize:none;"></textarea>
                </div>
                
                <div style="grid-column: span 2; display:flex; justify-content:flex-end; gap:12px; margin-top:10px; border-top:1px solid #edf2f7; padding-top:15px;">
                    <button type="button" onclick="closePRModal()" style="padding:10px 20px; border-radius:8px; cursor:pointer; background:white; border:1px solid #cbd5e1;">Cancel</button>
                    <button type="submit" name="bulk_resolve" onclick="return prepareSubmission();" 
                            style="background:#072d7a; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">
                            Save & Download
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<datalist id="maker_options">
    <option value="Samsung">
    <option value="TDK Philippines">
    <option value="Murata">
    <option value="Kyocera">
</datalist>

