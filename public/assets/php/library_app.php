<?php
/* ========================================================================
 * File Name: library_app.php - Library of specific functions for the application
 * Author: CUINET Antoine
 * Version: 1.0
 * Date: September 2024
 *
 * Note: This code was developed by CUINET Antoine, see https://acuinet.fr
======================================================================== */


// Force display of errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting( E_ALL );

// Development (IS_DEV = true) or production (IS_DEV = false) phase
define ('IS_DEV', false);

// Encryption key for URLs (for the AES-128 algorithm in CBC mode)
define('CLE_CHIFFREMENT', 'z31uU2g22y/XhFpfuilzEw==');

// Sets the default time zone to use. Available since PHP 5.1
date_default_timezone_set('Europe/Paris');


// Constants: connection parameters to the MariaDB server
define ('BD_NAME', ''); // TODO: connexion informations for the database
define ('BD_USER', '');
define ('BD_PASS', '');
define ('BD_SERVER', 'localhost');

// Constants: limits related to the sizes of the fields in the DB
define('LMAX_PSEUDO', 20);
define('LMIN_PSEUDO', 6);
define('LMAX_LAST_NAME', 64);
define('LMAX_FIRST_NAME', 64);
define('LMAX_MAIL', 64);
define('LMIN_PASSWORD', 8);

define('TITLE', 'OFNI');
define('KEY_WORDS', 'association, etudiant, étudiants, fac, OFNI, code, Fun, Algo, Bat. C,Programmation, PDL, Détente, Entraide, Informatique, Java, JS, C++, Python, Framework, PHP, Soirées, Jeux Vidéo, Films, Jeux de société, Sorties, Ski, Révisions, Laser Game, Trampo Park, Réseau, Asso, NUIT DE L’INFO');

// Constants: definition of fonts
define('FONT_TITLE', 'https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;700&display=swap');
define('FONT_PARAGRAPH', 'https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap');



// ========================================================================



/**
 * Display of the start of the HTML page (head + menu + header)
 *
 * @param  string  $title       the title of the page
 * @param  string  $description the description of the page
 * @param  string  $prefix      the relative path prefix to the site root
 *
 * @return void
 */
function render_head(string $title, string $description, string $prefix = '.') : void {

    echo
'<!--
    _____ _   _ ___ __  __ ____________
   / ____/ / / /  _/ | / / ____/_  __/
  / /   / / / // //  |/ / __/   / /
 / /___/ /_/ // // /|  / /___  / /
 \____/\____/___/_/ |_/_____/ /_/

 CUINET realization, see the website https://acuinet.fr
-->
',
    '<!doctype html>',
    '<html lang="fr">',
        '<head>',
            '<meta charset="UTF-8">',
            '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
            '<link rel="meta" type="application/json" href="', $prefix,'/meta.json">',
            '<meta name="description" content="', $description, '">',
            '<meta name="keywords" content="', KEY_WORDS, '">',
            '<title>', TITLE, ' | ', $title, '</title>',
            '<link rel="stylesheet" type="text/css" href="', $prefix,'/style.css">',
            '<link rel="icon" type="image/x-icon" href="', $prefix,'/favicon.ico"/>',

            '<meta property="og:image" content="http://', TITLE, '.fr/favicon.jpeg">',
            '<meta property="og:url" content="https://', TITLE, '.fr/">',
            '<meta property="og:description" content="', $description, '">',
            '<meta property="og:title" content="', TITLE, '">',
            '<meta property="og:type" content="website">',


            '<link rel="preconnect" href="https://fonts.googleapis.com">',
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
            '<link href="', FONT_TITLE, '" rel="stylesheet">', 
            '<link href="', FONT_PARAGRAPH, '" rel="stylesheet">',
        '</head>',
        '<body>';

        render_header($prefix);
}


/**
 * Display the navigation menu
 *
 * @param  string  $prefix     the relative path prefix to the site root
 *
 * @return void
 */
function render_header(string $prefixe = '.') : void {

    echo
    '<div class="fixed-top-box"></div>',
    '<header>',
    '</header>';
}


/**
 * Footer display
 *
 * @return void
 */
function render_footer() : void {

    echo
    '<footer>',
        '<div class="footer-row">',

            '<div class="footer-col">',
                '<h3>Informations</h3>',
                '<p>OFNI est l\'association des étudiants de la faculté des sciences de l\'Université de Franche-Comté.</p>',
                '<p>E-mail: <a href="mailto:contact@ofni.asso.fr">Mail de contact</a></p>',
            '</div>',
    
            '<div class="footer-col">',
                '<h3>Liens</h3>',
                '<ul>',
                    '<li><a href="./index.php">Accueil</a></li>',
                    '<li><a href="./mentions.php">Mentions légales</a></li>',
                '</ul>',
            '</div>',

            '<div class="footer-col">',
                '<h3>Newsleter</h3>',
                '<form class="form-newsletter">',
                    '<svg viewBox="0 0 24 24" width="20" height="20"><path d="M18.5,1H5.5A5.506,5.506,0,0,0,0,6.5v11A5.506,5.506,0,0,0,5.5,23h13A5.506,5.506,0,0,0,24,17.5V6.5A5.506,5.506,0,0,0,18.5,1Zm0,3a2.476,2.476,0,0,1,1.643.631l-6.5,6.5a2.373,2.373,0,0,1-3.278,0l-6.5-6.5A2.476,2.476,0,0,1,5.5,4Zm0,16H5.5A2.5,2.5,0,0,1,3,17.5V8.017l5.239,5.239a5.317,5.317,0,0,0,7.521,0L21,8.017V17.5A2.5,2.5,0,0,1,18.5,20Z"/></svg>',
                    '<input type="email" name="email" placeholder="Votre meilleure adresse mail" required>',
                    '<button type="submit"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M15.75,9.525,11.164,4.939A1.5,1.5,0,0,0,9.043,7.061l4.586,4.585a.5.5,0,0,1,0,.708L9.043,16.939a1.5,1.5,0,0,0,2.121,2.122l4.586-4.586A3.505,3.505,0,0,0,15.75,9.525Z"/></svg></button>',
                '</form>',

                '<h3>Réseaux</h3>',
                '<div>',
                    '<button></button>',
                    '<button><svg viewBox="0 0 128 128" width="40" height="40"><path d="M116.42 5.07H11.58a6.5 6.5 0 00-6.5 6.5v104.85a6.5 6.5 0 006.5 6.5H68V77.29H52.66V59.5H68V46.38c0-15.22 9.3-23.51 22.88-23.51a126 126 0 0113.72.7v15.91h-9.39c-7.39 0-8.82 3.51-8.82 8.66V59.5H104l-2.29 17.79H86.39v45.64h30a6.51 6.51 0 006.5-6.5V11.58a6.5 6.5 0 00-6.47-6.51z"></path></svg></button>',
                    '<button><svg viewBox="0 0 128 128" width="40" height="40"><path d="M116.659 45.061c-5.075-3.895-12.286-5.754-21.087-5.754-9.648 0-16.383 2.199-19.939 6.77-1.626-1.76-3.659-3.162-5.963-4.229-2.444-2.479-4.981-3.986-7.574-4.717 5.754-3.695 12.497-8.684 21.036-15.426-34.694 3.555-51.459 6.94-81.245 24.881 1.219 0 2.396.006 3.546.02-.711 5.572-.341 13.883-.208 16.342-3.991 10.197-.406 18.488 2.579 25.945 0-8.979.44-16.244-2.109-25.557l20.25-16.051c-.326 3.025-.277 6.555.305 10.807l.063.021-.232 1.162.918.162c-.227 1.506.001 3.073.001 4.749V106h18V66.383c0-1.982.65-3.719 1.053-5.217 2.765-.465 5.64-1.256 8.167-2.338l.182.447c.162-.203.357-.447.515-.66 2.034-.908 4.166-2.014 6.001-3.316C64.178 56.986 66 60.654 66 66.383V106h21V66.383c0-8.123 2.4-12.182 9-12.182s9 4.059 9 12.182V106h21V64.186c0-8.463-3.419-14.725-9.341-19.125z"></path></svg></button>',

                '</div>',
            '</div>',

        '</div>',
        '<div class="footer-copyright">',
            '<p>&copy; <span class="or">', TITLE, '</span> - Septembre 2024 - Tous droits réservés</p>',
        '</div>',
    '</footer>',
    '<script src="./assets/scripts/script.js"></script>',
    '</body></html>';
}


/**
* Determines if the user is authenticated
*
* @return bool      true if the user is authenticated, false otherwise
*/
function is_authenticate(): bool {
    return  isset($_SESSION['pseudo']);
}


/**
 * Ends a session and redirects to the page passed as a parameter
 *
 * @param  string  $page     URL of the page the user is redirected to
 *
 * @return void
 */
function exit_session(string $page = './index.php'): void {

    if (ini_get("session.use_cookies")) {
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
