<?php
session_start();
require_once 'database/db.php';

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $role = $_POST['role'] ?? 'medecin';

    $errors = [];
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$email) $errors[] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (!$password) $errors[] = "Le mot de passe est obligatoire.";
    elseif ($password !== $password_confirm) $errors[] = "Les mots de passe ne correspondent pas.";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif) VALUES (:nom, :email, :mot_de_passe, :role, 1)");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':mot_de_passe' => $hash,
                ':role' => $role
            ]);
            $alert = '<div class="alert success">Inscription réussie ! <a href="login.php">Connectez-vous</a></div>';
        } catch (PDOException $e) {
            $alert = '<div class="alert danger">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $alert = '<div class="alert warning"><ul><li>' . implode('</li><li>', $errors) . '</li></ul></div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/singup.css">

</head>
<body>
<div class="container">
    <h2>Créer un compte</h2>
    <?php if ($alert) echo $alert; ?>
    <form method="POST" novalidate>
        <label for="nom">Nom complet</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" id="password_confirm" name="password_confirm" required>

        <label for="role">Rôle</label>
        <select name="role" id="role">
            <option value="medecin" <?= (!isset($_POST['role']) || $_POST['role'] == 'medecin') ? 'selected' : '' ?>>Médecin</option>
            <option value="secretaire" <?= ($_POST['role'] ?? '') == 'secretaire' ? 'selected' : '' ?>>Secrétaire</option>
            <option value="admin" <?= ($_POST['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select>

        <button type="submit">S'inscrire</button>
        <p class="text-center"><a href="login.php">Déjà inscrit ? Se connecter</a></p>
    </form>
</div>
</body>
</html>
