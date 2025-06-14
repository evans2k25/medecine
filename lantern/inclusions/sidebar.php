<?php
$current = basename($_SERVER['PHP_SELF']);

?>
<div class="sidebar-header">
    <h2>Gestion Médicale</h2>
</div>

<nav class="sidebar-nav">
    <ul>
        <li>
            <a href="index.php" class="nav-link <?= $current == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Tableau de Bord
            </a>
        </li>
        <li>
            <a href="Modifier_personnel.php" class="nav-link <?= $current == 'Modifier_personnel.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Mon personnel
            </a>
        </li>
        <li>
            <a href="liste_patient.php" class="nav-link <?= $current == 'liste_patient.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> Patients
            </a>
        </li>
        <li>
            <a href="consultation.php" class="nav-link <?= $current == 'consultation.php' ? 'active' : '' ?>">
                <i class="fas fa-stethoscope"></i> Consultations
            </a>
        </li>
        <li>
            <a href="dossier_medicaux.php" cl0ass="nav-link <?= $current == 'dossier_medicaux.php' ? 'active' : '' ?>">
                <i class="fas fa-file-medical"></i> Dossiers Médicaux
            </a>
        </li>
        <li>
            <a href="reports.php" class="nav-link <?= $current == 'reports.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i> Rapports
            </a>
        </li>
        <li>
            <a href="settings.php" class="nav-link <?= $current == 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Paramètres
            </a>
        </li>
    </ul>
</nav>
