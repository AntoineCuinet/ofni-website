<?php
// Chargement des bibliothèques de fonctions
require_once('./php/library_app.php');
require_once('./php/library_general.php');

// Bufferisation des sorties
ob_start();

// Démarrage ou reprise de la session
session_start();

// Si l'utilisateur est déjà authentifié, on le redirige vers la page d'accueil
if (! estAuthentifie()){
    header ('Location: ./index.php');
    exit();
}

if (isset($_POST['btnComfirmerChangementImg'])) {
    $err = traitementAjoutImg();
} else {
    $err = null;
}

affEntete(htmlProtegerSorties($_SESSION['pseudo']), '.');

// Génération du contenu de la page
affContenuL($err);

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
 * @param   array   $err    tableau associatif contenant les erreurs de saisie
 * 
 * @return  void
 */
function affContenuL(?array $err): void {
    $pseudo = htmlProtegerSorties($_SESSION['pseudo']);

    if(isset($_GET['picture'])) {
        $id = verifGet('picture', 'profile');

        echo 
        '<div class="modal">',
            '<form method="post" action="profile.php" class="modal-content" enctype="multipart/form-data">',
                '<h3>Choisissez une nouvelle photo de profile.</h3>',
                '<input type="file" name="file" required>',
                '<input type="hidden" name="MAX_FILE_SIZE" value="1024000">',
                '<input type="hidden" name="idUser" value="', $id, '">',
                '<br>',
                '<input class="btn" type="submit" name="btnComfirmerChangementImg" value="Valider"> ',
                '<a class="btn-danger" href="./profile.php">Annuler</a>',  
            '</form>',
        '</div>';
    }
    echo '<main>',
            '<section class="profile">';

                if (! empty($err)) {
                    echo    '<div class="erreur">Les erreurs suivantes ont été relevées lors de l\'enregistrement de l\'article :',
                            '<ul>';
                foreach ($err as $e) {
                    echo        '<li>', $e, '</li>';
                }
                    echo        '</ul>',
                            '</div>';
                }

                $bd = bdConnect();

                $sql = "SELECT * 
                        FROM RDD_user 
                        WHERE RDDuserPseudo = '$pseudo'";

                $result = bdSendRequest($bd, $sql);
            
                $res = htmlProtegerSorties(mysqli_fetch_assoc($result));

                $RDDuserId = $res['RDDuserId'];
                $RDDuserIdCript = chiffrerSignerURL($RDDuserId);
                echo 
                
                '<div class="profil-info-conatainer">',
                    '<div class="div-img-profile">',
                        '<a href="./profile.php?picture=', $RDDuserIdCript, '"><img src="./assets/uploads/', $RDDuserId, '.jpeg" alt="photo de profil" width="100" height="100" onerror="this.onerror=null; this.src=\'./assets/pictures/none.jpeg\';"></a>',
                        '<h2>', $pseudo, '</h2>',
                        '<p>', $res['RDDuserFirstName'], '</p>',
                    '</div>',

                    '<div class="div-description-profile">',
                        '<h3>Votre déscription : </h3>', 
                        '<textarea id="RDDuserDescription" name="RDDuserDescription" rows="20" cols="40" placeholder="Écrivez votre description ici">', $res['RDDuserDescription'], '</textarea>',
                    '</div>',
                '</<div>',

                '<p><a class="btn" href="./setting.php">Paramètres</a><a class="btn-danger" href="./disconnection.php">Se déconnecter</a></p>';

                // '<p>Prénom : ', $res['RDDuserFirstName'], '</p>',
                // '<p>Nom : ', $res['RDDuserLastName'], '</p>',
                // '<p>Email : ', $res['RDDuserMail'], '</p>',
                // '<p>Date de naissance : ', $res['RDDuserBirth'], '</p>',
                // '<p>Sexe : ', $res['RDDuserCivility'], '</p>',
                // '<p>Téléphone : ', $res['RDDuserTel'], '</p>',
                // '<p>News letter : ', $res['RDDuserNewsLetter'], '</p>',
                // '<p>Éditeur : ', $res['RDDuserEditor'], '</p>';

                $sqlArticle = "SELECT RDD_post.RDDpostTitle, RDD_post.RDDpostContent, RDD_post.RDDpostPublicationDate, RDD_post.RDDpostAuthor, RDD_post.RDDpostIsQuestion, RDD_post.RDDpostAnswer,
                                    COUNT(DISTINCT RDD_like.RDDlikeId) AS nbLikes, 
                                    COUNT(DISTINCT RDD_comment.RDDcommentId) AS nbComments
                            FROM RDD_post
                            LEFT JOIN RDD_like ON RDD_post.RDDpostId = RDD_like.RDDlikePost
                            LEFT JOIN RDD_comment ON RDD_post.RDDpostId = RDD_comment.RDDcommentPost
                            WHERE RDDpostAuthor = '$RDDuserId'
                            GROUP BY RDD_post.RDDpostId
                            ORDER BY nbLikes DESC;";

                $resultArticle = bdSendRequest($bd, $sqlArticle);

                mysqli_close($bd);

                if (mysqli_num_rows($resultArticle) > 0) {
                    echo '<p>Nombre de blagues écrites : ', mysqli_num_rows($resultArticle), '</p>';

                    while ($article = mysqli_fetch_assoc($resultArticle)) {
                        $article = htmlProtegerSorties($article);
                        echo '<article>',
                        '<h3>', $article['RDDpostTitle'], '</h3>',
                        '<a href="#">',
                            '<img src="./assets/uploads/', $RDDuserId, '.jpeg" alt="photo de profil" width="35" height="35" onerror="this.onerror=null; this.src=\'./assets/pictures/none.jpeg\';">',
                            '<span>', $pseudo, '</span>',
                        '</a>';

                        if ($article['RDDpostIsQuestion']) {
                            echo '<p>', $article['RDDpostContent'], '</p>',
                                    '<p class="reponse">Réponse: ', $article['RDDpostAnswer'], '</p>';
                        } else {
                            echo '<p>', $article['RDDpostContent'], '</p>';
                        }

                        echo '<footer class="footer-post">',
                        '<p>Publié le ', $article['RDDpostPublicationDate'], '</p>',
                        '<p>', $article['nbLikes'], ' J\'aime(s), ', $article['nbComments'], ' Commentaire(s)</p>',
                        '</footer>',
                        '</article>';
                    }
                } else {
                    echo '<p>Vous n\'avez pas encore écrit de blague, <a href="./add_post.php">Écrivez votre première blague !</a></p>';
                }

                echo '<p class="info-footer">Vous êtes connecté en tant que ', $pseudo, ', profil créer le ', $res['RDDuserCreationDate'], '</p>', 
            '</section>',
        '</main>';
}


//_______________________________________________________________
/**
 * Traitement de l'ajout d'une image de profil
 *
 * @return  array|null  tableau associatif contenant les erreurs de saisie ou null si l'ajout a été effectué
 */
function traitementAjoutImg(): array|null {
    // if(!parametresControle('post', ['file', 'idUser', 'MAX_FILE_SIZE', 'btnComfirmerChangementImg'])) {
    //     sessionExit();
    // }

    $erreurs = [];

    // Vérification de l'image (si elle est présente)
    if(isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
        verifUpload($erreurs);
    }

    // Ajout de l'article
    if (empty($erreurs)) {
        // Vérification du droit d'écriture sur le répertoire upload
        $uploadDir = './assets/uploads/';
        verifDroitEcriture($uploadDir);
        $Id = $_POST['idUser'];

        // Vérification de l'existence d'une ancienne image + suppression
        $cheminFichier = './assets/uploads/' . $Id . '.jpg';
        if (file_exists($cheminFichier)) {
            unlink($cheminFichier);
        }

        // Enregistrement du fichier
        depotFile($Id, $uploadDir);
        return null;
    } else {
        return $erreurs;
    }
}