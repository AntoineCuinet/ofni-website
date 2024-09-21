<?php require('db.php'); 

//poour supprimer la session
//session_destroy();

if(empty($_SESSION['user'])) {
    header('Location: login.php');
}

$title_page = 'Votre espace';
$description_page = 'Espace personelle';
$user = $_SESSION['user'];
$title = 'Salut '.$user->firstname.' !';
$firstnameError = $lastnameError = $emailError = $photoError = "";

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
    $succes = '';
    
    if(empty($firstname) || strlen($firstname) < 3) {
        $firstnameError = 'Le prénom n\'est pas valide.';
    }
    if(empty($lastname) || strlen($lastname) < 3) {
        $lastnameError = 'Le nom n\'est pas valide.';
    }
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'L\'email n\'est pas valide.';
    }

    if(empty($firstnameError) && empty($lastnameError) && empty($emailError)){
        $req = $db->prepare('SELECT * FROM users WHERE email = :email AND id != :id');
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->bindValue(':id', $user->id, PDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() > 0) {
            $emailError = 'L\'Email est déjà utilisé.';
        }

        // traitement image
        if(!empty($_FILES['file']['name'])){
            $photo = $_FILES['file'];
            $filepath = 'photos/'.$user->id;
            @mkdir($filepath, 0777, true);
            $allowedExt = ['jpeg', 'jpg', 'png'];
            $ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

            if(!in_array($ext, $allowedExt)) {
                $photoError = 'Le fichier n\'est pas autorisé.';
            } else {
                $info = getimagesize($photo['tmp_name']);
                $width = $info[0];
                $height = $info[1];

                if($width < 150 || $height < 150) {
                    $photoError = 'L\'image est trop petite.';
                } else {
                    $filename = uniqid($user->id, true).'.'.$ext;
                    move_uploaded_file($photo['tmp_name'], $filepath.'/'.$filename);
                }
            }
        }

        if(empty($emailError) && empty($photoError)) {
            $req = $db->prepare('SELECT * FROM users WHERE id = :id');
            $req->bindValue(':id', $user->id, PDO::PARAM_INT);
            $req->execute();

            $user = $req->fetch();

            if($user->file) {
                $oldFilePath = 'photos/'.$user->id.'/'.$user->file;
            }

            $req = $db->prepare('UPDATE users SET firstname=:firstname, lastname=:lastname, email=:email, file=:file WHERE id = :id');
            $req->bindValue(':firstname', $firstname, PDO::PARAM_STR);
            $req->bindValue(':lastname', $lastname, PDO::PARAM_STR);
            $req->bindValue(':email', $email, PDO::PARAM_STR);
            $req->bindValue(':file', $filename ?? $user->file, PDO::PARAM_STR);
            $req->bindValue(':id', $user->id, PDO::PARAM_INT);
            $req->execute();

            $req = $db->prepare('SELECT * FROM users WHERE id = :id');
            $req->bindValue(':id', $user->id, PDO::PARAM_INT);
            $req->execute();

            $user = $req->fetch();

            unset($_SESSION['user']);
            $_SESSION['user'] = $user;

            if(!empty($oldFilePath) && !empty($filename)) {
                @unlink($oldFilePath);
            }
            
            $succes = 'Informations mises à jour.';

            $title = 'Salut '.$user->firstname.' !';
        }
    }
}
?>

<?php include('header.php'); ?>

    <h2><?= $title_page; ?></h2>
    <br><br>
    <h4><?= $title; ?></h4>
    <br>

    <?php if(!empty($succes)): ?>
        <div class="alert alert-succes">
            <p><?= $succes; ?></p>
        </div>
    <?php endif; ?>


    <?php if(!empty($user->file)): ?>
        <a href="photos/<?= $user->id.'/'.$user->file; ?>" style="float: right;">
            <img src="photos/<?= $user->id.'/'.$user->file; ?>" alt="photo de profil" width="50px" height="50px">
        </a>
    <?php endif; ?>

    <form method="POST" action="dashboard.php" role="modification" enctype="multipart/form-data">

        <div class="form-group">
            <label>
                Votre photo de profil
                <input type="file" name="file" value=""> <!--TODO: modifier value-->
            </label>
            <!-- afficher message erreur -->
            <?php if(!empty($photoError)): ?>
                <div class="alert alert-danger">
                    <p><?= $photoError; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>

        <div class="form-group">
            <label>
                Votre prénom 
                <input type="text" name="firstname" placeholder="Ton prénom" value="<?= $firstname ?? $user->firstname; ?>" required>
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
                <input type="text" name="lastname" placeholder="Ton nom" value="<?= $lastname ?? $user->lastname; ?>" required>
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
                <input type="email" name="email" placeholder="Ton email" value="<?= $email ?? $user->email; ?>" required>
            </label>
            <!-- afficher message erreur -->
            <?php if(!empty($emailError)): ?>
                <div class="alert alert-danger">
                    <p><?= $emailError; ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>

        <input type="submit" value="Modifier" name="valider">
    </form>

    <br>
    <p><a href="password.php">Modifier mon mot de passe.</a></p>
    <br>
    <a onclick="return confirm('Confirmer la suppression de votre compte ?');" href="delate.php" class="btn btn-danger delate">Supprimer son compte.</a>

</body>
</html>