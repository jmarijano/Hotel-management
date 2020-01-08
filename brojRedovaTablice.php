<?php

$naslov = "Broj redova u tablici";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezBrojRedovaTablice", brojRedovaTabliceIzmjena());
$smarty->display("templates/brojRedovaTablice.tpl");
$smarty->display("templates/_footer.tpl");
