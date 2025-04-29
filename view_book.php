<?php
$pageTitle = 'Book Details';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

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

// Process borrow form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'borrow') {
    // Check if user is logged in
    if (!is_logged_in()) {
        // Store current URL for redirect after login
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        
        set_flash_message('info', 'Please login to borrow books');
        header("Location: login.php");
        exit;
    }
    
    // Calculate due date (2 weeks from now)
    $due_date = date('Y-m-d H:i:s', strtotime('+2 weeks'));
    
    // Borrow the book
    $result = $bookModel->borrow($book_id, $_SESSION['user_id'], $due_date);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Redirect to refresh page
    header("Location: view_book.php?id=$book_id");
    exit;
}

// Process return form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return') {
    // Get loan ID
    $loan_id = $_POST['loan_id'] ?? 0;
    
    // Return the book
    $result = $bookModel->returnBook($loan_id);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Redirect to refresh page
    header("Location: view_book.php?id=$book_id");
    exit;
}

// Get active loan if book is borrowed
$active_loan = null;
if (!$book['available']) {
    $active_loan = $bookModel->getActiveLoan($book_id);
}

// Update page title
$pageTitle = $book['title'] . ' - Book Details';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="books.php">Books</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($book['title']); ?></li>
                </ol>
            </nav>
            
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h1>
                    <h5 class="card-subtitle mb-3 text-muted">By <?php echo htmlspecialchars($book['author']); ?></h5>
                    
                    <div class="mb-4">
                        <span class="badge <?php echo $book['available'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                            <?php echo $book['available'] ? 'Available' : 'Borrowed'; ?>
                        </span>
                        
                        <?php if ($book['publication_year']): ?>
                        <span class="badge bg-secondary p-2 ms-2">Published: <?php echo $book['publication_year']; ?></span>
                        <?php endif; ?>
                        
                        <?php if ($book['isbn']): ?>
                        <span class="badge bg-secondary p-2 ms-2">ISBN: <?php echo htmlspecialchars($book['isbn']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($book['description']): ?>
                    <div class="card-text mb-4">
                        <h5>Description</h5>
                        <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-text text-muted small mb-4">
                        <p>
                            Added by: <?php echo htmlspecialchars($book['added_by_username']); ?><br>
                            Added on: <?php echo date('F j, Y', strtotime($book['created_at'])); ?>
                        </p>
                    </div>
                    
                    <?php if (is_logged_in()): ?>
                        <?php if ($book['available']): ?>
                            <form action="view_book.php?id=<?php echo $book_id; ?>" method="POST">
                                <input type="hidden" name="action" value="borrow">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-book me-2"></i>Borrow this Book
                                </button>
                            </form>
                        <?php else: ?>
                            <?php if ($active_loan && $active_loan['user_id'] == $_SESSION['user_id']): ?>
                                <div class="alert alert-info">
                                    <p>You have borrowed this book. Due date: <?php echo date('F j, Y', strtotime($active_loan['due_date'])); ?></p>
                                    <form action="view_book.php?id=<?php echo $book_id; ?>" method="POST">
                                        <input type="hidden" name="action" value="return">
                                        <input type="hidden" name="loan_id" value="<?php echo $active_loan['id']; ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-undo me-2"></i>Return this Book
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <p>This book is currently borrowed by another user.</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p><a href="login.php">Login</a> to borrow this book.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (is_admin()): ?>
                    <div class="mt-4">
                        <a href="edit_book.php?id=<?php echo $book_id; ?>" class="btn btn-outline-primary me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-1"></i>Delete
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mt-3 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Book Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Title:</span>
                            <span class="text-end"><?php echo htmlspecialchars($book['title']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Author:</span>
                            <span class="text-end"><?php echo htmlspecialchars($book['author']); ?></span>
                        </li>
                        <?php if ($book['publication_year']): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Publication Year:</span>
                            <span class="text-end"><?php echo $book['publication_year']; ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($book['isbn']): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>ISBN:</span>
                            <span class="text-end"><?php echo htmlspecialchars($book['isbn']); ?></span>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Status:</span>
                            <span class="badge <?php echo $book['available'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                                <?php echo $book['available'] ? 'Available' : 'Borrowed'; ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <?php if (!$book['available'] && $active_loan && is_admin()): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Loan Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Borrowed By:</span>
                            <span class="text-end"><?php echo htmlspecialchars($active_loan['username']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Borrowed Date:</span>
                            <span class="text-end"><?php echo date('F j, Y', strtotime($active_loan['borrowed_date'])); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Due Date:</span>
                            <span class="text-end"><?php echo date('F j, Y', strtotime($active_loan['due_date'])); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (is_admin()): ?>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the book "<?php echo htmlspecialchars($book['title']); ?>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="delete_book.php" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>