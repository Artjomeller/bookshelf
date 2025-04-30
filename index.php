<?php
$pageTitle = 'Avaleht';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/Book.php';

// Loon raamatu mudeli instantsi
$bookModel = new Book($pdo);

// Võtan viimased 5 raamatut (näidatakse ainult sisseloginud kasutajatele)
$latest_books = $bookModel->getAll(5);
?>

<div class="jumbotron bg-light p-5 rounded">
    <div class="container">
        <h1 class="display-4">Tere tulemast BookShelf-i</h1>
        <p class="lead">Teie digitaalne raamatukogu haldussüsteem. Sirvi raamatuid, laenuta neid ja jälgi oma lugemisreisi.</p>
        
        <?php if (!is_logged_in()): ?>
        <hr class="my-4">
        <p>Meie kogu sirvimiseks ja raamatute laenutamiseks palun logi sisse või loo konto.</p>
        <a class="btn btn-primary btn-lg" href="login.php" role="button">Logi sisse</a>
        <a class="btn btn-outline-primary btn-lg" href="register.php" role="button">Registreeru</a>
        <?php else: ?>
        <hr class="my-4">
        <p>Tutvu meie raamatukoguga või külasta oma töölaua.</p>
        <a class="btn btn-primary btn-lg" href="books.php" role="button">Sirvi raamatuid</a>
        <a class="btn btn-outline-primary btn-lg" href="dashboard.php" role="button">Mine töölauaale</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!is_logged_in()): ?>
<!-- Sisu sisselogimata kasutajatele -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-4x text-primary mb-3"></i>
                    <h3 class="card-title">Ulatuslik kogu</h3>
                    <p class="card-text">Uurige meie mitmekesist raamatukogu erinevates žanrites. Klassikast tänapäevaste bestselleriteni, meil on midagi igaühele.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bookmark fa-4x text-primary mb-3"></i>
                    <h3 class="card-title">Lihtne laenutamine</h3>
                    <p class="card-text">Meie lihtne laenutussüsteem võimaldab teil raamatuid laenutada vaid mõne klikiga. Jälgige tagastamistähtaegu ja tagastage raamatud endale sobival ajal.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                    <h3 class="card-title">Isiklik töölaud</h3>
                    <p class="card-text">Hallake oma laenutatud raamatuid, vaadake oma lugemisajalugu ja uuendage oma profiiliteavet isikliku töölaua kaudu.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Teave BookShelf kohta</h3>
                </div>
                <div class="card-body">
                    <p>BookShelf on kaasaegne raamatukogu haldussüsteem, mis on loodud muutma raamatute laenutamise ja jälgimise lihtsaks ja tõhusaks. Meie platvorm pakub sujuvat digitaalset kogemust raamatusõpradele.</p>
                    
                    <p>BookShelf-iga saate:</p>
                    <ul>
                        <li>Sirvida meie ulatuslikku raamatukogu</li>
                        <li>Vaadata üksikasjalikku teavet iga raamatu kohta</li>
                        <li>Laenutada raamatuid veebis mõne klikiga</li>
                        <li>Jälgida oma laenutusajalugu</li>
                        <li>Saada meeldetuletusi tagastustähtaegade kohta</li>
                        <li>Hallata oma isiklikku profiili</li>
                    </ul>
                    
                    <p>Meie teenuste kasutamiseks palun <a href="register.php">registreerige konto</a> või <a href="login.php">logige sisse</a>, kui teil juba on konto.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Sisu sisseloginud kasutajatele - näita viimaseid raamatuid -->
<div class="container mt-5">
    <h2 class="mb-4">Viimased raamatud</h2>
    
    <div class="row">
        <?php foreach ($latest_books as $book): ?>
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
                    <small class="text-muted">Avaldatud: <?php echo htmlspecialchars($book['publication_year']); ?></small>
                    <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary float-end">Vaata detaile</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4 mb-5">
        <a href="books.php" class="btn btn-primary">Vaata kõiki raamatuid</a>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>