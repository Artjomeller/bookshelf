<?php
$pageTitle = 'Otsingutulemused';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Võta otsingu päring
$search_query = trim($_GET['q'] ?? '');

// Loo raamatu mudeli eksemplar
$bookModel = new Book($pdo);

// Kui otsingu päring on antud, otsi raamatuid
$books = [];
if (!empty($search_query)) {
    $books = $bookModel->search($search_query);
}

?>

<div class="container">
    <h1 class="mb-4">Otsingutulemused</h1>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="search.php" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Otsi raamatuid..." 
                           value="<?php echo htmlspecialchars($search_query); ?>" aria-label="Otsi">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Otsi
                    </button>
                </div>
            </form>
            
            <?php if (empty($search_query)): ?>
            <div class="alert alert-info">
                Raamatute leidmiseks sisestage otsingusõna.
            </div>
            <?php elseif (empty($books)): ?>
            <div class="alert alert-warning">
                Otsingule "<?php echo htmlspecialchars($search_query); ?>" ei leitud ühtegi raamatut.
            </div>
            <?php else: ?>
            <p>Leiti <?php echo count($books); ?> raamatut otsingule "<?php echo htmlspecialchars($search_query); ?>":</p>
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
    
    <div class="mt-4 mb-4">
        <a href="books.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Tagasi raamatute juurde
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>