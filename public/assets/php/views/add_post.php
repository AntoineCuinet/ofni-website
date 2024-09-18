<?php 
// Chargement des bibliothèques de fonctions
require_once('./php/library_app.php');
require_once('./php/library_general.php');

// Bufferisation des sorties
ob_start();

// Démarrage ou reprise de la session
session_start();


// Si l'utilisateur n'est pas authentifié ou s'il n'a pas les droit de rédacteur, on le redirige sur la page index.php
if (! estAuthentifie() || ! $_SESSION['editor']){
    header('Location: ./index.php');
    exit;
}

affEntete('Ajouter une bonne blague !', '.');


if (isset($_POST['btnCreerArticle'])) {
    $err = traitementAjoutAr();
} else {
    $err = null;
}


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
    // réaffichage des données soumises en cas d'erreur
    if (isset($_POST['btnCreerArticle'])){
        $values = htmlProtegerSorties($_POST);
    } else {
        $values = ['RDDpostTitle' => '', 'RDDpostCategory' => '', 'RDDpostContent' => '', 'RDDpostIsQuestion' => '', 'RDDpostAnswer' => ''];
    }
    echo '<main>',
    '<section>',
        '<h2>Écrivez votre meilleure blague !</h2>';

        if (! empty($err)) {
            echo    '<div class="erreur">Les erreurs suivantes ont été relevées lors de l\'enregistrement de la blague :',
                    '<ul>';
        foreach ($err as $e) {
            echo        '<li>', $e, '</li>';
        }
        echo        '</ul>',
                '</div>';
        } 

        else if (isset($_POST['btnCreerArticle'])) {
            header('Location: ./profile.php');
            exit();
        }
        // else if (isset($_POST['btnCreerArticle'])) {

        //     $bd = bdConnect();
        //     // Requête pour récupérer l'id de l'article créé
        //     $sql = "SELECT RDDpostId FROM RDD_post 
        //             WHERE RDDpostAuthor = '{$_SESSION['pseudo']}'
        //             ORDER BY RDDpostPublicationDate DESC
        //             LIMIT 1;";
        //     $result = bdSendRequest($bd, $sql);

        //     // fermeture de la connexion à la base de données
        //     mysqli_close($bd);

        //     $row = mysqli_fetch_assoc($result);
        //     $id = $row['RDDpostId'];
        //     // Chiffrement de l'id pour le passage dans l'URL
        //     $id_chiffre = chiffrerSignerURL($id); 
        //     echo    '<div class="succes">L\'article à bien été créer. <a href="./profile.php?id=', $id_chiffre, '">cliquez ici pour le voir !</a></div>';
        // }

        echo '<form method="post" action="add_post.php">',
        '<table>';

        echo 
        '<tr>',
            '<td><label for="RDDpostIsQuestion">C\'est une devinette ? : </label></td>',
            '<td>',
            '<select name="RDDpostIsQuestion" id="RDDpostIsQuestion" required>',
                '<option value="" disabled selected>Choisir une option</option>',
                '<option value="1" ', isset($values['RDDpostIsQuestion']) && $values['RDDpostIsQuestion'] == 1 ? ' selected' : '', '>OUI, il y a une réponse</option>',
                '<option value="0" ', isset($values['RDDpostIsQuestion']) && $values['RDDpostIsQuestion'] == 0 ? ' selected' : '', '>NON, pas de réponse</option>',
            '</select>',
            '</td>',
        '</tr>';

    echo 
    '<tr>',
        '<td><label for="RDDpostCategory">Le style de blague : </label></td>',
        '<td>',
        '<select name="RDDpostCategory" id="RDDpostCategory" required>',
            '<option value="" disabled selected>Choisir une option</option>';

            $bd = bdConnect();

            $sql = "SELECT RDDcategoryName 
                    FROM RDD_category
                    ORDER BY RDDcategoryName DESC;";
        
            $result = bdSendRequest($bd, $sql);
            mysqli_close($bd);
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="', $row['RDDcategoryName'], '"', $row['RDDcategoryName'] == $values['RDDpostCategory'] ? ' selected' : '', '>', $row['RDDcategoryName'], '</option>';
            }
        echo '</select>',
        '</td>',
    '</tr>';

    affLigneInput('Le titre de la blague : ', array('type' => 'text', 'name' => 'RDDpostTitle', 'placeholder' => 'Titre', 'value' => $values['RDDpostTitle'], 'required' => null));

    echo '<tr>',
            '<td><label for="RDDpostContent">Le contenu de la blague : </label></td>',
            '<td><textarea id="RDDpostContent" name="RDDpostContent" rows="30" cols="40" placeholder="Écrire la blague ici" required>', $values['RDDpostContent'], '</textarea></td>',
        '</tr>',
        '<tr>',
            '<td><label for="RDDpostAnswer">La réponse à la devinnette <br>(si il y en a une) : </label></td>',
            '<td><textarea id="RDDpostAnswer" name="RDDpostAnswer" rows="30" cols="40" placeholder="Écrire la réponce ici, QUE SI IL Y EN A UNE, sinon laissez VIDE !" required>', $values['RDDpostAnswer'], '</textarea></td>',
        '</tr>';

        echo '<tr>',
            '<td colspan="2">',
                '<input class="btn" type="submit" name="btnCreerArticle" value="Publiez votre blague !"> ',
                '<input class="btn-danger" type="reset" value="Réinitialiser">',
            '</td>',
        '</tr>',
        '</table>',
        '</form>',
        '</section>',
    '</main>';
}


//_______________________________________________________________
/**
 * Traitement de l'ajout d'un nouvel article
 *
 * @return  array|null  tableau associatif contenant les erreurs de saisie ou null si l'ajout a été effectué
 */
function traitementAjoutAr(): array|null {
    if(!parametresControle('post', ['RDDpostTitle', 'RDDpostContent', 'RDDpostCategory', 'RDDpostAnswer', 'RDDpostIsQuestion', 'btnCreerArticle'])) {
        sessionExit();
    }

    $erreurs = [];

    // Vérification des entrées
    $title = $_POST['RDDpostTitle'] = trim($_POST['RDDpostTitle']);
    if(strip_tags($title) != $title){
        $erreurs[] = "Le titre ne doit pas contenir de tags HTML.";
    }
    
    $textAr = $_POST['RDDpostContent'] = trim($_POST['RDDpostContent']);
    verifierTexte($textAr, 'Le texte', $erreurs);

    $isQuestion = $_POST['RDDpostIsQuestion'];

    if ($isQuestion == 1) {
        $answer = $_POST['RDDpostAnswer'] = trim($_POST['RDDpostAnswer']);
        verifierTexte($answer, 'La réponse', $erreurs);
    } else {
        $answer = null;
    }

    $category = $_POST['RDDpostCategory'];

    // Ajout de la blague
    if (empty($erreurs)) {
        $pseudo = $_SESSION['pseudo'];

        // ouverture de la connexion à la base
        $bd = bdConnect();

        // protection des entrées
        $title2 = mysqli_real_escape_string($bd, $title);
        $textAr2 = mysqli_real_escape_string($bd, $textAr);

        if($answer != null) {
            $answer = mysqli_real_escape_string($bd, $answer);
        }

        $sqlInsert = "SELECT RDDuserId 
                      FROM RDD_user 
                      WHERE RDDuserPseudo = '$pseudo';";
        
        $sqlInsert2 = "SELECT RDDcategoryId 
                       FROM RDD_category
                       WHERE RDDcategoryName = '$category';";
        
        $result = bdSendRequest($bd, $sqlInsert);
        $row = mysqli_fetch_assoc($result);
        $userId = $row['RDDuserId'];
        $result2 = bdSendRequest($bd, $sqlInsert2);
        $row2 = mysqli_fetch_assoc($result2);
        $category2 = $row2['RDDcategoryId'];

        // Requête d'insertion
        $sql = "INSERT INTO RDD_post (RDDpostTitle, RDDpostContent, RDDpostCategory, RDDpostPublicationDate, RDDpostModificationDate, RDDpostAuthor, RDDpostStatusPublication, RDDpostIsQuestion, RDDpostAnswer)
                VALUES ('$title2', '$textAr2', $category2, NOW(), NULL, $userId, 1, $isQuestion, '$answer');";

        bdSendRequest($bd, $sql);

        mysqli_close($bd);

        return null;
    } else {
        return $erreurs;
    }
}
