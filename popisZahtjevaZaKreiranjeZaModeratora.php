<?php

$naslov = "Popis zahtjeva za kreiranje";
include '_header.php';
$smarty->assign("uloga", moderator());
$smarty->display("templates/popisZahtjevaZaKreiranjeZaModeratora.tpl");
$smarty->display("templates/_footer.tpl");

