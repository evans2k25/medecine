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
$user = 'root'; // À adapter si besoin
$pass = '';     // À adapter si besoin

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Traitement
$alert = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $alert = '<div class="alert alert-danger mt-3">Aucun patient sélectionné.</div>';
} else {
    $patient_id = intval($_GET['id']);
    // Vérifier que le patient existe et récupérer nom/prenom
    $stmt = $pdo->prepare("SELECT nom, prenom FROM patients WHERE id = :id");
    $stmt->execute(['id' => $patient_id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        $alert = '<div class="alert alert-danger mt-3">Patient introuvable.</div>';
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données POST et valider
            $temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
            $tension_arterielle = isset($_POST['tension_arterielle']) ? trim($_POST['tension_arterielle']) : null;
            $frequence_cardiaque = isset($_POST['frequence_cardiaque']) ? intval($_POST['frequence_cardiaque']) : null;
            $frequence_respiratoire = isset($_POST['frequence_respiratoire']) ? intval($_POST['frequence_respiratoire']) : null;
            $saturation = isset($_POST['saturation']) ? intval($_POST['saturation']) : null;
            $glycemie = isset($_POST['glycemie']) ? floatval($_POST['glycemie']) : null;

            // Contrôles simples
            if (
                $temperature === null || $temperature < 30 || $temperature > 45 ||
                empty($tension_arterielle) ||
                $frequence_cardiaque === null || $frequence_cardiaque < 30 || $frequence_cardiaque > 200 ||
                $frequence_respiratoire === null || $frequence_respiratoire < 5 || $frequence_respiratoire > 60 ||
                $saturation === null || $saturation < 50 || $saturation > 100 ||
                $glycemie === null || $glycemie < 0.2 || $glycemie > 5.0
            ) {
                $alert = '<div class="alert alert-danger mt-3">Veuillez remplir correctement tous les champs de constantes.</div>';
            } else {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO constantes (
                            patient_id, patient_nom, patient_prenom, personnel_id, temperature, tension_arterielle,
                            frequence_cardiaque, frequence_respiratoire, saturation, glycemie, date_prise
                        ) VALUES (
                            :patient_id, :patient_nom, :patient_prenom, :personnel_id, :temperature, :tension_arterielle,
                            :frequence_cardiaque, :frequence_respiratoire, :saturation, :glycemie, NOW()
                        )
                    ");
                    $stmt->execute([
                        'patient_id' => $patient_id,
                        'patient_nom' => $patient['nom'],
                        'patient_prenom' => $patient['prenom'],
                        'personnel_id' => $_SESSION['personnel_id'],
                        'temperature' => $temperature,
                        'tension_arterielle' => $tension_arterielle,
                        'frequence_cardiaque' => $frequence_cardiaque,
                        'frequence_respiratoire' => $frequence_respiratoire,
                        'saturation' => $saturation,
                        'glycemie' => $glycemie
                    ]);
                    // Redirection vers la fiche patient avec message de succès
                    header('Location: fiche_patient.php?id=' . $patient_id . '&constantes=ok');
                    exit();
                } catch (PDOException $e) {
                    $alert = '<div class="alert alert-danger mt-3">Erreur lors de l\'enregistrement : ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
        }
    }
}

// Affichage si erreur (non utilisé lors de la redirection)
if ($alert) {
    echo $alert;
    echo '<a href="javascript:history.back()" class="btn btn-secondary mt-2">Retour</a>';
}
?>