<?php

$naslov = "Blokiran oglas";
include '_header.php';
$smarty->assign("uloga", registriraniKorisnik());
$smarty->assign("rezBlokiraj", zahtjevBlokirajOglasBlokiraj());
$smarty->assign("rezPopisOglasa", zahtjevBlokirajOglasOglasi());
$smarty->display("templates/zahtjevBlokirajOglas.tpl");
$smarty->display("templates/_footer.tpl");

