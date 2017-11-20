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

//déclaration des valeures de base pour les notifications
  $notification = 'Aucune notification à afficher.';
  $_SESSION['notification_alert'] = FALSE;

//stockage en variable du résulatat du hash du mot de passe
  $hash = cryptPassword($_POST['password']);

//boucle si l'email et le password sont renseignés
  if (!empty($_POST['email']) AND !empty($_POST['password'])) {

//commande SQL pour comparer l'email et le password
  	$compare = "SELECT email, "
  					. "password "
  					. "FROM utilisateurs "
  					. "WHERE email = :email "
  					. "AND password = :password";

  }

  	/* @var $bdd PDO */

//sécurisation des variables à traiter dans le SQL
  $sth = $bdd->prepare($compare);
	$sth->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
	$sth->bindValue(':password', $hash, PDO::PARAM_STR);

//si l'éxecution de la requête se déroule sans erreur
    if ($sth->execute() == TRUE ){
      
//comptage du nombre d'entrées identiques au couple email-password
      $count = $sth->rowCount();
      if ($count > 0) {

//si il y a au moins un couple email-password
      	$sid = sid($_POST['email']);

//commande SQL de mise à jour du sid dans la bdd  	
      	$update_sid = "UPDATE utilisateurs "
      					. "SET sid = :sid "
      					. "WHERE email = :email";

//sécurisation des variables de la commande SQL
      	$sth_update = $bdd->prepare($update_sid);
      	$sth_update->bindValue(':sid', $sid, PDO::PARAM_STR);
      	$sth_update->bindValue(':email', $_POST['email'], PDO::PARAM_STR);

//si l'éxecution de la requête se déroule sans erreur
      		if ($sth_update->execute() == TRUE ){

//création du cookie avec son nom, sa valeur et le temps qu'il lui reste avant son expiration avec redirection vers la page index.php
      			setcookie('sid', $sid, time() + (60*60*24));
      			$notification = 'Félicitations, vous êtes connecté !';
      			$_SESSION['notification'] = $notification;
      			$_SESSION['notification_alert'] = TRUE;
      			header("Location: index.php");
      			exit();

//sortie en cas d'erreur
      		} else {

      			$notification = 'Une erreur s\'est produite.';
      			$_SESSION['notification_alert'] = FALSE;

      		}

//sortie en cas d'erreur dans la saisie des logs
      	} else {

      	$notification = 'L\'email et/ou le mot de passe saisie est incorrect.';
      	$_SESSION['notification_alert'] = FALSE;

      }

//sortie en cas d'erreur si le bouton est déclenché alors que les champs ne sont pas renseignés
    } else {

    	$notification = 'Veuillez renseigner les champs obligatoires.';
    	$_SESSION['notification_alert'] = FALSE;

    }

//récuperation de la valeur final de la notification pour la session et redirection sur la page elle-même afin de retomber dans la boucle else ci-dessous
    $_SESSION['notification'] = $notification;
    header('Location: connexion.php');

  } else {

//inclusion du header
	include 'includes/header.inc.php';

?>
    <!-- page web -->
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h1 class="mt-5">Connexion</h1>
          <p class="lead">Veuillez renseigner votre e-mail et votre mot de passe pour pouvoir vous connecter</p>
          <ul class="list-unstyled">
            <li class="nav-item">
            Vous n'avez pas encore de compte ? Cliquez <a class="text-dark" href="inscriptions.php">ici</a>.
            </li>
          </ul>
        </div>
      </div>

<!-- code php pour les notification selon les valeurs de $notification et $notification_alert -->
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

<!--  formulaire de connexion -->
    <form action="connexion.php" method="post" enctype="multipart/form-data" id="form_article">
        <div class="form-group">
        	<label for="email">Email</label>
            <input type="email" class="col-sm-12 col-md-8 col-lg-6 col-xl-6 justify-content-center form-control" id="email" placeholder="Email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="col-sm-12 col-md-8 col-lg-6 col-xl-6 justify-content-center form-control" id="password" placeholder="Mot de passe" name="password" required>
        </div>

    	<button type="submit" class="btn btn-primary" name="submit">Valider</button>
      </form>

    </div>

<!-- script nécessaire à la page -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/dist/jquery.validate.min.js"></script>
    <script src="js/dist/localization/messages_fr.min.js"></script>

    <script >
      $(document).ready(function () {
        $("#form_article").validate();
      })
    </script>

<!-- inclusion du footer -->
<?php
include 'includes/footer.inc.php';
}
?>