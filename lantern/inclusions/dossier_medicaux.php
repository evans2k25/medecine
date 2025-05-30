<?php
require_once '../../sessions/session_userunloged_admin.php';
require_once '../../database/db.php';

// Récupération de la liste des patients
try {
    $stmt = $pdo->query("
        SELECT p.id, p.numero_dossier, p.nom, p.prenom, p.date_naissance, p.sexe, p.telephone, p.email, p.adresse
        FROM patients p
        ORDER BY p.nom, p.prenom
    ");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    $patients = [];
    $alert = '<div class="alert alert-danger">Erreur lors de la récupération des patients : ' . htmlspecialchars($e->getMessage()) . '</div>';
}

try {
    $stmt = $pdo->query("
        SELECT 
            p.id,
            p.numero_dossier,
            p.nom,
            p.prenom,
            p.date_naissance,
            p.sexe,
            p.poids AS poids_patient,
            p.taille,
            p.adresse,
            p.telephone,
            p.email,
            p.groupe_sanguin,
            e.nom AS etablissement,
            c.temperature,
            c.tension_arterielle,
            c.frequence_cardiaque,
            c.frequence_respiratoire,
            c.saturation,
            c.glycemie,
            c.date_prise
        FROM patients p
        LEFT JOIN etab_enreg e ON p.etablissement_id = e.id
        LEFT JOIN (
            SELECT co.*
            FROM constantes co
            INNER JOIN (
                SELECT patient_id, MAX(date_prise) as max_date
                FROM constantes
                GROUP BY patient_id
            ) x ON co.patient_id = x.patient_id AND co.date_prise = x.max_date
        ) c ON p.id = c.patient_id
        ORDER BY p.nom, p.prenom
    ");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    $patients = [];
    $alert = '<div class="alert alert-danger">Erreur lors de la récupération des patients : ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="overlay"></div>
        <div class="dashboard-body">
            <main class="main-content">
                <section id="patients" class="content-section active">
                    <h2 class="mb-4">Liste de tous les Patients</h2>
                    <?php if (!empty($alert)) echo $alert; ?>
                    <div class="toolbar mb-3 d-flex flex-wrap gap-2">
                        <input type="text" placeholder="Rechercher un patient..." class="form-control search-input" style="max-width:250px;">
                        <a href="ajout_patient.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Ajouter Patient
                        </a>
                    </div>
                    <div class="table-container">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>N° Dossier</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Date de Naissance</th>
                                    <th>Sexe</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($patients)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Aucun patient trouvé.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($patients as $index => $p): ?>
                                    <?php
                                    // Récupérer la précédente consultation et constantes pour ce patient
                                    $consult = null;
                                    $constantes = null;
                                    try {
                                        $stmtC = $pdo->prepare("
                                            SELECT c.id, c.date_consultation, c.motif, c.observations, 
                                                const.ta, const.pulse, const.temp, const.poids, const.taille
                                            FROM consultations c
                                            LEFT JOIN constantes const ON c.id = const.consultation_id
                                            WHERE c.patient_id = ?
                                            ORDER BY c.date_consultation DESC
                                            LIMIT 1
                                        ");
                                        $stmtC->execute([$p['id']]);
                                        $consult = $stmtC->fetch();
                                    } catch (PDOException $e) {
                                        $consult = null;
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($p['numero_dossier']) ?></td>
                                        <td><?= htmlspecialchars($p['nom']) ?></td>
                                        <td><?= htmlspecialchars($p['prenom']) ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($p['date_naissance']))) ?></td>
                                        <td><?= htmlspecialchars($p['sexe']) ?></td>
                                        <td><?= htmlspecialchars($p['telephone']) ?></td>
                                        <td><?= htmlspecialchars($p['email']) ?></td>
                                        <td>
                                            <button 
                                                class="btn btn-sm btn-outline-info"
                                                title="Voir"
                                                data-bs-toggle="modal"
                                                data-bs-target="#patientModal"
                                                data-id="<?= $p['id'] ?>"
                                                data-numero="<?= htmlspecialchars($p['numero_dossier']) ?>"
                                                data-nom="<?= htmlspecialchars($p['nom']) ?>"
                                                data-prenom="<?= htmlspecialchars($p['prenom']) ?>"
                                                data-date-naissance="<?= htmlspecialchars(date('d/m/Y', strtotime($p['date_naissance']))) ?>"
                                                data-sexe="<?= htmlspecialchars($p['sexe']) ?>"
                                                data-telephone="<?= htmlspecialchars($p['telephone']) ?>"
                                                data-email="<?= htmlspecialchars($p['email']) ?>"
                                                data-adresse="<?= htmlspecialchars($p['adresse']) ?>"
                                                <?php if($consult): ?>
                                                    data-lastconsult-date="<?= htmlspecialchars(date('d/m/Y H:i', strtotime($consult['date_consultation']))) ?>"
                                                    data-lastconsult-motif="<?= htmlspecialchars($consult['motif']) ?>"
                                                    data-lastconsult-obs="<?= htmlspecialchars($consult['observations']) ?>"
                                                    data-ta="<?= htmlspecialchars($consult['ta']) ?>"
                                                    data-pulse="<?= htmlspecialchars($consult['pulse']) ?>"
                                                    data-temp="<?= htmlspecialchars($consult['temp']) ?>"
                                                    data-poids="<?= htmlspecialchars($consult['poids']) ?>"
                                                    data-taille="<?= htmlspecialchars($consult['taille']) ?>"
                                                <?php else: ?>
                                                    data-lastconsult-date=""
                                                    data-lastconsult-motif=""
                                                    data-lastconsult-obs=""
                                                    data-ta=""
                                                    data-pulse=""
                                                    data-temp=""
                                                    data-poids=""
                                                    data-taille=""
                                                <?php endif; ?>
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a class="btn btn-sm btn-outline-primary" title="Modifier"
                                                href="modifier_patient.php?id=<?= $p['id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                href="supprimer_patient.php?id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce patient ?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Modal Patient -->
    <div class="modal fade" id="patientModal" tabindex="-1" aria-labelledby="patientModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="patientModalLabel"><i class="fas fa-user"></i> Informations du Patient</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>N° Dossier :</strong> <span id="modal-numero"></span></li>
                        <li class="list-group-item"><strong>Nom :</strong> <span id="modal-nom"></span></li>
                        <li class="list-group-item"><strong>Prénom :</strong> <span id="modal-prenom"></span></li>
                        <li class="list-group-item"><strong>Date de Naissance :</strong> <span id="modal-date-naissance"></span></li>
                        <li class="list-group-item"><strong>Sexe :</strong> <span id="modal-sexe"></span></li>
                        <li class="list-group-item"><strong>Téléphone :</strong> <span id="modal-telephone"></span></li>
                        <li class="list-group-item"><strong>Email :</strong> <span id="modal-email"></span></li>
                        <li class="list-group-item"><strong>Adresse :</strong> <span id="modal-adresse"></span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-2 bg-light mb-2">
                        <h6 class="mb-2 text-primary"><i class="fas fa-heartbeat"></i> Constantes (précédente consultation)</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>TA :</strong> <span id="modal-ta"></span></li>
                            <li><strong>Pouls :</strong> <span id="modal-pulse"></span></li>
                            <li><strong>Température :</strong> <span id="modal-temp"></span></li>
                            <li><strong>Poids :</strong> <span id="modal-poids"></span></li>
                            <li><strong>Taille :</strong> <span id="modal-taille"></span></li>
                        </ul>
                    </div>
                    <div class="border rounded p-2 bg-light">
                        <h6 class="mb-2 text-primary"><i class="fas fa-notes-medical"></i> Dernière consultation</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Date :</strong> <span id="modal-lastconsult-date"></span></li>
                            <li><strong>Motif :</strong> <span id="modal-lastconsult-motif"></span></li>
                            <li><strong>Observations :</strong> <span id="modal-lastconsult-obs"></span></li>
                        </ul>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var patientModal = document.getElementById('patientModal');
        patientModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('modal-numero').textContent = button.getAttribute('data-numero') || '';
            document.getElementById('modal-nom').textContent = button.getAttribute('data-nom') || '';
            document.getElementById('modal-prenom').textContent = button.getAttribute('data-prenom') || '';
            document.getElementById('modal-date-naissance').textContent = button.getAttribute('data-date-naissance') || '';
            document.getElementById('modal-sexe').textContent = button.getAttribute('data-sexe') || '';
            document.getElementById('modal-telephone').textContent = button.getAttribute('data-telephone') || '';
            document.getElementById('modal-email').textContent = button.getAttribute('data-email') || '';
            document.getElementById('modal-adresse').textContent = button.getAttribute('data-adresse') || '';
            document.getElementById('modal-ta').textContent = button.getAttribute('data-ta') || '-';
            document.getElementById('modal-pulse').textContent = button.getAttribute('data-pulse') || '-';
            document.getElementById('modal-temp').textContent = button.getAttribute('data-temp') || '-';
            document.getElementById('modal-poids').textContent = button.getAttribute('data-poids') || '-';
            document.getElementById('modal-taille').textContent = button.getAttribute('data-taille') || '-';
            document.getElementById('modal-lastconsult-date').textContent = button.getAttribute('data-lastconsult-date') || '-';
            document.getElementById('modal-lastconsult-motif').textContent = button.getAttribute('data-lastconsult-motif') || '-';
            document.getElementById('modal-lastconsult-obs').textContent = button.getAttribute('data-lastconsult-obs') || '-';
        });
    });
    </script>
</body>
</html>