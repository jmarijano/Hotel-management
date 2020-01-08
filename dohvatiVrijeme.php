<?php

$naslov = "Dohvati vrijeme";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezVrijeme", pomakVremena());
$smarty->display("templates/dohvatiVrijeme.tpl");
$smarty->display("templates/_footer.tpl");

