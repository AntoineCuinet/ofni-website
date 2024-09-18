<?php 
// Chargement des bibliothèques de fonctions
require_once('./php/library_app.php');
require_once('./php/library_general.php');

// Bufferisation des sorties
ob_start();

// Démarrage ou reprise de la session
session_start();

affEntete('Le site de blagues n°1 en France !', '.');

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
            '<p>J\'espère que vous aimer l\'humour, car ici, vous allez être servie !</p>',
            '<section>',
                '<h2>Les 10 blagues les plus aimées</h2>';

    // Connexion au serveur de BD
    $bd = bdConnect();

    // Requête SQL pour récupérer les 10 blagues les plus likées
    $sql = 'SELECT RDD_post.RDDpostTitle, RDD_post.RDDpostContent, RDD_post.RDDpostPublicationDate, RDD_post.RDDpostAuthor, RDD_post.RDDpostIsQuestion, RDD_post.RDDpostAnswer, RDD_user.RDDuserId, RDD_user.RDDuserPseudo, 
                    COUNT(DISTINCT RDD_like.RDDlikeId) AS nbLikes, 
                    COUNT(DISTINCT RDD_comment.RDDcommentId) AS nbComments
            FROM RDD_post
            LEFT JOIN RDD_like ON RDD_post.RDDpostId = RDD_like.RDDlikePost
            LEFT JOIN RDD_comment ON RDD_post.RDDpostId = RDD_comment.RDDcommentPost
            LEFT JOIN RDD_user ON RDD_post.RDDpostAuthor = RDD_user.RDDuserId
            GROUP BY RDD_post.RDDpostId
            ORDER BY nbLikes DESC
            LIMIT 10;';

    $result = bdSendRequest($bd, $sql);

    // Fermeture de la connexion au serveur de BdD
    mysqli_close($bd);

    while ($post = mysqli_fetch_assoc($result)) {
        echo '<article>',
        '<h3>', $post['RDDpostTitle'], '</h3>',
        '<a href="#">',
            '<img src="./assets/uploads/', $post['RDDuserId'], '.jpeg" alt="photo de profil" width="35" height="35" onerror="this.onerror=null; this.src=\'./assets/pictures/none.jpeg\';">',
            '<span>', $post['RDDuserPseudo'], '</span>',
        '</a>';

        if ($post['RDDpostIsQuestion']) {
            echo '<p>', $post['RDDpostContent'], '</p>',
                    '<p class="reponse">Réponse: ', $post['RDDpostAnswer'], '</p>';
        } else {
            echo '<p>', $post['RDDpostContent'], '</p>';
        }

        echo '<footer class="footer-post">',
        '<p>Publié le ', $post['RDDpostPublicationDate'], '</p>',
        '<p>', $post['nbLikes'], ' J\'aime(s), ', $post['nbComments'], ' Commentaire(s)</p>',
        '</footer>',
        '</article>';
    }

    echo '</section>',
        '</main>';
}