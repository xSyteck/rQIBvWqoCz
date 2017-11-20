<?php

//déclaration de l'état comme déconnecté par défaut
$is_connect = FALSE;

//si il existe un cookie qui s'appelle sid et qu'il n'est pas vide alors...
if (isset ($_COOKIE['sid']) AND !empty ($_COOKIE['sid'])) {


//on récupère le contenu du cookie dans la variable suivante
	$cookie_sid = $_COOKIE['sid'];

//on compte le nombre d'utilisateur qui ont pour sid le même que celui contenu dans le cookie
	$cookie_compare = "SELECT COUNT(sid) AS nb_sid, "
					. "nom, "
					. "prenom "
					. "FROM utilisateurs "
					. "WHERE sid = :cookie_sid";

//préparation et sécurisation
	$sth_cookie = $bdd->prepare($cookie_compare);
	$sth_cookie->bindValue(':cookie_sid', $cookie_sid, PDO::PARAM_STR);

//exécution de la commande SQL
	if ($sth_cookie->execute() == TRUE) {

//on récupère les infos de la ligne de l'utilisateur
		$cookie_tab = $sth_cookie->fetch();

//si il y a au moins un utilisateur qui est ressorti de la commande SQL
		if ($cookie_tab > 0) {

//alors l'état de connexion passe à vrai et on récupère son nom et prénom
			$is_connect = TRUE;
			$nom_connect = $cookie_tab['nom'];
			$prenom_connect = $cookie_tab['prenom'];

		}
	}
}
