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
    $email = trim($_POST['email'] ?? '');
    $mdp_defaut = $_POST['mdp_defaut'] ?? '';

    if (!$email || !$mdp_defaut) {
        $alert = '<div class="alert alert-warning mt-2">Veuillez remplir tous les champs.</div>';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM personnel WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $personnel = $stmt->fetch();

        if ($personnel && $mdp_defaut === $personnel['mdp_defaut']) {
            // Connexion réussie
            $_SESSION['personnel_id'] = $personnel['id'];
            $_SESSION['personnel_nom'] = $personnel['nom'];
            $_SESSION['personnel_prenom'] = $personnel['prenom'];
            $_SESSION['personnel_fonction'] = $personnel['fonction'];
            header('Location: dashboard_personnel.php');
            exit();
        } else {
            $alert = '<div class="alert alert-danger mt-2">Identifiants incorrects.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Personnel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container" style="max-width:400px;">
    <h2 class="mt-5 mb-4 text-center">Connexion du personnel</h2>
    <?php if ($alert) echo $alert; ?>
    <form method="POST" class="needs-validation" novalidate>
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
            <div class="invalid-feedback">Veuillez saisir votre email.</div>
        </div>
        <div class="form-group">
            <label for="mdp_defaut">Mot de passe</label>
            <input type="password" class="form-control" id="mdp_defaut" name="mdp_defaut" required>
            <div class="invalid-feedback">Veuillez saisir votre mot de passe.</div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
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