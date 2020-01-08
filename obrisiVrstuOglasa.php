<?php

$naslov = "Obriši vrstu oglasa";
include '_header.php';
$smarty->assign("uloga", administrator());

$smarty->assign("vrsteOglasa", kreirajZahtjevZaKreiranjeVrstaOglasa());
$smarty->display("templates/obrisiVrstuOglasa.tpl");
$smarty->display("templates/_footer.tpl");

