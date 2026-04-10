
<?php include "../../include/signupResponseHandler.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Sign Up</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="signup.css">
    
</head>
<body>

<div class="login-container"> 
    <div class="login-wrapper">
        <div class="brand-side hide-on-mobile">
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div style="background: white; width: 300px; height: 300px; border-radius: 30px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; padding: 10px;">

                    <img src="../../src/unnamed-2.png" alt="HEPC Logo" style="width: 80%; height: auto;">
                </div>
                <p style="font-size: 1.2rem; font-weight: 500; text-align: center;">Inventory Management System</p>
            </div>
            
            <footer style="width: 100%; text-align: center; color: rgba(255,255,255,0.6); font-size: 0.75rem;">
                <p style="margin: 0; font-size: 10px;">&copy; <?= date("Y"); ?> JIG IMS</p>
                <p style="margin-top: 2px; font-size: 10px;">Developed by Elijah Boon & Chaz Honrada</p>
            </footer>
        </div>

        <div class="form-side">
            <div class="form-header">
                 <div class="logo-box">
                    <img src="../../src/unnamed-2.png" alt="logo" class="mobile-logo">
                </div>

                <div class="header-text">
                    <h2>Sign Up</h2>
                    <p style="color: #999; font-size: 0.9rem;">Create your account</p>
                </div>
            </div>

            <form action="" method="POST">

                <div class="input-box">
                    <span class="material-symbols-outlined">badge</span>
                    <input type="text" name="full_name" placeholder="Full Name" >
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">business_center</span>
                    <input type="text" name="position" placeholder="Position" >
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">person</span>
                    <input type="text" name="username" placeholder="Username" >
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" name="password" placeholder="Password" >
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">lock_reset</span>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" >
                </div>

                <button type="submit" class="login-btn">
                    SIGN UP
                    <span class="material-symbols-outlined">double_arrow</span>
                </button>
            </form> 
            
            <p style="text-align:center; margin-top:15px; font-size:0.85rem; color:#666;">
                Already have an account? <a href="../../index.php" style="color:#d32f2f; text-decoration:none; font-weight:bold;">Login</a>
            </p>
        </div>
    </div>
</div>

<script src="../../icons/sweetalert2.all.min.js"></script>

<?php if (!empty($status)): ?>
<script>
    window.phpStatus = <?= json_encode($status) ?>;
    window.phpMsg = <?= json_encode($msg_text) ?>;
</script>
<?php endif; ?>

<script src="signupResponseHandler.js"></script>
</body>
</html>
