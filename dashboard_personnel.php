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
    <!-- BOOTSTRAP 5 + ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f6f7fb;
            font-family: 'Open Sans', Arial, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(90deg, #114488 0%, #00b894 100%);
        }
        .navbar-custom .navbar-brand, 
        .navbar-custom .navbar-text, 
        .navbar-custom .nav-link, 
        .navbar-custom .btn-outline-light {
            color: #fff !important;
        }
        .nav-tabs .nav-link.active {
            background: #00b894 !important;
            color: #fff !important;
            border-color: #00b894 #00b894 #fff;
        }
        .nav-tabs .nav-link {
            color: #114488 !important;
        }
        .tab-content {
            background: #ffffff;
            border-radius: 1rem;
            margin-top: 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06), 0 0.5px 1.5px rgba(0,0,0,0.03);
        }
        .card-header {
            background: linear-gradient(90deg, #114488 0%, #00b894 100%) !important;
            color: #fff !important;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
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
        .btn-info, .btn-info:focus, .btn-info:hover {
            background: #114488 !important;
            border-color: #114488 !important;
            color: #fff !important;
        }
        .btn-success, .btn-success:focus, .btn-success:hover {
            background: #00b894 !important;
            border-color: #00b894 !important;
            color: #fff !important;
        }
        .modal-header {
            background: #00b894;
            color: #fff;
        }
        .modal-title {
            color: #fff;
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #f3f7fb;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
        .tab-pane {
            padding-top: 1.2rem;
        }
        .nav-tabs {
            margin-bottom: 0;
            border-bottom: 2px solid #00b894;
        }
        .mt-4 {
            margin-top: 2rem !important;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .tab-content {
                padding: 0.5rem !important;
            }
            .navbar-custom .navbar-text {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-user-md"></i> Personnel</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="navbar-text text-white me-3 fw-semibold">
                <?= htmlspecialchars($personnel['prenom'] . " " . $personnel['nom']); ?> (<?= htmlspecialchars($personnel['fonction']); ?>)
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h2 class="mb-4 fw-bold" style="color:#114488;">Bienvenue sur votre espace personnel</h2>
    <!-- Onglets Bootstrap -->
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="infos-tab" data-bs-toggle="tab" data-bs-target="#infos" type="button" role="tab" aria-controls="infos" aria-selected="true">
                <i class="fas fa-id-badge"></i> Mes informations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">
                <i class="fas fa-envelope"></i> Messages
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="planning-tab" data-bs-toggle="tab" data-bs-target="#planning" type="button" role="tab" aria-controls="planning" aria-selected="false">
                <i class="fas fa-calendar-alt"></i> Planning
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ressources-tab" data-bs-toggle="tab" data-bs-target="#ressources" type="button" role="tab" aria-controls="ressources" aria-selected="false">
                <i class="fas fa-folder-open"></i> Ressources
            </button>
        </li>
    </ul>
    <div class="tab-content shadow-sm border-top-0 p-4" id="dashboardTabsContent">
        <!-- Onglet Mes informations -->
        <div class="tab-pane fade show active" id="infos" role="tabpanel" aria-labelledby="infos-tab">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header">Vos informations</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($personnel['nom']); ?></li>
                        <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($personnel['prenom']); ?></li>
                        <li class="list-group-item"><strong>Fonction :</strong> <?= htmlspecialchars($personnel['fonction']); ?></li>
                        <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($personnel['email']); ?></li>
                        <li class="list-group-item"><strong>Téléphone :</strong> <?= htmlspecialchars($personnel['telephone']); ?></li>
                        <li class="list-group-item"><strong>Date d'embauche :</strong> <?= htmlspecialchars($personnel['date_embauche']); ?></li>
                        <li class="list-group-item"><strong>Établissement :</strong> <?= htmlspecialchars($personnel['etablissement_nom']); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Onglet Messages -->
        <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
            <h5>Vos messages</h5>
            <p class="text-muted">Aucun message pour le moment.</p>
            <!-- Vous pouvez intégrer ici un système de messagerie interne -->
        </div>
        <!-- Onglet Planning -->
        <div class="tab-pane fade" id="planning" role="tabpanel" aria-labelledby="planning-tab">
            <h5>Planning</h5>
            <p class="text-muted">Votre planning ou emploi du temps apparaîtra ici.</p>
            <!-- À remplacer par un vrai module planning -->
        </div>
        <!-- Onglet Ressources -->
        <div class="tab-pane fade" id="ressources" role="tabpanel" aria-labelledby="ressources-tab">
            <h5>Ressources</h5>
            <div class="mb-3">
                <!-- Bouton Consultation ouvre le modal -->
                <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#consultationModal">
                    <i class="fas fa-search"></i> Consultation
                </button>
                <a href="register.php" class="btn btn-success"><i class="fas fa-user-plus"></i> Nouveau patient</a>
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
                            <h5 class="modal-title" id="consultationModalLabel"><i class="fas fa-search"></i> Rechercher un patient pour consultation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="patient_search" class="form-label">Nom, prénom ou N° dossier</label>
                                <input type="text" class="form-control" name="q" id="patient_search" placeholder="Entrez nom, prénom ou N° dossier..." required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-main">Rechercher</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Fin modal -->
        </div>
    </div>
    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-main"><i class="fas fa-arrow-left"></i> Retour Dashboard Admin</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>