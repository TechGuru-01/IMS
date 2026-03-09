<?php
 require_once '../../auth_checker.php';
?>



<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../../style.css" /> 
    
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="dashBoard.css" />
    <link rel="stylesheet" href="../../component/dollarBox/dollar.css">
    <link rel="stylesheet" href="../../component/inventoryAlertBox/inventoryAlerts.css">
     <link rel="stylesheet" href="../../component/graphBox/graph.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const allChartData = <?php echo json_encode($chartData); ?>;
        const currentView = "<?php echo $view; ?>";
    </script>

    <script src="../../component/graphBox/chart.js"></script>
    </body>
</html>