<?php
require_once 'config/config.php';

// Redirect to login if not authenticated
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Redirect to dashboard
redirect('/dashboard.php');
?>
