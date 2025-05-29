 <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPatientModalLabel">Ajouter un Nouveau Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPatientForm">
                        <div class="mb-3">
                            <label for="patientNomComplet" class="form-label">Nom Complet</label>
                            <input type="text" class="form-control" id="patientNomComplet" required>
                        </div>
                        <div class="mb-3">
                            <label for="patientDateNaissance" class="form-label">Date de Naissance</label>
                            <input type="date" class="form-control" id="patientDateNaissance" required>
                        </div>
                        <div class="mb-3">
                            <label for="patientGenre" class="form-label">Genre</label>
                            <select class="form-select" id="patientGenre">
                                <option selected disabled value="">Choisir...</option>
                                <option value="homme">Homme</option>
                                <option value="femme">Femme</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="patientAdresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="patientAdresse">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patientTelephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="patientTelephone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patientEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="patientEmail">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="patientNotes" class="form-label">Notes (Antécédents, allergies, etc.)</label>
                            <textarea class="form-control" id="patientNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="addPatientForm" class="btn btn-primary">Ajouter Patient</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Patient Modal -->
    <div class="modal fade" id="viewPatientModal" tabindex="-1" aria-labelledby="viewPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPatientModalLabel">Détails du Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-user-circle fa-5x text-secondary"></i>
                            </div>
                            <h5 class="text-center" id="viewPatientName">Alice Dubois</h5>
                            <p class="text-center text-muted">ID: <span id="viewPatientId">P001</span></p>
                        </div>
                        <div class="col-md-8">
                            <h5>Informations Personnelles</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Date de Naissance:</strong> <span id="viewPatientDob">15/05/1985</span></p>
                                    <p><strong>Genre:</strong> Femme</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Téléphone:</strong> +33 6 12 34 56 78</p>
                                    <p><strong>Email:</strong> alice.dubois@example.com</p>
                                </div>
                            </div>
                            <h5>Adresse</h5>
                            <p id="viewPatientAddress">123 Rue de la République, 75001 Paris, France</p>
                            <h5>Notes Médicales</h5>
                            <p id="viewPatientNotes">Allergie à la pénicilline. Antécédents d'asthme.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPatientModalLabel">Modifier les Informations du Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPatientForm">
                        <input type="hidden" id="editPatientId" value="P001">
                        <div class="mb-3">
                            <label for="editPatientNomComplet" class="form-label">Nom Complet</label>
                            <input type="text" class="form-control" id="editPatientNomComplet" value="Alice Dubois" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPatientDateNaissance" class="form-label">Date de Naissance</label>
                            <input type="date" class="form-control" id="editPatientDateNaissance" value="1985-05-15" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPatientGenre" class="form-label">Genre</label>
                            <select class="form-select" id="editPatientGenre">
                                <option value="homme">Homme</option>
                                <option value="femme" selected>Femme</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPatientAdresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="editPatientAdresse" value="123 Rue de la République, 75001 Paris, France">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPatientTelephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="editPatientTelephone" value="+33612345678">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPatientEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editPatientEmail" value="alice.dubois@example.com">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editPatientNotes" class="form-label">Notes (Antécédents, allergies, etc.)</label>
                            <textarea class="form-control" id="editPatientNotes" rows="3">Allergie à la pénicilline. Antécédents d'asthme.</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="editPatientForm" class="btn btn-primary">Enregistrer Modifications</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Patient Modal -->
    <div class="modal fade" id="deletePatientModal" tabindex="-1" aria-labelledby="deletePatientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePatientModalLabel">Confirmation de Suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible.</p>
                    <p><strong>Patient:</strong> Alice Dubois (ID: <span id="deletePatientIdDisplay">P001</span>)</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeletePatientBtn">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Profile Modal -->
    <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userProfileModalLabel">Profil Utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4 class="text-center">Dr. Admin</h4>
                    <p class="text-center text-muted">Administrateur Système</p>
                    
                    <div class="mt-4">
                        <h5>Informations du Compte</h5>
                        <p><strong>Email:</strong> admin@medicalsystem.com</p>
                        <p><strong>Dernière connexion:</strong> 26/10/2023 14:30</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary">Modifier le Profil</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-labelledby="logoutConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutConfirmationModalLabel">Confirmation de Déconnexion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir vous déconnecter du système ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmLogoutBtn">Déconnexion</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Appointment Modal -->
    <div class="modal fade" id="planAppointmentModal" tabindex="-1" aria-labelledby="planAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="planAppointmentModalLabel">Planifier un Rendez-vous</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planAppointmentForm">
                        <div class="mb-3">
                            <label for="appointmentPatient" class="form-label">Patient</label>
                            <select class="form-select" id="appointmentPatient" required>
                                <option value="" selected disabled>Choisir un patient...</option>
                                <option value="P001">Alice Dubois</option>
                                <option value="P002">Bob Martin</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointmentDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="appointmentDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="appointmentTime" class="form-label">Heure</label>
                                <input type="time" class="form-control" id="appointmentTime" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="appointmentType" class="form-label">Type de rendez-vous</label>
                            <select class="form-select" id="appointmentType" required>
                                <option value="" selected disabled>Choisir un type...</option>
                                <option value="consultation">Consultation</option>
                                <option value="followup">Suivi</option>
                                <option value="emergency">Urgence</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="appointmentNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="appointmentNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="planAppointmentForm" class="btn btn-primary">Planifier RDV</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Consultation Modal -->
    <div class="modal fade" id="addConsultationModal" tabindex="-1" aria-labelledby="addConsultationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addConsultationModalLabel">Ajouter une Consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addConsultationForm">
                        <div class="mb-3">
                            <label for="consultationPatient" class="form-label">Patient</label>
                            <select class="form-select" id="consultationPatient" required>
                                <option value="" selected disabled>Choisir un patient...</option>
                                <option value="P001">Alice Dubois</option>
                                <option value="P002">Bob Martin</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="consultationDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="consultationDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="consultationTime" class="form-label">Heure</label>
                                <input type="time" class="form-control" id="consultationTime" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="consultationReason" class="form-label">Motif de consultation</label>
                            <input type="text" class="form-control" id="consultationReason" required>
                        </div>
                        <div class="mb-3">
                            <label for="consultationDiagnosis" class="form-label">Diagnostic</label>
                            <textarea class="form-control" id="consultationDiagnosis" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="consultationTreatment" class="form-label">Traitement prescrit</label>
                            <textarea class="form-control" id="consultationTreatment" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="consultationNotes" class="form-label">Notes complémentaires</label>
                            <textarea class="form-control" id="consultationNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="addConsultationForm" class="btn btn-primary">Enregistrer Consultation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Consultation Modal -->
    <div class="modal fade" id="viewConsultationModal" tabindex="-1" aria-labelledby="viewConsultationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewConsultationModalLabel">Détails de la Consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Patient:</strong> <span id="viewConsultationPatient">Jean Dupont</span></p>
                            <p><strong>Date:</strong> <span id="viewConsultationDate">26/10/2023</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Heure:</strong> <span id="viewConsultationTime">14:00</span></p>
                            <p><strong>Médecin:</strong> <span id="viewConsultationDoctor">Dr. Admin</span></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h5>Motif de consultation</h5>
                        <p id="viewConsultationReason">Douleurs abdominales persistantes depuis 3 jours</p>
                    </div>
                    <div class="mb-3">
                        <h5>Diagnostic</h5>
                        <p id="viewConsultationDiagnosis">Suspicion d'appendicite. Examens complémentaires nécessaires.</p>
                    </div>
                    <div class="mb-3">
                        <h5>Traitement prescrit</h5>
                        <p id="viewConsultationTreatment">Antalgiques (Paracétamol 1g 3x/jour), régime léger, repos. Scanner abdominal programmé.</p>
                    </div>
                    <div class="mb-3">
                        <h5>Notes complémentaires</h5>
                        <p id="viewConsultationNotes">Patient à surveiller. Contacter immédiatement si fièvre ou aggravation des douleurs.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary">Imprimer</button>
                </div>
            </div>
        </div>
    </div>