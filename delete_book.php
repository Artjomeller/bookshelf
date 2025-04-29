<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'models/Book.php';

// Require admin privileges
require_admin();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Create book model instance
    $bookModel = new Book($pdo);
    
    // Delete the book
    $result = $bookModel->delete($book_id);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Redirect to books page
    header("Location: books.php");
    exit;
} else {
    // If not POST request, redirect to books page
    set_flash_message('danger', 'Invalid request');
    header("Location: books.php");
    exit;
}