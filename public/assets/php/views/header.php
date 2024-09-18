<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $description_page ?? ''; ?>">
    <meta name="keywords" content="">
    <!-- <link rel="canonical" href="http://rnday.ofni.asso.fr/" /> -->
    <link rel="shortcut icon" type="image/icon" href="./assets/favicon/favicon.ico"/>
    <link rel="meta" type="application/json" href="./meta.json">
    <link rel="stylesheet" type="text/css" href="./style.css">
    <title><?= $title.' - '.$title_page ?? ''; ?></title>

    <!-- Share -->
    <!-- <meta property="og:image" content="http://rnday.ofni.asso.fr/logo-partage.png"> -->
    <!-- <meta property="og:url" content="http://rnday.ofni.asso.fr/"> -->
    <meta property="og:description" content="<?= $description_page ?? ''; ?>">
    <meta property="og:title" content="<?= $title; ?>">
    <meta property="og:type" content="website">

    <!-- Favicon (see https://realfavicongenerator.net/) -->
    <!-- <link rel="apple-touch-icon" sizes="180x180" href="../../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff"> -->
    
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;700&display=swap">
</head>
<body>

    <!-- Loader -->
    <div id="loader">
        Loading...
    </div>

    
    <header>
        <!-- Infinit headband -->
        <section class="infinit-part">
            <div class="container-infinit-part">
                <span class="txt">Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;</span>
                <span class="txt">Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;Site en <span class="it">construction</span>&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;</span>
            </div>
        </section>

        <!-- Main menu - Navbar -->
        <nav id="navbar">
            <div class="first-container">
                
                <!-- Logo -->
                <a href="./index.php" class="nav-icon" aria-label="Visit homepage" aria-current="page">
                    <img src="./assets/favicon/favicon.ico" alt="Web site icon" width="40">
                    <span><?= $title; ?></span>
                </a>

                <!-- Hamburger menu -->
                <div class="main-navlinks">
                    <button class="hamburger" type="button" aria-label="Toggle navigation" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>

                <!-- Navigation links -->
                <div class="navlinks-container">
                    <a href="./index.php" aria-current="page">Accueil</a>
                    <a href="./index.php">Informations</a>
                    <a href="./index.php">Intervenant</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
