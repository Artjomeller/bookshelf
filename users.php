<?php
$pageTitle = 'Kasutajate haldamine';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'models/User.php';

// Nõua administraatori õigusi
require_admin();

// Loo kasutaja mudeli eksemplar
$userModel = new User($pdo);

// Hangi kõik kasutajad
$users = $userModel->getAllUsers();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mt-4 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Kasutajate haldamine</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['flash_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kasutajanimi</th>
                                    <th>E-post</th>
                                    <th>Täisnimi</th>
                                    <th>Roll</th>
                                    <th>Registreeritud</th>
                                    <th>Tegevused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Kasutajaid pole leitud</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                                            <td>
                                                <?php if ($user['is_admin']): ?>
                                                    <span class="badge bg-danger">Administraator</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Tavaline kasutaja</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                            <td>
                                                <?php if ($user['id'] != $_SESSION['user_id'] && !$user['is_admin']): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteUserModal" 
                                                            data-user-id="<?php echo $user['id']; ?>"
                                                            data-username="<?php echo htmlspecialchars($user['username']); ?>">
                                                        <i class="fas fa-trash-alt"></i> Kustuta
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted"><?php echo ($user['id'] == $_SESSION['user_id']) ? 'Praegune kasutaja' : 'Administraator'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kustutamise kinnitusdialoog -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">Kustuta kasutaja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Kas olete kindel, et soovite kustutada kasutaja <strong id="deleteUsername"></strong>?</p>
                <p class="text-danger"><strong>Hoiatus:</strong> See tegevus on pöördumatu ja kustutab kõik kasutajaga seotud andmed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                <form action="delete_user.php" method="POST">
                    <input type="hidden" name="user_id" id="deleteUserId" value="">
                    <button type="submit" class="btn btn-danger">Kustuta kasutaja</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seadistame modaali väärtused, kui see avatakse
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            
            // Seame modaali väärtused
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUsername').textContent = username;
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>