<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/bookshelf/index.php">
            <i class="fas fa-book-open me-2"></i>BookShelf
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="/bookshelf/index.php">Avaleht</a>
                </li>
                
                <?php if (is_logged_in()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'books.php') ? 'active' : ''; ?>" href="/bookshelf/books.php">Raamatud</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="/bookshelf/dashboard.php">Töölaud</a>
                </li>
                <?php if (is_admin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_book.php') ? 'active' : ''; ?>" href="/bookshelf/add_book.php">Lisa raamat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" href="/bookshelf/users.php">Kasutajad</a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            
            <?php if (is_logged_in()): ?>
            <form class="d-flex mx-2 my-2 my-lg-0" action="/bookshelf/search.php" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Otsi raamatuid..." aria-label="Otsi">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <?php endif; ?>
            
            <ul class="navbar-nav">
                <?php if (is_logged_in()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i><?php echo $_SESSION['username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/bookshelf/profile.php">Profiil</a></li>
                        <li><a class="dropdown-item" href="/bookshelf/my_books.php">Minu laenutused</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/bookshelf/logout.php">Logi välja</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>" href="/bookshelf/login.php">Logi sisse</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>" href="/bookshelf/register.php">Registreeru</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>