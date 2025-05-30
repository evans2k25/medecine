<?php
session_start();

// Vérifier si le personnel est connecté
if (!isset($_SESSION['personnel_id'])) {
    header('Location: login_personnel.php');
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'medecine';
$user = 'root'; // À adapter
$pass = '';     // À adapter

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Traitement de la recherche
$resultats = [];
$alerte = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $recherche = trim($_GET['q']);

    // Préparer la requête : on cherche sur nom, prénom ou numero_dossier
    $sql = "SELECT * FROM patients 
            WHERE nom LIKE :recherche 
               OR prenom LIKE :recherche 
               OR numero_dossier LIKE :recherche
            ORDER BY nom, prenom";
    $stmt = $pdo->prepare($sql);
    $like = '%' . $recherche . '%';
    $stmt->execute([':recherche' => $like]);
    $resultats = $stmt->fetchAll();

    if (empty($resultats)) {
        $alerte = '<div class="alert alert-warning mt-3">Aucun patient trouvé pour "<strong>' . htmlspecialchars($recherche) . '</strong>".</div>';
    }
} else {
    if (isset($_GET['q'])) {
        $alerte = '<div class="alert alert-warning mt-3">Veuillez saisir un critère de recherche.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de patient - Consultation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Résultat de la recherche de patient</h2>
    <form method="get" class="form-inline mb-4">
        <input type="text" class="form-control mr-2" name="q" placeholder="Nom, prénom ou N° dossier" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required>
        <button type="submit" class="btn btn-success">Rechercher</button>
        <a href="dashboard_personnel.php#ressources" class="btn btn-secondary ml-2">Retour</a>
    </form>
    <?php if ($alerte) echo $alerte; ?>
    <?php if (!empty($resultats)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="thead-dark">
                    <tr>
                        <th>N° Dossier</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de naissance</th>
                        <th>Sexe</th>
                        <th>Téléphone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($resultats as $patient): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patient['numero_dossier']); ?></td>
                        <td><?php echo htmlspecialchars($patient['nom']); ?></td>
                        <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                        <td><?php echo htmlspecialchars($patient['sexe']); ?></td>
                        <td><?php echo htmlspecialchars($patient['telephone']); ?></td>
                        <td>
                            <a href="lantern/inclusions/fiche_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">Consulter</a>
                            <!-- Ajoutez ici d'autres actions possibles -->
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($_GET['q']) && empty($alerte)): ?>
        <div class="alert alert-warning">Aucun résultat trouvé.</div>
    <?php endif; ?>
</div>
</body>
</html>