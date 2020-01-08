<?php

$naslov = "Kreiraj vrstu oglasa";
include '_header.php';
$smarty->assign("uloga", moderator());
$smarty->assign("rezVrstaOglasa", kreirajVrstuOglasa());
$smarty->assign("popisPozicija", dohvatiPozicijeKorisnika());
$smarty->display("templates/kreirajVrstuOglasa.tpl");
$smarty->display("templates/_footer.tpl");
