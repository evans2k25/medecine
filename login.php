<?php
require_once 'sessions/session_userloged.php';
require_once 'database/db.php';

$alert = '';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $etablissement_id = $_POST['etablissement_id'] ?? '';

    if (empty($username) || empty($password) || empty($etablissement_id)) {
        $alert = '<div class="alert alert-warning mt-2">Veuillez remplir tous les champs.</div>';
    } else {
        // 1. Connexion via la table 'utilisateurs'
        // Correction ici : on ne cherche QUE par email car nom_utilisateur n'existe pas
        $stmt = $pdo->prepare("SELECT *, role AS type_user FROM utilisateurs WHERE email = :username AND actif = 1");
        $stmt->execute([':username' => $username]);
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
            $stmt = $pdo->prepare("SELECT *, fonction AS type_user FROM personnel WHERE email = :username AND etablissement_id = :etablissement_id");
            $stmt->execute([':username' => $username, ':etablissement_id' => $etablissement_id]);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Hôpital Central de la Ville</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
        }
        .login-card {
            max-width: 400px;
            margin: 48px auto;
            box-shadow: 0 2px 20px 0 rgba(44,62,80,0.09);
            border-radius: 14px;
            padding: 32px 28px;
            background: #fff;
        }
        .login-title {
            color: #1877f2;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .header-content h1 {
            color: #1877f2;
            margin-bottom: 0;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .header-content p {
            color: #3d3d3d;
            margin-bottom: 0;
        }
        .header-actions {
            text-align: right;
        }
        .header-actions .btn {
            margin-top: 12px;
        }
        nav {
            background: #e9ecef;
            padding: 10px 0;
            margin-bottom: 0px;
        }
        nav ul {
            list-style-type: none;
            display: flex;
            justify-content: center;
            gap: 32px;
            margin: 0;
            padding: 0;
        }
        nav ul li a {
            color: #1877f2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        nav ul li a:hover {
            color: #0d5bc4;
        }
        footer {
            text-align: center;
            margin-top: 32px;
            color: #777;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <header class="container py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-content">
                <h1>Hôpital Central de la Ville</h1>
                <p>Votre santé, notre priorité</p>
            </div>
            <div class="header-actions">
                <a href="index.php" class="btn btn-outline-primary">Accueil</a>
            </div>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="index.html#services">Nos Services</a></li>
            <li><a href="index.html#about">À Propos</a></li>
            <li><a href="index.html#contact">Contact</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <div class="login-card shadow">
                <h2 class="login-title mb-3">Connexion</h2>
                <?= $alert ?>
                <form action="" method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label">Email</label>
                        <input type="email" class="form-control" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="etablissement_id" class="form-label">Établissement</label>
                        <select id="etablissement_id" name="etablissement_id" class="form-select" required>
                            <option value="">-- Sélectionnez --</option>
                            <?php foreach ($etabs as $etab): ?>
                                <option value="<?= htmlspecialchars($etab['id']) ?>"
                                    <?= (isset($_POST['etablissement_id']) && $_POST['etablissement_id'] == $etab['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($etab['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="submit-form" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="mt-4">
        <p>&copy; 2023 Hôpital Central de la Ville. Tous droits réservés.</p>
    </footer>
</body>
</html>