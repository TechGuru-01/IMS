<?php
include "inventoryFunction.php"; 

// I-set ang header para maging Excel file ang download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Inventory_Data_Only.xls");

echo '<table border="1">';
// Siguraduhin na ang pagkakasunod-sunod dito ay tugma sa columns ng Template mo
echo '<tr style="background-color: #eee;">';
echo '<th>Category</th><th>Item</th><th>Description</th><th>Cabinet</th><th>QTY</th><th>Price</th><th>Total</th>';
echo '</tr>';

while($row = $result->fetch_assoc()) {
    $qty = (float)($row['quantity'] ?? 0);
    $price = (float)($row['price'] ?? 0);
    $total = $qty * $price;

    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
    echo '<td>' . htmlspecialchars($row['item']) . '</td>';
    echo '<td>' . htmlspecialchars($row['description']) . '</td>';
    echo '<td>' . htmlspecialchars($row['cabinet']) . '</td>';
    echo '<td>' . $qty . '</td>';
    echo '<td>' . $price . '</td>'; // Raw number lang para hindi mag-error ang Excel formula
    echo '<td>' . $total . '</td>';
    echo '</tr>';
}
echo '</table>';
?>