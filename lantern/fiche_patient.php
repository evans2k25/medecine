<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';


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

// --- Récupérer l'ID du patient ---
$alert = '';
$patient = null;
$consultations = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $alert = '<div class="alert alert-danger mt-3">Aucun patient sélectionné.</div>';
} else {
    $id = intval($_GET['id']);
    // Récupérer les infos du patient
    try {
        $stmt = $pdo->prepare("SELECT p.*, e.nom AS etab_nom FROM patients p LEFT JOIN etab_enreg e ON p.etablissement_id = e.id WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        $patient = $stmt->fetch();
        if (!$patient) {
            $alert = '<div class="alert alert-danger mt-3">Patient introuvable.</div>';
        } else {
            // Récupérer les consultations de ce patient
            $cstmt = $pdo->prepare("
                SELECT * FROM consultations 
                WHERE patient_id = :id 
                ORDER BY date_consultation DESC, id DESC
            ");
            $cstmt->execute(['id' => $id]);
            $consultations = $cstmt->fetchAll();
        }
    } catch (PDOException $e) {
        $alert = '<div class="alert alert-danger mt-3">Erreur lors de la récupération des données : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Patient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    :root {
      --main-bg: #f9fafc;
      --widget-bg: #ffffff;
      --widget-shadow: rgba(44,62,80,0.07);
      --blue: #1877f2;
      --green: #28c76f;
      --red: #ea5455;
      --orange: #ff9f43;
      --gray: #6c757d;
      --text-dark: #212529;
      --text-light: #fff;
    }
    body {
      background: var(--main-bg);
    }
    .container {
      background: var(--widget-bg);
      border-radius: 18px;
      box-shadow: 0 2px 12px 0 var(--widget-shadow);
      padding: 38px 36px 32px 36px;
      margin-top: 30px;
      margin-bottom: 30px;
    }
    h2.page-title {
      color: var(--blue);
      font-weight: 700;
      letter-spacing: 1px;
    }
    .label-info {
      color: var(--gray);
      font-size: 1.07rem;
      font-weight: 600;
      margin-bottom: 0.2rem;
    }
    .patient-info-block {
      background: #f4f6fa;
      border-radius: 12px;
      padding: 22px 25px 14px 25px;
      margin-bottom: 26px;
      box-shadow: 0 1px 8px 0 var(--widget-shadow);
    }
    .badge-success { background: var(--green); }
    .badge-danger { background: var(--red); }
    .badge-warning { background: var(--orange); color: #fff; }
    .table th, .table td { vertical-align: middle; }
    .table th { background: var(--blue); color: #fff; }
    .table-striped>tbody>tr:nth-of-type(even) { background-color: #f4f6fa; }

    .constantes-block {
      background: #f4f9ff;
      border: 2px solid var(--blue);
      border-radius: 14px;
      padding: 22px 18px 16px 18px;
      margin-bottom: 28px;
      box-shadow: 0 1px 8px 0 var(--widget-shadow);
    }
    .const-title {
      font-weight: 600;
      color: var(--blue);
      margin-bottom: 18px;
      font-size: 1.18rem;
    }
    .form-control:focus {
      border-color: var(--blue) !important;
      box-shadow: 0 0 0 0.12rem var(--blue, #1877f2, 0.09);
    }
    </style>
</head>
<body>
<div class="container">
    <h2 class="page-title mb-4">Fiche du Patient</h2>
    <?php if ($alert): ?>
        <?php echo $alert; ?>
    <?php elseif ($patient): ?>
    <div class="patient-info-block mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="label-info">N° Dossier</div>
                <div><?= htmlspecialchars($patient['numero_dossier']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="label-info">Nom</div>
                <div><?= htmlspecialchars($patient['nom']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="label-info">Prénom</div>
                <div><?= htmlspecialchars($patient['prenom']) ?></div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="label-info">Date de naissance</div>
                <div><?= htmlspecialchars($patient['date_naissance']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="label-info">Sexe</div>
                <div><?= htmlspecialchars($patient['sexe']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="label-info">Poids</div>
                <div><?= ($patient['poids'] !== null && $patient['poids'] !== "") ? htmlspecialchars($patient['poids']).' kg' : "<span class='badge badge-warning'>Non renseigné</span>" ?></div>
            </div>
            <div class="col-md-3">
                <div class="label-info">Taille</div>
                <div><?= ($patient['taille'] !== null && $patient['taille'] !== "") ? htmlspecialchars($patient['taille']).' cm' : "<span class='badge badge-warning'>Non renseignée</span>" ?></div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="label-info">Téléphone</div>
                <div><?= $patient['telephone'] ? htmlspecialchars($patient['telephone']) : "<span class='badge badge-warning'>Non renseigné</span>" ?></div>
            </div>
            <div class="col-md-3">
                <div class="label-info">Email</div>
                <div><?= $patient['email'] ? htmlspecialchars($patient['email']) : "<span class='badge badge-warning'>Non renseigné</span>" ?></div>
            </div>
            <div class="col-md-3">
                <div class="label-info">Adresse</div>
                <div><?= $patient['adresse'] ? htmlspecialchars($patient['adresse']) : "<span class='badge badge-warning'>Non renseignée</span>" ?></div>
            </div>
            <div class="col-md-3">
                <div class="label-info">Groupe sanguin</div>
                <div><?= $patient['groupe_sanguin'] ? htmlspecialchars($patient['groupe_sanguin']) : "<span class='badge badge-warning'>Non renseigné</span>" ?></div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="label-info">Établissement</div>
                <div><?= $patient['etab_nom'] ? htmlspecialchars($patient['etab_nom']) : "<span class='badge badge-warning'>Non renseigné</span>" ?></div>
            </div>
            <div class="col-md-6">
                <div class="label-info">Date d'enregistrement</div>
                <div><?= isset($patient['date_enregistrement']) && $patient['date_enregistrement'] ? htmlspecialchars($patient['date_enregistrement']) : "<span class='badge badge-warning'>Non renseignée</span>" ?></div>
            </div>
        </div>
    </div>

    <!-- Formulaire de prise de constantes -->
    <div class="constantes-block mb-4">
        <div class="const-title"><i class="fas fa-heartbeat"></i> Prise de constantes du patient</div>
        <form method="post" action="prise_constante.php?id=<?= htmlspecialchars($patient['id']) ?>">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="temperature">Température (°C)</label>
                    <input type="number" step="0.1" min="30" max="45" class="form-control" id="temperature" name="temperature" placeholder="Ex: 37.2">
                </div>
                <div class="form-group col-md-2">
                    <label for="tension_arterielle">TA (mmHg)</label>
                    <input type="text" class="form-control" id="tension_arterielle" name="tension_arterielle" placeholder="Ex: 120/80">
                </div>
                <div class="form-group col-md-2">
                    <label for="frequence_cardiaque">FC (bpm)</label>
                    <input type="number" min="30" max="200" class="form-control" id="frequence_cardiaque" name="frequence_cardiaque" placeholder="Ex: 82">
                </div>
                <div class="form-group col-md-2">
                    <label for="frequence_respiratoire">FR (cpm)</label>
                    <input type="number" min="5" max="60" class="form-control" id="frequence_respiratoire" name="frequence_respiratoire" placeholder="Ex: 18">
                </div>
                <div class="form-group col-md-2">
                    <label for="saturation">SpO2 (%)</label>
                    <input type="number" min="50" max="100" class="form-control" id="saturation" name="saturation" placeholder="Ex: 97">
                </div>
                <div class="form-group col-md-2">
                    <label for="glycemie">Glycémie (g/L)</label>
                    <input type="number" step="0.01" min="0.2" max="5.0" class="form-control" id="glycemie" name="glycemie" placeholder="Ex: 1.05">
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-2"><i class="fas fa-save"></i> Enregistrer les constantes</button>
        </form>
    </div>

    <!-- Tableau des consultations -->
    <div class="mb-3">
        <h4 style="color: var(--blue); font-weight:600;">Consultations</h4>
        <?php if (empty($consultations)): ?>
            <div class="alert alert-secondary">Aucune consultation enregistrée.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-2">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Motif</th>
                        <th>Diagnostic</th>
                        <th>Traitement</th>
                        <th>Observations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultations as $cons): ?>
                    <tr>
                        <td><?= htmlspecialchars($cons['date_consultation']) ?></td>
                        <td><?= htmlspecialchars($cons['motif']) ?></td>
                        <td><?= htmlspecialchars($cons['diagnostic']) ?></td>
                        <td><?= htmlspecialchars($cons['traitement']) ?></td>
                        <td><?= htmlspecialchars($cons['observations']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <a href="consultation.php?patient_id=<?= htmlspecialchars($patient['id']) ?>" class="btn btn-primary mr-2"><i class="fas fa-notes-medical"></i> Nouvelle consultation</a>
    <a href="javascript:window.print()" class="btn btn-success"><i class="fas fa-print"></i> Imprimer</a>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>