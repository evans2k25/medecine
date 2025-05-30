<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) && !isset($_SESSION['evaans_users_auth'])) {
    header('Location: login.php');
    exit();
}

// Affiche les infos de l'utilisateur connecté
if (!isset($user_infos)) {
    $user_infos = [];
    $user_id = $_SESSION['user_id'] ?? ($_SESSION['evaans_users_auth'] ?? null);
    if ($user_id && isset($pdo)) {
        $stmt = $pdo->prepare("SELECT nom, email, role, docteur_hopital FROM utilisateurs WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user_infos = $stmt->fetch();
    }
}
?>
<header class="dashboard-header">
    <div class="d-flex align-items-center">
        <button class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo-container">
            <i class="fas fa-hospital-user header-logo-icon"></i>
            <h1>Espace Admin - Dossiers Médicaux</h1>
        </div>
    </div>
    <div class="user-profile">
        <a href="#" data-bs-toggle="modal" data-bs-target="#userProfileModal" class="user-profile-link">
            <i class="fas fa-user-circle"></i>
            <span class="navbar-text ms-auto">
                <?php if (!empty($user_infos['nom'])): ?>
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user_infos['nom']) ?>
                <?php endif; ?>
            </span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt logout-icon" title="Déconnexion" data-bs-toggle="modal"
               data-bs-target="#logoutConfirmationModal" style="cursor: pointer;"></i>
        </a>
    </div>
</header>

<!-- MODAL de profil utilisateur (à placer après le header ou dans inclusions/modal.php) -->
<div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userProfileModalLabel">Mon profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <?php if ($user_infos): ?>
          <ul class="list-unstyled mb-0">
            <li><strong>Nom:</strong> <?= htmlspecialchars($user_infos['nom']) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($user_infos['email']) ?></li>
            <li><strong>Rôle:</strong> <?= htmlspecialchars($user_infos['role']) ?></li>
            <?php if (!empty($user_infos['docteur_hopital'])): ?>
                <li><strong>Spécialité:</strong> <?= htmlspecialchars($user_infos['docteur_hopital']) ?></li>
            <?php endif; ?>
          </ul>
        <?php else: ?>
          <em>Impossible de charger les informations utilisateur.</em>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>