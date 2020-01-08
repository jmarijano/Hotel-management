<?php
$naslov = "Početna stranica";
include "_header.php";
$smarty->assign("oglasi", prikazOglasa(basename($_SERVER['PHP_SELF'])));

$smarty->display("templates/pocetna.tpl");
$smarty->display("templates/_footer.tpl");
