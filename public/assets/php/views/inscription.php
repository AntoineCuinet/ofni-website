<?php require('db.php'); 

//permet de rediriger direct sur le dashbord si déjà connecter auparavant
if(!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
}

$title_page = 'Inscription';
$description_page = 'Page d\'inscription';
$firstname = $lastname = $email = $password = "";
$firstnameError = $lastnameError = $emailError = $passwordError = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

    //fonction de vérif
	function verifyInput($var) {
		$var = trim($var);
		$var = stripslashes($var);
		$var = htmlspecialchars($var);
	  
		return $var;
	}

    $firstname = verifyInput($_POST["firstname"]);
    $lastname = verifyInput($_POST["lastname"]);
    $email = verifyInput($_POST["email"]);
    $password = $_POST["password"];
    
    
    if(empty($firstname) || strlen($firstname) < 3) {
        $firstnameError = 'Le prénom n\'est pas valide.';
        $firstname = '';
    }
    if(empty($lastname) || strlen($lastname) < 3) {
        $lastnameError = 'Le nom n\'est pas valide.';
        $lastname = '';
    }
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'L\'email n\'est pas valide.';
        $email = '';
    }
    if(empty($password) || strlen($password) < 7) {
        $passwordError = 'Le mot de passe doit contenir au moins 7 charactères.';
    }

    if(empty($firstnameError) && empty($lastnameError) && empty($emailError) && empty($passwordError)){
        $req = $db->prepare('SELECT * FROM users WHERE email = :email');
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute();

        if($req->rowCount() > 0) {
            $emailError = 'Un utilisateur est déjà enregistré avec cet Email.';
        }

        if(empty($firstnameError) && empty($lastnameError) && empty($emailError) && empty($passwordError)) {
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

    <h2><?= $title_page; ?></h2>

    <?php if(!empty($succes)): ?>
        <div class="alert alert-succes">
            <p><?= $succes; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="inscription.php" role="inscription">

        <div class="form-group">
            <label>
                Votre prénom 
                <input type="text" name="firstname" placeholder="Ton prénom" value="<?= $firstname ?? ''; ?>" required>
            </label>
            <!-- afficher message erreur -->
            <?php if(!empty($firstnameError)): ?>
                <div class="alert alert-danger">
                    <p><?= $firstnameError; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>

        <div class="form-group">
            <label>
                Votre nom 
                <input type="text" name="lastname" placeholder="Ton nom" value="<?= $lastname ?? ''; ?>" required>
            </label>
            <!-- afficher message erreur -->
            <?php if(!empty($lastnameError)): ?>
                <div class="alert alert-danger">
                    <p><?= $lastnameError; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>
        
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

        <input type="submit" value="m'inscrire" name="valider">
    </form>
</body>
</html>