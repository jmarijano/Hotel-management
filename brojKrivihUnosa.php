<?php

$naslov = "Broj krivih unosa";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezBrojKrivihUnosa", brojKrivihUnosaIzmjena());
$smarty->display("templates/brojKrivihUnosa.tpl");
$smarty->display("templates/_footer.tpl");

