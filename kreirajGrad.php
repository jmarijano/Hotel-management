<?php

$naslov = "Kreiraj grad";
include "./_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezKreirajGradUnos", kreirajGradUnos());
$smarty->display("templates/kreirajGrad.tpl");
$smarty->display("templates/_footer.tpl");

