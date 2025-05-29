<?php
session_start();

// Vérifier si l'utilisateur est connecté et est admin (à adapter selon votre gestion des rôles)
if (!isset($_SESSION['user_id']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'medecine';
$user = 'root'; // À adapter
$pass = '';     // À adapter

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Suppression d'un utilisateur (optionnelle)
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $userId = $_GET['delete'];
    if ($userId != $_SESSION['user_id']) { // Empêche de se supprimer soi-même
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        header("Location: liste_utilisateurs.php");
        exit();
    }
}

$utilisateurs = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des utilisateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="dashboard.php">Dashboard</a>
    <div class="ml-auto">
        <span class="navbar-text text-white mr-3">Administration</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
    </div>
</nav>
<div class="container py-4">
    <h2 class="mb-4">Liste des utilisateurs</h2>
    <a href="register.php" class="btn btn-success mb-3">Ajouter un utilisateur</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <?php if ($user['actif']): ?>
                            <span class="badge badge-success">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['date_creation']); ?></td>
                    <td>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="?delete=<?php echo $user['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Confirmer la suppression de cet utilisateur ?');">Supprimer</a>
                        <?php else: ?>
                            <span class="badge badge-info">Vous</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($utilisateurs)): ?>
                <tr>
                    <td colspan="7" class="text-center">Aucun utilisateur trouvé.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>