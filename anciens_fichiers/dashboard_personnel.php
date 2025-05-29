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

// Récupérer les infos du personnel connecté
$id = $_SESSION['personnel_id'];
$stmt = $pdo->prepare("SELECT p.*, e.nom AS etablissement_nom FROM personnel p LEFT JOIN etab_enreg e ON p.etablissement_id = e.id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$personnel = $stmt->fetch();

if (!$personnel) {
    // S'il n'existe pas, on déconnecte
    session_destroy();
    header('Location: login_personnel.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Personnel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .nav-tabs .nav-link.active {
            background-color: #28a745 !important;
            color: #fff !important;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <a class="navbar-brand" href="#">Personnel</a>
    <div class="ml-auto">
        <span class="navbar-text text-white mr-3">
            <?php echo htmlspecialchars($personnel['prenom'] . " " . $personnel['nom']); ?> (<?php echo htmlspecialchars($personnel['fonction']); ?>)
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
    </div>
</nav>
<div class="container py-4">
    <h2 class="mb-4">Bienvenue sur votre espace personnel</h2>
    <!-- Onglets Bootstrap -->
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="infos-tab" data-toggle="tab" href="#infos" role="tab" aria-controls="infos" aria-selected="true">
                Mes informations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="messages-tab" data-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">
                Messages
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="planning-tab" data-toggle="tab" href="#planning" role="tab" aria-controls="planning" aria-selected="false">
                Planning
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ressources-tab" data-toggle="tab" href="#ressources" role="tab" aria-controls="ressources" aria-selected="false">
                Ressources
            </a>
        </li>
    </ul>
    <div class="tab-content bg-white p-4 border-bottom border-left border-right" id="dashboardTabsContent">
        <!-- Onglet Mes informations -->
        <div class="tab-pane fade show active" id="infos" role="tabpanel" aria-labelledby="infos-tab">
            <div class="card mb-4 border-0">
                <div class="card-header bg-success text-white">Vos informations</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nom :</strong> <?php echo htmlspecialchars($personnel['nom']); ?></li>
                        <li class="list-group-item"><strong>Prénom :</strong> <?php echo htmlspecialchars($personnel['prenom']); ?></li>
                        <li class="list-group-item"><strong>Fonction :</strong> <?php echo htmlspecialchars($personnel['fonction']); ?></li>
                        <li class="list-group-item"><strong>Email :</strong> <?php echo htmlspecialchars($personnel['email']); ?></li>
                        <li class="list-group-item"><strong>Téléphone :</strong> <?php echo htmlspecialchars($personnel['telephone']); ?></li>
                        <li class="list-group-item"><strong>Date d'embauche :</strong> <?php echo htmlspecialchars($personnel['date_embauche']); ?></li>
                        <li class="list-group-item"><strong>Établissement :</strong> <?php echo htmlspecialchars($personnel['etablissement_nom']); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Onglet Messages -->
        <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
            <h5>Vos messages</h5>
            <p>Aucun message pour le moment.</p>
            <!-- Vous pouvez intégrer ici un système de messagerie interne -->
        </div>
        <!-- Onglet Planning -->
        <div class="tab-pane fade" id="planning" role="tabpanel" aria-labelledby="planning-tab">
            <h5>Planning</h5>
            <p>Votre planning ou emploi du temps apparaîtra ici.</p>
            <!-- À remplacer par un vrai module planning -->
        </div>
        <!-- Onglet Ressources -->
        <div class="tab-pane fade" id="ressources" role="tabpanel" aria-labelledby="ressources-tab">
            <h5>Ressources</h5>
            <div class="mb-3">
                <!-- Bouton Consultation ouvre le modal -->
                <button type="button" class="btn btn-info mr-2" data-toggle="modal" data-target="#consultationModal">
                    Consultation
                </button>
                <a href="register.php" class="btn btn-success">Nouveau patient</a>
            </div>
            <ul>
                <li><a href="#" target="_blank">Document 1</a></li>
                <li><a href="#" target="_blank">Document 2</a></li>
            </ul>
            <!-- Modal Consultation -->
            <div class="modal fade" id="consultationModal" tabindex="-1" aria-labelledby="consultationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="GET" action="consultation.php" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="consultationModalLabel">Rechercher un patient pour consultation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="patient_search">Nom, prénom ou N° dossier</label>
                                <input type="text" class="form-control" name="q" id="patient_search" placeholder="Entrez nom, prénom ou N° dossier..." required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Fin modal -->
        </div>
    </div>
    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-primary">Retour Dashboard Admin</a>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>