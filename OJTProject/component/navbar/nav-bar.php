<?php
$raw_pos = $_SESSION['position'] ?? 'NOT_SET';
echo "";

$current_position = strtoupper(trim($raw_pos)); 
$isAdmin = ($current_position === 'ADMIN' || $current_position === 'SUPER ADMIN');
$profile_link = $isAdmin 
                ? "/OJTProject/pages/profile/adminProfile.php"
                : "/OJTProject/pages/profile/staffProfile.php";


?>

<header>
    <nav class="nav-bar">
        <div class="logo" style= background:white; border-radius=20px; >
            <a href="../../pages/dashBoard/dashBoard.php">
            <img src="../../src/unnamed-2.png" alt="logo">
            </a>
        </div>
        
        <div class="hamburger mobile-only" id="hamburger"> 
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>

       <div class="menu-links" id="menu-links">
    <ul>

        
        <li>
            <div class="cell-links">
                <a href="../../pages/dashBoard/dashBoard.php">
                <span class="material-symbols-outlined mobile-only">home</span>Dashboard
                </a>
            </div>
        </li>


        <li class="mobile-only">
             <div class="cell-links">
                <a href="<?php echo $profile_link; ?>">
                <span class="material-symbols-outlined">account_circle</span>Manage Profile
                </a>
            </div>
        </li>
        
        <?php if ($isAdmin): ?>
        <li class="mobile-only">
            <div class="cell-links">
                <a href="../../pages/addUser/addUser.php">
                <span class="material-symbols-outlined mobile-only">assignment_add</span>Manage Pull Out Form
                </a>
            </div>
        </li>
        <?php endif; ?>
        
        <li>
            <div class="cell-links">
                <a href="../../pages/inventory/inventory.php">
                <span class="material-symbols-outlined mobile-only">inventory_2</span>Inventory
                </a>
            </div>
        </li>
       
       
        <li>
            <div class="cell-links">
                <a href="../../pages/history/history.php">
                <span class="material-symbols-outlined mobile-only">history</span>History</a>
            </div>
        </li>
      
        <li class="mobile-only"> 
            <div class="cell-links">
                <a href="../../pages/PRS/prsStatus.php">
                <span class="material-symbols-outlined mobile-only">list_alt_check</span>View PRS Status</a> 
            </div>
        </li>
       
        <div class="cell-links mobile-only">
            <a href="../../component/utils/logout.php">
            <span class="material-symbols-rounded mobile-only">logout</span>Logout
            </a>
        </div>

    </ul>
</div>

    </nav>
</header>

<script>
const hamburger = document.getElementById('hamburger');
const menuLinks = document.getElementById('menu-links');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    menuLinks.classList.toggle('active');
});
</script>