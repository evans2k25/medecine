<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';

// Récupération des infos utilisateur
$user_id = $_SESSION['user_id'] ?? ($_SESSION['evaans_users_auth'] ?? null);
$user_infos = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user_infos = $stmt->fetch();
}

// Traitement du formulaire de modification
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirmer_mdp = $_POST['confirmer_mdp'] ?? '';

    // Mise à jour des infos de profil
    if (!empty($nom) && !empty($prenom) && !empty($email)) {
        $update_fields = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email];
        $update_query = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email";

        // Si l'utilisateur veut changer le mot de passe
        if ($ancien_mdp || $nouveau_mdp || $confirmer_mdp) {
            if (empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirmer_mdp)) {
                $alert = '<div class="alert alert-warning">Veuillez remplir tous les champs de mot de passe pour changer le mot de passe.</div>';
            } elseif (!password_verify($ancien_mdp, $user_infos['mot_de_passe'])) {
                $alert = '<div class="alert alert-danger">Ancien mot de passe incorrect.</div>';
            } elseif ($nouveau_mdp !== $confirmer_mdp) {
                $alert = '<div class="alert alert-warning">Les deux nouveaux mots de passe ne correspondent pas.</div>';
            } elseif (strlen($nouveau_mdp) < 6) {
                $alert = '<div class="alert alert-warning">Le nouveau mot de passe doit contenir au moins 6 caractères.</div>';
            } else {
                $update_query .= ", mot_de_passe = :mot_de_passe";
                $update_fields['mot_de_passe'] = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            }
        }

        $update_query .= " WHERE id = :id";
        $update_fields['id'] = $user_id;

        if (empty($alert)) {
            $stmt = $pdo->prepare($update_query);
            $stmt->execute($update_fields);
            $alert = '<div class="alert alert-success">Profil mis à jour avec succès.</div>';
            // Rafraîchir les infos utilisateur
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $user_infos = $stmt->fetch();
        }
    } else {
        $alert = '<div class="alert alert-warning">Veuillez remplir tous les champs principaux.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres du compte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f6f7fb; }
        .container-settings {
            max-width: 600px;
            margin: 48px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 10px 0 rgba(44,62,80,0.09);
            padding: 36px 32px 28px 32px;
        }
        .settings-title {
            color: #1877f2;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        label { font-weight: 500; }
        .form-control:disabled, .form-control[readonly] {
            background-color: #f2f2f2;
            opacity: 1;
        }
        @media (max-width: 767px) {
            .container-settings {
                padding: 20px 3vw;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'inclusions/header.php'; ?>
    <div class="d-flex" style="min-height: 100vh;">
        <aside class="sidebar">
            <?php require_once 'inclusions/sidebar.php'; ?>
        </aside>
        <main class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="container-settings w-100">
                <h2 class="settings-title">
                    <i class="fas fa-cog"></i> Paramètres du compte
                </h2>
                <?= $alert ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required
                               value="<?= htmlspecialchars($user_infos['nom'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required
                               value="<?= htmlspecialchars($user_infos['prenom'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= htmlspecialchars($user_infos['email'] ?? '') ?>">
                    </div>
                    <hr class="my-4">
                    <h5 class="mb-3"><i class="fas fa-key"></i> Changer le mot de passe</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ancien_mdp" class="form-label">Ancien mot de passe</label>
                            <input type="password" class="form-control" id="ancien_mdp" name="ancien_mdp" autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nouveau_mdp" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="nouveau_mdp" name="nouveau_mdp" autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="confirmer_mdp" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirmer_mdp" name="confirmer_mdp" autocomplete="off">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 w-100">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </form>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>