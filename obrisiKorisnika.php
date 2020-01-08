<?php

$naslov = "Obriši korisnika";
include '_header.php';
$smarty->assign("uloga", administrator());
$smarty->assign("brisanje", obrisiKorisnika());
$smarty->assign("korisnici", dohvatiKorisnike());
$smarty->display("templates/obrisiKorisnika.tpl");
$smarty->display("templates/_footer.tpl");

