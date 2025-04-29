<?php
require_once 'includes/session.php';

// Log out the user
logout();

// Set flash message
set_flash_message('success', 'You have been successfully logged out.');

// Redirect to home page
header("Location: index.php");
exit;