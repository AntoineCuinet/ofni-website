<?php
/* ========================================================================
 * File Name: wireframe.php - Wireframe page
 * Author: CUINET Antoine
 * Version: 1.0
 * Date: September 2024
 *
 * Note: This code was developed by CUINET Antoine, see https://acuinet.fr
======================================================================== */

// Loading function libraries
require_once('./library_app.php');
require_once('./library_general.php');
// Buffering of outputs
ob_start();
// Starting or resuming the session
session_start();
// Display of the header
render_head('Wireframe', 'Wireframe page', '../../');


// Generating page content
show_content();


// Display of the footer
render_footer();
// Sending the buffer
ob_end_flush();



/* ========================================================================
 *
 * Definitions of local page functions
 *
======================================================================== */

/**
 * Affichage du contenu principal de la page
 *
 * @return  void
 */
function show_content(): void {

    echo 
    '<main>',
        '<div>',
            '<h1>TITLE-1</h1>',
        '</div>',
        '<div>',
            '<h2>TITLE-2</h2>',
        '</div>',
        '<div>',
            '<h3>TITLE-3</h3>',
        '</div>',
        '<div>',
            '<p>Paragraph</p>',
        '</div>',
        '<div>',
            '<a href="#">link</a>',
        '</div>',
        '<div>',
            '<button class="btn">button</button>',
            '<button class="btn-danger">button danger</button>',
        '</div>',
        '<section>',
            '<h2>Section</h2>',
            '<p>Blabla à propos de l\'OFNI</p>',
        '</section>',
    '</main>';
}
