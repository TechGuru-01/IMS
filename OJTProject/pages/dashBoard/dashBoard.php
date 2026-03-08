<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../../style.css" /> 
    
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="dashBoard.css" />
    <link rel="stylesheet" href="../../component/dollarBox/dollar.css">
</head>
<body>
      <?php
            include "../../component/navbar/nav-bar.php"
        ?>
    <section class="main">
        <div class="box">
            <div class="box-1">
                <?php include __DIR__ . "/../../component/dollarBox/dollar.php"; ?>
               
            </div>
            <?php include __DIR__ . "/../../component/inventoryAlertBox/inventoryAlerts.php"; ?>
            <?php include __DIR__ . "/../../component/graphBox/graph.php"; ?>
        </div>
    </section>
</body>
</html>