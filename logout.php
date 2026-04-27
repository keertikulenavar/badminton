<?php
session_start();

// Determine where to redirect based on who was logged in
$redirect = "index.php";
if (isset($_SESSION['admin_id'])) {
    $redirect = "admin_login.php?logged_out=1";
} elseif (isset($_SESSION['user_id'])) {
    $redirect = "user_login.php";
}

session_unset();
session_destroy();
header("Location: " . $redirect);
exit();
?>
