<?php

$naslov = "Dodijeli poziciju";
include '_header.php';
$smarty->assign("uloga", administrator());
$smarty->assign("popisModeratora", dohvatiModeratore());
$smarty->assign("popisPozicija", dohvatiPozicije());
$smarty->assign("rezDodijeli", dodijeliPoziciju());
$smarty->display("templates/dodijeliPoziciju.tpl");
$smarty->display("templates/_footer.tpl");


