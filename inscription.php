<?php
session_start();

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

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $role = $_POST['role'] ?? 'medecin';

    // Validation
    $errors = [];
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$email) $errors[] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (!$password) $errors[] = "Le mot de passe est obligatoire.";
    elseif ($password !== $password_confirm) $errors[] = "Les mots de passe ne correspondent pas.";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";

    // Vérifier unicité email
    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif) VALUES (:nom, :email, :mot_de_passe, :role, 1)");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':mot_de_passe' => $hash,
                ':role' => $role
            ]);
            $alert = '<div class="alert alert-success mt-3">Inscription réussie ! <a href="login.php">Connectez-vous</a></div>';
        } catch (PDOException $e) {
            $alert = '<div class="alert alert-danger mt-3">Erreur lors de l\'inscription : ' . htmlspecialchars($e->getMessage()) . '</div>';
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
    <title>Inscription utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container" style="max-width:500px;">
    <h2 class="mt-5 mb-4 text-center">Inscription</h2>
    <?php if ($alert) echo $alert; ?>
    <form method="POST" class="needs-validation" novalidate>
        <div class="form-group">
            <label for="nom">Nom complet</label>
            <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
            <div class="invalid-feedback">Veuillez saisir votre nom complet.</div>
        </div>
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <div class="invalid-feedback">Veuillez saisir une adresse email valable.</div>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="invalid-feedback">Veuillez saisir un mot de passe.</div>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            <div class="invalid-feedback">Veuillez confirmer votre mot de passe.</div>
        </div>
        <div class="form-group">
            <label for="role">Rôle</label>
            <select name="role" id="role" class="form-control">
                <option value="medecin" <?php if(!isset($_POST['role']) || $_POST['role']=='medecin') echo 'selected'; ?>>Médecin</option>
                <option value="secretaire" <?php if(isset($_POST['role']) && $_POST['role']=='secretaire') echo 'selected'; ?>>Secrétaire</option>
                <option value="admin" <?php if(isset($_POST['role']) && $_POST['role']=='admin') echo 'selected'; ?>>Administrateur</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success btn-block">S'inscrire</button>
        <p class="mt-3 text-center"><a href="login.php">Déjà inscrit ? Se connecter</a></p>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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