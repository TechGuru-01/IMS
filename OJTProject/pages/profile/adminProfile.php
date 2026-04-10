<?php require_once "../../include/adminProfileFunction.php"?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | HEPC JIG IMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="./profile.css">
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
             <?php include "../../component/userAcountManagement/userAccountManagement.php"?>
             <?php include "../../component/RecentTransaction/myRecentTransaction.php"?>
            
            <?php include "../../component/recentAdminView/recentAdminView.php"?>   
        </div> 
    </div>
</div>
<?php include "../../component/profileCard/profileModal.php"?>

<script src="profile.js"></script>
<div id="adminViewModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 380px;">
        <div class="modal-header">
            <h3>Manage Credentials: <span id="target-user-display" style="color: var(--primary-blue-solid);"></span></h3>
            <span class="close-modal" onclick="closeAdminViewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                Note: Old passwords are encrypted. Setting a new one is the only way to reset access.
            </p>

            <div class="form-group" style="position: relative;">
                <label>Set New Password</label>
                <input type="password" id="new-pass-input" class="form-control" placeholder="Enter new password">
                <i class="fas fa-eye-slash" onclick="togglePasswordVisibility('new-pass-input', this)" 
                style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: #666;"></i>
            </div>

            <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px; border: 1px solid #eee;">
                <p style="font-size: 11px; color: #555; margin: 0;">
                    <strong>Requirements:</strong> 8+ chars, Upper & Lower case, Number, and Special Char ($#@!?).
                </p>
            </div>

            <input type="hidden" id="target-id-input">
            
            <button class="btn-save" onclick="processAdminUpdate()" style="width: 100%; margin-top: 20px;">
                <i class="fas fa-sync-alt"></i> Update & Save Credentials
            </button>
        </div>
    </div>
</div>
<?php include "../../component/utils/utils.php"?>
<script src="../../component/utils/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>