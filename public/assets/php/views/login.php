<?php require('db.php'); 

//permet de rediriger direct sur le dashbord si déjà connecter auparavant
if(!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
}

$title_page = 'Connexion';
$description_page = 'Page de connexion';
$email = $password = "";
$emailError = $passwordError = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

    //fonction de vérif
	function verifyInput($var) {
		$var = trim($var);
		$var = stripslashes($var);
		$var = htmlspecialchars($var);
	  
		return $var;
	}
    $email = verifyInput($_POST["email"]);
    $password = $_POST["password"];
    
    
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'L\'email n\'est pas valide.';
        $email = '';
    }
    if(empty($password) || strlen($password) < 7) {
        $passwordError = 'Le mot de passe doit contenir au moins 7 charactères.';
    }

    if(empty($emailError) && empty($passwordError)){
        $req = $db->prepare('SELECT * FROM users WHERE email = :email');
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute();

        $user = $req->fetch();
        if($user && password_verify($password, $user->password)) {
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
        }
        $passwordError = 'Mauvais identifiants.';



        if(empty($emailError) && empty($passwordError)) {
            $req = $db->prepare('INSERT INTO users (firstname, lastname, email, password, created_at) VALUES (:firstname, :lastname, :email, :password, NOW())');
            $req->bindValue(':firstname', $firstname, PDO::PARAM_STR);
            $req->bindValue(':lastname', $lastname, PDO::PARAM_STR);
            $req->bindValue(':email', $email, PDO::PARAM_STR);
            $req->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $req->execute();

            unset($firstname, $lastname, $email, $password);
            $succes = 'Votre inscription est validée ! <br> Vous pouvez <a href="login.php">vous connecter</a> !';
        }
    }
}
?>

<?php include('header.php'); ?>


    <h2><?php $title_page; ?></h2>

    <form method="POST" action="login.php" role="inscription">
        
        <div class="form-group">
            <label>
                Email
                <input type="email" name="email" placeholder="Ton email" value="<?= $email ?? ''; ?>" required>
            </label>
            <!-- afficher message erreur -->
            <?php if(!empty($emailError)): ?>
                <div class="alert alert-danger">
                    <p><?= $emailError; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>

        <div class="form-group">
            <label>
                Mot de passe
                <input type="password" name="password" placeholder="Ton mot de passe" required>
            </label>
            <!-- afficher message erreur -->
            <?php if(!empty($passwordError)): ?>
                <div class="alert alert-danger">
                    <p><?= $passwordError; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>

        <input type="submit" value="Se connecter" name="valider">
    </form>
</body>
</html>