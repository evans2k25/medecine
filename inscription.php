<?php
session_start();
require_once 'database/db.php';

$alert = '';
// Charger les établissements pour la liste déroulante
try {
    $stmt = $pdo->query("SELECT id, nom FROM etab_enreg ORDER BY nom");
    $etabs = $stmt->fetchAll();
} catch (PDOException $e) {
    $etabs = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $role = $_POST['role'] ?? 'medecin';
    $etablissement_id = $_POST['etablissement_id'] ?? '';
    $docteur_hopital = $_POST['docteur_hopital'] ?? '';

    $errors = [];
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$email) $errors[] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (!$password) $errors[] = "Le mot de passe est obligatoire.";
    elseif ($password !== $password_confirm) $errors[] = "Les mots de passe ne correspondent pas.";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    if (empty($etablissement_id)) $errors[] = "L'établissement est obligatoire.";
    if (empty($docteur_hopital)) $errors[] = "Le type de docteur ou spécialiste est obligatoire.";

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
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif, etablissement_id, docteur_hopital) VALUES (:nom, :email, :mot_de_passe, :role, 1, :etablissement_id, :docteur_hopital)");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':mot_de_passe' => $hash,
                ':role' => $role,
                ':etablissement_id' => $etablissement_id,
                ':docteur_hopital' => $docteur_hopital
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

            <label for="etablissement_id">Établissement d'Affectation</label>
            <select name="etablissement_id" id="etablissement_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($etabs as $etab): ?>
                    <option value="<?= htmlspecialchars($etab['id']) ?>"
                        <?= (isset($_POST['etablissement_id']) && $_POST['etablissement_id'] == $etab['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($etab['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="docteur-hopital">Choisissez votre spécialité :</label>
            <select name="docteur_hopital" id="docteur-hopital" required>
                <option value="">-- Veuillez sélectionner --</option>
                <optgroup label="Spécialités Médicales">
                    <option value="cardiologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'cardiologue') ? 'selected' : '' ?>>Cardiologue</option>
                    <option value="pneumologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'pneumologue') ? 'selected' : '' ?>>Pneumologue</option>
                    <option value="gastro_enterologue_hepatologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'gastro_enterologue_hepatologue') ? 'selected' : '' ?>>Gastro-entérologue et Hépatologue</option>
                    <option value="nephrologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'nephrologue') ? 'selected' : '' ?>>Néphrologue</option>
                    <option value="endocrinologue_diabetologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'endocrinologue_diabetologue') ? 'selected' : '' ?>>Endocrinologue-Diabétologue</option>
                    <option value="neurologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'neurologue') ? 'selected' : '' ?>>Neurologue</option>
                    <option value="rhumatologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'rhumatologue') ? 'selected' : '' ?>>Rhumatologue</option>
                    <option value="dermatologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'dermatologue') ? 'selected' : '' ?>>Dermatologue</option>
                    <option value="infectiologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'infectiologue') ? 'selected' : '' ?>>Infectiologue</option>
                    <option value="oncologue_cancerologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'oncologue_cancerologue') ? 'selected' : '' ?>>Oncologue (Cancérologue)</option>
                    <option value="hematologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'hematologue') ? 'selected' : '' ?>>Hématologue</option>
                    <option value="geriatre" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'geriatre') ? 'selected' : '' ?>>Gériatre</option>
                    <option value="pediatre" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'pediatre') ? 'selected' : '' ?>>Pédiatre (incl. néonatalogie)</option>
                    <option value="medecin_interniste" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_interniste') ? 'selected' : '' ?>>Médecin Interniste</option>
                    <option value="allergologue_immunologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'allergologue_immunologue') ? 'selected' : '' ?>>Allergologue / Immunologue</option>
                    <option value="medecin_mpr" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_mpr') ? 'selected' : '' ?>>Médecin en Médecine Physique et de Réadaptation (MPR)</option>
                    <option value="psychiatre" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'psychiatre') ? 'selected' : '' ?>>Psychiatre (incl. pédopsychiatre)</option>
                </optgroup>
                <optgroup label="Spécialités Chirurgicales">
                    <option value="chirurgien_visceral_digestif" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_visceral_digestif') ? 'selected' : '' ?>>Chirurgien Viscéral et Digestif</option>
                    <option value="chirurgien_orthopediste_traumatologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_orthopediste_traumatologue') ? 'selected' : '' ?>>Chirurgien Orthopédiste et Traumatologue</option>
                    <option value="neurochirurgien" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'neurochirurgien') ? 'selected' : '' ?>>Neurochirurgien</option>
                    <option value="chirurgien_cardiaque_thoracique" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_cardiaque_thoracique') ? 'selected' : '' ?>>Chirurgien Cardiaque et Thoracique</option>
                    <option value="chirurgien_vasculaire" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_vasculaire') ? 'selected' : '' ?>>Chirurgien Vasculaire</option>
                    <option value="urologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'urologue') ? 'selected' : '' ?>>Urologue</option>
                    <option value="gynecologue_obstetricien" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'gynecologue_obstetricien') ? 'selected' : '' ?>>Gynécologue-Obstétricien</option>
                    <option value="chirurgien_pediatrique" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_pediatrique') ? 'selected' : '' ?>>Chirurgien Pédiatrique</option>
                    <option value="chirurgien_maxillo_facial_stomatologue" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_maxillo_facial_stomatologue') ? 'selected' : '' ?>>Chirurgien Maxillo-Facial et Stomatologue</option>
                    <option value="ophtalmologue_chirurgien" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'ophtalmologue_chirurgien') ? 'selected' : '' ?>>Ophtalmologue (consultations et chirurgie)</option>
                    <option value="orl_chirurgien" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'orl_chirurgien') ? 'selected' : '' ?>>Oto-Rhino-Laryngologiste (ORL) et Chirurgien Cervico-Facial</option>
                    <option value="chirurgien_plasticien_reconstructeur" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chirurgien_plasticien_reconstructeur') ? 'selected' : '' ?>>Chirurgien Plasticien et Reconstructeur</option>
                </optgroup>
                <optgroup label="Médecins des Plateaux Médico-Techniques">
                    <option value="anesthesiste_reanimateur" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'anesthesiste_reanimateur') ? 'selected' : '' ?>>Anesthésiste-Réanimateur</option>
                    <option value="radiologue_imagerie_medicale" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'radiologue_imagerie_medicale') ? 'selected' : '' ?>>Radiologue et Médecin en Imagerie Médicale</option>
                    <option value="medecin_nucleaire" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_nucleaire') ? 'selected' : '' ?>>Médecin Nucléaire</option>
                    <option value="anatomopathologiste" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'anatomopathologiste') ? 'selected' : '' ?>>Anatomopathologiste</option>
                    <option value="biologiste_medical" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'biologiste_medical') ? 'selected' : '' ?>>Biologiste Médical</option>
                </optgroup>
                <optgroup label="Médecins des Services d'Accueil et d'Urgence">
                    <option value="medecin_urgentiste" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_urgentiste') ? 'selected' : '' ?>>Médecin Urgentiste</option>
                    <option value="medecin_generaliste_urgence_polyvalent" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_generaliste_urgence_polyvalent') ? 'selected' : '' ?>>Médecin Généraliste (Urgences/Polyvalent)</option>
                </optgroup>
                <optgroup label="Autres Profils de Médecins en Milieu Hospitalier">
                    <option value="medecin_douleur" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_douleur') ? 'selected' : '' ?>>Médecin de la Douleur</option>
                    <option value="medecin_soins_palliatifs" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_soins_palliatifs') ? 'selected' : '' ?>>Médecin en Soins Palliatifs</option>
                    <option value="pharmacologue_clinicien" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'pharmacologue_clinicien') ? 'selected' : '' ?>>Pharmacologue Clinicien</option>
                    <option value="medecin_sante_publique_hopital" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_sante_publique_hopital') ? 'selected' : '' ?>>Médecin de Santé Publique (en hôpital)</option>
                    <option value="medecin_travail_hopital" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_travail_hopital') ? 'selected' : '' ?>>Médecin du Travail (pour le personnel hospitalier)</option>
                    <option value="medecin_legiste_hopital" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'medecin_legiste_hopital') ? 'selected' : '' ?>>Médecin Légiste (si service existant)</option>
                </optgroup>
                <optgroup label="Médecins en Formation (Personnel Médical)">
                    <option value="interne_medecine" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'interne_medecine') ? 'selected' : '' ?>>Interne en Médecine</option>
                    <option value="resident_assistant_hopital" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'resident_assistant_hopital') ? 'selected' : '' ?>>Résident / Assistant des Hôpitaux</option>
                    <option value="chef_de_clinique" <?= (isset($_POST['docteur_hopital']) && $_POST['docteur_hopital'] == 'chef_de_clinique') ? 'selected' : '' ?>>Chef de Clinique (CHU)</option>
                </optgroup>
            </select>

            <button type="submit">S'inscrire</button>
            <p class="text-center"><a href="login.php">Déjà inscrit ? Se connecter</a></p>
        </form>
    </div>
</body>
</html>