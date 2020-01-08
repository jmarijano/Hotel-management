<?php

$naslov = "Top lista korisnika";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->display("templates/topListaKorisnika.tpl");
$smarty->display("templates/_footer.tpl");


