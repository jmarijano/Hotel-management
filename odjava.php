<?php

session_start();
require_once './baza.class.php';
require_once './funkcije.php';
$pomakVremena = dohvatiVrijemePHP();
$veza = new Baza();
$veza->spojiDB();
$upit = "INSERT into dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','odjava','Korisnik {$_SESSION["korisnicko_ime"]} se odjavio')";
$veza->updateDB($upit);
$veza->zatvoriDB();
session_destroy();
header("Location:index.php");
?>


