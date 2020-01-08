<?php

$naslov = "Obriši sobu";
include '_header.php';
$smarty->assign("uloga", administrator());
$smarty->assign("brisi", obrisiSobu());
$smarty->assign("sobe", dohvatiSobe());
$smarty->display("templates/obrisiSobu.tpl");
$smarty->display("templates/_footer.tpl");

