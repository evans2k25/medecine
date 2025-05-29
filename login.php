<?php
session_start();

require_once '../database/db.php';

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $alert = '<div class="alert alert-warning mt-2">Veuillez remplir tous les champs.</div>';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email AND actif = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: dashboard.php');
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
    <title>Connexion utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container" style="max-width:400px;">
    <h2 class="mt-5 mb-4 text-center">Connexion</h2>
    <?php if ($alert) echo $alert; ?>
    <form method="POST" class="needs-validation" novalidate>
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
            <div class="invalid-feedback">Veuillez saisir votre email.</div>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="invalid-feedback">Veuillez saisir votre mot de passe.</div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>
    <div class="text-center mt-3">
        <a href="inscription.php" class="btn btn-link">S'inscrire</a>
    </div>
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