<?php
/* Smarty version 3.1.30, created on 2017-12-17 20:14:06
  from "E:\Cours\Php-html-mysql\UwAmp\www\templates\connexion.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5a36c1fe854751_12009414',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c1ededd2246d4c4b4cb0c7c320f584b476017eed' => 
    array (
      0 => 'E:\\Cours\\Php-html-mysql\\UwAmp\\www\\templates\\connexion.tpl',
      1 => 1513534622,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a36c1fe854751_12009414 (Smarty_Internal_Template $_smarty_tpl) {
?>

    <!-- page web -->
    <div class="container">
      <div class="row">
        <!-- test si il y a des notifications à afficher et affichage dynamique -->
        <?php if (isset($_smarty_tpl->tpl_vars['tab_session']->value['notification'])) {?>
        <div class="alert <?php echo $_smarty_tpl->tpl_vars['alert']->value;?>
 alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        <?php echo $_smarty_tpl->tpl_vars['tab_session']->value['notification'];?>

        </div>
        <?php }?>
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
    <?php echo '<script'; ?>
 src="vendor/jquery/jquery.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="vendor/popper/popper.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="vendor/bootstrap/js/bootstrap.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/dist/jquery.validate.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/dist/localization/messages_fr.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 >
      $(document).ready(function () {
        $("#form_article").validate();
      })
    <?php echo '</script'; ?>
><?php }
}
