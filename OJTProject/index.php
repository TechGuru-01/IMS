<?php
require 'config.php';


$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory");
$totalRow = $totalQuery->fetch_assoc();
$pesoTotal = (float)($totalRow['grand_total'] ?? 0);

$exchangeRate = 57.74;
$dollarTotal = $pesoTotal / $exchangeRate;


$yenTotal = $pesoTotal / 0.37; 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="nav-bar.css" />
    
    <script src="app.js"></script>
  </head>
  <body>
    <script src="nav-bar.js"></script>
    <div id="navbar-container"></div>
    <section class="main">
      <div class="box">
        <div class="box-content box-1">
          <div class="box-content" id="box-dollar">
            <div class="inner-box"> <h1><span style="color: black;">$<?= number_format($dollarTotal, 2) ?></span></h1></div>
            
          </div>
          <div class="box-content" id="box-yen">
            <div class="inner-box">
              <div class="inner-box"> <h1><span style="color: black;">¥<?= number_format($yenTotal, 2) ?></span></h1></div>
            </div>
          </div>
        </div>

        <div class="box-content box-3" id="box-history"><div class="inner-box"></div></div>

        <div class="box-content box-4" id="box-graph"><div class="inner-box"></div></div>
      </div>
    </section>
    
  </body>
</html>
