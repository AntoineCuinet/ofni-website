<?php
/* ========================================================================
 * File Name: index.php - Home page
 * Author: CUINET Antoine
 * Version: 1.0
 * Date: September 2024
 *
 * Note: This code was developed by CUINET Antoine, see https://acuinet.fr
======================================================================== */

// Loading function libraries
require_once('./assets/php/library_app.php');
require_once('./assets/php/library_general.php');
// Buffering of outputs
ob_start();
// Starting or resuming the session
session_start();
// Display of the header
render_head('Le site de blagues n°1 en France !', '', '.');


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

    echo '<main>';

    echo 
    '<div>',
        '<a href="assets/php/wireframe.php">wireframe</a>',
    '</div>';

    // // Connecting to the DB server
    // $bd = db_connect();

    // // SQL Query
    // $sql = 'SELECT RDD_post.RDDpostTitle, RDD_post.RDDpostContent, RDD_post.RDDpostPublicationDate, RDD_post.RDDpostAuthor, RDD_post.RDDpostIsQuestion, RDD_post.RDDpostAnswer, RDD_user.RDDuserId, RDD_user.RDDuserPseudo, 
    //                 COUNT(DISTINCT RDD_like.RDDlikeId) AS nbLikes, 
    //                 COUNT(DISTINCT RDD_comment.RDDcommentId) AS nbComments
    //         FROM RDD_post
    //         LEFT JOIN RDD_like ON RDD_post.RDDpostId = RDD_like.RDDlikePost
    //         LEFT JOIN RDD_comment ON RDD_post.RDDpostId = RDD_comment.RDDcommentPost
    //         LEFT JOIN RDD_user ON RDD_post.RDDpostAuthor = RDD_user.RDDuserId
    //         GROUP BY RDD_post.RDDpostId
    //         ORDER BY nbLikes DESC
    //         LIMIT 10;';

    // $result = db_send_request($bd, $sql);

    // // Closing the connection to the DB server
    // mysqli_close($bd);

    // while ($post = mysqli_fetch_assoc($result)) {
    //     echo 
    //     '<article>',
    //         '<h3>', $post['RDDpostTitle'], '</h3>',
    //         '<a href="#">',
    //             '<img src="./assets/uploads/', $post['RDDuserId'], '.jpeg" alt="photo de profil" width="35" height="35" onerror="this.onerror=null; this.src=\'./assets/pictures/none.jpeg\';">',
    //             '<span>', $post['RDDuserPseudo'], '</span>',
    //         '</a>';
    //     '</article>';
    // }

    echo '</main>';
}