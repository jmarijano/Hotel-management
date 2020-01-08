<?php

$naslov = "Moja statistika oglasa";
include "_header.php";
$smarty->assign("uloga", registriraniKorisnik());
$smarty->assign("popisVrstaOglasa", kreirajZahtjevZaKreiranjeVrstaOglasa());
$smarty->display("templates/mojaStatistiOglasa.tpl");
$smarty->display("templates/_footer.tpl");
