<?php
/* ========================================================================
 * File Name: library_general.php - Library of general functions
 * Author: CUINET Antoine
 * Version: 1.0
 * Date: September 2024
 *
 * Note: This code was developed by CUINET Antoine, see https://acuinet.fr
 * 
 * Naming rules: 
 *   - Function names respect SNAKE CASE notation
 *   - The names of the functions are explicit (begin with db_, render_, ...)
======================================================================== */



/**
 * Stopping the script if database error
 *
 * Displaying an error message, then stopping the script
 * Function called when a 'database' error occurs:
 *      - During the connection phase to the MySQL or MariaDB server
 *      - Or when sending a request fails
 *
 * @param  array    $err    Useful information for debugging
 *
 * @return void
 */
function db_exit_error(array $err):void {
    ob_end_clean(); // Deleting anything that may have already been generated

    render_head('Erreur...', '', '.');
    echo '<main>';
    
    if (IS_DEV){
        echo    '<h4>', $err['titre'], '</h4>',
                '<pre>',
                    '<strong>Erreur mysqli</strong> : ',  $err['code'], "\n",
                    $err['message'], "\n";
        if (isset($err['autres'])){
            echo "\n";
            foreach($err['autres'] as $cle => $valeur){
                echo    '<strong>', $cle, '</strong> :', "\n", $valeur, "\n";
            }
        }
        echo    "\n",'<strong>Pile des appels de fonctions :</strong>', "\n", $err['appels'],
                '</pre>';
    }
    else {
        echo '<h1>Oups... une erreur s\'est produite</h1>',
        '<p>Une erreur est survenue, veuillez réessayer ultérieurement.</p>',
        '<button class="btn" onclick="window.location.href=\'./index.php\'">Retour à l\'accueil</button>';
    }
    echo '</main>';
    render_footer();

    if (! IS_DEV){
        // Storing errors in a log file
        $fichier = @fopen('error.log', 'a');
        if($fichier){
            fwrite($fichier, '['.date('d/m/Y').' '.date('H:i:s')."]\n");
            fwrite($fichier, $err['titre']."\n");
            fwrite($fichier, "Erreur mysqli : {$err['code']}\n");
            fwrite($fichier, "{$err['message']}\n");
            if (isset($err['autres'])){
                foreach($err['autres'] as $cle => $valeur){
                    fwrite($fichier,"{$cle} :\n{$valeur}\n");
                }
            }
            fwrite($fichier,"Pile des appels de fonctions :\n");
            fwrite($fichier, "{$err['appels']}\n\n");
            fclose($fichier);
        }
    }
    exit(1);        // ==> SCRIPT STOP
}

/**
 * Opening the connection to the database
 *
 * Opening the connection to the database while managing errors
 * In case of connection error, a "clean" page with an appropriate error message is displayed
 * AND the script is stopped
 *
 * @return mysqli  database connector object
 */
function db_connect(): mysqli {
    // If the connection fails
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try{
        $conn = mysqli_connect(BD_SERVER, BD_USER, BD_PASS, BD_NAME);
    }
    catch(mysqli_sql_exception $e){
        $err['titre'] = 'Erreur de connexion';
        $err['code'] = $e->getCode();
        // $e->getMessage() is encoded in ISO-8859-1, it must be converted to UTF-8
        $err['message'] = mb_convert_encoding($e->getMessage(), 'UTF-8', 'ISO-8859-1');
        $err['appels'] = $e->getTraceAsString(); // Stack of function calls
        $err['autres'] = array('Paramètres' =>   'BD_SERVER : '. BD_SERVER
                                                    ."\n".'BD_USER : '. BD_USER
                                                    ."\n".'BD_PASS : '. BD_PASS
                                                    ."\n".'BD_NAME : '. BD_NAME);
        db_exit_error($err); // ==> SCRIPT STOP
    }
    try{
        //mysqli_set_charset() sets the default character set to use when sending data to and from the database server.
        mysqli_set_charset($conn, 'utf8');
        return $conn;     // ===> CONNECTION OK
    }
    catch(mysqli_sql_exception $e){
        $err['titre'] = 'Erreur lors de la définition du charset';
        $err['code'] = $e->getCode();
        $err['message'] = mb_convert_encoding($e->getMessage(), 'UTF-8', 'ISO-8859-1');
        $err['appels'] = $e->getTraceAsString();
        db_exit_error($err); // ==> SCRIPT STOP
    }
}

/**
 * Sending an SQL request to the database server while managing errors
 *
 * In case of an error, a clean page with an error message is displayed and the script is stopped
 * If the request is successful, this function returns:
 *      - an object of type mysqli_result in the case of a SELECT request
 *      - true in the case of an INSERT, DELETE or UPDATE request
 *
 * @param  mysqli              $db     database connector object
 * @param  string              $sql    SQL request
 *
 * @return mysqli_result|bool          Request result
 */
function db_send_request(mysqli $db, string $sql): mysqli_result|bool {
    try{
        return mysqli_query($db, $sql);
    }
    catch(mysqli_sql_exception $e){
        $err['titre'] = 'Erreur de requête';
        $err['code'] = $e->getCode();
        $err['message'] = $e->getMessage();
        $err['appels'] = $e->getTraceAsString();
        $err['autres'] = array('Requête' => $sql);
        db_exit_error($err);    // ==> SCRIPT STOP
    }
}


/**
 * Output protection (HTML code generated for the client)
 *
 * Function to call for all strings coming from:
 *    - user input (forms)
 *    - from the database
 * Helps protect against XSS (Cross site scripting) attacks
 * Converts all eligible characters to HTML entities, including:
 *    - characters with special meaning in HTML (<, >, ...)
 *    - accented characters
 *
 * If we pass an array to it, the function returns an array where all the strings
 * that it contains are protected, the other data in the table is not modified
 *
 * @param  array|string  $content   the string to protect or an array containing strings to protect
 *
 * @return array|string             the protected string or array
 */
function html_protect_outputs(array|string $content): array|string {
    if (is_array($content)) {
        foreach ($content as &$value) {
            if (is_array($value) || is_string($value)){
                $value = html_protect_outputs($value);
            }
        }
        unset ($value);
        return $content;
    }
    return htmlentities($content, ENT_QUOTES, encoding:'UTF-8');
}


/**
 * Control of keys present in $_GET or $_POST tables - hacking?
 *
 * @param  string    $global_table      'post' or 'get'
 * @param  array     $obligatory_keys   table containing the keys that must be present
 * @param  array     $optional_keys     array containing optional keys
 *
 * @return bool                         true if the parameters are correct, false otherwise
 */
function parameters_control(string $global_table, array $obligatory_keys, array $optional_keys = []): bool{
    $x = strtolower($global_table) == 'post' ? $_POST : $_GET;

    $x = array_keys($x);
    if (count(array_diff($obligatory_keys, $x)) > 0){
        return false;
    }
    if (count(array_diff($x, array_merge($obligatory_keys, $optional_keys))) > 0){
        return false;
    }
    return true;
}


/**
 * Checking the presence, validity, and decryption of a GET parameter
 *
 * @param  string  $key    GET parameter key
 * 
 * @return int     decrypted article identifier
 */
function get_verification(string $key): int {
    if (! parameters_control('get', ["$key"])){
        exit(1); // ==> End of the function
    }

    // URL decryption
    $id = decrypt_sign_URL($_GET["$key"]);

    if (! is_integer($id)){
        exit(1); // ==> End of the function
    }

    if ($id <= 0){
        exit(1); // ==> End of the function
    }

    return $id;
}


/**
 * Returns an array containing the names of the months (useful for certain displays)
 *
 * @return array    Numerical index table containing the names of the months
 */
function get_array_months() : array {
    return array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
}


/**
 * Checking form text fields
 *
 * @param  string        $text     text to check
 * @param  string        $name     string to add in the one that describes the error
 * @param  array         $errors   table in which errors are added
 * @param  ?int          $length   maximum length of the corresponding field in the database
 * @param  ?string       $regex    regular expression that the text must satisfy
 *
 * @return void
 */
function check_text(string $text, string $name, array &$errors, ?int $length = null, ?string $regex = null) : void{
    if (empty($text)){
        $errors[] = "$name ne doit pas être vide.";
    }
    else {
        if(strip_tags($text) != $text){
            $errors[] = "$name ne doit pas contenir de tags HTML.";
        }
        else if ($regex !== null && ! preg_match($regex, $text)){
            $errors[] = "$name n'est pas valide.";
        }
        if ($length !== null && mb_strlen($text, encoding:'UTF-8') > $length){
            $errors[] = "$name ne peut pas dépasser $length caractères.";
        }
    }
}


/**
 * Encrypts and signs a value to pass it into a URL using the AES-128 algorithm in CBC mode
 *
 * @param  string  $val   The value to be quantified
 * 
 * @return string  The URL encoded encrypted value
 */
function encrypt_sign_URL(string $val) : string {
	$ivlen = openssl_cipher_iv_length($cipher='AES-128-CBC');
	$iv = openssl_random_pseudo_bytes($ivlen);
	$x = openssl_encrypt($val, $cipher, base64_decode(CLE_CHIFFREMENT), OPENSSL_RAW_DATA, $iv);
	$x = $iv.$x;
	$x = base64_encode($x);
	return urlencode($x);
}


/**
 * Decrypts an encrypted value with encryptSignerURL()
 *
 * @param  string  $x   The value to decipher
 * 
 * @return string|false The decrypted value or false if error
 */
function decrypt_sign_URL(string $x) : string|false {
	$x = base64_decode($x);
    $ivlen = openssl_cipher_iv_length($cipher='AES-128-CBC');
    $iv = substr($x, 0, $ivlen);
    $x = substr($x, $ivlen);
    return openssl_decrypt($x, $cipher, base64_decode(CLE_CHIFFREMENT), OPENSSL_RAW_DATA, $iv);
}
