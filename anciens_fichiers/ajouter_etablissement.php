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




$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom'] ?? '');
    $type        = trim($_POST['type'] ?? '');
    $adresse     = trim($_POST['adresse'] ?? '');
    $telephone   = trim($_POST['telephone'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $responsable = trim($_POST['responsable'] ?? '');

    $errors = [];
    if (!$nom) $errors[] = "Le nom de l'établissement est obligatoire.";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Adresse email invalide.";
    if ($telephone && !preg_match('/^(\+?\d{1,3}[- ]?)?\d{8,14}$/', $telephone)) $errors[] = "Numéro de téléphone invalide.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO etab_enreg (nom, type, adresse, telephone, email, responsable)
                VALUES (:nom, :type, :adresse, :telephone, :email, :responsable)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':type' => $type,
                ':adresse' => $adresse,
                ':telephone' => $telephone ?: null,
                ':email' => $email ?: null,
                ':responsable' => $responsable ?: null
            ]);
            $alert = '<div class="alert alert-success mt-3">Établissement ajouté avec succès !</div>';
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
    <title>Ajouter un établissement sanitaire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:600px;">
    <h2 class="mb-4">Ajouter un établissement sanitaire</h2>
    <?php if ($alert) echo $alert; ?>
    <form method="POST" class="needs-validation" novalidate>
        <div class="form-group">
            <label for="nom">Nom de l'établissement <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
            <div class="invalid-feedback">Veuillez saisir le nom de l'établissement.</div>
        </div>
        <div class="form-group">
            <label for="type">Type d'établissement</label>
            <select class="form-control" id="type" name="type">
                <option value="">-- Sélectionner --</option>
                <option value="Hôpital" <?php if(isset($_POST['type']) && $_POST['type']=='Hôpital') echo 'selected'; ?>>Hôpital</option>
                <option value="Clinique" <?php if(isset($_POST['type']) && $_POST['type']=='Clinique') echo 'selected'; ?>>Clinique</option>
                <option value="Centre de santé" <?php if(isset($_POST['type']) && $_POST['type']=='Centre de santé') echo 'selected'; ?>>Centre de santé</option>
                <option value="Pharmacie" <?php if(isset($_POST['type']) && $_POST['type']=='Pharmacie') echo 'selected'; ?>>Pharmacie</option>
                <option value="Laboratoire" <?php if(isset($_POST['type']) && $_POST['type']=='Laboratoire') echo 'selected'; ?>>Laboratoire</option>
                <option value="Autre" <?php if(isset($_POST['type']) && $_POST['type']=='Autre') echo 'selected'; ?>>Autre</option>
            </select>
        </div>
        <div class="form-group">
            <label for="adresse">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?>">
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="telephone">Téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="email">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="responsable">Nom du responsable</label>
            <input type="text" class="form-control" id="responsable" name="responsable" value="<?php echo isset($_POST['responsable']) ? htmlspecialchars($_POST['responsable']) : ''; ?>">
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