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
define('FONT_TITLE', 'https://fonts.googleapis.com/css2?family=Josefin+Sans');
define('FONT_PARAGRAPH', 'https://fonts.googleapis.com/css2?family=Noto+Sans');



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
            '<link rel="icon" type="image/icon" href="', $prefix,'/favicon.ico"/>',

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
        '<p>&copy; ', TITLE, ' - Septembre 2024 - Tous droits réservés</p>',
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
