<?php

//fonction de cryptage
function cryptPassword($mdp){

  	$mdp_crypt = sha1($mdp);
  	return $mdp_crypt;
}

//fonction de de cryptage du sid avec l'heure depuis 1970
function sid($email){

	$sid = md5($email . time());
	return $sid;
	
}

//fonction de pagination
function pagination($numPage, $nbArticles){
	$index = ($numPage - 1) * $nbArticles;
	return $index;
}

//fonction de comptage d'articles totaux pour pagination
function totalArticles($bdd){

	/* @var $bdd PDO */

	$sql = "SELECT COUNT(*) as total "
			. "FROM articles "
			. "WHERE publie = 1";

	$sth = $bdd->prepare($sql);
	$sth->execute(); 
	$tab_result = $sth->fetch(PDO::FETCH_ASSOC);

	return $tab_result['total'];
}

//fonction de comptage d'articles selon la recherche effectuée
function sqlPagination($bdd, $search){

	$sql = "SELECT COUNT(*) as total "
				. "FROM articles "
                . "WHERE (titre LIKE :search OR texte LIKE :search) "
                . "AND publie=1 ";

	$sth = $bdd->prepare($sql);
	$sth->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
	$sth->execute(); 
	$tab_result = $sth->fetch(PDO::FETCH_ASSOC);

	return $tab_result['total'];
}

function delete($id){

	echo "<script>alert('Attention cette action est irréversible')</script>";

	$delete = "DELETE FROM articles"
				. "WHERE id = :id";

	$sth = $bdd->prepare($delete);
	$sth->bindValue(':id', $id, PDO::PARAM_INT);
	$sth-execute();

}