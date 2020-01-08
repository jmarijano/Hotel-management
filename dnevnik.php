<?php

$naslov = "Dnevnik";
include"./_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("popuniTipove", dohvatiTipoveLogova());
$smarty->display("templates/dnevnik.tpl");

$smarty->display("templates/_footer.tpl");

