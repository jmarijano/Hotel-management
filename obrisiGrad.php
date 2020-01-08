<?php

$naslov = "Obriši grad";
include '_header.php';
$smarty->assign("uloga", administrator());
$smarty->assign("brisanje", obrisiGrad());
$smarty->assign("gradovi", popuniGradove());

$smarty->display("templates/obrisiGrad.tpl");
$smarty->display("templates/_footer.tpl");

