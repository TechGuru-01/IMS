<?php
$usd_rate = 1; 
$jpy_rate = 1;
$res_settings = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('dollar_rate', 'yen_rate')");

while($row = $res_settings->fetch_assoc()) {
    if ($row['setting_key'] == 'dollar_rate') $usd_rate = $row['setting_value'];
    if ($row['setting_key'] == 'yen_rate') $jpy_rate = $row['setting_value'];
}

date_default_timezone_set('Asia/Manila'); 

$current_month = date('Y-m');
$res_count = $conn->query("SELECT COUNT(*) as total_this_month FROM pr_reports WHERE date_created LIKE '$current_month%'");
$row_count = $res_count->fetch_assoc();
$next_num = ($row_count['total_this_month'] ?? 0) + 1;
$report_num = str_pad($next_num, 4, '0', STR_PAD_LEFT);
$generated_ref = "JIG-" . date('Ymd') . "-" . $report_num;
?>