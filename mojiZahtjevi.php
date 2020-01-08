<?php

$naslov = "Moji zahtjevi";
include "_header.php";
$smarty->assign("uloga", registriraniKorisnik());
$smarty->assign("rezAzuriranja", azurirajZahtjevZaKreiranje());
$smarty->display("templates/mojiZahtjevi.tpl");
$smarty->display("templates/_footer.tpl");

