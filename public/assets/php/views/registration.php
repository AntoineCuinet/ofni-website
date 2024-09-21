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


// si formulaire soumis, traitement de la demande d'inscription
if (isset($_POST['btnInscription'])) {
    $erreurs = traitementInscriptionL(); // ne revient pas quand les données soumises sont valides
}
else{
    $erreurs = null;
}


affEntete('Inscription', '.');

// Génération du contenu de la page
affFormulaireL($erreurs);

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
 * Contenu de la page : affichage du formulaire d'inscription
 *
 * En absence de soumission (i.e. lors du premier affichage), $err est égal à null
 * Quand l'inscription échoue, $err est un tableau de chaînes
 *
 * @param ?array    $err    Tableau contenant les erreurs en cas de soumission du formulaire, null lors du premier affichage
 *
 * @return void
 */
function affFormulaireL(?array $err): void {
    // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
    if (isset($_POST['btnInscription'])){
        $values = htmlProtegerSorties($_POST);
        $values['RDDuserCivility'] = (int)($_POST['RDDuserCivility'] ?? -1);
        $values['RDDuserNewsLetter'] = isset($_POST['RDDuserNewsLetter']);
    }
    else{
        $values['RDDuserPseudo'] = $values['RDDuserFirstName'] = $values['RDDuserLastName'] = $values['RDDuserMail'] = $values['RDDuserBirth'] = '';
        $values['RDDuserCivility'] = -1;
        $values['RDDuserNewsLetter'] = true;
    }

    echo
        '<main>',
            '<section>',
                '<h2>Formulaire d\'inscription</h2>',
                '<p>Pour vous inscrire, remplissez le formulaire ci-dessous.</p>';

    if (is_array($err)) {
        echo    '<div class="erreur">Les erreurs suivantes ont été relevées lors de votre inscription :',
                    '<ul>';
        foreach ($err as $e) {
            echo        '<li>', $e, '</li>';
        }
        echo        '</ul>',
                '</div>';
    }


    echo
            '<form method="post" action="registration.php">',
                '<table>';

    affLigneInput(  'Choisissez un pseudo :', array('type' => 'text', 'name' => 'RDDuserPseudo', 'value' => $values['RDDuserPseudo'],
                    'placeholder' => LMIN_PSEUDO . ' caractères alphanumériques minimum', 'required' => null));
    echo
                    '<tr>',
                        '<td>Votre civilité :</td>',
                        '<td>';
    $radios = [1 => 'Monsieur', 2 => 'Madame', 3 => 'Autre'];
    foreach ($radios as $value => $label){
        echo                '<label><input type="radio" name="RDDuserCivility" value="', $value, '"',
                            $value === $values['RDDuserCivility'] ? ' checked' : '', '> ', $label, '</label> ';
    }
    echo                '</td>',
                    '</tr>';


    affLigneInput('Votre nom :', array('type' => 'text', 'name' => 'RDDuserLastName', 'value' => $values['RDDuserLastName'], 'required' => null));
    affLigneInput('Votre prénom :', array('type' => 'text', 'name' => 'RDDuserFirstName', 'value' => $values['RDDuserFirstName'], 'required' => null));
    affLigneInput('Votre date de naissance :', array('type' => 'date', 'name' => 'RDDuserBirth', 'value' => $values['RDDuserBirth'], 'required' => null));
    affLigneInput('Votre email :', array('type' => 'email', 'name' => 'RDDuserMail', 'value' => $values['RDDuserMail'], 'required' => null));
    affLigneInput(  'Choisissez un mot de passe :', array('type' => 'password', 'name' => 'passe1', 'value' => '',
                    'placeholder' => LMIN_PASSWORD . ' caractères minimum', 'required' => null));
    affLigneInput('Répétez le mot de passe :', array('type' => 'password', 'name' => 'passe2', 'value' => '', 'required' => null));

    echo
                    '<tr>',
                        '<td colspan="2">',
                            '<label><input type="checkbox" name="cbCGU" value="1" required>',
                                ' J\'ai lu et j\'accepte les conditions générales d\'utilisation </label>',
                            '<label><input type="checkbox" name="RDDuserNewsLetter" value="1"',
                            $values['RDDuserNewsLetter'] ? ' checked' : '',
                                '> J\'accepte de recevoir des tonnes de mails drôles !</label>',
                        '</td>',
                    '</tr>',
                    '<tr>',
                        '<td colspan="2">',
                            '<input class="btn" type="submit" name="btnInscription" value="S\'inscrire"> ',
                            '<input class="btn-danger" type="reset" value="Réinitialiser">',
                        '</td>',
                    '</tr>',
                '</table>',
                '<p>Déjà inscrit ? <a href="./connection.php">connectez-vous</a> !</p>',
            '</form>',
        '</section>',
    '</main>';
}


/**
 * Traitement d'une demande d'inscription
 *
 * Vérification de la validité des données
 * Si on trouve des erreurs => return un tableau les contenant
 * Sinon
 *     Enregistrement du nouvel inscrit dans la base
 *     Enregistrement du pseudo (et du droit de redacteur fixé à 0) de l'utilisateur dans une variable de session, et redirection vers la page protegee.php
 * FinSi
 *
 * Toutes les erreurs détectées qui nécessitent une modification du code HTML sont considérées comme des tentatives de piratage
 * et donc entraînent l'appel de la fonction em_sessionExit() sauf :
 * - les éventuelles suppressions des attributs required car l'attribut required est une nouveauté apparue dans la version HTML5 et
 *   nous souhaitons que l'application fonctionne également correctement sur les vieux navigateurs qui ne supportent pas encore HTML5
 * - une éventuelle modification de l'input de type date en input de type text car c'est ce que font les navigateurs qui ne supportent
 *   pas les input de type date
 *
 *  @return array    un tableau contenant les erreurs s'il y en a
 */
function traitementInscriptionL(): array {

    if( !parametresControle('post', ['RDDuserPseudo', 'RDDuserFirstName', 'RDDuserLastName', 'RDDuserBirth',
                                     'passe1', 'passe2', 'RDDuserMail', 'cbCGU', 'RDDuserCivility', 'btnInscription'], ['RDDuserNewsLetter'])) {
        sessionExit();
    }

    $erreurs = [];

    // vérification du pseudo
    $pseudo = $_POST['RDDuserPseudo'] = trim($_POST['RDDuserPseudo']);

    if (!preg_match('/^[0-9a-zA-Z]{' . LMIN_PSEUDO . ',' . LMAX_PSEUDO . '}$/u', $pseudo)) {
        $erreurs[] = 'Le pseudo doit contenir entre '. LMIN_PSEUDO .' et '. LMAX_PSEUDO . ' caractères alphanumériques, sans signe diacritique.';
    }

    // vérification de la civilité
    if (! isset($_POST['RDDuserCivility'])){
        $erreurs[] = 'Vous devez choisir une civilité.';
    }
    else if (! (estEntier($_POST['RDDuserCivility']) && estEntre($_POST['RDDuserCivility'], 1, 3))){
        sessionExit();
    }

    // vérification des noms et prénoms
    $expRegNomPrenom = '/^[[:alpha:]]([\' -]?[[:alpha:]]+)*$/u';
    $nom = $_POST['RDDuserFirstName'] = trim($_POST['RDDuserFirstName']);
    $prenom = $_POST['RDDuserLastName'] = trim($_POST['RDDuserLastName']);
    verifierTexte($nom, 'Le nom', $erreurs, LMAX_LAST_NAME, $expRegNomPrenom);
    verifierTexte($prenom, 'Le prénom', $erreurs, LMAX_FIRST_NAME, $expRegNomPrenom);

    // vérification du format de l'adresse email
    $email = $_POST['RDDuserMail'] = trim($_POST['RDDuserMail']);
    verifierTexte($email, 'L\'adresse email', $erreurs, LMAX_MAIL);

    // la validation faite par le navigateur en utilisant le type email pour l'élément HTML input
    // est moins forte que celle faite ci-dessous avec la fonction filter_var()
    // Exemple : 'l@i' passe la validation faite par le navigateur et ne passe pas
    // celle faite ci-dessous
    if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'L\'adresse email n\'est pas valide.';
    }

    // vérification de la date de naissance
    if (empty($_POST['RDDuserBirth'])){
        $erreurs[] = 'La date de naissance doit être renseignée.';
    }
    else{
        if(! preg_match('/^\\d{4}(-\\d{2}){2}$/u', $_POST['RDDuserBirth'])){ //vieux navigateur qui ne supporte pas le type date ?
            $erreurs[] = 'la date de naissance doit être au format "AAAA-MM-JJ".';
        }
        else{
            list($annee, $mois, $jour) = explode('-', $_POST['RDDuserBirth']);
            if (!checkdate($mois, $jour, $annee)) {
                $erreurs[] = 'La date de naissance n\'est pas valide.';
            }
            else if (mktime(0,0,0,$mois,$jour,$annee + AGE_MINIMUM) > time()) {
                $erreurs[] = 'Vous devez avoir au moins '. AGE_MINIMUM. ' ans pour vous inscrire.';
            }
        }
    }

    // vérification des mots de passe
    $_POST['passe1'] = trim($_POST['passe1']);
    $_POST['passe2'] = trim($_POST['passe2']);
    if ($_POST['passe1'] !== $_POST['passe2']) {
        $erreurs[] = 'Les mots de passe doivent être identiques.';
    }
    $nb = mb_strlen($_POST['passe1'], encoding:'UTF-8');
    if ($nb < LMIN_PASSWORD){
        $erreurs[] = 'Le mot de passe doit être constitué d\'au moins '. LMIN_PASSWORD . ' caractères.';
    }

    // vérification de la valeur de l'élément cbCGU
    if (! isset($_POST['cbCGU'])){
        $erreurs[] = 'Vous devez accepter les conditions générales d\'utilisation .';
    }
    else if ($_POST['cbCGU'] !== '1'){
        sessionExit();
    }

    // vérification de la valeur de $_POST['RDDuserNewsLetter'] si l'utilisateur accepte de recevoir des mails pourris
    if (isset($_POST['RDDuserNewsLetter']) && $_POST['RDDuserNewsLetter'] !== '1'){
        sessionExit();
    }

    // si erreurs --> retour
    if (count($erreurs) > 0) {
        return $erreurs;   //===> FIN DE LA FONCTION
    }

    // on vérifie si le pseudo et l'adresse email ne sont pas encore utilisés que si tous les autres champs
    // sont valides car ces 2 dernières vérifications nécessitent une connexion au serveur de base de données
    // consommatrice de ressources système

    // ouverture de la connexion à la base
    $bd = bdConnect();

    // protection des entrées
    $pseudo2 = mysqli_real_escape_string($bd, $pseudo); // fait par principe, mais inutile ici car on a déjà vérifié que le pseudo
                                                        // ne contenait que des caractères alphanumériques
    $email = mysqli_real_escape_string($bd, $email);

    $sql = "SELECT RDDuserPseudo, RDDuserMail FROM RDD_user WHERE RDDuserPseudo = '$pseudo2' OR RDDuserMail = '$email'";
    $res = bdSendRequest($bd, $sql);

    while($tab = mysqli_fetch_assoc($res)) {
        if ($tab['RDDuserPseudo'] == $pseudo){
            $erreurs[] = 'Le pseudo choisi est déjà utilisé.';
        }
        if ($tab['RDDuserMail'] == $email){
            $erreurs[] = 'L\'adresse email est déjà utilisée.';
        }
    }
    // Libération de la mémoire associée au résultat de la requête
    mysqli_free_result($res);


    // si erreurs --> retour
    if (count($erreurs) > 0) {
        // fermeture de la connexion à la base de données
        mysqli_close($bd);
        return $erreurs;   //===> FIN DE LA FONCTION
    }

    // calcul du hash du mot de passe pour enregistrement dans la base.
    $passe = password_hash($_POST['passe1'], PASSWORD_DEFAULT);

    $passe = mysqli_real_escape_string($bd, $passe);

    $dateNaissance = $annee . '-' . $mois . '-' . $jour;

    $nom = mysqli_real_escape_string($bd, $nom);
    $prenom = mysqli_real_escape_string($bd, $prenom);

    $civilite = (int) $_POST['RDDuserCivility'];
    $civilite = $civilite == 1 ? 'M' : ($civilite == 2 ? 'W' : 'O');

    $mail = isset($_POST['RDDuserNewsLetter']) ? 1 : 0;

    // les valeurs sont écrites en respectant l'ordre de création des champs dans la table usager
    $sql = "INSERT INTO RDD_user (RDDuserPseudo, RDDuserFirstName, RDDuserLastName, RDDuserMail, RDDuserPassword, RDDuserBirth, RDDuserEditor, RDDuserCivility, RDDuserNewsLetter, RDDuserCreationDate)
            VALUES ('$pseudo2', '$nom', '$prenom', '$email', '$passe', '$dateNaissance', 0, '$civilite', $mail, NOW())";

    bdSendRequest($bd, $sql);


    // fermeture de la connexion à la base de données
    mysqli_close($bd);

    // mémorisation du pseudo dans une variable de session (car affiché dans la barre de navigation sur toutes les pages)
    // enregistrement dans la variable de session du pseudo avant passage par la fonction mysqli_real_escape_string()
    // car, d'une façon générale, celle-ci risque de rajouter des antislashs
    // Rappel : ici, elle ne rajoutera jamais d'antislash car le pseudo ne peut contenir que des caractères alphanumériques
    $_SESSION['pseudo'] = $pseudo;

    $_SESSION['editor'] = false; // utile pour l'affichage de la barre de navigation

    // redirection vers la page index.php
    header('Location: ./profile.php');
    exit(); //===> Fin du script
}
