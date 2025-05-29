<?php
session_start();

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

// Compter les utilisateurs, patients, établissements
$nbUsers = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$nbPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$nbEtablissements = $pdo->query("SELECT COUNT(*) FROM etab_enreg")->fetchColumn();

$user_nom = htmlspecialchars($_SESSION['user_nom']);
$user_role = htmlspecialchars($_SESSION['user_role']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Tableau de bord</a>
    <div class="ml-auto">
        <span class="navbar-text text-white mr-3">Bienvenue, <?php echo $user_nom; ?> (<?php echo $user_role; ?>)</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
    </div>
</nav>
<div class="container py-4">
    <h2 class="mb-4">Tableau de bord</h2>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4"><?php echo $nbUsers; ?></p>
                    <a href="liste_utilisateurs.php" class="btn btn-light btn-sm">Voir la liste</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Patients</h5>
                    <p class="card-text display-4"><?php echo $nbPatients; ?></p>
                    <a href="liste_patients.php" class="btn btn-light btn-sm">Voir la liste</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Établissements</h5>
                    <p class="card-text display-4"><?php echo $nbEtablissements; ?></p>
                    <a href="liste_etablissements.php" class="btn btn-light btn-sm">Voir la liste</a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="register.php" class="btn btn-success">Ajouter un patient</a>
        <a href="ajouter_etablissement.php" class="btn btn-secondary">Ajouter un établissement</a>
        <a href="Ajout_personel.php" class="btn btn-primary">Ajouter un utilisateur</a>
    </div>
</div>
</body>
</html>