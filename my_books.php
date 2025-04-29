<?php
$pageTitle = 'My Borrowed Books';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Require login
require_login();

// Create book model instance
$bookModel = new Book($pdo);

// Get user's borrowed books
$loans = $bookModel->getUserLoans($_SESSION['user_id']);
?>

<div class="container">
    <h1 class="mb-4">My Borrowed Books</h1>
    
    <?php if (empty($loans)): ?>
    <div class="alert alert-info">
        <p>You have not borrowed any books yet.</p>
        <a href="books.php" class="btn btn-primary mt-2">Browse Books</a>
    </div>
    <?php else: ?>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Borrowed Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($loan['title']); ?></td>
                            <td><?php echo htmlspecialchars($loan['author']); ?></td>
                            <td><?php echo htmlspecialchars($loan['isbn'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M j, Y', strtotime($loan['borrowed_date'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($loan['due_date'])); ?></td>
                            <td>
                                <?php 
                                $status_class = 'bg-primary';
                                $status_text = ucfirst($loan['status']);
                                
                                if ($loan['status'] === 'returned') {
                                    $status_class = 'bg-success';
                                } elseif ($loan['status'] === 'overdue' || strtotime($loan['due_date']) < time()) {
                                    $status_class = 'bg-danger';
                                    if ($loan['status'] !== 'overdue') {
                                        $status_text = 'Overdue';
                                    }
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_book.php?id=<?php echo $loan['book_id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                
                                <?php if ($loan['status'] === 'borrowed'): ?>
                                <form action="view_book.php?id=<?php echo $loan['book_id']; ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Return</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php endif; ?>
    
    <div class="mt-4 mb-4">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>