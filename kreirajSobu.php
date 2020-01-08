<?php

$naslov = "Kreiraj sobu";
include "_header.php";
$smarty->assign("uloga", moderator());
$smarty->assign("rezHotel", dodjelaKorisnikuHotelHotel());
$smarty->assign("rezNovaSoba", kreirajSobuNovaSoba());
$smarty->display("templates/kreirajSobu.tpl");
$smarty->display("templates/_footer.tpl");

