<?php

$naslov = "Ažuriraj rezervaciju";
include "_header.php";
$smarty->assign("rezervacije", dohvatiRezervacije());
$smarty->display("templates/azurirajRezervaciju.tpl");
$smarty->display("templates/_footer.tpl");

