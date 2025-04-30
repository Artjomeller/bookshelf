<?php
$pageTitle = 'Töölaud';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';
require_once 'models/User.php';

// Nõua sisselogimist
require_login();

// Loo mudeli eksemplarid
$bookModel = new Book($pdo);
$userModel = new User($pdo);

// Võta kasutaja andmed
$user = $userModel->getById($_SESSION['user_id']);

// Võta kasutaja laenutatud raamatud
$loans = $bookModel->getUserLoans($_SESSION['user_id']);
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Kasutaja profiil</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted">
                        <?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Nimi puudub'; ?>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-calendar me-2"></i>Liitunud: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
                    </p>
                    <a href="profile.php" class="btn btn-outline-primary">Muuda profiili</a>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Kiirlingid</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="books.php" class="text-decoration-none">
                                <i class="fas fa-book me-2"></i>Sirvi raamatuid
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="my_books.php" class="text-decoration-none">
                                <i class="fas fa-book-reader me-2"></i>Minu laenutused
                            </a>
                        </li>
                        <?php if (is_admin()): ?>
                        <li class="list-group-item">
                            <a href="add_book.php" class="text-decoration-none">
                                <i class="fas fa-plus me-2"></i>Lisa uus raamat
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item">
                            <a href="logout.php" class="text-decoration-none text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logi välja
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Töölaud</h5>
                </div>
                <div class="card-body">
                    <h3>Tere, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                    <p>See on teie isiklik töölaud, kus saate hallata oma raamatute laenutusi ja konto seadeid.</p>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Teie laenutatud raamatud</h5>
                    <a href="my_books.php" class="btn btn-sm btn-light">Vaata kõiki</a>
                </div>
                <div class="card-body">
                    <?php if (empty($loans)): ?>
                    <div class="alert alert-info">
                        Te pole veel ühtegi raamatut laenutanud. <a href="books.php">Sirvige raamatuid</a>, et leida midagi lugemiseks!
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pealkiri</th>
                                    <th>Autor</th>
                                    <th>Laenutuse kuupäev</th>
                                    <th>Tagastuse tähtaeg</th>
                                    <th>Staatus</th>
                                    <th>Tegevused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Kuva ainult 5 kõige hilisemat laenutust
                                $recent_loans = array_slice($loans, 0, 5);
                                foreach ($recent_loans as $loan): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($loan['title']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['author']); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($loan['borrowed_date'])); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($loan['due_date'])); ?></td>
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
                                                echo 'Tähtaeg ületatud';
                                            } elseif ($loan['status'] === 'borrowed') {
                                                echo 'Laenutatud';
                                            } elseif ($loan['status'] === 'returned') {
                                                echo 'Tagastatud';
                                            } elseif ($loan['status'] === 'overdue') {
                                                echo 'Tähtaeg ületatud';
                                            }
                                            ?>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>