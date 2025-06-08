<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';

// Connexion à la base de données
$host = 'localhost';
$dbname = 'medecine';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Récupérer la liste des patients
try {
    $stmt = $pdo->query("
        SELECT p.*, e.nom AS etab_nom 
        FROM patients p 
        LEFT JOIN etab_enreg e ON p.etablissement_id = e.id 
        ORDER BY p.nom, p.prenom
    ");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur lors de la récupération des patients : " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Dossiers Médicaux - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table-actions a { margin-right: 0.5rem; }
        .container-main {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px 0 rgba(44,62,80,0.07);
            margin-top: 36px;
            margin-bottom: 36px;
            padding: 36px 24px 24px 24px;
        }
        body { background: #f9fafc; }
        .page-title { color: #1877f2; font-weight: 700; letter-spacing: 1px; }
        .search-input {
            max-width: 250px;
        }
        .badge-patients {
            background: #114488;
            color: #fff;
            padding: 8px 18px;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 600;
        }
        .table-responsive {
            width: 100%;
        }
        @media (max-width: 900px) {
            .container-main {
                padding: 16px 2px 8px 2px;
            }
            .table-responsive {
                min-width: unset !important;
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

            <main class="content flex-grow-1 margin-left">
                <div class="container-main">
                    <h2 class="mb-4 page-title">
                        <i class="fas fa-users"></i> Liste des patients enregistrés
                    </h2>
                    <div class="mb-3 row align-items-center">
                        <div class="col-md-7 col-12 d-flex gap-2 align-items-center mb-2 mb-md-0">
                            <input type="text" id="search-input" class="form-control search-input" placeholder="Rechercher un patient...">
                            <button class="btn btn-primary d-flex align-items-center gap-1" id="search-btn" type="button">
                                <i class="fas fa-search"></i>
                                <span class="d-none d-md-inline">Rechercher</span>
                            </button>
                        </div>
                        <div class="col-md-5 col-12 d-flex align-items-center gap-2 justify-content-md-end">
                            <a href="ajout_patient.php" class="btn btn-success">
                                <i class="fas fa-user-plus"></i> <span class="d-none d-sm-inline">Ajouter un patient</span>
                            </a>
                            <span class="badge badge-patients"><?php echo count($patients); ?> patients</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover bg-white mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>N° Dossier</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Date naissance</th>
                                    <th>Sexe</th>
                                    <th>Téléphone</th>
                                    <th>Établissement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="patients-table-body">
                                <?php if (empty($patients)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Aucun patient trouvé.</td>
                                    </tr>
                                <?php else: foreach ($patients as $idx => $patient): ?>
                                    <tr>
                                        <td><?php echo $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($patient['numero_dossier']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['sexe']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['telephone']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['etab_nom']); ?></td>
                                        <td class="table-actions">
                                            <a href="fiche_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-info btn-sm" title="Détails"><i class="fas fa-info-circle"></i></a>
                                            <a href="modifier_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                                            <a href="liste_patients.php?delete=<?php echo $patient['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce patient ?');" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="javascript:history.back()" class="btn btn-secondary mt-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </main>
        </div>
    </div>

    <?php require_once 'inclusions/modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('search-input');
        const btn = document.getElementById('search-btn');
        const tbody = document.getElementById('patients-table-body');

        function filterRows() {
            const searchVal = input.value.trim().toLowerCase();
            const rows = tbody.querySelectorAll('tr');
            let found = false;
            rows.forEach(row => {
                // Ignore "Aucun patient trouvé"
                if (row.querySelectorAll('td').length === 1) return;
                const txt = row.textContent.toLowerCase();
                if (txt.includes(searchVal)) {
                    row.style.display = '';
                    found = true;
                } else {
                    row.style.display = 'none';
                }
            });
            // Afficher le message "Aucun patient trouvé" s'il n'y a aucun résultat visible
            let noDataRow = tbody.querySelector('.no-data-row');
            if (!found) {
                if (!noDataRow) {
                    noDataRow = document.createElement('tr');
                    noDataRow.className = 'no-data-row';
                    noDataRow.innerHTML = '<td colspan="9" class="text-center text-muted">Aucun patient trouvé.</td>';
                    tbody.appendChild(noDataRow);
                }
            } else if (noDataRow) {
                noDataRow.remove();
            }
        }

        input.addEventListener('keyup', filterRows);
        btn.addEventListener('click', filterRows);
    });
    </script>
</body>
</html>