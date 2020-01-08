<?php

$naslov = "Kreiraj moderatora";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezModeratori", kreiranjeModeratoraKreiraj());
$smarty->assign("rezKorisnici", kreiranjeModeratoraPopuniKorisnike());
$smarty->display("templates/kreiranjeModeratora.tpl");
$smarty->display("templates/_footer.tpl");

