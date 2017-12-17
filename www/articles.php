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
$action = (isset($_GET['action'])) ? $_GET['action'] : "ajouter";
$id_article = (isset($_GET['id_article'])) ? $_GET['id_article'] : '';

//test si on utilise la commande sql d'insertion, d'ajout de commentaire ou d'update
if (isset($_POST['action'])){
    $action = implode($_POST['action']);
    $id_article = (isset($_POST['id_article'])) ? implode($_POST['id_article']) : $id_article;
}

//déclaration variables en cas de non passage dans les boucles comme après une update
$bouton = "Ajouter";
$checked = "";
$disabled = "";
$titreForm = "";
$texteForm = "";

//test si l'utilisateur est connecté et a le droit d'être içi
if ($is_connect == FALSE && !($action == 'consulter')) {
  header('Location: index.php');
  exit();
} else {
    
//si on consulte un article on récupère toutes valeurs nécessaires à l'affichage de l'article qu'on cherche à consulter
  if($action == 'consulter'){
      
  $articleInfo = "SELECT * "
                . "FROM articles "
                . "WHERE id = :id_article";

//on récupère les info sur l'articles lui-même
  $sth = $bdd->prepare($articleInfo);
  $sth->bindValue(':id_article', $id_article, PDO::PARAM_INT);
  $sth->execute();
      
  $info = $sth->fetch(PDO::FETCH_ASSOC);
      
  $sqlComments = "SELECT * "
              . "FROM commentaires as co "
              . "INNER JOIN utilisateurs as ut "
              . "ON co.idUser = ut.id "
              . "WHERE idArticle = :id_article";
                  
//on récupère les infos sur les commentaires de l'article
  $sth = $bdd->prepare($sqlComments);
  $sth->bindValue(':id_article', $id_article, PDO::PARAM_INT);
  $sth->execute();
      
  $comments = $sth->fetchAll(PDO::FETCH_ASSOC);
      
  }
      
//si on modifie l'article...
  if ($action == "modifier"){
        
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
          
      if ($checked == "checked" && $disabled == "disabled"){
          
        $publie = '1';
            
      }
          
    }
        
  }
      
//si on consulte l'article et que l'on soumet le bouton submit c'est pour poster un commentaire alors on prépare la commande sql en conséquence
if ($action == 'consulter'){
    
  $sqlComments = "SELECT * "
                . "FROM commentaires "
                . "WHERE idArticle = :id_article";
                    
                    
}
    
//commandes php lorsque le bouton a été déclenché
if (isset($_POST['submit'])) {
    
//si il n'y a pas d'erreur lors de l'upload du fichier et prévention d'erreur sur la page de consultation en passant dans cette boucle que si l'on ne consulte pas
if(isset($_FILES) && !($action == 'consulter')){
  
  if($_FILES['file']['error'] == 0){
    $noerror = 'noerror';

//sinon si il a une erreur modification des variables 
  } else {
    $notification = 'Une erreur est survenue lors du traitement de l\'image';
    $_SESSION['notification_alert'] = FALSE;
  }  
}
   
//récupération de la date selon le format année-mois-jour
  $date = date("Y-m-d");
      
//test si les sections texte et titre sont vides ou non
  if( (!empty($_POST['titre']) AND !empty($_POST['texte'])) OR (!empty($_POST['texteCommentaire'])) ){
      
//récupération du status du checkbox
    $publie = isset($_POST['publie']) ? 1 : 0;
        
//choix de la fonction à adopter
    //si on modifie
    if ($action == "modifier"){
        
      $insertUpdate = "UPDATE articles "
              . "SET titre = :titre, "
              . "texte = :texte, "
              . "publie = :publie "
              . "WHERE id = :id_article";
                  
      $notification = 'Votre article a été mis à jour !';
          
    //si on consulte et commente   
    } if($action == "consulter"){
        
      $insertCommentaire = "INSERT INTO commentaires (idArticle, idUser, commentaireTexte) "
                          . "VALUES (:id_article, :idUser, :texteCommentaire)";
                              
      $sthCommentaire = $bdd->prepare($insertCommentaire);
      $sthCommentaire->bindValue(':id_article', $id_article, PDO::PARAM_INT);
      $sthCommentaire->bindValue('idUser', $idUser, PDO::PARAM_INT);
      $sthCommentaire->bindValue('texteCommentaire', $_POST['texteCommentaire'], PDO::PARAM_STR);
       
//exécution et vérification du bon fonctionnement de la requête   
      if($sthCommentaire->execute()){
          
      $_SESSION['notification'] = 'Votre commentaire a été publié !';
      $_SESSION['notification_alert'] = TRUE;
          
    } else {
        
      $_SESSION['notification'] = 'Une erreur est survenue...';
      $_SESSION['notification_alert'] = FALSE;
          
    }

//redirection vers le même article après le post du commentaire
    header("Location:articles.php?action=consulter&id_article=$id_article");
        
    //si on ajoute un article
    } else {
        
      $insertUpdate = "INSERT INTO articles (titre, texte, publie, date) "
              . "VALUES (:titre, :texte, :publie, :date)";
                  
      $notification = 'Votre article a été publié !';
          
    }
        
      $_SESSION['notification_alert'] = TRUE;

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
      
//test de l'existance d'une image et remplacement pour une mise à jour d'article (vider le cache est nécessaire pour voir le changement)      
        if (file_exists('img/' . $id_article . '.' . $extension)){
          unlink('img/' . $id_article . '.' . $extension);
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
    
<!-- base de la page -->
<div class="container">
    <div class="row">
        <div class="col-lg-12 text-center">
          <!-- choix de l'affichage de la page en fonction des paramètres -->
          <?php
          if($action == 'ajouter'){
          ?>
            <h1 class="mt-5">Ajout d'un article</h1>
            <p class="lead">
                Vous pouvez ajouter un article grâce au formulaire suivant.</br> 
                Attention tout article ne suivant pas la charte du site sera supprimé sans préavis</p>
          <?php

        } if ($action == 'modifier'){
          
          ?>
              
            <h1 class="mt-5">Modification d'un article</h1>
            <p class="lead">
                Vous pouvez modifer un article grâce au formulaire suivant.</br> 
            Attention tout article ne suivant pas la charte du site sera supprimé sans préavis</p>          
                
          <?php
        } if ($action == 'consulter') {
          ?>
              
            <h1 class="mt-5">Consultation d'un article</h1>
            <p class="lead"><?= $info['titre'] ?></p>
            
            <div class="card">
                <div class="card-block mt-2">
                    <p><?= $info['texte'] ?></p>
                </div>
            </div>
            
            </br><h3>Commentaires</h3></br>
            
            <?php
//affichage d'un message si il n'y a aucun commentaire
              if(empty($comments)){
            ?>
            
              <div class="card">
                <div class="card-block mt-2">
                  <p>Il n'y a pour l'instant aucun commentaire.</p>
                </div>    
            </div>

            <?php 

              } else {

//boucle d'affichage des commentaires
                foreach ($comments as $commentaire) {
            ?> 
            <div class="card">
                <div class="card-block">
                    <div class="card-block col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card-header"><?= $commentaire['prenom'] ?> <?= $commentaire['nom'] ?></div>
                        <div class="card-title mt-2"><?= $commentaire['commentaireTexte'] ?></div>
                    </div>
                </div>    
            </div>
          </br>
          <?php
          } 
        }
      }
          ?>
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
              
<!-- formulaire d'ajout d'article ou de commentaire -->
    <form action="articles.php" method="post" enctype="multipart/form-data" id="form_article">
        <?php 
        if ($is_connect == TRUE){
//dans le cas d'un ajout de comentaire
            if($action == 'consulter'){
          ?>
        <div class="form-group ml-4">
        <input type="hidden" name="id_article[]" value="<?= $id_article ?>">
        <input type="hidden" name="action[]" value="<?= $action ?>">
        <label for="texte">Commentaire</label>
        <textarea class="form-control col-sm-10 col-md-10 col-lg-10 col-xl-10" id="texteCommentaire" placeholder="Insérer votre commentaire ici." rows="3" name="texteCommentaire" required></textarea>
        <div class=" form-group form-check">
            <label class="form-check-label" for="accordCommentaire">
                <input type="checkbox" class="form-check-input" id="accordCommentaire" name="accordCommentaire" value="1" required> Ce commentaire ne contient rien d'insultant, de sexuel, ou tout autre caractère aggressif et/ou inaproprié sur ce site.</label>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Commenter</button>
        </div>
        <?php
//dans le cas d'une modification d'article avec remplissage des éléments avec les informations de l'article déjà en base de données
          } if ($action == "modifier"){
        ?>
        <input type="hidden" name="id_article[]" value="<?= $id_article ?>">
        <input type="hidden" name="action[]" value="<?= $action ?>"> 
        <?php
              
//verrouillage de la checkbox si l'article est modifiable alors il a été publié
          if ($checked == "checked" && $disabled == "disabled"){
        ?>
            
               <input type="hidden" name="publie[]" value="1";>
                   
        <?php
            
          }
              
        ?>

<!-- formulaire avec les éléments préremplis si besoin -->
               <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" class="col-sm-12 col-md-8 col-lg-6 col-xl-4 justify-content-center form-control" id="titre" placeholder="Titre" name="titre" required value="<?= $titreForm ?>">
            <label for="texte">Texte</label>
            <textarea class="form-control col-sm-10 col-md-10 col-lg-10 col-xl-10" id="texte" placeholder="Insérer votre article ici." rows="3" name="texte" required><?= $texteForm ?></textarea>
            <div class="form-group mt-4">
          <?php
//affichage d'une image si il en existe
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
    <?php
      }
    }
  }
    ?>
    </form>
    
    
    <!-- script nécessaire à la page -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        
    <!-- inclusion du footer -->
<?php
include 'includes/footer.inc.php';
    }
?>