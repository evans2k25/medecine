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

// Traitement de la recherche
$resultats = [];
$alerte = '';
if (isset($_GET['q'])) {
    $recherche = trim($_GET['q']);

    if ($recherche === '') {
        $alerte = '<div class="alert alert-warning mt-3">Veuillez saisir un critère de recherche.</div>';
    } else {
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

        // Si un seul résultat, rediriger directement vers lantern/choix_service.php
        if (count($resultats) === 1) {
            $patient_id = $resultats[0]['id'];
            header("Location: lantern/choix_service.php?id=" . urlencode($patient_id));
            exit();
        }

        if (empty($resultats)) {
            $alerte = '<div class="alert alert-warning mt-3">Aucun patient trouvé pour "<strong>' . htmlspecialchars($recherche) . '</strong>".</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de patient - Consultation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- BOOTSTRAP 5 + ICONS + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f3f7fb;
            font-family: 'Open Sans', Arial, sans-serif;
        }
        .main-card {
            background: #fff;
            border-radius: 1rem;
            margin: 40px auto 0 auto;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06), 0 0.5px 1.5px rgba(0,0,0,0.03);
            max-width: 900px;
            padding: 2rem 2rem 1.5rem 2rem;
        }
        .page-title {
            color: #114488;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .btn-main {
            background: #00b894;
            color: #fff;
            border: none;
        }
        .btn-main:hover, .btn-main:focus {
            background: #114488;
            color: #fff;
        }
        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background: #114488 !important;
            border-color: #114488 !important;
            color: #fff !important;
        }
        .table-actions .btn {
            margin-right: 0.5rem;
        }
        .search-form .form-control {
            max-width: 350px;
        }
        .thead-dark th {
            background: #00b894;
            color: #fff;
            border-color: #00b894;
        }
        @media (max-width: 700px) {
            .main-card {
                padding: 1rem 0.2rem;
            }
        }
    </style>
</head>
<body>
<div class="container main-card">
    <h2 class="mb-4 page-title"><i class="fas fa-search"></i> Recherche de patient pour consultation</h2>
    <form method="get" class="row align-items-center search-form mb-4 g-2">
        <div class="col-sm-8 col-12">
            <input type="text" class="form-control" name="q" placeholder="Nom, prénom ou N° dossier" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required autofocus>
        </div>
        <div class="col-sm-4 col-12 d-flex gap-2 mt-2 mt-sm-0">
            <button type="submit" class="btn btn-main flex-fill"><i class="fas fa-search"></i> Rechercher</button>
            <a href="dashboard_personnel.php#ressources" class="btn btn-secondary flex-fill"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </form>
    <?php if ($alerte) echo $alerte; ?>
    <?php if (!empty($resultats)): ?>
        <div class="table-responsive d-flex justify-content-center">
            <table class="table table-bordered table-hover bg-white mx-auto" style="width:95%; max-width:900px;">
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
                        <td class="table-actions">
                            <a href="lantern/choix_service.php?id=<?php echo $patient['id']; ?>" class="btn btn-main btn-sm"><i class="fas fa-folder-open"></i> Consulter</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($_GET['q']) && !$alerte && empty($resultats)): ?>
        <div class="alert alert-warning mt-4">Aucun résultat trouvé.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>