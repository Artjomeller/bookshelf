<?php
$pageTitle = 'Sisselogimine';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/User.php';

// Suuna edasi, kui juba sisselogitud
if (is_logged_in()) {
    header("Location: dashboard.php");
    exit;
}

// Töötleme sisselogimisvormi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Valideeri sisend
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Kasutajanimi või e-post on kohustuslik";
    }
    
    if (empty($password)) {
        $errors[] = "Parool on kohustuslik";
    }
    
    // Kui valideerimisvigu pole, proovi sisse logida
    if (empty($errors)) {
        $userModel = new User($pdo);
        $result = $userModel->login($username, $password);
        
        if ($result['success']) {
            // Kontrolli, kas sessioonis on salvestatud suunamise URL
            $redirect_to = $_SESSION['redirect_to'] ?? 'dashboard.php';
            unset($_SESSION['redirect_to']);
            
            // Sea õnnestumise teade
            set_flash_message('success', 'Sisselogimine õnnestus! Tere tulemast tagasi, ' . htmlspecialchars($_SESSION['username']));
            
            // Suuna kasutaja soovitud lehele
            header("Location: $redirect_to");
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
            <div class="card shadow mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Logi sisse</h3>
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
                    
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kasutajanimi või e-post</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Parool</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Logi sisse</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Pole veel kontot? <a href="register.php">Registreeru siin</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>