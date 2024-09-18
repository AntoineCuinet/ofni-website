<?php 
// Chargement des bibliothèques de fonctions
require_once('./php/library_app.php');
require_once('./php/library_general.php');

// Bufferisation des sorties
ob_start();

// Démarrage ou reprise de la session
session_start();

affEntete('Les catégories', '.');

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
    if (isset($_GET) && isset($_GET['category']) && !empty($_GET['category'])) {

        if (! parametresControle('get', ["category"])){
            affErreur('Il faut utiliser une URL de la forme : http://.../category.php?category=XXX');
            exit(1); // ==> fin de la fonction
        }

        $category = $_GET['category'];
        echo '<main>',
                '<section>',
                    '<h2>Notre meilleur séléction de "', $category, '"</h2>';

        $bd = bdConnect();

        $sql = "SELECT *
                FROM RDD_post
                LEFT JOIN RDD_category ON RDD_post.RDDpostCategory = RDD_category.RDDcategoryId
                WHERE RDDcategoryName = '$category';";

        $res = bdSendRequest($bd, $sql);

        mysqli_close($bd);

        if(mysqli_num_rows($res) === 0) {
            echo '<p>Encore aucun article pour cette catégorie...</p>';
        } else {
            while($t = mysqli_fetch_assoc($res)) {
                echo '<article>',
                        '<h3>', htmlspecialchars($t['RDDpostTitle'], ENT_QUOTES, 'UTF-8'), '</h3>',
                        '<p>', htmlspecialchars($t['RDDpostContent'], ENT_QUOTES, 'UTF-8'), '</p>',
                    '</article>';
            }
        }

        echo '<p><a href="./category.php">Voir les différentes catégories</a></p>';

        echo '</section>',
            '</main>';
    } else {
        affListeCategories();
    }
}


//_______________________________________________________________
/**
 * Affichage de la liste des catégories
 *
 * @return  void
 */
function affListeCategories(): void {
    echo '<main>',
            '<section>',
                '<h2>Liste des différentes catégories</h2>';

    $bd = bdConnect();

    $sql = 'SELECT RDDcategoryName
            FROM RDD_category
            ORDER BY RDDcategoryName DESC;';

    $res = bdSendRequest($bd, $sql);
    mysqli_close($bd);

    while($t = mysqli_fetch_assoc($res)){
        echo '<p class="categorie"><a href="./category.php?category=', $t['RDDcategoryName'], '">', $t['RDDcategoryName'], '</a></p>';
    }

    echo '</section>',
        '</main>';
}