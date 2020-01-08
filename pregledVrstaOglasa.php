<?php

$naslov = "Pregled vrsta oglasa";
include "_header.php";
$smarty->assign("uloga", registriraniKorisnik());
$smarty->display("templates/pregledVrstaOglasa.tpl");
$smarty->display("templates/_footer.tpl");

