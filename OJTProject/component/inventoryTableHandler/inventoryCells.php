<?php
if ($col === 'price') {
    echo "₱" . number_format($row[$col], 2);
} 
elseif ($col === 'beginning_inventory') {
    echo "<span style='color: #4a90e2; font-weight: 500;'>" . htmlspecialchars($row[$col] ?? '0') . "</span>";
} 
elseif ($col === 'received_qty') {
    echo "<span style='color: #27ae60;'>+" . htmlspecialchars($row[$col] ?? '0') . "</span>";
} 
elseif ($col === 'is_acknowledged') {
    if ($isAck === 0 && $qty <= $minQty) {
        echo "<span class='status-badge pending'>Pending</span>";
    } 
    elseif ($isAck === 1 && $qty <= $minQty) {
        echo "<span class='status-badge acknowledged'>Acknowledged</span>";
    } 
    else {
        echo "<span class='status-badge healthy'>Healthy</span>";
    }
}
else {
    echo htmlspecialchars($row[$col] ?? '');
}
?>