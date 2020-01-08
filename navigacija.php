<?php

$smarty->assign("adminGrad", navigacijaGradAdmin());
$smarty->assign("adminHotel", navigacijaHotelAdmin());
$smarty->assign("ulogeSoba", navigacijaSoba());
$smarty->assign("konfiguracija", konfiguracijaIKorisnici());
$smarty->assign("statistike", Statistike());
$smarty->assign("lokacije", korisniciLokacije());
$smarty->assign("pozicije", korisniciPozicija());
$smarty->assign("oglasi", korisniciOglasi());
$smarty->display("templates/navigacija.tpl");
