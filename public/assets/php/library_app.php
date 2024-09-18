<?php
/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *         à l'application "Rire 2 Délire.fr"            *
 *********************************************************/

// Force l'affichage des erreurs
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting( E_ALL );

// Phase de développement (IS_DEV = true) ou de production (IS_DEV = false)
define ('IS_DEV', true);

/** Constantes : les paramètres de connexion au serveur MariaDB */
define ('BD_NAME', 'rire2delire');
define ('BD_USER', 'admin');
define ('BD_PASS', 'LeRireNoir');
define ('BD_SERVER', 'localhost');

// Définit le fuseau horaire par défaut à utiliser. Disponible depuis PHP 5.1
date_default_timezone_set('Europe/Paris');


// limites liées aux tailles des champs de la table RDD_user
define('LMAX_PSEUDO', 20);       // taille du champ RDDuserPseudo
define('LMIN_PSEUDO', 6);
define('LMAX_LAST_NAME', 64);    // taille du champ RDDuserLastName
define('LMAX_FIRST_NAME', 64);   // taille du champ RDDuserFirstName
define('LMAX_MAIL', 64);         // taille du champ RDDuserMail
define('LMIN_PASSWORD', 8);
define('AGE_MINIMUM', 15);

// limites liées aux tailles des champs de la table RDD_post
define('LMAX_TITLE', 255);   // taille du champ RDDpostTitle
define('LMAX_CATEGORY', 20);   // taille du champ RDDpostCategory


// Clé de chiffrement pour les urls (pour l'algorithme AES-128 en mode CBC)
define('CLE_CHIFFREMENT', 'z31uU2g22y/XhFpfuilzEw==');




//_______________________________________________________________
/**
 * Affichage du début de la page HTML (head + menu + header).
 *
 * @param  string  $titre       le titre de la page (<head> et <h1>)
 * @param  string  $prefixe     le préfixe du chemin relatif vers la racine du site
 *
 * @return void
 */
function affEntete(string $titre, string $prefixe = '..') : void {

    echo
'<!--
    _____ _   _ ___ __  __ ____________
   / ____/ / / /  _/ | / / ____/_  __/
  / /   / / / // //  |/ / __/   / /   
 / /___/ /_/ // // /|  / /___  / /    
 \____/\____/___/_/ |_/_____/ /_/ 

 A CUINET realization, see the website https://acuinet.fr    
 -->
 ',
        '<!doctype html>',
        '<html lang="fr">',
            '<head>',
                '<meta charset="UTF-8">',
                '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
                '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
                '<link rel="meta" type="application/json" href="./meta.json">',
                // '<meta name="description" content="', $description, '">',
                // '<meta name="keywords" content="', $keywords, '">',
                '<title>Rire 2 Délire | ', $titre, '</title>',
                '<link rel="stylesheet" type="text/css" href="', $prefixe,'/style.css">',
                '<link rel="icon" type="image/icon" href="./assets/favicon/favicon.jpeg"/>',

                '<meta property="og:image" content="http://rire2delire.fr/assets/favicon/favicon.jpeg">',
                '<meta property="og:url" content="https://rire2delire.fr/">',
                // '<meta property="og:description" content="', $description, '">',
                '<meta property="og:title" content="Rire 2 Délire">',
                '<meta property="og:type" content="website">',
                '<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">',
            '</head>',
            '<body>';

    affMenu($prefixe);

    echo        '<header>',
                    // '<img src="', $prefixe, '/assets/pictures/titre.png" alt=" Image du titre | Rire 2 Délire" width="780" height="83">',
                    '<h1>', $titre, '</h1>',
                '</header>';
}

//_______________________________________________________________
/**
 * Affichage du menu de navigation.
 *
 * @param  string  $prefixe     le préfixe du chemin relatif vers la racine du site
 *
 * @return void
 */
function affMenu(string $prefixe = '..') : void {

    echo    
    '<nav id="navbar">',
        '<div class="first-container">',
            '<a href="', $prefixe, '/index.php" class="nav-icon" aria-label="Visit homepage" aria-current="page">',
                '<span>Rire 2 Délire</span>',
            '</a>',

            '<div class="main-navlinks">',
                '<button class="hamburger" type="button" aria-label="Toggle navigation" aria-expanded="false">',
                    '<span></span>',
                    '<span></span>',
                    '<span></span>',
                '</button>',
            '</div>',

            '<div class="navlinks-container">';
    if (estAuthentifie()){
        echo
            $_SESSION['editor'] ? "<a href='$prefixe/add_post.php'>+</a>" : '',
            '<a href="', $prefixe, '/category.php">Catégories</a>',
            '<a href="', $prefixe, '/research.php">Recherche</a>',
            '<a href="', $prefixe, '/profile.php">Mon profil</a>';

    } else {
        echo    
            '<a href="', $prefixe, '/category.php">Catégories</a>',
            '<a href="', $prefixe, '/research.php">Recherche</a>',
            '<a href="', $prefixe, '/connection.php">Se connecter</a>';
    }

    echo    '</div>',
        '</div>',
    '</nav>';
}

//_______________________________________________________________
/**
 * Affichage du pied de page.
 *
 * @return  void
 */
function affPiedDePage() : void {

    echo 
    '<div id="to-top-btn">',
        '<i class="bx bx-up-arrow-circle"></i>',
    '</div>',
    '<footer>&copy; Rire 2 Délire - Juin 2024 - Tous droits réservés</footer>',
    '<script src="./script.js"></script>',
    '</body></html>';
}

//_______________________________________________________________
/**
* Détermine si l'utilisateur est authentifié
*
* @return bool     true si l'utilisateur est authentifié, false sinon
*/
function estAuthentifie(): bool {
    return  isset($_SESSION['pseudo']);
}


//_______________________________________________________________
/**
 * Termine une session et effectue une redirection vers la page transmise en paramètre
 *
 * Cette fonction est appelée quand l'utilisateur se déconnecte "normalement" et quand une
 * tentative de piratage est détectée. On pourrait améliorer l'application en différenciant ces
 * 2 situations. Et en cas de tentative de piratage, on pourrait faire des traitements pour
 * stocker par exemple l'adresse IP, etc.
 *
 * @param string    $page URL de la page vers laquelle l'utilisateur est redirigé
 *
 * @return void
 */
function sessionExit(string $page = './index.php'): void {

    // suppression de toutes les variables de session
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        // suppression du cookie de session
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 86400,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: $page");
    exit();
}


//_______________________________________________________________
/**
 * Affiche la pagination de la page
 *
 * @param  string   $titre     Le titre de l'article.
 * @param  int      $id        L'id de l'article.
 * @param  string   $resume    Le résumé de l'article.
 *
 * @return void
 */
function affUnArticle(string $titre, int $id, string $resume): void {
    $titre = htmlProtegerSorties($titre);
    $resume = htmlProtegerSorties($resume);

    // Chiffrement de l'id pour le passage dans l'URL
    $id_chiffre = chiffrerSignerURL($id);

    echo '<article class="resume">',
    '<img src="../upload/', $id, '.jpg" alt="Photo d\'illustration | ', $titre, '" onerror="this.onerror=null; this.src=\'../images/none.jpg\';">',
    '<h3>', $titre, '</h3>',
    '<p>', $resume, '</p>',
    '<footer><a href="../php/article.php?id=', $id_chiffre, '">Lire l\'article</a></footer>',
    '</article>';
}



//_______________________________________________________________
/**
 * Parcours les articles à afficher sur la page actuelle et les affiches par mois de création.
 *
 * @param  array   $articles     Les articles à parcourir.
 *
 * @return void
 */
function ParcoursEtAffArticlesParMois(array $articles): void {
    foreach ($articles as $mois => $articlesDuMois) {
        echo '<section>',
        '<h2>', $mois, '</h2>';
        
        // Parcourir les articles du mois
        foreach ($articlesDuMois as $article) {
            affUnArticle($article['arTitre'], $article['arID'], $article['arResume']);
        }
        echo '</section>';
    }
}

//_______________________________________________________________
/**
 * Affichage d'un message d'erreur dans une zone dédiée de la page.
 *
 * @param  string  $msg    le message d'erreur à afficher.
 *
 * @return void
 */
function affErreur(string $message) : void {
    echo
        '<main>',
            '<section>',
                '<h2>Oups, il y a eu une erreur...</h2>',
                '<p>La page que vous avez demandée a terminé son exécution avec le message d\'erreur suivant :</p>',
                '<blockquote>', $message, '</blockquote>',
            '</section>',
        '</main>';
        affPiedDePage();
}