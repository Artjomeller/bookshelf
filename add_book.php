<?php
$pageTitle = 'Add Book';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Require admin privileges
require_admin();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $publication_year = trim($_POST['publication_year'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    
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
    
    // If no validation errors, add the book
    if (empty($errors)) {
        $bookModel = new Book($pdo);
        $result = $bookModel->add($title, $author, $description, $publication_year, $isbn, $_SESSION['user_id']);
        
        if ($result['success']) {
            // Set success message
            set_flash_message('success', 'Book added successfully');
            
            // Redirect to books page
            header("Location: books.php");
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
                    <h3 class="card-title mb-0">Add New Book</h3>
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
                    
                    <form action="add_book.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author" name="author" 
                                   value="<?php echo htmlspecialchars($author ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="publication_year" class="form-label">Publication Year</label>
                                <input type="number" class="form-control" id="publication_year" name="publication_year" 
                                       value="<?php echo htmlspecialchars($publication_year ?? ''); ?>" min="1" max="<?php echo date('Y'); ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" 
                                       value="<?php echo htmlspecialchars($isbn ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="books.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Book</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>