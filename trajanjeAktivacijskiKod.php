<?php

$naslov = "Trajanje aktivacijskog koda";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezKodIzmjena", trajanjeAktivacijskogKodaIzmjena());
$smarty->display("templates/trajanjeAktivacijskiKod.tpl");
$smarty->display("templates/_footer.tpl");

