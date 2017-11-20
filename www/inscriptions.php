<?php
//ouverture session
session_start();

//utilisation des fichiers annexes ci-dessous
require_once 'config/init.conf.php';
require_once 'config/bdd.conf.php';
require_once 'config/connexion.inc.php';
require_once 'includes/fonctions.inc.php';

//commandes php lorsque le bouton a été déclenché
if (isset($_POST['submit'])) {

//commande SQL pour insérer une nouvelle ligne dans la bdd pour un nouvel utilisateur
$insert = "INSERT INTO utilisateurs (nom, prenom, email, password) "
          . "VALUES (:nom, :prenom, :email, :password)";    

        /* @var $bdd PDO */

//préparation de la requête et sécurisation des valeurs
    $sth = $bdd->prepare($insert);
    $sth->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
    $sth->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
    $sth->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
    $sth->bindValue(':password', cryptPassword($_POST['password']), PDO::PARAM_STR);

//exécution de la requête
  if ($sth->execute() == TRUE ){
      
//mise en mémoire de l'id du dernier article posté
      $id_article = $bdd->lastInsertId();

      $_SESSION['notification'] = "Votre compte a bien été ajouté !";
      $_SESSION['notification_alert'] = TRUE;

      header('Location: inscriptions.php');

    } else {

      $_SESSION['notification'] = "Une erreur est survenue lors de la création de votre compte...";
      $_SESSION['notification_alert'] = FALSE;

      header('Location: inscriptions.php');

    }

  } else {

//inclusion du header
include 'includes/header.inc.php';

?>
    <!-- page web -->
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h1 class="mt-5">Inscription</h1>
          <p class="lead">Vous pouvez vous inscrire grâce au formulaire suivant.</br>
          Veuillez remplir correctement le formulaire pour créer votre compte utilisateur.</p>
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
          include 'includes/header.inc.php';
          ?>

<!-- mise en place du formulaire -->
      <form action="inscriptions.php" method="post" enctype="multipart/form-data" id="form_article">
        <div class="form-group">
          <div>
            <label for="nom">Nom</label>
          </div>
          <div>
            <input type="text" class="col-sm-12 col-md-8 col-lg-6 col-xl-6 justify-content-center form-control" id="nom" placeholder="Nom" name="nom" required>
          </div>
          <div>
            <label for="prenom">Prénom</label>
          </div>
          <div>
            <input type="text" class="col-sm-12 col-md-8 col-lg-6 col-xl-6 justify-content-center form-control" id="prenom" placeholder="Prénom" name="prenom" required>
          </div>
          <div>
            <label for="email">Email</label>
          </div>
          <div>
            <input type="email" class="col-sm-12 col-md-8 col-lg-6 col-xl-6 justify-content-center form-control" id="email" placeholder="Email" name="email" required>
          </div>
           <div>
            <label for="password">Mot de passe</label>
          </div>
          <div>
            <input type="password" class="col-sm-12 col-md-8 col-lg-6 col-xl-6 justify-content-center form-control" id="password" placeholder="Mot de passe" name="password" required>
          </div>
        </div>        
        <div class="form-check">
          <label class="form-check-label" for="publie">
            <input type="checkbox" class="form-check-input" id="publie" name="publie" value="1" required>
          Voulez-vous valider cette inscription ? (cochez pour oui)
          </label>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Valider</button>
      </form>
    </div>

<!-- script nécessaire à la page -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- inclusion du footer -->
<?php
include 'includes/footer.inc.php';
}
?>