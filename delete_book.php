<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'models/Book.php';

// Nõua administraatori õigusi
require_admin();

// Kontrolli, kas vorm on esitatud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Loo raamatu mudeli eksemplar
    $bookModel = new Book($pdo);
    
    // Kustuta raamat
    $result = $bookModel->delete($book_id);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Suuna raamatute lehele
    header("Location: books.php");
    exit;
} else {
    // Kui pole POST päring, suuna raamatute lehele
    set_flash_message('danger', 'Vigane päring');
    header("Location: books.php");
    exit;
}