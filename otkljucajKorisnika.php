<?php

$naslov = "Otključaj korisnika";
include"_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezOtkljucavanje", otkljucajKorisnikaOtkljucavanje());
$smarty->assign("rezPopisBlokiranih", otkljucajKorisnikaPopisBlokiranih());
$smarty->display("templates/otkljucajKorisnika.tpl");
$smarty->display("templates/_footer.tpl");
