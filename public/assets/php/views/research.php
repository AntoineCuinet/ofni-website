<?php 
// Chargement des bibliothèques de fonctions
require_once('./php/library_app.php');
require_once('./php/library_general.php');

// Bufferisation des sorties
ob_start();

// Démarrage ou reprise de la session
session_start();

affEntete('Recherche', '.');

// Génération du contenu de la page
affContenuL();

affPiedDePage();

// Envoi du buffer
ob_end_flush();



/*********************************************************
 *
 * Définitions des fonctions locales de la page
 *
 *********************************************************/
//_______________________________________________________________
/**
 * Affichage du contenu principal de la page
 *
 * @return  void
 */
function affContenuL(): void {
    echo '<main>',
            '<section>',
                '<h2>Recherche</h2>';



    echo '</section>',
        '</main>';
}