<?php

$naslov = "Ažuriraj grad";
include '_header.php';
$smarty->assign("uloga", administrator());
$smarty->assign("rez", azurirajGrad());
$smarty->assign("gradovi", dohvatiGradove());
$smarty->display("templates/azurirajGrad.tpl");
$smarty->display("templates/_footer.tpl");
