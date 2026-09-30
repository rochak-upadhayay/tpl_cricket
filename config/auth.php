<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function admin_required() {
    if (empty($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
}
?>
