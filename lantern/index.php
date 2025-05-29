<?php
require_once '../sessions/session_userunloged_admin.php';
require_once '../database/db.php';
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
                <section id="dashboard" class="content-section active">
                    <h2>Tableau de Bord</h2>
                    <div class="stats-cards">
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-users"></i></div>
                            <div class="card-content">
                                <h3>Total Patients</h3>
                                <p>1,234</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-content">
                                <h3>Rendez-vous Aujourd'hui</h3>
                                <p>27</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-folder-open"></i></div>
                            <div class="card-content">
                                <h3>Dossiers Récents</h3>
                                <p>15</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="card-content">
                                <h3>Alertes</h3>
                                <p>3</p>
                            </div>
                        </div>
                    </div>
                    <div class="recent-activity">
                        <h3>Activité Récente</h3>
                        <ul>
                            <li><i class="fas fa-user-plus activity-icon"></i> Nouveau patient ajouté : Jean Dupont</li>
                            <li><i class="fas fa-calendar-plus activity-icon"></i> Rendez-vous programmé : Marie Curie - 14:00</li>
                            <li><i class="fas fa-file-alt activity-icon"></i> Dossier mis à jour : Paul Martin</li>
                        </ul>
                    </div>
                </section>

                <section id="patients" class="content-section">
                    <h2>Gestion des Patients</h2>
                    <div class="toolbar">
                        <input type="text" placeholder="Rechercher un patient..." class="form-control search-input">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal"><i
                                class="fas fa-plus"></i> Ajouter Patient</button>
                    </div>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Patient</th>
                                    <th>Nom Complet</th>
                                    <th>Date de Naissance</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>P001</td>
                                    <td>Alice Dubois</td>
                                    <td>15/05/1985</td>
                                    <td>alice.dubois@example.com</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info view-patient-btn" title="Voir"
                                            data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                                            data-patient-id="P001"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-primary edit-patient-btn" title="Modifier"
                                            data-bs-toggle="modal" data-bs-target="#editPatientModal"
                                            data-patient-id="P001"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete-patient-btn"
                                            title="Supprimer" data-bs-toggle="modal"
                                            data-bs-target="#deletePatientModal" data-patient-id="P001"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>P002</td>
                                    <td>Bob Martin</td>
                                    <td>22/11/1990</td>
                                    <td>bob.martin@example.com</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info view-patient-btn" title="Voir"
                                            data-bs-toggle="modal" data-bs-target="#viewPatientModal"
                                            data-patient-id="P002"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-primary edit-patient-btn" title="Modifier"
                                            data-bs-toggle="modal" data-bs-target="#editPatientModal"
                                            data-patient-id="P002"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete-patient-btn"
                                            title="Supprimer" data-bs-toggle="modal"
                                            data-bs-target="#deletePatientModal" data-patient-id="P002"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="appointments" class="content-section">
                    <h2>Gestion des Rendez-vous</h2>
                    <div class="toolbar">
                        <input type="date" class="form-control date-picker">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planAppointmentModal"><i
                                class="fas fa-calendar-plus"></i> Planifier RDV</button>
                    </div>
                    <div class="calendar-placeholder">
                        <p>[Calendrier des rendez-vous sera affiché ici]</p>
                        <ul class="appointment-list list-group">
                            <li class="list-group-item"><strong>10:00 - Dr. Admin:</strong> Jean Dupont (Consultation)</li>
                            <li class="list-group-item"><strong>11:30 - Dr. Admin:</strong> Marie Curie (Suivi)</li>
                            <li class="list-group-item"><strong>14:00 - Dr. Admin:</strong> Pierre Bernard (Nouveau Patient)</li>
                        </ul>
                    </div>
                </section>

                <section id="consultations" class="content-section">
                    <h2>Gestion des Consultations</h2>
                    <div class="toolbar">
                        <input type="text" placeholder="Filtrer par patient, date, motif..."
                            class="form-control search-input" id="filterConsultationsInput">
                        <button class="btn btn-primary" id="addConsultationBtn" data-bs-toggle="modal"
                            data-bs-target="#addConsultationModal"><i class="fas fa-plus"></i> Ajouter Consultation</button>
                    </div>
                    <div class="consultations-list-placeholder">
                        <p>[Liste des consultations sera affichée ici]</p>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Consultation
                                #C001 - Patient: Jean Dupont - 26/10/2023 - Suivi <button
                                    class="btn btn-sm btn-outline-info" title="Détails" data-bs-toggle="modal"
                                    data-bs-target="#viewConsultationModal" data-consultation-id="C001"><i
                                        class="fas fa-eye"></i></button></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Consultation
                                #C002 - Patient: Marie Curie - 27/10/2023 - Nouvelle plainte <button
                                    class="btn btn-sm btn-outline-info" title="Détails" data-bs-toggle="modal"
                                    data-bs-target="#viewConsultationModal" data-consultation-id="C002"><i
                                        class="fas fa-eye"></i></button></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Consultation
                                #C003 - Patient: Paul Martin - 28/10/2023 - Examen de routine <button
                                    class="btn btn-sm btn-outline-info" title="Détails" data-bs-toggle="modal"
                                    data-bs-target="#viewConsultationModal" data-consultation-id="C003"><i
                                        class="fas fa-eye"></i></button></li>
                        </ul>
                    </div>
                </section>

                <section id="records" class="content-section">
                    <h2>Dossiers Médicaux</h2>
                    <div class="alert alert-info">
                        Cette section est en cours de développement. Elle affichera bientôt la liste complète des dossiers médicaux.
                    </div>
                </section>

                <section id="reports" class="content-section">
                    <h2>Rapports</h2>
                    <div class="alert alert-info">
                        Cette section est en cours de développement. Elle affichera bientôt les rapports statistiques.
                    </div>
                </section>

                <section id="settings" class="content-section">
                    <h2>Paramètres</h2>
                    <div class="settings-form">
                        <div class="mb-3">
                            <label for="systemLanguage" class="form-label">Langue du système</label>
                            <select class="form-select" id="systemLanguage">
                                <option value="fr" selected>Français</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="themePreference" class="form-label">Thème</label>
                            <select class="form-select" id="themePreference">
                                <option value="light" selected>Clair</option>
                                <option value="dark">Sombre</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">Enregistrer les paramètres</button>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Modals -->
   <?php require_once 'inclusions/modal.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* Navigation entre les sections
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Masquer toutes les sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Désactiver tous les liens
                document.querySelectorAll('.nav-link').forEach(navLink => {
                    navLink.classList.remove('active');
                });
                
                // Activer le lien cliqué
                this.classList.add('active');
                
                // Afficher la section correspondante
                const target = this.getAttribute('href');
                document.querySelector(target).classList.add('active');
            });
        });

        // Gestion du menu mobile
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');

        mobileMenuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Gestion des boutons de suppression de patient
        document.querySelectorAll('.delete-patient-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const patientId = this.getAttribute('data-patient-id');
                document.getElementById('deletePatientIdDisplay').textContent = patientId;
            });
        });

        // Confirmation de suppression
        document.getElementById('confirmDeletePatientBtn').addEventListener('click', function() {
            alert('Patient supprimé avec succès (simulation)');
            document.querySelector('#deletePatientModal .btn-close').click();
        });

        // Confirmation de déconnexion
        document.getElementById('confirmLogoutBtn').addEventListener('click', function() {
            alert('Déconnexion réussie (simulation)');
            // Redirection vers la page de login
            window.location.href = 'login.html';
        });

        // Simulation de chargement des données du patient pour la visualisation
        document.querySelectorAll('.view-patient-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const patientId = this.getAttribute('data-patient-id');
                // En réalité, vous feriez une requête AJAX pour récupérer les données
                console.log(`Chargement des données pour le patient ${patientId}`);
            });
        });

        // Simulation de chargement des données de consultation
        document.querySelectorAll('[data-bs-target="#viewConsultationModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const consultationId = this.getAttribute('data-consultation-id');
                console.log(`Chargement des données pour la consultation ${consultationId}`);
            });
        });*/
    </script>
</body>
</html>