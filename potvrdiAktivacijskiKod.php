<?php

$naslov = "Potvrdi aktivacijski kod";
include "_header.php";
$smarty->assign("kod", potvrdiAktivacijskiKod());
$smarty->display("templates/potvrdiAktivacijskiKod.tpl");
$smarty->display("templates/_footer.tpl");
