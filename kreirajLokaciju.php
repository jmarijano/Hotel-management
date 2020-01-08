<?php

$naslov = "Kreiraj lokaciju";
include "_header.php";
$smarty->assign("uloga", moderator());
$smarty->assign("rezKreiraj", kreirajLokacijuKreiraj());
$smarty->assign("popisPozicija", dohvatiPozicije());
$smarty->assign("popisStranica", dohvatiStranice());
$smarty->assign("popisOglasa", dohvatiOglase());
$smarty->display("templates/kreirajLokaciju.tpl");
$smarty->display("templates/_footer.tpl");

