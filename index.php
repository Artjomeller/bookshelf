<?php
$pageTitle = 'Home';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Create book model instance
$bookModel = new Book($pdo);

// Get latest 5 books
$latest_books = $bookModel->getAll(5);
?>

<div class="jumbotron bg-light p-5 rounded">
    <div class="container">
        <h1 class="display-4">Welcome to BookShelf</h1>
        <p class="lead">Your digital library management system. Browse books, borrow them, and keep track of your reading journey.</p>
        
        <?php if (!is_logged_in()): ?>
        <hr class="my-4">
        <p>To borrow books and access more features, please login or create an account.</p>
        <a class="btn btn-primary btn-lg" href="login.php" role="button">Login</a>
        <a class="btn btn-outline-primary btn-lg" href="register.php" role="button">Register</a>
        <?php else: ?>
        <hr class="my-4">
        <p>Check out our collection of books or visit your dashboard.</p>
        <a class="btn btn-primary btn-lg" href="books.php" role="button">Browse Books</a>
        <a class="btn btn-outline-primary btn-lg" href="dashboard.php" role="button">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-5">
    <h2 class="mb-4">Latest Books</h2>
    
    <div class="row">
        <?php foreach ($latest_books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">By <?php echo htmlspecialchars($book['author']); ?></h6>
                    <p class="card-text">
                        <?php 
                        $desc = htmlspecialchars($book['description']);
                        echo strlen($desc) > 150 ? substr($desc, 0, 150) . '...' : $desc;
                        ?>
                    </p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Published: <?php echo htmlspecialchars($book['publication_year']); ?></small>
                    <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary float-end">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4 mb-5">
        <a href="books.php" class="btn btn-primary">View All Books</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>