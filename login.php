<?php
require_once 'sessions/session_userloged.php';
require_once 'database/db.php';

$alert = '';
if(isset($_POST['submit-form'])) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("Veuillez remplir tous les champs.");
    }

    // 1. Chercher dans 'utilisateurs'
    $stmt = $pdo->prepare("SELECT *, role AS type_user FROM utilisateurs WHERE email = :email AND actif = 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    $mot_de_passe_utilisateur = null;

    if ($user) {
        $mot_de_passe_utilisateur = $user['mot_de_passe']; // mot de passe depuis 'utilisateurs'
    }

    // 2. Sinon chercher dans 'personnels'
    if (!$user) {
        $stmt = $pdo->prepare("SELECT *, fonction AS type_user FROM personnels WHERE email = :email AND actif = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        $mot_de_passe_utilisateur = $user ? $user['mdp_defaut'] : null;
    }

    // 3. Vérifier le mot de passe
    if ($user && password_verify($password, $mot_de_passe_utilisateur)) {
        $_SESSION['evaans_users_auth'] = $user['id'];
        $_SESSION['evaans_users_auth_type'] = [
            'type_user' => strtolower($user['type_user']), // peut être 'utilisateur', 'personnel', 'admin', etc.
        ];

        // Redirection selon la fonction ou type_user
        $redirectRoles = ['personnel', 'admin','medecin']; // à adapter selon les rôles autorisés
        if (in_array(strtolower($user['type_user']), $redirectRoles)) {
            header('Location: lantern/');
        }
        var_dump($user['type_user']);
        exit;
    } else {
        $alert = "Email ou mot de passe incorrect.";
    }
}
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container">
        <h2>Connexion</h2>
        <?php if ($alert) echo $alert; ?>
        <form method="POST" novalidate>
            <div>
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn" name="submit-form">Se connecter</button>
        </form>
        <div class="text-center">
            <a href="inscription.php" class="btn-link">S'inscrire</a>
        </div>
    </div>
</body>

</html>