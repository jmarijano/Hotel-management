<?php

$naslov = "Kreiraj vrstu oglasa";
include "_header.php";
$smarty->assign("rezKorisnik", registriraniKorisnik());
$smarty->assign("rezVrstaOglasa", kreirajZahtjevZaKreiranjeVrstaOglasa());
$smarty->assign("rezUpis", kreirajZahtjevZaKreiranjeUpisi());
$smarty->display("templates/kreirajZahtjevZaKreiranje.tpl");
$smarty->display("templates/_footer.tpl");
