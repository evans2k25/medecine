<?php
require_once '../../sessions/session_userunloged_admin.php';
require_once '../../database/db.php';

// Vérifier si l'utilisateur est connecté (optionnel, à adapter selon votre application)    
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

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
    <title>Gestion du personnel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS & FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .table-actions a { margin-right: 7px; }
        .page-title { color: #1877f2; font-weight: 700; letter-spacing: 1px; }
        .container { background: #fff; border-radius: 14px; box-shadow: 0 2px 10px 0 rgba(44,62,80,0.07); margin-top: 36px; margin-bottom: 36px; }
        body { background: #f9fafc; }
    </style>
</head>
<body>
<div class="container py-4">
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
    <a href="../index.php" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left"></i> Retour</a>
</div>
<!-- JS dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>