<?php
$pageTitle = 'Minu laenutatud raamatud';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Nõua sisselogimist
require_login();

// Loo raamatu mudeli eksemplar
$bookModel = new Book($pdo);

// Võta kasutaja laenutatud raamatud
$loans = $bookModel->getUserLoans($_SESSION['user_id']);
?>

<div class="container">
    <h1 class="mb-4">Minu laenutatud raamatud</h1>
    
    <?php if (empty($loans)): ?>
    <div class="alert alert-info">
        <p>Te pole veel ühtegi raamatut laenutanud.</p>
        <a href="books.php" class="btn btn-primary mt-2">Sirvi raamatuid</a>
    </div>
    <?php else: ?>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Pealkiri</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Laenutuse kuupäev</th>
                            <th>Tagastuse tähtaeg</th>
                            <th>Staatus</th>
                            <th>Tegevused</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($loan['title']); ?></td>
                            <td><?php echo htmlspecialchars($loan['author']); ?></td>
                            <td><?php echo htmlspecialchars($loan['isbn'] ?? 'N/A'); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($loan['borrowed_date'])); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($loan['due_date'])); ?></td>
                            <td>
                                <?php 
                                $status_class = 'bg-primary';
                                $status_text = 'Laenutatud';
                                
                                if ($loan['status'] === 'returned') {
                                    $status_class = 'bg-success';
                                    $status_text = 'Tagastatud';
                                } elseif ($loan['status'] === 'overdue' || strtotime($loan['due_date']) < time()) {
                                    $status_class = 'bg-danger';
                                    $status_text = 'Tähtaeg ületatud';
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_book.php?id=<?php echo $loan['book_id']; ?>" class="btn btn-sm btn-outline-primary">Vaata</a>
                                
                                <?php if ($loan['status'] === 'borrowed'): ?>
                                <form action="view_book.php?id=<?php echo $loan['book_id']; ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Tagasta</button>
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
            <i class="fas fa-arrow-left me-1"></i> Tagasi töölauaale
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>