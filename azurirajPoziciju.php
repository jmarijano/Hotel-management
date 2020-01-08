<?php

$naslov = "Ažuriraj poziciju";
include"_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezAzuriraj", azurirajPozicijuAzuriraj());
$smarty->assign("svePozicije", azurirajPozicijuDohvatiSvePozicije());
$smarty->display("templates/azurirajPoziciju.tpl");
$smarty->display("templates/_footer.tpl");
