<?php
// qr_generator.php
include "../../include/config.php"; 

// SQL Query: Sinigurado nating 'item' at 'description' ang kinukuha
$sql = "SELECT id, item, description FROM inventory ORDER BY id DESC";
$result = $conn->query($sql);

$inventory_data = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $inventory_data[] = [
            'id' => $row['id'],
            'item' => $row['item'], // Key is 'item'
            'desc' => $row['description']
        ];
    }
}
$conn->close();

$json_data = json_encode($inventory_data);
?>

<!DOCTYPE html>
<html>
<head>
    <title>HEPC JIG IMS | Inventory QR Generator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
   <style>
    body { font-family: sans-serif; text-align: center; background: #f9f9f9; }
    
    .grid { 
        display: flex; 
        flex-wrap: wrap; 
        justify-content: center; 
        gap: 10px; /* Para malinis ang pagitan */
    }

    .qr-card {
        background: #fff;
        border: 2px solid #333;
        padding: 20px; /* Dinagdagan ko ng konti para hindi dikit sa border */
        margin: 10px;
        border-radius: 10px;
        width: 200px; 
        
        /* ETO YUNG FIX: Flexbox para sa alignment */
        display: flex;
        flex-direction: column; /* Para pababa ang ayos */
        align-items: center;    /* Gitna horizontal */
        justify-content: center; /* Gitna vertical */
    }

    /* Siguraduhin na ang loob ng QR div ay laging nasa gitna */
    .qr-card div canvas, .qr-card div img {
        margin: 0 auto;
        display: block;
    }

    .name-label { 
        margin-top: 15px; 
        font-weight: bold; 
        font-size: 1.1em; 
        text-transform: uppercase;
        width: 100%; /* Para hindi mag-shrink */
        text-align: center;
    }

    .desc-label { 
        font-size: 0.9em; 
        color: #666; 
        width: 100%;
        text-align: center;
        margin-top: 5px;
    }
</style>
</head>
<body>

    <h1>Inventory QR Codes</h1>
    <button onclick="window.print()" style="padding: 10px 20px; cursor:pointer;">Print All QR Codes</button>
    
    <div id="qr-container" class="grid"></div>

    <script>
        const data = <?php echo $json_data; ?>;
        const container = document.getElementById('qr-container');

        data.forEach(itemRecord => {
            let card = document.createElement('div');
            card.className = 'qr-card';
            
            let qrDiv = document.createElement('div');
            
            // FIX: Changed item.name to itemRecord.item to match the PHP array key
            let nameLabel = document.createElement('div');
            nameLabel.className = 'name-label';
            nameLabel.innerText = itemRecord.item; 

            let descLabel = document.createElement('div');
            descLabel.className = 'desc-label';
            descLabel.innerText = itemRecord.desc;

            card.appendChild(qrDiv);
            card.appendChild(nameLabel);
            card.appendChild(descLabel);
            container.appendChild(card);

            let finalLink = "http://192.168.10.178/user_ims/index.php?id=" + itemRecord.id;

            new QRCode(qrDiv, {
                text: finalLink,
                width: 160,
                height: 160,
                correctLevel : QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>