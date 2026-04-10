<?php require_once '../../include/staffProfileFunction.php'?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile | IMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../../component/navbar/nav-bar.css">
    <link rel="stylesheet" href="../../component/utils/utils.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
</head>
<body>

<?php include '../../component/navbar/nav-bar.php'; ?>

<div class="profile-container">
    <div class="profile-main-wrapper">
        
        <?php include "../../component/profileCard/adminProfileCard.php"?>

        <div class="content-column">
            <?php include "../../component/RecentTransaction/myPerformance.php"?>
            <?php include "../../component/RecentTransaction/myRecentActivityLogs.php"?>
        </div> 
    </div>
</div>
<?php include "../../component/utils/utils.php"; ?>
<script src="../../component/utils/utils.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="profile.js"></script>

</body>
</html>