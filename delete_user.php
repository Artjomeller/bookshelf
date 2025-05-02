<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'models/User.php';

// Nõua administraatori õigusi
require_admin();

// Kontrolli, kas vorm on esitatud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Kontrolli, et kasutaja ei kustutaks iseennast
    if ($user_id == $_SESSION['user_id']) {
        set_flash_message('danger', 'Te ei saa kustutada iseennast!');
        header("Location: users.php");
        exit;
    }
    
    // Loo kasutaja mudeli eksemplar
    $userModel = new User($pdo);
    
    // Kustuta kasutaja
    $result = $userModel->deleteUser($user_id);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Suuna kasutajate lehele
    header("Location: users.php");
    exit;
} else {
    // Kui pole POST päring, suuna kasutajate lehele
    set_flash_message('danger', 'Vigane päring');
    header("Location: users.php");
    exit;
}