<?php

$naslov = "Blokiraj korisnika";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("oglasi", prikazOglasa(basename($_SERVER['PHP_SELF'])));
$smarty->assign("rezBlokiranje", blokiranjeKorisnikaBlokiranje());
$smarty->assign("rezBlokiranjePopis", blokiranjeKorisnikaPopisOtkljucanih());
$smarty->display("templates/blokiranjeKorisnika.tpl");
$smarty->display("templates/_footer.tpl");
