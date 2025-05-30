<?php
require_once 'sessions/session_userloged.php';
require_once 'database/db.php';

$alert = '';

// Si le formulaire est soumis
if (isset($_POST['submit-form']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $etablissement_id = $_POST['etablissement_id'] ?? '';

    if (empty($email) || empty($password) || empty($etablissement_id)) {
        $alert = '<div class="alert alert-warning mt-2">Veuillez remplir tous les champs.</div>';
    } else {
        // 1. Connexion via la table 'utilisateurs' (optionnel: si utilisateurs ont aussi un etablissement)
        $stmt = $pdo->prepare("SELECT *, role AS type_user FROM utilisateurs WHERE email = :email AND actif = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['evaans_users_auth'] = $user['id'];
            $_SESSION['evaans_users_auth_type'] = [
                'type_user' => strtolower($user['type_user']),
            ];
            $_SESSION['etablissement_id'] = $etablissement_id;

            // Redirection selon le rôle
            $redirectRoles = ['personnel', 'admin', 'medecin'];
            if (in_array(strtolower($user['type_user']), $redirectRoles)) {
                header('Location: lantern/');
                exit();
            }
            header('Location: dashboard.php');
            exit();
        } else {
            // 2. Sinon, tentative via la table 'personnel'
            $stmt = $pdo->prepare("SELECT *, fonction AS type_user FROM personnel WHERE email = :email AND etablissement_id = :etablissement_id");
            $stmt->execute([':email' => $email, ':etablissement_id' => $etablissement_id]);
            $personnel = $stmt->fetch();

            if ($personnel && $password === $personnel['mdp_defaut']) {
                $_SESSION['personnel_id'] = $personnel['id'];
                $_SESSION['personnel_nom'] = $personnel['nom'];
                $_SESSION['personnel_prenom'] = $personnel['prenom'];
                $_SESSION['personnel_fonction'] = $personnel['fonction'];
                $_SESSION['etablissement_id'] = $etablissement_id;
                header('Location: dashboard_personnel.php');
                exit();
            } else {
                $alert = '<div class="alert alert-danger mt-2">Email, mot de passe ou établissement incorrect.</div>';
            }
        }
    }
}

// Récupérer la liste des établissements pour le menu déroulant
try {
    $stmt = $pdo->query("SELECT id, nom FROM etab_enreg ORDER BY nom");
    $etabs = $stmt->fetchAll();
} catch (PDOException $e) {
    $etabs = [];
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
                <input type="email" id="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div>
                <label for="etablissement_id">Établissement</label>
                <select id="etablissement_id" name="etablissement_id" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach ($etabs as $etab): ?>
                        <option value="<?= htmlspecialchars($etab['id']) ?>"
                            <?= (isset($_POST['etablissement_id']) && $_POST['etablissement_id'] == $etab['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($etab['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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