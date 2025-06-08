<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';

// Récupérer l'établissement de l'utilisateur connecté
$etablissement_id = $_SESSION['etablissement_id'] ?? null;
$etablissement_nom = '';
if ($etablissement_id) {
    $stmt = $pdo->prepare("SELECT nom FROM etab_enreg WHERE id = :id");
    $stmt->execute([':id' => $etablissement_id]);
    $row = $stmt->fetch();
    if ($row) {
        $etablissement_nom = $row['nom'];
    }
}

$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom           = trim($_POST['nom'] ?? '');
    $prenom        = trim($_POST['prenom'] ?? '');
    $fonction      = trim($_POST['fonction'] ?? '');
    $telephone     = trim($_POST['telephone'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $mdp_defaut    = trim($_POST['mdp_defaut'] ?? '');
    $date_embauche = trim($_POST['date_embauche'] ?? '');

    $errors = [];
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$prenom) $errors[] = "Le prénom est obligatoire.";
    if (!$fonction) $errors[] = "La fonction est obligatoire.";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email est invalide.";
    if ($telephone && !preg_match('/^(\+?\d{1,3}[- ]?)?\d{8,14}$/', $telephone)) $errors[] = "Le téléphone est invalide.";
    if (!$mdp_defaut) $errors[] = "Le mot de passe par défaut est obligatoire.";
    if (!$etablissement_id) $errors[] = "Impossible de déterminer votre établissement. Veuillez vous reconnecter.";

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
                ':etablissement_id' => $etablissement_id
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Dossiers Médicaux - Admin</title>
    <?php require_once 'inclusions/head.php'; ?>
    <style>
        .centered-form-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .centered-form-card {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06), 0 0.5px 1.5px rgba(0,0,0,0.03);
            border-radius: 18px;
            background: #fff;
        }
        @media (max-width: 700px) {
            .centered-form-card {
                padding: 0 8px;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <?php require_once 'inclusions/header.php'; ?>

        <div class="overlay"></div>

        <div class="dashboard-body">
            <aside class="sidebar">
                <?php require_once 'inclusions/sidebar.php'; ?>
            </aside>

            <main class="main-content">
                <!-- Affichage du nom de l'établissement courant -->
                <?php if ($etablissement_nom): ?>
                    <div class="alert alert-info mb-4 text-center">Établissement courant : <strong><?= htmlspecialchars($etablissement_nom) ?></strong></div>
                <?php endif; ?>

                <div class="centered-form-container">
                    <div class="card centered-form-card">
                        <div class="card-body">
                            <h2 class="mb-4 text-center">Ajouter un membre du personnel</h2>
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
                                        <?php
                                        // Récupère le nom de l'établissement à partir de l'id
                                        $etab_nom = '';
                                        if (!empty($etablissement_id)) {
                                            $stmtEtab = $pdo->prepare("SELECT nom FROM etab_enreg WHERE id = :id");
                                            $stmtEtab->execute([':id' => $etablissement_id]);
                                            $rowEtab = $stmtEtab->fetch();
                                            if ($rowEtab) {
                                                $etab_nom = $rowEtab['nom'];
                                            }
                                        }
                                        ?>
                                        <input type="text" class="form-control" id="etablissement_nom" name="etablissement_nom" value="<?php echo htmlspecialchars($etab_nom); ?>" readonly>
                                        <input type="hidden" name="etablissement_id" value="<?php echo htmlspecialchars($etablissement_id); ?>">
                                        <small class="form-text text-muted">Établissement automatiquement sélectionné.</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center gap-3 mt-3">
                                    <button type="submit" class="btn btn-success">Ajouter</button>
                                    <a href="Modifier_personnel.php" class="btn btn-danger">Retour</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Modals -->
    <?php require_once 'inclusions/modal.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ... scripts JS ... -->
</body>

</html>