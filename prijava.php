<?php

$naslov = "Prijava";
include "_header.php";
$zaPrijavu = 0;
$sqlZaPrijavu = "SELECT idkorisnik,korisnicko_ime,sifra,tipKorisnika_idtipKorisnika,blokiran,aktiviran FROM korisnik";
$veza = new Baza();
$veza->spojiDB();
$rezultatZaPrijavu = $veza->selectDB($sqlZaPrijavu);

$smarty->assign("oglasi", prikazOglasa(basename($_SERVER['PHP_SELF'])));
$smarty->assign("rezPopuniKorime", kolacicPopunjavanjeKorisnickogImena());
$smarty->assign("rezultatProvjeraKorisnikaLogin", provjeraKorisnikaLogin($zaPrijavu, $rezultatZaPrijavu));
$smarty->display("templates/prijava.tpl");
$smarty->display("templates/_footer.tpl");
?>
