<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';

// Récupérer l'ID de l'établissement courant depuis la session
$etablissement_id = $_SESSION['etablissement_id'] ?? null;

// Chercher le nom de l'établissement
$etablissement_nom = '';
if ($etablissement_id) {
    $stmt = $pdo->prepare("SELECT nom FROM etab_enreg WHERE id = :id");
    $stmt->execute([':id' => $etablissement_id]);
    $row = $stmt->fetch();
    if ($row) {
        $etablissement_nom = $row['nom'];
    }
}

// Récupérer toutes les infos de l'utilisateur connecté
$user_infos = [];
$user_id = $_SESSION['user_id'] ?? ($_SESSION['evaans_users_auth'] ?? null);
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user_infos = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Dossiers Médicaux - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <?php require_once 'inclusions/header.php'; ?>

        <div class="overlay"></div>

        <div class="dashboard-body">
            <aside class="sidebar">
                <?php require_once 'inclusions/sidebar.php'; ?>
            </aside>

            <main class="main-content">
                <!-- Affichage du nom de l'établissement courant -->
                <?php if ($etablissement_nom): ?>
                    <div class="alert alert-info mb-4">Établissement courant : <strong><?= htmlspecialchars($etablissement_nom) ?></strong></div>
                <?php endif; ?>

                <!-- Affichage des infos de l'utilisateur connecté -->
                <?php if ($user_infos): ?>
                    <div class="card mb-4" style="max-width:500px; margin-left:auto; margin-right:0;">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-user-circle"></i> Informations de l'utilisateur connecté
                            <button type="button" class="btn btn-link text-white" data-bs-toggle="modal" data-bs-target="#userProfileModal">
                                Voir le profil
                            </button>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><strong>Nom :</strong> <?= htmlspecialchars($user_infos['nom'] ?? '') ?></li>
                                <?php if (!empty($user_infos['email'])): ?>
                                    <li><strong>Email :</strong> <?= htmlspecialchars($user_infos['email']) ?></li>
                                <?php endif; ?>
                                <?php if (!empty($user_infos['role'])): ?>
                                    <li><strong>Rôle :</strong> <?= htmlspecialchars($user_infos['role']) ?></li>
                                <?php endif; ?>
                                <?php if (!empty($user_infos['docteur_hopital'])): ?>
                                    <li><strong>Spécialité :</strong> <?= htmlspecialchars($user_infos['docteur_hopital']) ?></li>
                                <?php endif; ?>
                                <?php if (!empty($user_infos['etablissement_id'])): ?>
                                    <li><strong>ID Établissement :</strong> <?= htmlspecialchars($user_infos['etablissement_id']) ?></li>
                                <?php endif; ?>
                                <?php if (isset($user_infos['actif'])): ?>
                                    <li><strong>Statut :</strong> <?= $user_infos['actif'] ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-danger">Inactif</span>' ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <section id="dashboard" class="content-section active">
                    <h2>Tableau de Bord</h2>
                    <div class="stats-cards">
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-users"></i></div>
                            <div class="card-content">
                                <h3>Total Patients</h3>
                                <p>1,234</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-content">
                                <h3>Rendez-vous Aujourd'hui</h3>
                                <p>27</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-folder-open"></i></div>
                            <div class="card-content">
                                <h3>Dossiers Récents</h3>
                                <p>15</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="card-content">
                                <h3>Alertes</h3>
                                <p>3</p>
                            </div>
                        </div>
                    </div>
                    <div class="recent-activity">
                        <h3>Activité Récente</h3>
                        <ul>
                            <li><i class="fas fa-user-plus activity-icon"></i> Nouveau patient ajouté : Jean Dupont</li>
                            <li><i class="fas fa-calendar-plus activity-icon"></i> Rendez-vous programmé : Marie Curie - 14:00</li>
                            <li><i class="fas fa-file-alt activity-icon"></i> Dossier mis à jour : Paul Martin</li>
                        </ul>
                    </div>
                </section>

                <!-- ... reste du contenu inchangé ... -->

                <section id="patients" class="content-section">
                    <h2>Gestion des Patients</h2>
                    <div class="toolbar">
                        <input type="text" placeholder="Rechercher un patient..." class="form-control search-input">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal"><i
                                class="fas fa-plus"></i> Ajouter Patient</button>
                    </div>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Patient</th>
                                    <th>Nom Complet</th>
                                    <th>Date de Naissance</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>P001</td>
                                    <td>Alice Dubois</td>
                                    <td>15/05/1985</td>
                                    <td>alice.dubois@example.com</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info view-patient-btn" title="Voir"
                                            data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                                            data-patient-id="P001"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-primary edit-patient-btn" title="Modifier"
                                            data-bs-toggle="modal" data-bs-target="#editPatientModal"
                                            data-patient-id="P001"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete-patient-btn"
                                            title="Supprimer" data-bs-toggle="modal"
                                            data-bs-target="#deletePatientModal" data-patient-id="P001"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>P002</td>
                                    <td>Bob Martin</td>
                                    <td>22/11/1990</td>
                                    <td>bob.martin@example.com</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info view-patient-btn" title="Voir"
                                            data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                                            data-patient-id="P002"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-primary edit-patient-btn" title="Modifier"
                                            data-bs-toggle="modal" data-bs-target="#editPatientModal"
                                            data-patient-id="P002"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete-patient-btn"
                                            title="Supprimer" data-bs-toggle="modal"
                                            data-bs-target="#deletePatientModal" data-patient-id="P002"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ... autres sections inchangées ... -->

            </main>
        </div>
    </div>

    <!-- Modals -->
    <?php require_once 'inclusions/modal.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ... scripts JS ... -->
</body>

</html>