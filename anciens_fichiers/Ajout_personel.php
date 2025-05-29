<?php
session_start();

// Vérifier si l'utilisateur est connecté (optionnel, à adapter selon votre application)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'medecine';
$user = 'root'; // À adapter selon votre config
$pass = '';     // À adapter selon votre config

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Récupérer la liste des établissements pour le menu déroulant
$stmt = $pdo->query("SELECT id, nom FROM etab_enreg ORDER BY nom");
$etablissements = $stmt->fetchAll();

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom           = trim($_POST['nom'] ?? '');
    $prenom        = trim($_POST['prenom'] ?? '');
    $fonction      = trim($_POST['fonction'] ?? '');
    $telephone     = trim($_POST['telephone'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $mdp_defaut    = trim($_POST['mdp_defaut'] ?? '');
    $date_embauche = trim($_POST['date_embauche'] ?? '');
    $etablissement_id = $_POST['etablissement_id'] ?? null;

    $errors = [];
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$prenom) $errors[] = "Le prénom est obligatoire.";
    if (!$fonction) $errors[] = "La fonction est obligatoire.";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email est invalide.";
    if ($telephone && !preg_match('/^(\+?\d{1,3}[- ]?)?\d{8,14}$/', $telephone)) $errors[] = "Le téléphone est invalide.";
    if (!$mdp_defaut) $errors[] = "Le mot de passe par défaut est obligatoire.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO personnel (nom, prenom, fonction, telephone, email, mdp_defaut, date_embauche, etablissement_id)
                VALUES (:nom, :prenom, :fonction, :telephone, :email, :mdp_defaut, :date_embauche, :etablissement_id)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':fonction' => $fonction,
                ':telephone' => $telephone ?: null,
                ':email' => $email ?: null,
                ':mdp_defaut' => $mdp_defaut,
                ':date_embauche' => $date_embauche ?: null,
                ':etablissement_id' => $etablissement_id ?: null
            ]);
            $alert = '<div class="alert alert-success mt-3">Personnel ajouté avec succès !</div>';
        } catch (PDOException $e) {
            $alert = '<div class="alert alert-danger mt-3">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $alert = '<div class="alert alert-warning mt-3"><ul><li>' . implode('</li><li>', $errors) . '</li></ul></div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un membre du personnel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:600px;">
    <h2 class="mb-4">Ajouter un membre du personnel</h2>
    <?php if ($alert) echo $alert; ?>
    <form method="POST" class="needs-validation" novalidate>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nom">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                <div class="invalid-feedback">Veuillez saisir le nom.</div>
            </div>
            <div class="form-group col-md-6">
                <label for="prenom">Prénom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
                <div class="invalid-feedback">Veuillez saisir le prénom.</div>
            </div>
        </div>
        <div class="form-group">
            <label for="fonction">Fonction <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="fonction" name="fonction" required value="<?php echo isset($_POST['fonction']) ? htmlspecialchars($_POST['fonction']) : ''; ?>">
            <div class="invalid-feedback">Veuillez saisir la fonction.</div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="telephone">Téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="mdp_defaut">Mot de passe par défaut <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="mdp_defaut" name="mdp_defaut" required value="<?php echo isset($_POST['mdp_defaut']) ? htmlspecialchars($_POST['mdp_defaut']) : ''; ?>">
            <div class="invalid-feedback">Veuillez saisir le mot de passe par défaut.</div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="date_embauche">Date d'embauche</label>
                <input type="date" class="form-control" id="date_embauche" name="date_embauche" value="<?php echo isset($_POST['date_embauche']) ? htmlspecialchars($_POST['date_embauche']) : ''; ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="etablissement_id">Établissement</label>
                <select class="form-control" id="etablissement_id" name="etablissement_id">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($etablissements as $etab): ?>
                        <option value="<?php echo $etab['id']; ?>" <?php if (isset($_POST['etablissement_id']) && $_POST['etablissement_id'] == $etab['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($etab['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success mt-3">Ajouter</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Validation Bootstrap
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.forEach.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
</body>
</html>