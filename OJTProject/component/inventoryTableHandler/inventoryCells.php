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
    // 1. PENDING: Dapat 0 ang flag AT below minimum ang stock.
    // Ito ang tunay na alert state.
    if ($isAck === 0 && $qty <= $minQty) {
        echo "<span class='status-badge pending'>Pending</span>";
    } 
    
    // 2. ACKNOWLEDGED: Pinindot ang DONE pero mababa pa rin ang stock.
    elseif ($isAck === 1 && $qty <= $minQty) {
        echo "<span class='status-badge acknowledged'>Acknowledged</span>";
    } 
    
    // 3. HEALTHY: Ito ang magiging status ng bagong data.
    // Magiging healthy siya kung:
    // - Mataas ang stock ($qty > $minQty) kahit 0 o 1 ang flag.
    else {
        echo "<span class='status-badge healthy'>Healthy</span>";
    }
}
else {
    echo htmlspecialchars($row[$col] ?? '');
}
?>