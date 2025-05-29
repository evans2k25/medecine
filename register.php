<?php
// --- Connexion à la base de données ---
$host = 'localhost';
$dbname = 'medecine';
$user = 'root'; // À personnaliser
$pass = '';     // À personnaliser

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Fonction pour générer un numéro de dossier unique automatiquement
function genererNumeroDossier(PDO $pdo) {
    $anneeMois = date('Ym'); // ex : 202505
    $stmt = $pdo->prepare("SELECT numero_dossier FROM patients WHERE numero_dossier LIKE ? ORDER BY numero_dossier DESC LIMIT 1");
    $like = "D" . $anneeMois . "%";
    $stmt->execute([$like]);
    $last = $stmt->fetchColumn();

    if ($last) {
        $num = intval(substr($last, 7, 4));
        $num++;
    } else {
        $num = 1;
    }
    return "D" . $anneeMois . str_pad($num, 4, "0", STR_PAD_LEFT);
}

// --- Traitement du formulaire (POST) ---
$alert = '';
// Récupérer la liste des établissements pour la sélection
$etabs = [];
try {
    $etab_stmt = $pdo->query("SELECT id, nom FROM etab_enreg ORDER BY nom");
    $etabs = $etab_stmt->fetchAll();
} catch (PDOException $e) {
    $alert .= '<div class="alert alert-danger mt-3">Erreur lors de la récupération des établissements : ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Générer le numéro de dossier automatiquement si nouveau formulaire ou champ vide
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['numeroDossier'])) {
    $numero_dossier_auto = genererNumeroDossier($pdo);
} else {
    $numero_dossier_auto = htmlspecialchars(trim($_POST['numeroDossier']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécuriser et récupérer les données
    $numero_dossier   = $numero_dossier_auto; // Utilise le numéro généré, même si champ masqué côté client
    $nom              = htmlspecialchars(trim($_POST['patientNom'] ?? ''));
    $prenom           = htmlspecialchars(trim($_POST['patientPrenom'] ?? ''));
    $date_naissance   = $_POST['patientDateNaissance'] ?? '';
    $sexe             = $_POST['patientSexe'] ?? '';
    $poids            = isset($_POST['patientPoids']) && $_POST['patientPoids'] !== '' ? floatval($_POST['patientPoids']) : null;
    $taille           = isset($_POST['patientTaille']) && $_POST['patientTaille'] !== '' ? floatval($_POST['patientTaille']) : null;
    $adresse          = htmlspecialchars(trim($_POST['patientAdresse'] ?? ''));
    $telephone        = htmlspecialchars(trim($_POST['patientTelephone'] ?? ''));
    $email            = htmlspecialchars(trim($_POST['patientEmail'] ?? ''));
    $groupe_sanguin   = $_POST['patientGroupeSanguin'] ?? null;
    $etablissement_id = isset($_POST['etablissement_id']) && $_POST['etablissement_id'] !== '' ? intval($_POST['etablissement_id']) : null;

    // Validation avancée
    $errors = [];
    if (!$numero_dossier) $errors[] = "Le numéro de dossier est obligatoire.";
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$prenom) $errors[] = "Le prénom est obligatoire.";
    if (!$date_naissance) $errors[] = "La date de naissance est obligatoire.";
    if (!$sexe) $errors[] = "Le sexe est obligatoire.";
    if (!$etablissement_id) $errors[] = "L'établissement est obligatoire.";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'adresse email n'est pas valide.";
    if ($telephone && !preg_match('/^(\+?\d{1,3}[- ]?)?\d{8,14}$/', $telephone)) $errors[] = "Le numéro de téléphone n'est pas valide.";
    if ($poids !== null && ($poids < 0 || $poids > 500)) $errors[] = "Le poids doit être compris entre 0 et 500 kg.";
    if ($taille !== null && ($taille < 0 || $taille > 300)) $errors[] = "La taille doit être comprise entre 0 et 300 cm.";

    // ------ Vérification existence patient ------
    if (empty($errors)) {
        try {
            $checkStmt = $pdo->prepare("
                SELECT id FROM patients 
                WHERE numero_dossier = :numero_dossier 
                  AND nom = :nom
                  AND prenom = :prenom
                  AND date_naissance = :date_naissance
                  AND etablissement_id = :etablissement_id
                LIMIT 1
            ");
            $checkStmt->execute([
                ':numero_dossier'   => $numero_dossier,
                ':nom'              => $nom,
                ':prenom'           => $prenom,
                ':date_naissance'   => $date_naissance,
                ':etablissement_id' => $etablissement_id
            ]);
            if ($checkStmt->fetch()) {
                $alert = '<div class="alert alert-warning mt-3">Ce patient existe déjà dans la base de données.</div>';
            } else {
                // Insertion
                $stmt = $pdo->prepare("
                    INSERT INTO patients 
                        (numero_dossier, nom, prenom, date_naissance, sexe, poids, taille, adresse, telephone, email, groupe_sanguin, etablissement_id)
                    VALUES 
                        (:numero_dossier, :nom, :prenom, :date_naissance, :sexe, :poids, :taille, :adresse, :telephone, :email, :groupe_sanguin, :etablissement_id)
                ");
                $stmt->execute([
                    ':numero_dossier'   => $numero_dossier,
                    ':nom'              => $nom,
                    ':prenom'           => $prenom,
                    ':date_naissance'   => $date_naissance,
                    ':sexe'             => $sexe,
                    ':poids'            => $poids,
                    ':taille'           => $taille,
                    ':adresse'          => $adresse,
                    ':telephone'        => $telephone,
                    ':email'            => $email,
                    ':groupe_sanguin'   => $groupe_sanguin ?: null,
                    ':etablissement_id' => $etablissement_id
                ]);
                $alert = '<div class="alert alert-success mt-3">Patient enregistré avec succès !</div>';
                // Après succès, régénère un numéro pour l'ajout suivant
                $numero_dossier_auto = genererNumeroDossier($pdo);
            }
        } catch (PDOException $e) {
            $alert = '<div class="alert alert-danger mt-3">Erreur lors de l\'enregistrement : ' . htmlspecialchars($e->getMessage()) . '</div>';
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
    <title>Enregistrement d’un Patient</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    :root {
      --main-bg: #f9fafc;
      --sidebar-bg: #212a3e;
      --widget-bg: #ffffff;
      --widget-shadow: rgba(44,62,80,0.07);
      --blue: #1877f2;
      --green: #28c76f;
      --orange: #ff9f43;
      --red: #ea5455;
      --gray: #6c757d;
      --text-dark: #212529;
      --text-light: #fff;
      --status-completed: #28c76f;
      --status-pending: #ff9f43;
    }
    body {
      background: var(--main-bg);
    }
    .container {
      background: var(--widget-bg);
      border-radius: 18px;
      box-shadow: 0 2px 12px 0 var(--widget-shadow);
      padding: 38px 36px 32px 36px;
      margin-top: 30px;
      margin-bottom: 30px;
    }
    h2#add-patient-title {
      color: var(--blue);
      font-weight: 700;
      letter-spacing: 1px;
    }
    .form-label {
      color: var(--sidebar-bg);
      font-weight: 500;
    }
    .form-control, .form-select, .custom-select {
      border-radius: 8px;
      border-color: var(--sidebar-bg);
      background: #f9fafc;
      color: var(--text-dark);
    }
    .form-control:focus, .form-select:focus, .custom-select:focus {
      border-color: var(--blue);
      box-shadow: 0 0 0 0.15rem var(--blue, #1877f2, 0.09);
    }
    .btn-success {
      background: var(--green) !important;
      border-color: var(--green) !important;
      color: #fff !important;
    }
    .btn-success:hover {
      background: #1eac5b !important;
      border-color: #1eac5b !important;
    }
    .alert-success { background: var(--green); color: #fff; border: none; }
    .alert-danger { background: var(--red); color: #fff; border: none; }
    .alert-warning { background: var(--orange); color: #fff; border: none; }
    .invalid-feedback { color: var(--red); }
    .was-validated .form-control:invalid, .was-validated .custom-select:invalid {
      background-image: none;
      border-color: var(--red);
      box-shadow: 0 0 0 0.15rem var(--red, #ea5455, 0.09);
    }
    .was-validated .form-control:valid, .was-validated .custom-select:valid {
      border-color: var(--green);
      box-shadow: 0 0 0 0.15rem var(--green, #28c76f, 0.09);
    }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 id="add-patient-title" class="mb-4">Enregistrement d’un Patient</h2>
    <?php if (!empty($alert)) echo $alert; ?>
    <form id="addPatientForm" class="needs-validation" method="POST" novalidate autocomplete="on">
        <div class="row">
            <div class="mb-3 col-md-4">
                <label for="numeroDossier" class="form-label">N° dossier <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="numeroDossier" name="numeroDossier" required readonly value="<?php echo htmlspecialchars($numero_dossier_auto); ?>">
                <div class="invalid-feedback">Veuillez entrer le numéro de dossier du patient.</div>
            </div>
            <div class="mb-3 col-md-4">
                <label for="patientNom" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="patientNom" name="patientNom" required autocomplete="family-name" placeholder="Ex: Dupont" value="<?php echo isset($_POST['patientNom']) ? htmlspecialchars($_POST['patientNom']) : ''; ?>">
                <div class="invalid-feedback">Veuillez entrer le nom du patient.</div>
            </div>
            <div class="mb-3 col-md-4">
                <label for="patientPrenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="patientPrenom" name="patientPrenom" required autocomplete="given-name" placeholder="Ex: Jean" value="<?php echo isset($_POST['patientPrenom']) ? htmlspecialchars($_POST['patientPrenom']) : ''; ?>">
                <div class="invalid-feedback">Veuillez entrer le prénom du patient.</div>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-4">
                <label for="patientDateNaissance" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="patientDateNaissance" name="patientDateNaissance" required autocomplete="bday" value="<?php echo isset($_POST['patientDateNaissance']) ? htmlspecialchars($_POST['patientDateNaissance']) : ''; ?>">
                <div class="invalid-feedback">Veuillez indiquer la date de naissance.</div>
            </div>
            <div class="mb-3 col-md-4">
                <label for="patientSexe" class="form-label">Sexe <span class="text-danger">*</span></label>
                <select class="form-select custom-select" id="patientSexe" name="patientSexe" required>
                    <option value="" disabled <?php if(empty($_POST['patientSexe'])) echo 'selected'; ?>>Choisir...</option>
                    <option value="Homme" <?php if(isset($_POST['patientSexe']) && $_POST['patientSexe']=="Homme") echo 'selected'; ?>>Homme</option>
                    <option value="Femme" <?php if(isset($_POST['patientSexe']) && $_POST['patientSexe']=="Femme") echo 'selected'; ?>>Femme</option>
                    <option value="Autre" <?php if(isset($_POST['patientSexe']) && $_POST['patientSexe']=="Autre") echo 'selected'; ?>>Autre</option>
                </select>
                <div class="invalid-feedback">Veuillez sélectionner le sexe.</div>
            </div>
            <div class="mb-3 col-md-4">
                <label for="etablissement_id" class="form-label">Établissement <span class="text-danger">*</span></label>
                <select class="form-select custom-select" id="etablissement_id" name="etablissement_id" required>
                    <option value="">Sélectionner...</option>
                    <?php foreach ($etabs as $etab): ?>
                        <option value="<?php echo $etab['id']; ?>" <?php if(isset($_POST['etablissement_id']) && $_POST['etablissement_id']==$etab['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($etab['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Veuillez choisir un établissement.</div>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label for="patientPoids" class="form-label">Poids (kg)</label>
                <input type="number" class="form-control" id="patientPoids" name="patientPoids" min="0" max="500" step="0.1" placeholder="Ex: 70.5" value="<?php echo isset($_POST['patientPoids']) ? htmlspecialchars($_POST['patientPoids']) : ''; ?>">
                <div class="invalid-feedback">Veuillez saisir un poids valide.</div>
            </div>
            <div class="mb-3 col-md-3">
                <label for="patientTaille" class="form-label">Taille (cm)</label>
                <input type="number" class="form-control" id="patientTaille" name="patientTaille" min="0" max="300" step="0.1" placeholder="Ex: 175" value="<?php echo isset($_POST['patientTaille']) ? htmlspecialchars($_POST['patientTaille']) : ''; ?>">
                <div class="invalid-feedback">Veuillez saisir une taille valide.</div>
            </div>
            <div class="mb-3 col-md-6">
                <label for="patientAdresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" id="patientAdresse" name="patientAdresse" autocomplete="street-address" placeholder="Ex: 123 rue de Paris" value="<?php echo isset($_POST['patientAdresse']) ? htmlspecialchars($_POST['patientAdresse']) : ''; ?>">
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label for="patientTelephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="patientTelephone" name="patientTelephone" pattern="^(\+?\d{1,3}[- ]?)?\d{8,14}$" autocomplete="tel" placeholder="Ex: 0612345678" value="<?php echo isset($_POST['patientTelephone']) ? htmlspecialchars($_POST['patientTelephone']) : ''; ?>">
                <div class="invalid-feedback">Numéro non valide.</div>
            </div>
            <div class="mb-3 col-md-3">
                <label for="patientEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="patientEmail" name="patientEmail" autocomplete="email" placeholder="Ex: patient@mail.com" value="<?php echo isset($_POST['patientEmail']) ? htmlspecialchars($_POST['patientEmail']) : ''; ?>">
                <div class="invalid-feedback">Adresse email non valide.</div>
            </div>
            <div class="mb-3 col-md-6">
                <label for="patientGroupeSanguin" class="form-label">Groupe sanguin</label>
                <select class="form-select custom-select" id="patientGroupeSanguin" name="patientGroupeSanguin">
                    <option value="" <?php if(empty($_POST['patientGroupeSanguin'])) echo 'selected'; ?>>Non renseigné</option>
                    <option value="A+" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="A+") echo 'selected'; ?>>A+</option>
                    <option value="A-" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="A-") echo 'selected'; ?>>A-</option>
                    <option value="B+" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="B+") echo 'selected'; ?>>B+</option>
                    <option value="B-" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="B-") echo 'selected'; ?>>B-</option>
                    <option value="AB+" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="AB+") echo 'selected'; ?>>AB+</option>
                    <option value="AB-" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="AB-") echo 'selected'; ?>>AB-</option>
                    <option value="O+" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="O+") echo 'selected'; ?>>O+</option>
                    <option value="O-" <?php if(isset($_POST['patientGroupeSanguin']) && $_POST['patientGroupeSanguin']=="O-") echo 'selected'; ?>>O-</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success mt-3">
            <i class="fas fa-user-plus"></i> Enregistrer le Patient
        </button>
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