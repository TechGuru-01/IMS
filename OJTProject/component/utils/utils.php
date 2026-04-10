<?php
$raw_pos = $_SESSION['position'] ?? 'NOT_SET';
echo "";

$current_position = strtoupper(trim($raw_pos)); 
$isAdmin = ($current_position === 'ADMIN' || $current_position === 'SUPER ADMIN');
$profile_link = $isAdmin 
                ? "/OJTProject/pages/profile/adminProfile.php" 
                : "/OJTProject/pages/profile/staffProfile.php";
?>

<div class="ubuntu-fab-container">
    <div class="ubuntu-menu" id="ubuntuMenu">
        
        <a href="<?php echo $profile_link; ?>">
            <button class="ubuntu-item">
               <span class="material-symbols-outlined">account_circle</span>
               Manage Profile
            </button>
        </a>

        <a href="/OJTProject/pages/PRS/prsStatus.php">
            <button class="ubuntu-item">
                <span class="material-symbols-outlined">list_alt_check</span>
                View PRS Status
            </button>
        </a>

        <?php if ($isAdmin): ?>
        <a href="/OJTProject/pages/addUser/addUser.php">
            <button class="ubuntu-item">
                <span class="material-symbols-outlined">manage_accounts</span>
                Manage User
            </button>
        </a>
        <?php endif; ?>
        
        <button class="ubuntu-item" onclick="confirmLogout()">
            <span class="material-symbols-rounded">logout</span>
            Logout
        </button>
    </div>

    <button class="ubuntu-launcher" id="launcherBtn">
        <span class="material-symbols-rounded">grid_view</span>
    </button>
</div>