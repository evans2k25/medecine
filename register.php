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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécuriser et récupérer les données
    $numero_dossier   = htmlspecialchars(trim($_POST['numeroDossier'] ?? ''));
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

    if (empty($errors)) {
        try {
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
        } catch (PDOException $e) {
            $alert = '<div class="alert alert-danger mt-3">Erreur lors de l\'enregistrement : ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $alert = '<div class="alert alert-warning mt-3"><ul><li>' . implode('</li><li>', $errors) . '</li></ul></div>';
    }

    // ...
if (empty($errors)) {
    try {
        $numero_dossier = genererNumeroDossier($pdo);
        $stmt = $pdo->prepare("
            INSERT INTO patients 
                (numero_dossier, nom, prenom, date_naissance, sexe, poids, taille, adresse, telephone, email, groupe_sanguin, etablissement_id, date_enregistrement)
            VALUES 
                (:numero_dossier, :nom, :prenom, :date_naissance, :sexe, :poids, :taille, :adresse, :telephone, :email, :groupe_sanguin, :etablissement_id, NOW())
        ");
        $stmt->execute([
            ':numero_dossier'   => $numero_dossier,
            // ... autres champs comme avant
        ]);
        // ...
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
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 id="add-patient-title" class="mb-4">Enregistrement d’un Patient</h2>
    <?php if (!empty($alert)) echo $alert; ?>
    <form id="addPatientForm" class="needs-validation" method="POST" novalidate autocomplete="on">
        <div class="row">
            <div class="mb-3 col-md-4">
                <label for="numeroDossier" class="form-label">N° dossier <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="numeroDossier" name="numeroDossier" required placeholder="Ex: D123456" value="<?php echo isset($_POST['numeroDossier']) ? htmlspecialchars($_POST['numeroDossier']) : ''; ?>">
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