<!DOCTYPE html>
<html lang="fr">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>&middot; Blog &middot;</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/bootstrap/css/perso.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style>
      body {
        padding-top: 54px;
      }
      @media (min-width: 992px) {
        body {
          padding-top: 56px;
        }
      }

    </style>

  </head>

  <body>

    <!-- menu de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="Index.php">Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Page d'acceuil</a>
            </li>            
            <?php
              if ($is_connect == TRUE){
            ?>
            <li class="nav-item">
              <a class="nav-link" href="articles.php">Ajouter un article</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="deconnexion.php">Se déconnecter</a>
            </li>            
            <?php
              } else {
            ?>
            <li class="nav-item">
              <a class="nav-link" href="inscriptions.php">S'inscrire</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="connexion.php">Se connecter</a>
            </li>
            <?php
              }
            ?>
          </ul>
        </div>
      </div>
    </nav>

<!-- affichage du nom de l'utilisateur si il est connecté-->
  <?php
    if ($is_connect == TRUE){
  ?>

    <div class="alert alert-info text-center" role="alert">
      Vous êtes connecté(e) en tant que <strong><?php echo "$prenom_connect  $nom_connect" ?></strong>.
    </div>

  <?php
    }
  ?>