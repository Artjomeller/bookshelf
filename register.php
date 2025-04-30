<?php
$pageTitle = 'Registreerimine';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/User.php';

// Suuna edasi, kui juba sisse logitud
if (is_logged_in()) {
    header("Location: dashboard.php");
    exit;
}

// Töötleme registreerimisvormi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    
    // Valideeri sisend
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Kasutajanimi on kohustuslik";
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Kasutajanimi peab olema 3 kuni 50 tähemärki pikk";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Kasutajanimi võib sisaldada ainult tähti, numbreid ja alakriipsu";
    }
    
    if (empty($email)) {
        $errors[] = "E-post on kohustuslik";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Palun sisestage kehtiv e-posti aadress";
    }
    
    if (empty($password)) {
        $errors[] = "Parool on kohustuslik";
    } elseif (strlen($password) < 8) {
        $errors[] = "Parool peab olema vähemalt 8 tähemärki pikk";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Parool peab sisaldama vähemalt ühte suurtähte";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Parool peab sisaldama vähemalt ühte väiketähte";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Parool peab sisaldama vähemalt ühte numbrit";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Paroolid ei ühti";
    }
    
    // Kui valideerimisvigu pole, proovime registreerida
    if (empty($errors)) {
        $userModel = new User($pdo);
        $result = $userModel->register($username, $email, $password, $full_name);
        
        if ($result['success']) {
            // Automaatne sisselogimine pärast registreerumist
            $login_result = $userModel->login($username, $password);
            
            // Seame õnnestumise teate
            set_flash_message('success', 'Registreerimine õnnestus! Tere tulemast BookShelf-i, ' . htmlspecialchars($username));
            
            // Suuname töölauaale
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow mt-5 mb-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Loo konto</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kasutajanimi</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                            <small class="form-text text-muted">Lubatud on ainult tähed, numbrid ja alakriipsud.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-post</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Täisnimi (valikuline)</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($full_name ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Parool</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="password-strength-feedback" class="form-text"></div>
                            <small class="form-text text-muted">Parool peab olema vähemalt 8 tähemärki pikk, sisaldama suurtähti, väiketähti ja numbreid.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Kinnita parool</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registreeru</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Juba on konto? <a href="login.php">Logi sisse siin</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>