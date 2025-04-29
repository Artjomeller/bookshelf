<?php
$pageTitle = 'Search Results';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Get search query
$search_query = trim($_GET['q'] ?? '');

// Create book model instance
$bookModel = new Book($pdo);

// If search query provided, search books
$books = [];
if (!empty($search_query)) {
    $books = $bookModel->search($search_query);
}

?>

<div class="container">
    <h1 class="mb-4">Search Results</h1>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="search.php" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Search for books..." 
                           value="<?php echo htmlspecialchars($search_query); ?>" aria-label="Search">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
            
            <?php if (empty($search_query)): ?>
            <div class="alert alert-info">
                Please enter a search term to find books.
            </div>
            <?php elseif (empty($books)): ?>
            <div class="alert alert-warning">
                No books found matching "<?php echo htmlspecialchars($search_query); ?>".
            </div>
            <?php else: ?>
            <p>Found <?php echo count($books); ?> books matching "<?php echo htmlspecialchars($search_query); ?>":</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($books)): ?>
    <div class="row">
        <?php foreach ($books as $book): ?>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge <?php echo $book['available'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $book['available'] ? 'Available' : 'Borrowed'; ?>
                        </span>
                        <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="mt-4 mb-4">
        <a href="books.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Books
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>