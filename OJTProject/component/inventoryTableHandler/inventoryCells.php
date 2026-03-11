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
elseif ($col === 'quantity') {
    if ($qty <= 0) {
        echo "<strong style='color: #000;'>0</strong> <span class='status-badge out-of-stock'>Out of Stock</span>";
    } elseif ($isCritical) {
        echo "<strong style='color: #e74c3c;'>" . $qty . "</strong>";
    } else {
        echo "<strong>" . $qty . "</strong>";
    }
}
elseif ($col === 'is_acknowledged') {
    if ($isAck === 1) {
        echo "<span class='status-badge acknowledged'>Acknowledged</span>";
    } elseif ($qty <= $minQty) {
        echo "<span class='status-badge pending'>Pending</span>";
    } else {
        echo "<span class='status-badge healthy'>Healthy</span>";
    }
}
else {
    echo htmlspecialchars($row[$col] ?? '');
}
?>