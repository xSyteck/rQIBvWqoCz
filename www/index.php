<?php
//ouverture session
session_start();

//utilisation des fichiers annexes ci-dessous
require_once 'config/init.conf.php';
require_once 'config/bdd.conf.php';
require_once 'config/connexion.inc.php';
require_once 'includes/fonctions.inc.php';

// récupération de la page courante, ainsi que du nombre d'articles par page
  $pageCourante = isset($_GET['page']) ? $_GET['page'] : 1;
  $nbArticles = isset($_GET['nbArticles']) ? intval($_GET['nbArticles']) : 3;
  $index = pagination($pageCourante, $nbArticles);
  $search = isset($_GET['search']) ? $_GET['search'] : '';
  $action = isset($_GET['action']) ? $_GET['action'] : '';
  $id = isset($_GET['id']) ? $_GET['id'] : '';

//test si on exécute une requête de suppression depuis le bouton supprimer qui redirige vers cette page avec un paramètre $_GET['action'] = delete
  if($action == 'delete'){

//définition de la requête
    $delete = "DELETE FROM articles "
            . "WHERE id = :id";

//sécurisation des variables et exécution
    $sth = $bdd->prepare($delete);
    $sth->bindValue(':id', $id, PDO::PARAM_INT);
    $sth->execute();

//redirection vers la page d'acceuil
    header('Location:index.php');
  }
  
//récupération de l'id qui a été transmit par la méthode post et qui nécessite la commande implode ou définition en variable vide pour éviter les messages d'erreur
  if(isset($_POST['id'])){
    $id= implode($_POST['id']);
  } else {
    $id='';
  }

//test si le formulaire a été soumit
  if(isset($_POST['submit'])){
                      
//exécution et redirection
    if($sth->execute() == TRUE){      
      header('Location: index.php');
    }
  }

//préparation du deuxième paramètre pour conserver la valeur de recherche en cas de recherche
  if (isset($_GET['nbArticles'])){
    $searchArticles = "&nbArticles=$nbArticles";
  } else {
    $searchArticles = '';
  }

//récupération du nombre total d'articles selon si on a fait une recherche ou pas
if (isset($_GET['search'])){

  $search = $_GET['search'];
  $searchPage = "&search=$search";
  $totalArticles = sqlPagination($bdd, $search);

} else {

  $searchPage = '';
  $totalArticles = totalArticles($bdd);

}
  $nbPages = ceil($totalArticles / $nbArticles);

//test si le formulaire est un formulaire de recherche
if (isset($_GET['search'])){

//commande sql de recherche filtrée et triée
  $sql = "SELECT id, "
      . "titre, "
      . "texte, "
      . "DATE_FORMAT(date, '%d/%m/%Y') as date_fr "
      . "FROM articles "
      . "WHERE (titre LIKE :search OR texte LIKE :search) "
      . "AND publie=1 "
      . "ORDER BY date DESC "
      . "LIMIT :index, :nbArticles";

//sécurisation de la requête
  $sth = $bdd->prepare($sql);
  $sth->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
  $sth->bindValue(':index', $index, PDO::PARAM_INT);
  $sth->bindValue(':nbArticles', $nbArticles, PDO::PARAM_INT);

  

//exécution de la requête
  if ($sth->execute() == TRUE ){
    $tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);
 
//sinon il y a une erreur
  } else {

    echo "Une erreur est survenue.";

  }

//si il n'y a pas de recherche on récupère les articles sans filtre hormis la publication et limitation pour pagination
} else {

//récupération des lignes de la bdd
  $select = "SELECT id, "
          . "titre, "
          . "texte, "
          . "DATE_FORMAT(date, '%d/%m/%Y') as date_fr "
          . "FROM articles "
          . "WHERE publie = :publie "
          . "LIMIT :index, :nbArticles";

/* @var $bdd PDO */

//préparation de la requête et sécurisation des valeurs
  $sth = $bdd->prepare($select);
  $sth->bindValue(':publie', 1, PDO::PARAM_BOOL);
  $sth->bindValue(':index', $index, PDO::PARAM_INT);
  $sth->bindValue(':nbArticles', $nbArticles, PDO::PARAM_INT);

//exécution de la requête
  if ($sth->execute() == TRUE ){
    $tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);

//sinon il y a une erreur
  } else {

  echo "Une erreur est survenue.";

  }

}

//inclusion du header commun à toutes les pages
include 'includes/header.inc.php';

//test de notification et affichage dynamique
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

<!-- récupération de la valeur nbArticles pour la pagination et le passage de cette valeur de page en page -->
<input type="hidden" name="nbArticles[]" value="<?= $nbArticles ?>">

<!-- base de la page -->
<div class="container">
  <div class="row">
    <div class="col-lg-12 text-center">
      <h1 class="mt-5">Page d'acceuil</h1>
      <p class="lead">Articles publiés</p>
    </div>
  </div>

<!-- barre de recherche -->

<nav class="navbar rounded navbar-light bg-dark">
  <form class="form-inline col-12">
      <input class="form-control mr-2 col-sm-6 col-md-6 col-lg-6 col-xl-6" type="search" placeholder="Rechercher..." aria-label="Search" name="search" id="search">
      <button class="btn btn-info my-2 my-sm-0" type="submit">Rechercher</button>
    </form>
  </nav>
      
<!-- boucle qui permet d'afficher chaque article sauvegarder en base de donnée avec leur titre, image si ils en ont, contenu de l'article et la date à laquelle ils ont étés sauvegarder  -->
  <div class="card-group">
    <?php
      foreach ($tab_articles as $value) {
    ?> 
        <div class="card-block col-sm-12 col-md-12 col-lg-6 col-xl-6 mt-4 faded">
          <h4 class="card-header text-left">
            <?php
              echo $value['titre'];
            ?>
          </h4>
          <div class="card-title">
            <img class="card-img-top" src="img/<?= $value['id'] ?>.jpg">
          </div>
          <h6 class="card-subtitle mb-2"> 
            <?php
              echo $value['texte'] . '</br>';
            ?> 
          </h6>
          <h6 class="card-subtitle mb-2 text-muted top-buffer text-right">                
            <?php
              echo 'Créé le : ' . $value['date_fr'];
            ?>
          </h6>

<!-- bouton qui redirige vers la page de consultation de l'article en question -->
          <a href="articles.php?action=consulter&id_article=<?= $value['id'] ?>" class="btn btn-secondary bot-buffer">Consulter l'article</a>

<!-- si le visiteur est logué il peut accèder aux boutons de modification et de suppression -->
          <?php
            if ($is_connect == TRUE){

          ?>  
          <a href="articles.php?action=modifier&id_article=<?= $value['id'] ?>" class="btn btn-primary bot-buffer">Modifier l'article</a>
          <a href="index.php?action=delete&id=<?= $value['id'] ?>" class="btn btn-danger bot-buffer" onclick="return confirm('Attention cette action est irreversible ! Etes-vous sûr de vouloir supprimer cet article ?');">Supprimer l'article</a>
          <?php
            }
          ?>
        </div>
    <?php
      }
    ?>
  </div>

              <!-- pagination selon si on utilise un lien avec le paramètre de page ou les paramètres de page et de recherche -->
  <div class="mt-5">
    <nav aria-label="Page navigation example ">
      <ul class="pagination">

      <?php 
          for($i = 1; $i <= $nbPages; $i++){
            $active = $pageCourante == $i ? 'active' : '';
      ?>
          
        <li class="page-item <?= $active ?>">
            <a class="page-link" href="?page=<?= $i ?><?= $searchPage ?><?= $searchArticles ?>"><?= $i ?></a>
        </li>
          
      <?php
        }
      ?>
          
      </ul>
    </nav>
  </div>
</div>

<!-- script nécessaire à la page -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/popper/popper.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- inclusion du footer -->
<?php
include 'includes/footer.inc.php';
?>