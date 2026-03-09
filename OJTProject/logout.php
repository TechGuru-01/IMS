<?php
session_start();
session_destroy(); // Burahin lahat ng session data
header("Location: index.php");
exit();
?>