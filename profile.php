<?php
$pageTitle = 'Profiil';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/User.php';

// Nõua sisselogimist
require_login();

// Loo kasutaja mudeli eksemplar
$userModel = new User($pdo);

// Võta kasutaja andmed
$user = $userModel->getById($_SESSION['user_id']);

// Töötleme profiili uuendamise vormi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Valideeri sisend
    $errors = [];
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Palun sisestage kehtiv e-posti aadress";
    }
    
    // Kui valideerimisvigu pole, uuenda profiili
    if (empty($errors)) {
        $result = $userModel->updateProfile($_SESSION['user_id'], [
            'full_name' => $full_name,
            'email' => $email
        ]);
        
        if ($result['success']) {
            set_flash_message('success', $result['message']);
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

// Töötleme parooli muutmise vormi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Valideeri sisend
    $errors = [];
    
    if (empty($current_password)) {
        $errors[] = "Praegune parool on kohustuslik";
    }
    
    if (empty($new_password)) {
        $errors[] = "Uus parool on kohustuslik";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Uus parool peab olema vähemalt 8 tähemärki pikk";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $errors[] = "Uus parool peab sisaldama vähemalt ühte suurtähte";
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $errors[] = "Uus parool peab sisaldama vähemalt ühte väiketähte";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $errors[] = "Uus parool peab sisaldama vähemalt ühte numbrit";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "Uued paroolid ei ühti";
    }
    
    // Kui valideerimisvigu pole, muuda parooli
    if (empty($errors)) {
        $result = $userModel->changePassword($_SESSION['user_id'], $current_password, $new_password);
        
        if ($result['success']) {
            set_flash_message('success', $result['message']);
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>

<div class="container">
    <h1 class="mb-4">Profiili seaded</h1>
    
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
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Navigatsioon</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="dashboard.php" class="text-decoration-none">
                                <i class="fas fa-tachometer-alt me-2"></i>Töölaud
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="my_books.php" class="text-decoration-none">
                                <i class="fas fa-book-reader me-2"></i>Minu laenutused
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="books.php" class="text-decoration-none">
                                <i class="fas fa-book me-2"></i>Sirvi raamatuid
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Muuda profiili</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors) && isset($_POST['action']) && $_POST['action'] === 'update_profile'): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Kasutajanimi</label>
                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly disabled>
                            <div class="form-text text-muted">Kasutajanime ei saa muuta.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-post</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Täisnimi</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Uuenda profiili</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Muuda parooli</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors) && isset($_POST['action']) && $_POST['action'] === 'change_password'): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Praegune parool</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Uus parool</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div id="password-strength-feedback" class="form-text"></div>
                            <div class="form-text text-muted">Parool peab olema vähemalt 8 tähemärki pikk, sisaldama suurtähti, väiketähti ja numbreid.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Kinnita uus parool</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Muuda parool</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>