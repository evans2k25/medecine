<?php
session_start();

// Vérifier si le personnel est connecté
if (!isset($_SESSION['personnel_id'])) {
    header('Location: login_personnel.php');
    exit();
}

// Vérifier que l'id patient est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: consultation_recherche.php');
    exit();
}

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

// Récupérer le patient
$patient = null;
$alert = '';
$id = intval($_GET['id']);
try {
    $stmt = $pdo->prepare("SELECT nom, prenom FROM patients WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        $alert = "<div class='alert alert-danger'>Patient non trouvé.</div>";
    }
} catch (PDOException $e) {
    $alert = "<div class='alert alert-danger'>Erreur lors de la récupération du patient : " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Traitement du formulaire d'enregistrement du service souhaité
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $patient) {
    $service = trim($_POST['service'] ?? '');
    if ($service !== '') {
        try {
            
            

            // Enregistrement du service souhaité
            $insert = $pdo->prepare("INSERT INTO demandes_service (patient_id, service, personnel_id, date_demande) VALUES (:patient_id, :service, :personnel_id, NOW())");
            $insert->execute([
                ':patient_id' => $id,
                ':service' => $service,
                ':personnel_id' => $_SESSION['personnel_id'],
            ]);
            $success = "<div class='alert alert-success'>Service souhaité enregistré : <strong>" . htmlspecialchars($service) . "</strong></div>";
        } catch (PDOException $e) {
            $alert = "<div class='alert alert-danger'>Erreur lors de l'enregistrement : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $alert = "<div class='alert alert-warning'>Veuillez indiquer un service souhaité.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix du service - Patient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f3f7fb; }
        .main-card {
            background: #fff;
            border-radius: 1rem;
            margin: 50px auto 0 auto;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06), 0 0.5px 1.5px rgba(0,0,0,0.03);
            max-width: 500px;
            padding: 2.2rem 2.2rem 2rem 2.2rem;
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
        .service-label {
            color: #114488;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container main-card">
    <h2 class="mb-4 page-title"><i class="fas fa-user"></i> Choix du service</h2>
    <?php if ($alert) echo $alert; ?>
    <?php if ($patient): ?>
        <div class="mb-3">
            <span class="service-label">Patient :</span>
            <span style="font-size:1.1rem; font-weight:600;">
                <?= htmlspecialchars($patient['prenom']) . ' ' . htmlspecialchars($patient['nom']) ?>
            </span>
        </div>
        <?php if ($success): ?>
            <?= $success ?>
            <a href="../consultation.php" class="btn btn-secondary ms-2">Retour à la recherche</a>
        <?php else: ?>
            <form method="post" class="mb-2">
                <div class="mb-3">
                    <label for="service" class="form-label"><i class="fas fa-hospital"></i> Service souhaité <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="service" name="service" required placeholder="Exemple : Cardiologie, Radiologie...">
                </div>
                <button type="submit" class="btn btn-main"><i class="fas fa-check"></i> Enregistrer</button>
                <a href="../consultation.php" class="btn btn-secondary ms-2">Retour à la recherche</a>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>