<?php
try{
	/* @var $bdd PDO */
	$bdd = new PDO('mysql:host=localhost;dbname=id3521846_bdd;charset=utf8', 'id3521846_root', 'Azerty123456');
	$bdd->exec("set names utf8");
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
	die('Erreur : ' .$e->getMessage());
}