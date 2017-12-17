<?php
//ouverture session
session_start();

//utilisation des fichiers annexes ci-dessous
require_once 'config/init.conf.php';
require_once 'config/bdd.conf.php';
require_once 'config/connexion.inc.php';
require_once 'includes/fonctions.inc.php';
require_once 'libs/Smarty.class.php';

//déclaration d'un fuseau horaire pour éviter un message d'erreur
date_default_timezone_set('Europe/Paris');

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

//code php pour les notification selon les valeurs de $notification et $notification_alert 
$alert= '';
        
        if(isset($_SESSION['notification'])){
            $alert = $_SESSION['notification_alert'] == TRUE ? 'alert-success' : 'alert-danger';

            unset($_SESSION['notification']);
            unset($_SESSION['notification_alert']);
          }        
  }
//définition des paramètres de Smarty
$smarty = new Smarty();

$smarty->setTemplateDir('templates/');

$smarty->setCompileDir('templates_c/');

$smarty->assign('tab_session', $_SESSION);
$smarty->assign('alert', $alert);

//inclusion du header et du footer
include 'includes/header.inc.php';
$smarty->display('connexion.tpl');
include 'includes/footer.inc.php';
?>