<?php require('db.php'); 

if(empty($_SESSION['user'])) {
    header('Location: login.php');
}

$user = $_SESSION['user'];
$filepath = 'photos/'.$user->id.'/'.$user->file;
$dir = new DirectoryIterator(dirname('photos/'.$user->id)); 

foreach($dir as $fileinfo) {
    if($fileinfo->isDot()) {
        unlink($fileinfo->getPathname());
    }
}
if($user->file && file_exists($filepath) && is_file($filepath)) {
    unlink($filepath);
    rmdir('photos/'.$user->id);
}

$req = $db->prepare('DELETE FROM users WHERE id = :id');
$req->bindValue(':id', $user->id, PDO::PARAM_INT);
$req->execute();

unset($_SESSION['user']);
session_destroy();
header('Location: ../../index.php');
?>
