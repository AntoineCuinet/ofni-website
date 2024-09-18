<?php 
// Chargement des bibliothèques de fonctions
require_once('./php/library_app.php');
require_once('./php/library_general.php');

// Bufferisation des sorties
ob_start();

// Démarrage ou reprise de la session
session_start();

// Si l'utilisateur est déjà authentifié, on le redirige vers la page d'accueil
if (estAuthentifie()){
    header ('Location: ./index.php');
    exit();
}


// Détermination de la page de destination
if (isset($_POST['destinationURL'])) {
    $destinationURL = $_POST['destinationURL'];
} else if (empty($destinationURL)) {
    $destinationURL = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : './index.php';
}


if (isset($_POST['btnConnexion'])) {
    $erreur = traitementConnexionL($destinationURL);
} else {
    $erreur = null;
}


affEntete('Connexion', '.');

// Génération du contenu de la page
affFormulaireL($erreur, $destinationURL);

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
 * Contenu de la page : affichage du formulaire de connexion
 *
 * En absence de soumission (i.e. lors du premier affichage), $err est égal à null
 * Quand l'inscription échoue, $err est un booleen à true
 *
 * @param ?bool     $err               Booleen à true si il y a des erreurs, false sinon
 * @param string    $destinationURL    URL de la page de destination
 *
 * @return void
 */
function affFormulaireL(?bool $err, string $destinationURL): void {
    // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
    if (isset($_POST['btnConnexion'])){
        $values = htmlProtegerSorties($_POST);
    } else {
        $values['RDDuserPseudo'] = '';
    }

    echo
        '<main>',
            '<section>',
                '<h2>Formulaire de connexion</h2>',
                '<p>Pour vous authentifier, remplissez le formulaire ci-dessous.</p>';

    if ($err) {
        echo    '<p class="erreur">Échec d\'authentification. Utilisateur inconnu ou mot de passe incorrect.</p>';
    }


    echo
            '<form method="post" action="connection.php">',
                '<table>';

    affLigneInput('Pseudo :', array('type' => 'text', 'name' => 'RDDuserPseudo', 'value' => $values['RDDuserPseudo'], 'required' => null));
    affLigneInput('Mot de passe :', array('type' => 'password', 'name' => 'RDDuserPassword', 'value' => '', 'required' => null));

    echo
                    '<tr><td colspan="2"><input type="hidden" name="destinationURL" value="', $destinationURL, '"></td></tr>',
                    '<tr>',
                        '<td colspan="2">',
                            '<input class="btn" type="submit" name="btnConnexion" value="Se connecter"> ',
                            '<input class="btn-danger" type="reset" value="Annuler">',
                        '</td>',
                    '</tr>',
                '</table>',
                '<p>Pas encore inscrit ? N\'attendez pas, <a href="./registration.php">inscrivez-vous</a> !</p>',
            '</form>',
            
        '</section>',
    '</main>';
}


/**
 * Traitement d'une demande de connexion
 *
 * Vérification de la validité des données
 * Si on trouve des erreurs => return un booleen à true
 * Sinon
 *     Connexion de l'utilisateur
 * FinSi
 * 
 * @param string    $destinationURL    URL de la page de destination
 *
 *  @return bool    un booleen à true si il y a des erreurs, false sinon
 */
function traitementConnexionL(string $destinationURL): bool {
    if( !parametresControle('post', ['RDDuserPseudo', 'RDDuserPassword', 'destinationURL', 'btnConnexion'])) {
        sessionExit();
    }

    $erreur = null;

    // vérification du pseudo et des mots de passe
    $pseudo = $_POST['RDDuserPseudo'] = trim($_POST['RDDuserPseudo']);
    $_POST['RDDuserPassword'] = trim($_POST['RDDuserPassword']);


    // ouverture de la connexion à la base de données
    $bd = bdConnect();

    $sql = "SELECT RDDuserPseudo, RDDuserPassword, RDDuserEditor
            FROM RDD_user
            WHERE RDDuserPseudo = '$pseudo'";
    $res = bdSendRequest($bd, $sql);

    while($t = mysqli_fetch_assoc($res)){
        $passe = $t['RDDuserPassword'];
        $redacteur = $t['RDDuserEditor'];
    }

    // si $passe est vide --> retour (pas d'utilisateur avec ce pseudo)
    if (empty($passe)) {
        // fermeture de la connexion à la base de données
        mysqli_close($bd);
        $erreur = true;
        return $erreur;   //===> FIN DE LA FONCTION
    }
    
    // vérification du mot de passe
    if (! password_verify($_POST['RDDuserPassword'], $passe)) {
        $erreur = true;
    }

    // Libération de la mémoire associée au résultat de la requête
    mysqli_free_result($res);


    // si erreur --> retour
    if ($erreur) {
        // fermeture de la connexion à la base de données
        mysqli_close($bd);
        return $erreur;   //===> FIN DE LA FONCTION
    }


    // fermeture de la connexion à la base de données
    mysqli_close($bd);

    $_SESSION['pseudo'] = $pseudo;
    $_SESSION['editor'] = $redacteur; // utile pour l'affichage de la barre de navigation

    
    // Redirection vers la page de destination
    if (! empty($destinationURL)) {
        header('Location: '. $destinationURL);
        exit(); //===> Fin du script
    }

    header('Location: ./profile.php');
    exit(); //===> Fin du script
}
