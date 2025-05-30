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

// Récupérer la liste des patients
try {
    $stmt = $pdo->query("
        SELECT p.*, e.nom AS etab_nom 
        FROM patients p 
        LEFT JOIN etab_enreg e ON p.etablissement_id = e.id 
        ORDER BY p.nom, p.prenom
    ");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur lors de la récupération des patients : " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des patients enregistrés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .table-actions a { margin-right: 0.5rem; }
        .container {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px 0 rgba(44,62,80,0.07);
            margin-top: 36px;
            margin-bottom: 36px;
        }
        body { background: #f9fafc; }
        .page-title { color: #1877f2; font-weight: 700; letter-spacing: 1px; }
    </style>
</head>
<body>
    
<div class="container py-4">
    
    <h2 class="mb-4 page-title"><i class="fas fa-users"></i> Liste des patients enregistrés</h2>
    
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="ajout_patient.php" class="btn btn-success">
            <i class="fas fa-user-plus"></i> Ajouter un patient
        </a>
        <span class="badge badge-secondary badge-pill"><?php echo count($patients); ?> patients</span>
    </div>
    <div class="table-responsive">
    <table class="table table-bordered table-hover bg-white">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>N° Dossier</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date naissance</th>
                <th>Sexe</th>
                <th>Téléphone</th>
                <th>Établissement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($patients)): ?>
            <tr><td colspan="9" class="text-center text-muted">Aucun patient trouvé.</td></tr>
        <?php else: foreach ($patients as $idx => $patient): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><?php echo htmlspecialchars($patient['numero_dossier']); ?></td>
                <td><?php echo htmlspecialchars($patient['nom']); ?></td>
                <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                <td><?php echo htmlspecialchars($patient['sexe']); ?></td>
                <td><?php echo htmlspecialchars($patient['telephone']); ?></td>
                <td><?php echo htmlspecialchars($patient['etab_nom']); ?></td>
                <td class="table-actions">
                    <a href="fiche_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-info btn-sm" title="Détails"><i class="fas fa-info-circle"></i></a>
                    <a href="modifier_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                    <a href="liste_patients.php?delete=<?php echo $patient['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce patient ?');" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
    <a href="javascript:history.back()" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left"></i> Retour</a>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>