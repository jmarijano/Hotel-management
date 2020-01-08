<?php

$naslov = "Obriši hotel";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("obrisi", obrisiHotel());
$smarty->assign("hoteli", dohvatiHotele());
$smarty->display("templates/obrisiHotel.tpl");
$smarty->display("templates/_footer.tpl");

