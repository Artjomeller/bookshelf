<?php
$pageTitle = 'Dashboard';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';
require_once 'models/User.php';

// Require login
require_login();

// Create model instances
$bookModel = new Book($pdo);
$userModel = new User($pdo);

// Get user data
$user = $userModel->getById($_SESSION['user_id']);

// Get user's borrowed books
$loans = $bookModel->getUserLoans($_SESSION['user_id']);
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">User Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted">
                        <?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'No name set'; ?>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-calendar me-2"></i>Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                    </p>
                    <a href="profile.php" class="btn btn-outline-primary">Edit Profile</a>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Quick Links</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="books.php" class="text-decoration-none">
                                <i class="fas fa-book me-2"></i>Browse Books
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="my_books.php" class="text-decoration-none">
                                <i class="fas fa-book-reader me-2"></i>My Borrowed Books
                            </a>
                        </li>
                        <?php if (is_admin()): ?>
                        <li class="list-group-item">
                            <a href="add_book.php" class="text-decoration-none">
                                <i class="fas fa-plus me-2"></i>Add New Book
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item">
                            <a href="logout.php" class="text-decoration-none text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Dashboard</h5>
                </div>
                <div class="card-body">
                    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                    <p>This is your personal dashboard where you can manage your book loans and account settings.</p>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Your Borrowed Books</h5>
                    <a href="my_books.php" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($loans)): ?>
                    <div class="alert alert-info">
                        You have not borrowed any books yet. <a href="books.php">Browse books</a> to find something to read!
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Borrowed Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Display only the 5 most recent loans
                                $recent_loans = array_slice($loans, 0, 5);
                                foreach ($recent_loans as $loan): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($loan['title']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['author']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($loan['borrowed_date'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($loan['due_date'])); ?></td>
                                    <td>
                                        <?php 
                                        $status_class = 'bg-primary';
                                        if ($loan['status'] === 'returned') {
                                            $status_class = 'bg-success';
                                        } elseif ($loan['status'] === 'overdue' || strtotime($loan['due_date']) < time()) {
                                            $status_class = 'bg-danger';
                                        }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>">
                                            <?php 
                                            if ($loan['status'] === 'borrowed' && strtotime($loan['due_date']) < time()) {
                                                echo 'Overdue';
                                            } else {
                                                echo ucfirst($loan['status']); 
                                            }
                                            ?>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>