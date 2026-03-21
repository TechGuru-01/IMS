<?php
$usd_rate = 1; 
$jpy_rate = 1;
$res_settings = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('dollar_rate', 'yen_rate')");

while($row = $res_settings->fetch_assoc()) {
    if ($row['setting_key'] == 'dollar_rate') $usd_rate = $row['setting_value'];
    if ($row['setting_key'] == 'yen_rate') $jpy_rate = $row['setting_value'];
}

date_default_timezone_set('Asia/Manila'); 

$res_max = $conn->query("SELECT MAX(pr_id) as last_id FROM pr_reports");
$row_max = $res_max->fetch_assoc();
$next_num = ($row_max['last_id'] ?? 0) + 1;

$report_num = str_pad($next_num, 4, '0', STR_PAD_LEFT);

$generated_ref = "JIG-" . date('Ymd') . "-" . $report_num;
?>

<style>
    .pr-items-grid {
        display: grid;
        grid-template-columns: 30px 1.5fr 1.8fr 1fr 1fr 70px 90px 120px 40px;
        gap: 10px;
        align-items: center;
    }

    .pr-input-style {
        width: 100%;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12px;
        box-sizing: border-box;
    }

    .pr-header-labels {
        padding: 10px;
        background: #f8fafc;
        border-bottom: 2px solid #cbd5e1;
        font-weight: bold;
        font-size: 11px;
        color: #475569;
        margin-bottom: 0;
        border-radius: 8px 8px 0 0;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }
</style>

<div id="prModal" class="modal-overlay">
    <div style="background:white; padding:25px; border-radius:12px; width:100%; max-width:1100px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow-y:auto; max-height:95vh;">
        
        <h3 id="modal_title" style="color:#072d7a; margin:0 0 20px 0; border-bottom:2px solid #edf2f7; padding-bottom:10px;">
            📝 Prepare Purchase Request
        </h3>

        <form id="exportForm" method="POST" action="../../prs_gen.php" target="_blank" onsubmit="return prepareSubmission();">
            <input type="hidden" name="form_mode" id="form_mode" value="create">
            <input type="hidden" name="ref_number" id="final_ref">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                
                <div style="grid-column: span 2;">
                    <label style="font-weight:600; font-size:13px;">Reference Number & Suffix</label>
                    <div style="display:flex; gap:10px; margin-top:5px;">
                        <input type="text" id="gen_ref" class="pr-input-style" style="background:#edf2f7; flex:2;" value="<?= $generated_ref ?>" readonly>
                        <input type="text" id="admin_suffix" name="admin_suffix" class="pr-input-style" placeholder="Suffix (e.g. URGENT)" style="flex:1;">
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
                </div>

                <div style="grid-column: span 2; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff;">
                    <div class="pr-items-grid pr-header-labels">
                        <div class="text-center">No.</div>
                        <div>Item Name</div>
                        <div>Description/Specs</div>
                        <div>Maker</div>
                        <div>UOM</div>
                        <div class="text-center">Qty</div>
                        <div>Unit Price</div>
                        <div class="text-right" style="padding-right:10px;">Subtotal</div>
                        <div></div>
                    </div>
                    
                    <div id="items_list_body" style="max-height:350px; overflow-y:auto; padding: 10px;">
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

                <div><label style="font-weight:600; font-size:13px;">RM / FG</label><input list="RMFG_options" name="rm_fg" id="modal_rmfg" class="pr-input-style" required></div>
                <div><label style="font-weight:600; font-size:13px;">Type</label><input list="ToR_options" name="ToR" id="modal_tor" class="pr-input-style" required></div>
                <div>
                        <label style="font-weight:600; font-size:13px;">Total Amount (<span id="currency_label">PHP</span>)</label>
                        <input type="number" step="0.01" id="pr_total" name="total_amount" class="pr-input-style" readonly 
                            style="background:#f1f5f9; font-weight:700; color:#072d7a;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="font-weight:600; font-size:13px;">Remarks</label>
                    <textarea name="remarks" id="modal_remarks" class="pr-input-style" style="height:45px; resize:none;" placeholder="Notes..."></textarea>
                </div>
                
                <div style="grid-column: span 2; display:flex; justify-content:flex-end; gap:12px; margin-top:10px; border-top:1px solid #edf2f7; padding-top:15px;">
                    <button type="button" onclick="closePRModal()" style="background:#fff; border:1px solid #e2e8f0; padding:10px 20px; border-radius:8px; cursor:pointer;">Cancel</button>
                    <button type="submit" name="bulk_resolve" style="background:#072d7a; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">
                        Save & Download
                    </button>
                </div>
            </div>

            <datalist id="company_options"><option value="Ms Carla"><option value="Mr Mark Agno"></datalist>
            <datalist id="uom_options"><option value="PC/s"><option value="Sheet"><option value="Roll"></datalist>
            <datalist id="RMFG_options"><option value="✔"><option value="✖"></datalist>
            <datalist id="ToR_options"><option value="Machinery"><option value="Supplies"></datalist>
            <datalist id="maker_options"><option value="Samsung"><option value="TDK"><option value="Local"></datalist>
        </form>
    </div>
</div>