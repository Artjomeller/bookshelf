<?php
$pageTitle = 'Edit Book';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Require admin privileges
require_admin();

// Get book ID from URL
$book_id = $_GET['id'] ?? 0;

// Create book model instance
$bookModel = new Book($pdo);

// Get book details
$book = $bookModel->getById($book_id);

// If book not found, redirect to books page
if (!$book) {
    set_flash_message('danger', 'Book not found');
    header("Location: books.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $publication_year = trim($_POST['publication_year'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $available = isset($_POST['available']) ? 1 : 0;
    
    // Validate input
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if (empty($author)) {
        $errors[] = "Author is required";
    }
    
    if (!empty($publication_year) && (!is_numeric($publication_year) || $publication_year < 0 || $publication_year > date('Y'))) {
        $errors[] = "Publication year must be a valid year not in the future";
    }
    
    // If no validation errors, update the book
    if (empty($errors)) {
        $result = $bookModel->update($book_id, [
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'publication_year' => $publication_year,
            'isbn' => $isbn,
            'available' => $available
        ]);
        
        if ($result['success']) {
            // Set success message
            set_flash_message('success', $result['message']);
            
            // Redirect to book details page
            header("Location: view_book.php?id=$book_id");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mt-4 mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Edit Book</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form action="edit_book.php?id=<?php echo $book_id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($book['title']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author" name="author" 
                                   value="<?php echo htmlspecialchars($book['author']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($book['description']); ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="publication_year" class="form-label">Publication Year</label>
                                <input type="number" class="form-control" id="publication_year" name="publication_year" 
                                       value="<?php echo htmlspecialchars($book['publication_year']); ?>" min="1" max="<?php echo date('Y'); ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" 
                                       value="<?php echo htmlspecialchars($book['isbn']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="available" name="available" 
                                   <?php echo $book['available'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="available">Available for borrowing</label>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="view_book.php?id=<?php echo $book_id; ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Book</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>