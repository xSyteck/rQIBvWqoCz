<?php

//suppression du cookie pour se déconnecter et redirection vers l'acceuil
setcookie('sid', '', -1);
header('Location: index.php');
exit();