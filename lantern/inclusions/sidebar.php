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
            <a href="patients.php" class="nav-link <?= $current == 'patients.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Patients
            </a>
        </li>
        <li>
            <a href="appointments.php" class="nav-link <?= $current == 'appointments.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> Rendez-vous
            </a>
        </li>
        <li>
            <a href="consultations.php" class="nav-link <?= $current == 'consultations.php' ? 'active' : '' ?>">
                <i class="fas fa-stethoscope"></i> Consultations
            </a>
        </li>
        <li>
            <a href="records.php" class="nav-link <?= $current == 'records.php' ? 'active' : '' ?>">
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
