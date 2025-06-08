<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hôpital Central de la Ville</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f6f7fb;
        }
        .header-content h1 {
            color: #1877f2;
            margin-bottom: 0;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .header-content p {
            color: #3d3d3d;
            margin-bottom: 0;
        }
        .header-actions {
            text-align: right;
        }
        .header-actions .btn, .header-actions .button {
            margin-top: 12px;
        }
        nav {
            background: #e9ecef;
            padding: 10px 0;
            margin-bottom: 0px;
        }
        nav ul {
            list-style-type: none;
            display: flex;
            justify-content: center;
            gap: 32px;
            margin: 0;
            padding: 0;
        }
        nav ul li a {
            color: #1877f2;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        nav ul li a:hover {
            color: #0d5bc4;
        }
        #hero {
            background: linear-gradient(90deg, #e3f0fc 0%, #f6f7fb 100%);
            border-radius: 14px;
            text-align: center;
            margin: 32px auto 40px auto;
            padding: 48px 20px 32px 20px;
            max-width: 700px;
            box-shadow: 0 2px 20px 0 rgba(44,62,80,0.08);
        }
        .service-list {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
            margin-top: 24px;
        }
        .service-item {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px 0 rgba(44,62,80,0.08);
            padding: 24px 20px;
            min-width: 220px;
            max-width: 280px;
            flex: 1 1 220px;
            text-align: center;
        }
        .news-list {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .news-item {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 6px 0 rgba(44,62,80,0.07);
            padding: 20px 18px;
            min-width: 220px;
            max-width: 350px;
            flex: 1 1 220px;
        }
        section {
            margin-bottom: 48px;
        }
        footer {
            text-align: center;
            margin-top: 32px;
            color: #777;
            font-size: 1rem;
        }
        @media (max-width: 768px) {
            .service-list, .news-list {
                flex-direction: column;
                gap: 16px;
            }
            #hero {
                padding: 32px 8px 24px 8px;
            }
        }
    </style>
</head>
<body>
    <header class="container py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="header-content">
                <h1>Établissements de Santé Publics de Côte d'Ivoire</h1>
                <p>Votre santé, notre priorité</p>
            </div>
            <div class="header-actions">
                <a href="login.php" class="btn btn-outline-primary"><i class="fas fa-sign-in-alt me-1"></i> Connexion</a>
            </div>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="#services">Nos Services</a></li>
            <li><a href="#about">À Propos</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <main class="container">
        <section id="hero">
            <h2 class="mb-3">Bienvenue à l'Hôpital Central</h2>
            <p class="mb-0">Découvrez nos soins de qualité et notre équipe dévouée.</p>
        </section>

        <section id="services">
            <h2 class="mb-4 text-center"><i class="fas fa-stethoscope me-2"></i>Nos Services</h2>
            <div class="service-list">
                <div class="service-item">
                    <h3>Urgence 24/7</h3>
                    <p>Accès rapide aux soins d'urgence à tout moment.</p>
                </div>
                <div class="service-item">
                    <h3>Consultations Spécialisées</h3>
                    <p>Large gamme de spécialités médicales disponibles.</p>
                </div>
                <div class="service-item">
                    <h3>Chirurgie</h3>
                    <p>Blocs opératoires modernes et équipes expérimentées.</p>
                </div>
                <div class="service-item">
                    <h3>Imagerie Médicale</h3>
                    <p>Équipements de pointe pour diagnostics précis.</p>
                </div>
            </div>
        </section>

        <section id="about">
            <h2 class="mb-3 text-center"><i class="fas fa-hospital me-2"></i>À Propos de Nous</h2>
            <p>L'Hôpital Central est un établissement de santé leader engagé à fournir les meilleurs soins possibles à la communauté. Fondé en [Année], notre hôpital a une longue histoire d'excellence médicale et d'innovation. Notre équipe est composée de professionnels de la santé hautement qualifiés, dédiés au bien-être de nos patients.</p>
        </section>

        <section id="news">
            <h2 class="mb-4 text-center"><i class="fas fa-newspaper me-2"></i>Actualités & Événements</h2>
            <div class="news-list">
                <article class="news-item">
                    <h3>[Titre de l'actualité]</h3>
                    <p>[Court extrait de l'actualité] <a href="#">Lire la suite...</a></p>
                </article>
                <article class="news-item">
                    <h3>[Titre de l'actualité]</h3>
                    <p>[Court extrait de l'actualité] <a href="#">Lire la suite...</a></p>
                </article>
            </div>
        </section>

        <section id="contact" class="mb-5">
            <h2 class="mb-3 text-center"><i class="fas fa-envelope me-2"></i>Contactez-nous</h2>
            <p class="text-center">Adresse : [Adresse de l'hôpital]</p>
            <p class="text-center">Téléphone : [Numéro de téléphone]</p>
            <p class="text-center">Email : [Email de l'hôpital]</p>
        </section>
    </main>

    <footer class="mt-4">
        <p>&copy; 2023 Hôpital Central de la Ville. Tous droits réservés.</p>
    </footer>
</body>
</html>