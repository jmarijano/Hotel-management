<?php

$naslov = "Rezervacija sobe";
include"_header.php";
$smarty->assign("uloga", moderator());
$smarty->assign("rezRezerviraj", rezervacijaSobeRezerviraj());
$smarty->assign("rezPopisSoba", rezervacijaSobePopisHotela());
$smarty->assign("rezPopisKorisnika", rezervacijaSobeRegistriraniKorisnici());

$smarty->display("templates/rezervacijaSobe.tpl");
$smarty->display("templates/_footer.tpl");

