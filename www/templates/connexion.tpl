
    <!-- page web -->
    <div class="container">
      <div class="row">
        <!-- test si il y a des notifications à afficher et affichage dynamique -->
        {if isset($tab_session['notification'])}
        <div class="alert {$alert} alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        {$tab_session['notification']}
        </div>
        {/if}
        <!-- haut de la page de connexion avec un lien pour créer un compte si besoin -->
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