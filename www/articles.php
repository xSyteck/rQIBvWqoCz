<?php
//ouverture session
session_start();

//utilisation des fichiers annexes ci-dessous
require_once 'config/init.conf.php';
require_once 'config/bdd.conf.php';
require_once 'config/connexion.inc.php';
require_once 'includes/fonctions.inc.php';

//déclaration du fuseau horaire
date_default_timezone_set('Europe/Paris'); 

//déclaration variables en cas de non passage dans les boucles comme après une update
$bouton = "Ajouter";
$checked = "";
$disabled = "";
$titreForm = "";
$texteForm = "";

//test si l'utilisateur est connecté et a le droit d'être içi
if ($is_connect == FALSE) {
  header('Location: index.php');
  exit();
} else {

//récupération de la valeur action pour voir si modification ou ajout
  $action = (isset($_GET['action']) && ($_GET['action'] == "modifier")) ? "modifier" : "ajouter";

//récupération de l'id de l'article en cours de modification
  if ($action == "modifier"){
    $id_article = (isset($_GET['id_article']) ? $_GET['id_article'] : "erreur");

    //si l'id n'a pas pu être récupérer et que l'action est tout de même modifier on redirige
    if (!is_numeric($id_article)){

      $_SESSION['notification'] = "Une erreur est survenue veuillez rééssayer ultérieurement.";
      $_SESSION['notification_alert'] = FALSE;
      header('Location: index.php');

//si tout est en ordre...
    } else {

//on récupère la ligne dans la base de donnée où se trouve l'article à modifier
      $articleModification = "SELECT * "
                            . "FROM articles "
                            . "WHERE id = :id_article";

      $sthModification = $bdd->prepare($articleModification);
      $sthModification->bindValue(':id_article', $id_article, PDO::PARAM_INT);
      $sthModification->execute();

      $article = $sthModification->fetch(PDO::FETCH_ASSOC);

//on teste si l'id correspond à un article ou non si il n'y a rien on retourne le user vers l'acceuil avec un message d'erreur
      if (empty($article)){
      $_SESSION['notification'] = "L'article que vous essayez de modifier n'existe pas.";
      $_SESSION['notification_alert'] = FALSE;
      header('Location: index.php');
      }

      //déclaration des variables pour modifier le formulaire en fonction de la valeur de action
      $bouton = $action == "modifier" ? "Modifier" : "Ajouter";
      $checked = $action == "modifier" ? "checked" : "";
      $disabled = $action == "modifier" ? "disabled" : "";
      $titreForm = $article['titre'];
      $texteForm = $article['texte'];

      if ($checked == "checked" && $disabled == "disabled"){

        $publie = '1';

      }

    }

  }

//commandes php lorsque le bouton a été déclenché
if (isset($_POST['submit'])) {

//si il n'y a pas d'erreur lors de l'upload du fichier
  if($_FILES['file']['error'] == 0){
    $noerror = 'noerror';

//sinon si il a une erreur modification des variables 
  } else {
    $notification = 'Une erreur est survenue lors du traitement de l\'image';
    $_SESSION['notification_alert'] = FALSE;
  }

//récupération de la date selon le format année-mois-jour
  $date = date("Y-m-d");

//test si les sections texte et titre sont vides ou non
  if(!empty($_POST['titre']) AND !empty($_POST['texte'])){

//récupération du status du checkbox
    $publie = isset($_POST['publie']) ? 1 : 0;

//test si on utilise la commande sql d'insertion ou d'update
    if (isset($_POST['action'])){
      $action = implode($_POST['action']);
      $id_article = implode($_POST['id_article']);
    }

//choix de la fonction à adopter
    if ($action == "modifier"){

      $insertUpdate = "UPDATE articles "
              . "SET titre = :titre, "
              . "texte = :texte, "
              . "publie = :publie "
              . "WHERE id = :id_article";

      $notification = 'Votre article a été mis à jour !';
      
    } else {

      $insertUpdate = "INSERT INTO articles (titre, texte, publie, date) "
              . "VALUES (:titre, :texte, :publie, :date)";

      $notification = 'Votre article a été publié !';
      
    }

      $_SESSION['notification_alert'] = TRUE;
//
    /* @var $bdd PDO */

//insertion des paramètres en BDD avec test si date est à utiliser
    $sth = $bdd->prepare($insertUpdate);
    $sth->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
    $sth->bindValue(':texte', $_POST['texte'], PDO::PARAM_STR);
    $sth->bindValue(':publie', $publie, PDO::PARAM_BOOL);
    if ($action == "modifier"){
      $sth->bindValue(':id_article', $id_article, PDO::PARAM_INT);
    } else {
      $sth->bindValue(':date', $date, PDO::PARAM_STR);
    }

//si l'éxecution de la requête se déroule sans erreur
    if ($sth->execute() == TRUE ){

      if (!($action == 'modifier')){
//la variable id_article récupère l'id du dernier article ajouté si ajout
        $id_article = $bdd->lastInsertId();
      }

//récupération de l'extension du fichier uploadé
      if (isset($_FILES)){
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        $tab_extensions = array(
            'jpg', 
            'png',
            'jpeg'
          );

        $result_extension_image = in_array($extension, $tab_extensions);

        if (file_exists('img/' . $id_article . '.' . $extension)){
          unlink('img/' . $id_article . '.' . $extension);
          exit();
        }

        move_uploaded_file($_FILES['file']['tmp_name'], 'img/' . $id_article . '.' . $extension);

      }

//si l'article ne peut être publié on change les variables d'alerte
    } else {
      $notification = 'Votre article n\'a pas pu être publié...';
      $_SESSION['notification_alert'] = FALSE;
    }
  }
  
//si une erreur survient on change les variables d'alerte
  else {
    $notification = "Une erreur est survenue...";
    $_SESSION['notification_alert'] = FALSE;
  } 

//on récupère la dernière valeur qui a été stockée dans nottification 
  $_SESSION['notification'] = $notification;

//on recharge la page pour vider les champs du formulaire et afficher les pop-up avec les messages de notification
  header('Location: articles.php');

} else {

//inclusion du header
include 'includes/header.inc.php';
?>

    <!-- page web -->
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h1 class="mt-5">Ajout d'un article</h1>
          <p class="lead">
          Vous pouvez ajouter un article grâce au formulaire suivant.</br> 
          Attention tout article ne suivant pas la charte du site sera supprimé sans préavis</p>
        </div>
      </div>

<!-- affichage du pop-up si il y a un message à afficher -->
          <?php
          if(isset($_SESSION['notification'])){
            $alert = $_SESSION['notification_alert'] == TRUE ? 'alert-success' : 'alert-danger';
          ?>

      <div class="alert <?= $alert ?> alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
            <?= $_SESSION['notification']; ?>
      </div>

          <?php
            unset($_SESSION['notification']);
            unset($_SESSION['notification_alert']);
          }
          ?>

<!-- formulaire d'ajout d'article -->
      <form action="articles.php" method="post" enctype="multipart/form-data" id="form_article">
        <?php
          if ($action == "modifier"){
        ?>
          <input type="hidden" name="id_article[]" value="<?= $id_article ?>">
          <input type="hidden" name="action[]" value="<?= $action ?>"> 
        <?php
          }

          if ($checked == "checked" && $disabled == "disabled"){
        ?>

          <input type="hidden" name="publie[]" value="1";

        <?php

          }

        ?>

        <div class="form-group">
      			<label for="titre">Titre</label>
      			<input type="text" class="col-sm-12 col-md-8 col-lg-6 col-xl-4 justify-content-center form-control" id="titre" placeholder="Titre" name="titre" required value="<?= $titreForm ?>">
      		<label for="texte">Texte</label>
      		<textarea class="form-control col-sm-10 col-md-10 col-lg-10 col-xl-10" id="texte" placeholder="Insérer votre article ici." rows="3" name="texte" required><?= $texteForm ?></textarea>
          <div class="form-group mt-4">
          <?php
          if ($action == "modifier"){
            $idImg = $article['id'];
            $imgPath = "img/$idImg.jpg";
            if (file_exists($imgPath)){
          ?>
          <img src="img/<?= $idImg ?>.jpg" class="img-thumbnail top-buffer" alt="Image <?= $idImg ?>" width="150" height="150">
          <?php
            }
          }
          ?>
          <label for="file"></label>
          <input type="file" class="form-control-file mt-2" id="file" name="file">
          <label>(Optionnel)</label>
          </div>
          <div class=" form-group form-check">
      		  <label class="form-check-label" for="publie">
              <input type="checkbox" class="form-check-input" id="publie" name="publie" value="1" <?= $checked ?> <?= $disabled ?>> Voulez-vous publier votre article ? (cochez pour oui)</label>
          </div>
          <button type="submit" class="btn btn-primary" name="submit"><?= $bouton ?></button>
        </div>
      </form>

<!-- script nécessaire à la page -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- inclusion du footer -->
<?php
include 'includes/footer.inc.php';
    }
}
?>