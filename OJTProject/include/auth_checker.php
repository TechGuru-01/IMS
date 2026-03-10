<?php
// Siguraduhin na may session na nagaganap
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/OJTProject/index.php");
    exit(); // Napaka-importante nito para tumigil ang server sa pag-load ng page
}


$timeout = 3600; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/OJTProject/index.php");
    exit();
}
$_SESSION['last_activity'] = time(); // I-refresh ang activity timestamp
?>