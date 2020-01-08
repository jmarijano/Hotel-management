<?php

$naslov = "Dodijeli hotel";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezDodjela", dodjelaKorisnikuHotelDodijeli());
$smarty->assign("rezDodjelaHotel", dodjelaKorisnikuHotelHotel());
$smarty->assign("rezDodjelaKorisnik", dodjelaKorisnikuHotelKorisnik());
$smarty->display("templates/dodjelaKorisnikuHotel.tpl");
$smarty->display("templates/_footer.tpl");
