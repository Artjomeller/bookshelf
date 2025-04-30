<?php
$pageTitle = 'Raamatu detailid';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Võta raamatu ID URL-ist
$book_id = $_GET['id'] ?? 0;

// Loo raamatu mudeli eksemplar
$bookModel = new Book($pdo);

// Võta raamatu andmed
$book = $bookModel->getById($book_id);

// Kui raamatut ei leitud, suuna raamatute lehele
if (!$book) {
    set_flash_message('danger', 'Raamatut ei leitud');
    header("Location: books.php");
    exit;
}

// Töötleme laenutusvormi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'borrow') {
    // Kontrolli, kas kasutaja on sisse logitud
    if (!is_logged_in()) {
        // Salvesta praegune URL sessioonile suunamiseks pärast sisselogimist
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        
        set_flash_message('info', 'Palun logige sisse, et raamatuid laenutada');
        header("Location: login.php");
        exit;
    }
    
    // Arvuta tagastustähtaeg (2 nädalat)
    $due_date = date('Y-m-d H:i:s', strtotime('+2 weeks'));
    
    // Laenuta raamat
    $result = $bookModel->borrow($book_id, $_SESSION['user_id'], $due_date);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Suuna leht värskendamiseks
    header("Location: view_book.php?id=$book_id");
    exit;
}

// Töötleme tagastusvormi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return') {
    // Võta laenutuse ID
    $loan_id = $_POST['loan_id'] ?? 0;
    
    // Tagasta raamat
    $result = $bookModel->returnBook($loan_id);
    
    if ($result['success']) {
        set_flash_message('success', $result['message']);
    } else {
        set_flash_message('danger', $result['message']);
    }
    
    // Suuna leht värskendamiseks
    header("Location: view_book.php?id=$book_id");
    exit;
}

// Võta aktiivne laen, kui raamat on laenutatud
$active_loan = null;
if (!$book['available']) {
    $active_loan = $bookModel->getActiveLoan($book_id);
}

// Uuenda lehe pealkirja
$pageTitle = $book['title'] . ' - Raamatu detailid';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Avaleht</a></li>
                    <li class="breadcrumb-item"><a href="books.php">Raamatud</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($book['title']); ?></li>
                </ol>
            </nav>
            
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h1>
                    <h5 class="card-subtitle mb-3 text-muted">Autor: <?php echo htmlspecialchars($book['author']); ?></h5>
                    
                    <div class="mb-4">
                        <span class="badge <?php echo $book['available'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                            <?php echo $book['available'] ? 'Saadaval' : 'Laenutatud'; ?>
                        </span>
                        
                        <?php if ($book['publication_year']): ?>
                        <span class="badge bg-secondary p-2 ms-2">Avaldatud: <?php echo $book['publication_year']; ?></span>
                        <?php endif; ?>
                        
                        <?php if ($book['isbn']): ?>
                        <span class="badge bg-secondary p-2 ms-2">ISBN: <?php echo htmlspecialchars($book['isbn']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($book['description']): ?>
                    <div class="card-text mb-4">
                        <h5>Kirjeldus</h5>
                        <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-text text-muted small mb-4">
                        <p>
                            Lisanud: <?php echo htmlspecialchars($book['added_by_username']); ?><br>
                            Lisamise kuupäev: <?php echo date('d.m.Y', strtotime($book['created_at'])); ?>
                        </p>
                    </div>
                    
                    <?php if (is_logged_in()): ?>
                        <?php if ($book['available']): ?>
                            <form action="view_book.php?id=<?php echo $book_id; ?>" method="POST">
                                <input type="hidden" name="action" value="borrow">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-book me-2"></i>Laenuta see raamat
                                </button>
                            </form>
                        <?php else: ?>
                            <?php if ($active_loan && $active_loan['user_id'] == $_SESSION['user_id']): ?>
                                <div class="alert alert-info">
                                    <p>Olete selle raamatu laenutanud. Tagastustähtaeg: <?php echo date('d.m.Y', strtotime($active_loan['due_date'])); ?></p>
                                    <form action="view_book.php?id=<?php echo $book_id; ?>" method="POST">
                                        <input type="hidden" name="action" value="return">
                                        <input type="hidden" name="loan_id" value="<?php echo $active_loan['id']; ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-undo me-2"></i>Tagasta see raamat
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <p>See raamat on hetkel teise kasutaja poolt laenutatud.</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p><a href="login.php">Logige sisse</a>, et seda raamatut laenutada.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (is_admin()): ?>
                    <div class="mt-4">
                        <a href="edit_book.php?id=<?php echo $book_id; ?>" class="btn btn-outline-primary me-2">
                            <i class="fas fa-edit me-1"></i>Muuda
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-1"></i>Kustuta
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mt-3 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Raamatu info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Pealkiri:</span>
                            <span class="text-end"><?php echo htmlspecialchars($book['title']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Autor:</span>
                            <span class="text-end"><?php echo htmlspecialchars($book['author']); ?></span>
                        </li>
                        <?php if ($book['publication_year']): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Avaldamise aasta:</span>
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
                            <span>Staatus:</span>
                            <span class="badge <?php echo $book['available'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                                <?php echo $book['available'] ? 'Saadaval' : 'Laenutatud'; ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <?php if (!$book['available'] && $active_loan && is_admin()): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Laenutuse info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Laenutaja:</span>
                            <span class="text-end"><?php echo htmlspecialchars($active_loan['username']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Laenutuse kuupäev:</span>
                            <span class="text-end"><?php echo date('d.m.Y', strtotime($active_loan['borrowed_date'])); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Tagastustähtaeg:</span>
                            <span class="text-end"><?php echo date('d.m.Y', strtotime($active_loan['due_date'])); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (is_admin()): ?>
<!-- Kustutamise kinnitamise modaalaken -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Kinnita kustutamine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                Kas olete kindel, et soovite kustutada raamatu "<?php echo htmlspecialchars($book['title']); ?>"? Seda toimingut ei saa tagasi võtta.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                <form action="delete_book.php" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <button type="submit" class="btn btn-danger">Kustuta</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>