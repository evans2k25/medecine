<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';

// Connexion à la base de données
$host = 'localhost';
$dbname = 'medecine';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Suppression
$alert = '';
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $idToDelete = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM personnel WHERE id = ?");
        $stmt->execute([$idToDelete]);
        $alert = '<div class="alert alert-success mt-3">Personnel supprimé avec succès.</div>';
    } catch (PDOException $e) {
        $alert = '<div class="alert alert-danger mt-3">Erreur lors de la suppression : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Récupérer l'établissement de l'utilisateur connecté
$etablissement_id = $_SESSION['etablissement_id'] ?? null;
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

// Récupérer la liste du personnel
try {
    $stmt = $pdo->query("SELECT p.*, e.nom AS etab_nom FROM personnel p LEFT JOIN etab_enreg e ON p.etablissement_id = e.id ORDER BY p.nom, p.prenom");
    $personnels = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur lors de la récupération du personnel : " . htmlspecialchars($e->getMessage()) . "</div>");
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
    <style>
        .table-actions .btn {
            margin-right: 3px;
        }
        .page-title {
            color: #134da3;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .dashboard-container {
            background: #f1f5fa;
            min-height: 100vh;
        }
        .main-content {
            padding-top: 32px;
        }
        .badge.badge-pill {
            font-size: 1.06em;
            padding: 0.5em 1.1em;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <?php require_once 'inclusions/header.php';   ?>

        <div class="overlay"></div>

        <div class="dashboard-body">
            <aside class="sidebar">
                <?php require_once 'inclusions/sidebar.php'; ?>
            </aside>

            <main class="main-content">
                <!-- Affichage du nom de l'établissement courant -->
                <h2 class="mb-4 page-title"><i class="fas fa-users"></i> Gestion du personnel</h2>
                <?php if ($alert) echo $alert; ?>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="Ajout_personel.php" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Ajouter un personnel
                    </a>
                    <span class="badge badge-secondary badge-pill"><?php echo count($personnels); ?> membres</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover bg-white">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Fonction</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Établissement</th>
                                <th>Date embauche</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($personnels)): ?>
                            <tr><td colspan="9" class="text-center text-muted">Aucun membre du personnel trouvé.</td></tr>
                        <?php else: foreach ($personnels as $idx => $perso): ?>
                            <tr>
                                <td><?php echo $idx + 1; ?></td>
                                <td><?php echo htmlspecialchars($perso['nom']); ?></td>
                                <td><?php echo htmlspecialchars($perso['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($perso['fonction']); ?></td>
                                <td><?php echo htmlspecialchars($perso['telephone']); ?></td>
                                <td><?php echo htmlspecialchars($perso['email']); ?></td>
                                <td><?php echo htmlspecialchars($perso['etab_nom']); ?></td>
                                <td><?php echo htmlspecialchars($perso['date_embauche']); ?></td>
                                <td class="table-actions">
                                    <a href="Modifier_personnel.php?id=<?php echo $perso['id']; ?>" class="btn btn-primary btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                                    <a href="gestion_personnel.php?delete=<?php echo $perso['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce personnel ?');" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
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