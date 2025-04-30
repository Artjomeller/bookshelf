<?php
$pageTitle = 'Raamatud';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Nõua sisselogimist selle lehe vaatamiseks
require_login();

// Loo raamatu mudeli eksemplar
$bookModel = new Book($pdo);

// Võta kõik raamatud
$books = $bookModel->getAll();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Raamatute kogu</h1>
        
        <?php if (is_admin()): ?>
        <a href="add_book.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Lisa uus raamat
        </a>
        <?php endif; ?>
    </div>
    
    <?php if (empty($books)): ?>
    <div class="alert alert-info">
        Hetkel pole raamatuid saadaval.
    </div>
    <?php else: ?>
    
    <div class="row">
        <?php foreach ($books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Autor: <?php echo htmlspecialchars($book['author']); ?></h6>
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
                            <?php echo $book['available'] ? 'Saadaval' : 'Laenutatud'; ?>
                        </span>
                        <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">Vaata detaile</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>