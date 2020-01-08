<?php

function registracijaKlasaIme() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if (empty($_POST["regime"])) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijaKlasaPrezime() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if (empty($_POST["regprez"])) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijaKlasaKorime() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if (empty($_POST["regkorime"])) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijaKlasaMail() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if (empty($_POST["regwebadresa"])) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijaKlasaLozinka1() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if (empty($_POST["reglozinka1"]) || $_POST["reglozinka1"] !== $_POST["reglozinka2"]) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijaKlasaLozinka2() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if (empty($_POST["reglozinka2"]) || $_POST["reglozinka1"] !== $_POST["reglozinka2"]) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijacaptcha() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        if ($_POST["regCAPTCHA"] !== $_POST["popunaCAPTCHA"]) {
            return "losa";
        } else {
            return "dobra";
        }
    } else {
        return "standard";
    }
}

function registracijaAktivacijskiKod() {
    return substr(base64_encode(md5(mt_rand())), 0, 12);
}

function provjeraSesije() {
    if (isset($_SESSION["korisnicko_ime"])) {
        return '<p id="zaLinkPrijavaOdjava">
                            <a href="odjava.php" style=" background-color: #f44336;
                               color: white;
                               padding: 14px 25px;
                               text-align: center;
                               text-decoration: none;
                               display: inline-block;">Odjava</a>
                            
                        ';
    } else {
        return '
                            <a href="prijava.php" style=" background-color: #f44336;
                               color: white;
                               padding: 14px 25px;
                               text-align: center;
                               text-decoration: none;
                               display: inline-block;">Prijava</a>
                               
                        ';
    }
}

function provjeraKorisnikaLogin($zaPrijavu, $rezultatZaPrijavu) {
    if (array_key_exists("prislanjePodataka", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $veza = new Baza();
        if ($rezultatZaPrijavu->num_rows > 0) {
            while ($redovi = $rezultatZaPrijavu->fetch_assoc()) {
                $korime = $redovi["korisnicko_ime"];
                $lozinak = $redovi["sifra"];
                $blokiran = $redovi["blokiran"];
                $aktiviran = $redovi["aktiviran"];
                $idkorisnik = $redovi["idkorisnik"];
                $tipKorisnika = $redovi["tipKorisnika_idtipKorisnika"];
                $sql = "SELECT idkorisnik from korisnik where korisnicko_ime='" . $korime . "'";
                $veza->spojiDB();
                $rezultat = $veza->selectDB($sql);
                $red = $rezultat->fetch_assoc();
                $idKorisnika = $red["idkorisnik"];
                if ($_POST["prikorime"] == $korime && $_POST["prilozinka1"] == $lozinak && $blokiran == 0 && $aktiviran == 1) {
                    $_SESSION["korisnicko_ime"] = $korime;
                    $_SESSION["tipKorisnika"] = $tipKorisnika;
                    $_SESSION["idkorisnik"] = $idkorisnik;
                    $dnevnikSQL = "INSERT INTO dnevnikRada VALUES('default','$idKorisnika','$pomakVremena','prijava','Prijava u sustav')";
                    $veza->updateDB($dnevnikSQL);
                    $zacookie = str_replace(' ', '', $korime);
                    setcookie("kolacic", $zacookie, time() + 172800);
                    $zaPrijavu = 1;
                    $veza->zatvoriDB();
                    break;
                }
                if ($_POST["prikorime"] == $korime && $_POST["prilozinka1"] != $lozinak) {
                    $sql = "SELECT BrojKrivihUnosa from korisnik where korisnicko_ime='" . $korime . "'";

                    $veza->spojiDB();
                    $rezultatUpita = $veza->selectDB($sql);
                    $red = $rezultatUpita->fetch_assoc();
                    $brojKrivihUnosa = $red["BrojKrivihUnosa"];
                    if ($brojKrivihUnosa < $_SESSION["losePrijava"]) {
                        $sqlUpdate = "UPDATE korisnik SET BrojKrivihUnosa=BrojKrivihUnosa+1 WHERE korisnicko_ime='" . $korime . "'";
                        $sqlDnevnik = "INSERT INTO dnevnikRada VALUES('default',$idKorisnika,'$pomakVremena','Neuspješna prijava','Korisnik $korime unio je krivu lozinku')";
                        $veza->updateDB($sqlUpdate);
                        $veza->updateDB($sqlDnevnik);
                        $veza->zatvoriDB();
                        $zaPrijavu = 2;
                    } else {
                        $sqlBlokiran = "UPDATE korisnik SET blokiran=1 WHERE korisnicko_ime='" . $korime . "'";
                        $sqlDnevnikBlokiran = "INSERT INTO dnevnikRada VALUES('default',$idKorisnika,'$pomakVremena','Račun je blokiran','Korisnik $korime je blokiran')";
                        $veza->updateDB($sqlDnevnikBlokiran);
                        $veza->updateDB($sqlBlokiran);
                        $veza->zatvoriDB();
                        $zaPrijavu = 0;
                    }
                }
            }
        }
        if ($zaPrijavu == 0) {
            return "Korisnički račun ne postoji ili je račun  blokiran!";
        } else if ($zaPrijavu == 2) {
            return "Krivo unesena lozinka";
        } else {
            header("Location:index.php");
        }
    }
}

function kolacicPopunjavanjeKorisnickogImena() {
    if (empty($_SESSION["korisnicko_ime"]) && isset($_COOKIE["kolacic"])) {
        $vrati = str_replace(' ', '', $_COOKIE["kolacic"]);
        return $vrati;
    }
}

function pomakVremena() {

    ini_set("allow_url_fopen", 1);
    $json = file_get_contents("http://barka.foi.hr/WebDiP/pomak_vremena/pomak.php?format=json");
    $obj = json_decode($json, true);
    $polje = (array) $obj;
    $brojSati = $polje["WebDiP"]["vrijeme"]["pomak"]["brojSati"];
    $jsonDatoteka = file_get_contents("pomakVremena.json");
    $data = json_decode($jsonDatoteka, true);
    $data[0]["pomakVremena"] = $brojSati;
    $novi = json_encode($data);
    file_put_contents("pomakVremena.json", $novi);
}

function dohvatiVrijemeSkripte() {

    $jsonDatoteka = file_get_contents("../pomakVremena.json");
    $data = json_decode($jsonDatoteka, TRUE);
    $brojSati = $data[0]["pomakVremena"];
    $vrijeme_servera = time();
    $virtualno_vrijeme = $vrijeme_servera + ($brojSati * 60 * 60);
    return date("Y-m-d H:i:s", $virtualno_vrijeme);
}

function dohvatiVrijemePHP() {
    $jsonDatoteka = file_get_contents("pomakVremena.json");
    $data = json_decode($jsonDatoteka, true);
    $brojSati = $data[0]["pomakVremena"];
    $vrijeme_servera = time();
    $virtualno_vrijeme = $vrijeme_servera + ($brojSati * 60 * 60);
    return date("Y-m-d H:i:s", $virtualno_vrijeme);
}

function zaboravljenaLozinkaNovaSifra() {
    return substr(base64_encode(md5(mt_rand())), 0, 10);
}

function zaboravljenaLozinkaPosaljiMail() {
    if (array_key_exists("zabslanjePodataka", $_POST)) {
        if (!empty($_POST["zabwebadresa"]) && !empty($_POST["zabKor"])) {
            $veza = new Baza();
            $veza->spojiDB();
            $sqlUpit = "SELECT COUNT(*) as brojKorisnika FROM korisnik WHERE email='{$_POST["zabwebadresa"]}' and korisnicko_ime='{$_POST["zabKor"]}'";
            $rezultat = $veza->selectDB($sqlUpit);
            $red = $rezultat->fetch_assoc();
            $brojKorisnika = $red["brojKorisnika"];
            if ($brojKorisnika > 0) {
                $za = $_POST["zabwebadresa"];
                $predmet = "Nova lozinka";
                $novaSifra = zaboravljenaLozinkaNovaSifra();
                $txt = "Nova lozinka je : " . "$novaSifra";
                $sol = sha1(time());
                $kripitiranaLozinka = sha1($sol . '-' . $novaSifra);
                $headers = "From: jmarijano@foi.hr" . "\r\n" .
                        "CC: {$_POST["zabwebadresa"]}";
                mail($za, $predmet, $txt, $headers);
                $sqlUpdate = "UPDATE korisnik SET sifra='$novaSifra',kriptiranaSifra='$kripitiranaLozinka' where email='{$_POST["zabwebadresa"]}' and korisnicko_ime='{$_POST["zabKor"]}'";
                $veza->updateDB($sqlUpdate);
                $veza->zatvoriDB();
            } else {
                return "Ne postoji korisnik s tom E - mail adresom";
            }
        }
    }
}

function trajanjeAktivacijskogKoda() {
    if (!isset($_SESSION["trajanjeAktivacijskogKoda"])) {
        $_SESSION["trajanjeAktivacijskogKoda"] = 24;
    }
}

function pretvoriuHTTPS() {
    if (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

function tablicaPaginacija() {
    if (!isset($_SESSION["paginacija"])) {
        $_SESSION["paginacija"] = 3;
    }
}

function brojLosihPrijava() {
    if (!isset($_SESSION["losePrijava"])) {
        $_SESSION["losePrijava"] = 3;
    }
}

function popuniGradove() {
    $upit = "SELECT * from grad";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function kreirajGradUnos() {
    if (array_key_exists("kreGradSlanjePodataka", $_POST)) {
        if (!empty($_POST["kreGradPostBroj"]) && !empty($_POST["kreGradNaziv"])) {
            if (!is_numeric($_POST["kreGradPostBroj"])) {
                return "Poštanski broj je broj!";
            }
            $veza = new Baza();
            $veza->spojiDB();
            $sql = "INSERT INTO grad VALUES('" . $_POST["kreGradPostBroj"] . "','" . $_POST["kreGradNaziv"] . "')";
            $veza->updateDB($sql);
            $veza->zatvoriDB();
        } elseif (!empty($_POST["kreGradPostBroj"]) && empty($_POST["kreGradNaziv"])) {
            return "Polje naziv je prazno";
        } elseif (empty($_POST["kreGradPostBroj"]) && !empty($_POST["kreGradNaziv"])) {
            return "Polje Poštanski broj je prazno";
        } else {
            return "Sva polja su prazna";
        }
    }
}

function kreirajHotelUnos() {
    $output = "";
    $validanUnos = 1;
    if (array_key_exists("kreHotSlanjePodataka", $_POST)) {
        $naziv = $_POST["kreHotNaziv"];
        $adresa = $_POST["kreHotAdresa"];
        $grad = $_POST["kreHotGrad"];
        $kategorija = $_POST["kreHotKategorija"];
        $telefon = $_POST["kreHotTelefon"];
        $mail = $_POST["kreHotEmail"];
        $format = "/^[a-zA-z0-9][a-zA-z0-9]+[.]?[a-zA-z0-9]+@{1}[a-zA-z0-9]+[.]{1}[a-zA-Z0-9]{1,}[a-zA-Z0-9]$/";
        if (empty($naziv)) {
            $output = $output . "Polje naziv je prazno!<br>";
            $validanUnos = 0;
        }
        if (empty($adresa)) {
            $output = $output . "Polje Adresa je prazno!<br>";
            $validanUnos = 0;
        }
        if (empty($grad)) {
            $output = $output . "Polje Grad je prazno!<br>";
            $validanUnos = 0;
        }
        if (empty($kategorija)) {
            $output = $output . "Polje Kategorija je prazno!<br>";
            $validanUnos = 0;
        }
        if (empty($telefon)) {
            $output = $output . "POlje Telefon je prazno!<br>";
            $validanUnos = 0;
        }
        if (empty($mail)) {
            $output = $output . "Polje E - mail adresa je prazno!<br>";
            $validanUnos = 0;
        }
        if (!preg_match($format, $mail)) {
            $output = $output . "Polje E - mail nije u dobrom formatu!<br>";
            $validanUnos = 0;
        }
        if ($validanUnos == 1) {
            $sql = "INSERT INTO hotel VALUES ('default','$naziv','$adresa','$grad','$kategorija','$telefon','$mail')";
            $veza = new Baza();
            $veza->spojiDB();
            $veza->updateDB($sql);
            $veza->zatvoriDB();
        } else {
            return $output = $output . "Nije validan unos!<br>";
        }
    }
    return $output;
}

function potvrdiAktivacijskiKod() {
    if (array_key_exists("potvrdiSlanjePodataka", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $korime = $_POST["potvrdiKorime"];
        $kod = $_POST["potvrdiKod"];
        if (empty($korime)) {
            return "Unesi korisničko ime!";
        }
        if (empty($kod)) {
            return "Unesi kod!";
        }
        $veza = new Baza();
        $veza->spojiDB();
        $sql = "SELECT COUNT(*),idkorisnik,korisnicko_ime,aktivacijskiKod,kodVrijediDo,email,aktiviran from korisnik where korisnicko_ime='$korime'";
        $rezultatUpita = $veza->selectDB($sql);
        $red = $rezultatUpita->fetch_assoc();
        $aktivacijskiKod = $red["aktivacijskiKod"];
        $vrijediDo = $red["kodVrijediDo"];
        $korisnickoIme = $red["korisnicko_ime"];
        $mail = $red["email"];
        $aktiviran = $red["aktiviran"];
        $idkorisnik = $red["idkorisnik"];
        if ($korisnickoIme != $korime) {
            return "Korisnik ne postoji!";
        }
        if ($aktiviran == 1) {
            return "Korisnički račun je već aktiviran!";
        }
        if ($aktivacijskiKod != $kod) {
            return "Aktivacijski kod nije važeći!";
        }
        if ($vrijediDo < $pomakVremena) {
            $novikod = registracijaAktivacijskiKod();
            $veza->updateDB("UPDATE korisnik set aktivacijskiKod='$novikod',kodVrijediDo=DATE_ADD('$pomakVremena',INTERVAL {$_SESSION["trajanjeAktivacijskogKoda"]} hour) where korisnicko_ime='$korime'");
            $upitDnevnik = "INSERT into dnevnikRada values('default','$idkorisnik','$pomakVremena','Aktivacija koda','Korisnik $korisnickoIme je pokušao aktivirati kod no on je istekao. Poslan je novi')";
            $veza->updateDB($upitDnevnik);
            $to = $mail;
            $subject = "Novi aktivacijski kod";
            $txt = "Novi aktivacijski kod je: " . $novikod;
            $headers = "From: jmarijano@foi.hr";
            mail($to, $subject, $txt, $headers);
            return "Aktivacijski kod je istekao. Na mail je poslan novi!";
        }
        $veza->updateDB("UPDATE korisnik set aktiviran=1 where korisnicko_ime='$korime'");
        $veza->updateDB("INSERT INTO dnevnikRada values('default','$idkorisnik','$pomakVremena','Aktivacija koda','Korisnik $korisnickoIme je usješno aktivirao račun')");
    }
}

function brojRedovaTabliceIzmjena() {
    if (array_key_exists("brojRedakaSlanjePodataka", $_POST)) {
        $validan = 1;
        $broj = (int) $_POST["brojRedaka"];
        if (empty($broj)) {
            $validan = 0;
            return "Polje Broj redaka je prazno!";
        }
        if (!is_int($broj)) {
            $validan = 0;
            return "Unesi broj";
        }
        if ($broj <= 0) {
            $validan = 0;
            return "Unesi pozitivan broj!";
        }
        if ($validan == 1) {
            $_SESSION["paginacija"] = $broj;
        }
    }
}

function trajanjeAktivacijskogKodaIzmjena() {
    if (array_key_exists("trajanjeKodSlanjePodataka", $_POST)) {
        $validan = 1;
        $broj = (int) $_POST["trajanjeKod"];
        if (empty($broj)) {
            $validan = 0;
            return "Polje trajanje Aktivacijskog koda je prazno!";
        }
        if ($broj <= 0) {
            $validan = 0;
            return "Unesi pozitivan broj!";
        }
        if ($validan == 1) {
            $_SESSION["trajanjeAktivacijskogKoda"] = $broj;
        }
    }
}

function brojKrivihUnosaIzmjena() {
    if (array_key_exists("brojKrivihUnosaSlanjePodataka", $_POST)) {
        $validan = 1;
        $broj = (int) $_POST["brojKrivihUnosa"];
        if (empty($broj)) {
            $validan = 0;
            return "Polje broj krivih unosa je prazno!";
        }
        if ($broj <= 0) {
            $validan = 0;
            return "Unesi pozitivan broj!";
        }
        if ($validan == 1) {
            $_SESSION["losePrijava"] = $broj;
        }
    }
}

function otkljucajKorisnikaPopisBlokiranih() {
    $upit = "SELECT korisnicko_ime from korisnik where blokiran=1";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function otkljucajKorisnikaOtkljucavanje() {
    if (array_key_exists("otkljucajSlanjePodataka", $_POST)) {
        $validan = 1;
        $korime = $_POST["korisnik"];
        if (empty($korime)) {
            $validan = 0;
            return "Polje Odaberi korisnika je prazno!";
        }
        if ($validan == 1) {
            $pomakVremena = dohvatiVrijemePHP();
            $upitIdKorisnik = "SELECT idkorisnik from korisnik where korisnicko_ime='$korime'";
            $upitBlokiran = "UPDATE korisnik set blokiran=0,BrojKrivihUnosa=0 where korisnicko_ime='$korime'";
            $veza = new Baza();
            $veza->spojiDB();
            $veza->updateDB($upitBlokiran);
            $rezultatIdKorisnik = $veza->selectDB($upitIdKorisnik);
            $redRezultatIdKorisnik = $rezultatIdKorisnik->fetch_assoc();
            $idKorisnik = $redRezultatIdKorisnik["idkorisnik"];
            $upitDnevnik = "INSERT INTO dnevnikRada VALUES('default','{$_SESSION["tipKorisnika"]}','$pomakVremena','Otključavanje korisnika','Otključan je korisnik $korime')";
            $veza->updateDB($upitDnevnik);
            $veza->zatvoriDB();
            return "Korisnik je uspješno otključan!";
        }
    }
}

function blokiranjeKorisnikaPopisOtkljucanih() {
    $upit = "SELECT korisnicko_ime from korisnik where blokiran=0";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function blokiranjeKorisnikaBlokiranje() {
    if (array_key_exists("blokiranjeSlanjePodataka", $_POST)) {
        $validan = 1;
        $korime = $_POST["blokirajKorisnik"];
        if (empty($korime)) {
            $validan = 0;
            return "Polje odaberi korisnika je prazno!";
        }
        if ($validan == 1) {
            $pomakVremena = dohvatiVrijemePHP();
            $upitIdKorisnik = "SELECT idkorisnik from korisnik where korisnicko_ime='$korime'";
            $upitOtkljucan = "UPDATE korisnik set blokiran=1 where korisnicko_ime='$korime'";
            $veza = new Baza();
            $veza->spojiDB();
            $veza->updateDB($upitOtkljucan);
            $rezultatIdKorisnik = $veza->selectDB($upitIdKorisnik);
            $redRezultatIdKorisnik = $rezultatIdKorisnik->fetch_assoc();
            $idKorisnik = $redRezultatIdKorisnik["idkorisnik"];
            $upitDnevnik = "INSERT INTO dnevnikRada VALUES('default','{$_SESSION["tipKorisnika"]}','$pomakVremena','Blokiranje korisnika','Blokiran je korisnik $korime')";
            $veza->updateDB($upitDnevnik);
            $veza->zatvoriDB();
            return "Korisnik je uspješno blokiran!";
        }
    }
}

function kreiranjeModeratoraPopuniKorisnike() {
    $upit = "SELECT korisnicko_ime from korisnik where tipKorisnika_idtipKorisnika!=2";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function kreiranjeModeratoraKreiraj() {
    if (array_key_exists("kreiranjeModeratoraSlanjePodataka", $_POST)) {
        $validan = 1;
        $korisnik = $_POST["kreirajModeratora"];
        if (empty($korisnik)) {
            $validan = 0;
            return "Niste odabrali korisnika!";
        }
        if ($validan == 1) {
            $pomakVremena = dohvatiVrijemePHP();
            $upitKorisnik = "UPDATE korisnik set tipKorisnika_idtipKorisnika=2 where korisnicko_ime='$korisnik'";
            $veza = new Baza();
            $veza->spojiDB();
            $veza->updateDB($upitKorisnik);
            $upitDnevnik = "INSERT INTO dnevnikRada VALUES('default','{$_SESSION["tipKorisnika"]}','$pomakVremena','Kreiranje moderatora','Kreiran je moderator $korisnik')";
            $veza->updateDB($upitDnevnik);
            $veza->zatvoriDB();
            return "Uspješno kreiraj novi moderator!";
        }
    }
}

function dodjelaKorisnikuHotelKorisnik() {
    $upit = "SELECT korisnicko_ime from korisnik where tipKorisnika_idtipKorisnika=2 or tipKorisnika_idtipKorisnika=1";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function dodjelaKorisnikuHotelHotel() {
    $upit = "";
    if ($_SESSION["tipKorisnika"] != 1) {
        $upit = "SELECT hotel.naziv from hotel,moderira,korisnik where hotel.idhotel=moderira.hotel_idhotel and moderira.korisnik_idkorisnik=korisnik.idkorisnik and korisnik.korisnicko_ime='{$_SESSION["korisnicko_ime"]}'";
    } else {
        $upit = "SELECT naziv from hotel";
    }

    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function dodjelaKorisnikuHotelDodijeli() {
    if (array_key_exists("dodjelaKorisnikuHotelSlanjePodataka", $_POST)) {
        $validan = 1;
        $korisnik = $_POST["dodjelaKorisnik"];
        $hotel = $_POST["dodjelaHotel"];
        if (empty($korisnik)) {
            $validan = 0;
            return "Unesi korisnika!";
        }
        if (empty($hotel)) {
            $validan = 0;
            return "Unesi hotel!";
        }
        if ($validan == 1) {
            $veza = new Baza();
            $veza->spojiDB();

            $upitKorisnik = "SELECT idkorisnik from korisnik where korisnicko_ime='$korisnik'";
            $rezultatKorisnik = $veza->selectDB($upitKorisnik);
            $redKorisnik = $rezultatKorisnik->fetch_assoc();
            $idKorisnik = $redKorisnik["idkorisnik"];

            $upitHotel = "SELECT idhotel from hotel where naziv='$hotel'";
            $rezultatHotel = $veza->selectDB($upitHotel);
            $redHotel = $rezultatHotel->fetch_assoc();
            $idHotel = $redHotel["idhotel"];

            $upitModerira = "INSERT into moderira values($idHotel,$idKorisnik)";
            $veza->updateDB($upitModerira);
            $veza->zatvoriDB();
            return "Uspješno dodan moderator hotelu";
        }
    }
}

function prikazOglasa($stranica) {
    $output = "";
    $sqlUpit = "SELECT *
FROM `lokacija` , stranica, pozicija
WHERE lokacija.stranica_idstranica = stranica.idstranica
AND stranica.naziv = '$stranica'
AND lokacija.pozicija_idpozicija = pozicija.idpozicija
GROUP BY lokacija.pozicija_idpozicija, lokacija.stranica_idstranica";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($sqlUpit);
    while ($red = $rezultat->fetch_assoc()) {
        $output = $output . "<div id='{$red["naziv"]}'></div>";
    }
    $veza->zatvoriDB();
    return $output;
}

function registriraniKorisnik() {
    if (!empty($_SESSION["tipKorisnika"])) {
        $uloga = $_SESSION["tipKorisnika"];
        if ($uloga != 2 && $uloga != 1 && $uloga != 3) {
            header("Location:index.php");
            exit();
        }
    } else {
        header("Location:index.php");
        exit();
    }
}

function moderator() {
    if (!empty($_SESSION["tipKorisnika"])) {
        $uloga = $_SESSION["tipKorisnika"];
        if ($uloga != 2 && $uloga != 1) {
            header("Location:index.php");
            exit();
        }
    } else {
        header("Location:index.php");
        exit();
    }
}

function administrator() {
    if (!empty($_SESSION["tipKorisnika"])) {
        $uloga = $_SESSION["tipKorisnika"];
        if ($uloga != 1) {
            header("Location:index.php");
            exit();
        }
    } else {
        header("Location:index.php");
        exit();
    }
}

function kreirajSobuNovaSoba() {
    if (array_key_exists("kreSobuSlanjePodataka", $_POST)) {
        $validan = 1;
        $output = "";
        $hotel = $_POST["kreSobuHotel"];
        $opis = $_POST["kreSobuOpis"];
        $brojLezajeva = $_POST["kreSobuLezaj"];
        $brojSobe = $_POST["kreSobuSoba"];
        $userfile = $_FILES['userfile']['tmp_name'];
        $userfile_name = $_FILES['userfile']['name'];
        $userfile_size = $_FILES['userfile']['size'];
        $userfile_type = $_FILES['userfile']['type'];
        $userfile_error = $_FILES['userfile']['error'];
        if ($userfile_error > 0) {
            $output = $output . 'Problem: ';
            switch ($userfile_error) {
                case 1: $output = $output . 'Veličina veća od ' . ini_get('upload_max_filesize') . "<br>";
                    break;
                case 2: $output = $output . 'Veličina veća od ' . $_POST["MAX_FILE_SIZE"] . 'B<br>';
                    break;
                case 3: $output = $output . 'Datoteka djelomično prenesena<br>';
                    break;
                case 4: $output = $output . 'Datoteka nije prenesena<br>';
                    break;
            }
        }
        if (!empty($userfile)) {
            $info = getimagesize($userfile);
            if ($info == FALSE) {
                $output = $output . "ne valja<br>";
            }
            if ($info[2] !== IMAGETYPE_PNG && $info[2] !== IMAGETYPE_JPEG) {
                $output = $output . "krivi format<br>";
            }
        }


        $upfile = 'slikeSoba/' . $userfile_name;

        if (is_uploaded_file($userfile)) {
            if (!move_uploaded_file($userfile, $upfile)) {
                $output = $output . 'Problem: nije moguće prenijeti datoteku na odredište<br>';
            }
        } else {
            $output = $output . 'Problem: mogući napad prijenosom. Datoteka: ' . $userfile_name . "<br>";
        }
        if (empty($hotel)) {
            $validan = 0;
            $output = $output . "Polje Odaberi hotel je prazno!<br>";
        }
        if (empty($opis)) {
            $validan = 0;
            $output = $output . "Polje Opis je prazno!<br>";
        }
        if (empty($brojLezajeva)) {
            $validan = 0;
            $output = $output . "Polje Broj ležajeva je prazno!<br>";
        }
        if ($brojLezajeva <= 0) {
            $validan = 0;
            $output = $output . "Unesi pozitivan broj u polje Broj ležajeva!<br>";
        }
        if (empty($brojSobe)) {
            $validan = 0;
            $output = $output . "Polje Broj sobe je prazno!<br>";
        }
        if ($brojSobe <= 0) {
            $validan = 0;
            $output = $output . "Unesi pozitivan broj u polje Broj sobe!<br>";
        }
        if ($output != "") {
            $validan = 0;
            return $output;
        }
        if ($validan == 1) {
            $veza = new Baza();
            $veza->spojiDB();
            $upitIdHotel = "SELECT idhotel from hotel where naziv='$hotel'";
            $rezultatIdHotel = $veza->selectDB($upitIdHotel);
            $redIdHotel = $rezultatIdHotel->fetch_assoc();
            $idHotel = $redIdHotel["idhotel"];

            $upitUnos = "INSERT INTO soba VALUES('default','$idHotel','$opis','$brojLezajeva','$brojSobe','$userfile_name')";
            $veza->updateDB($upitUnos);
            $veza->zatvoriDB();
        }
    }
}

function zahtjevBlokirajOglasOglasi() {
    $pomakVremena = dohvatiVrijemePHP();
    $upit = "SELECT * FROM  oglas, zahtjevZaKreiranje WHERE oglas.zahtjevZaKreiranje_ID = zahtjevZaKreiranje.idzahtjevZaKreiranje and oglas.pocetak_prikazivanja <= '$pomakVremena' and oglas.kraj_prikazivanja >='$pomakVremena' and oglas.blokiran=0";
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    $veza->zatvoriDB();
    return $redovi;
}

function zahtjevBlokirajOglasBlokiraj() {
    if (array_key_exists("zahtjevBlokirajOglasSlanjePodataka", $_POST)) {
        $korisnikID = $_SESSION["idkorisnik"];
        $idOglas = $_POST["oglas"];
        $opis = $_POST["opis"];
        $output = "";
        if (empty($idOglas)) {
            $output = $output . "Polje Odaberi oglas je prazno!<br>";
        }
        if (empty($opis)) {
            $output = $output . "Polje opis je prazno!<br>";
        }
        if ($output != "") {
            return $output;
        } else {
            $pomakVremena = dohvatiVrijemePHP();
            $upitZahtjev = "INSERT into zahtjevZaBlokiranje VALUES('default','$opis','$korisnikID','$idOglas','$pomakVremena')";
            $upitDnevnik = "INSERT into dnevnikRada values('default','$korisnikID','$pomakVremena','Zahtjev za blokiranje','Kreiran je zahtjev za blokiranje za oglas pod šifrom $idOglas')";
            $veza = new Baza();
            $veza->spojiDB();
            $veza->updateDB($upitDnevnik);
            $veza->updateDB($upitZahtjev);

            $veza->zatvoriDB();
        }
    }
}

function kreirajZahtjevZaKreiranjeVrstaOglasa() {
    $veza = new Baza();
    $upit = "SELECT * from vrstaOglasa";
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    $veza->zatvoriDB();
    return $redovi;
}

function kreirajZahtjevZaKreiranjeUpisi() {
    if (array_key_exists("kreZahtKreSlanjePodataka", $_POST)) {
        $naziv = $_POST["kreZahtKreNaziv"];
        $opis = $_POST["kreZahtKreOpis"];
        $url = $_POST["kreZahtKreURL"];
        $vrstaOglasa = $_POST["kreZahtKreVrstaOglasa"];
        $korisnik = $_SESSION["idkorisnik"];
        $korime = $_SESSION["korisnicko_ime"];
        $validan = 1;
        $output = "";
        $userfile = $_FILES['userfile']['tmp_name'];
        $userfile_name = $_FILES['userfile']['name'];
        $userfile_size = $_FILES['userfile']['size'];
        $userfile_type = $_FILES['userfile']['type'];
        $userfile_error = $_FILES['userfile']['error'];
        if ($userfile_error > 0) {
            $output = $output . 'Problem: ';
            switch ($userfile_error) {
                case 1: $output = $output . 'Veličina veća od ' . ini_get('upload_max_filesize') . "<br>";
                    break;
                case 2: $output = $output . 'Veličina veća od ' . $_POST["MAX_FILE_SIZE"] . 'B<br>';
                    break;
                case 3: $output = $output . 'Datoteka djelomično prenesena<br>';
                    break;
                case 4: $output = $output . 'Datoteka nije prenesena<br>';
                    break;
            }
        }
        if (!empty($userfile)) {
            $info = getimagesize($userfile);
            if ($info == FALSE) {
                $output = $output . "ne valja<br>";
            }
            if ($info[2] !== IMAGETYPE_PNG && $info[2] !== IMAGETYPE_JPEG) {
                $output = $output . "krivi format<br>";
            }
        }


        $upfile = 'slikeOglasa/' . $userfile_name;

        if (is_uploaded_file($userfile)) {
            if (!move_uploaded_file($userfile, $upfile)) {
                $output = $output . 'Problem: nije moguće prenijeti datoteku na odredište<br>';
            }
        } else {
            $output = $output . 'Problem: mogući napad prijenosom. Datoteka: ' . $userfile_name . "<br>";
        }
        if (empty($naziv)) {
            $validan = 0;
            $output .= "Polje naziv je prazno!<br>";
        }
        if (empty($opis)) {
            $validan = 0;
            $output .= "POlje opis je prazno!<br>";
        }
        if (empty($url)) {
            $validan = 0;
            $output .= "Polje URL je prazno!<br>";
        }
        if (empty($vrstaOglasa)) {
            $validan = 0;
            $output .= "Polje Vrsta oglasa je prazno!<br>";
        }
        if ($output != "") {
            $validan = 0;
            return $output;
        }
        if ($validan == 1) {
            $pomakVremena = dohvatiVrijemePHP();
            $veza = new Baza();
            $veza->spojiDB();
            $upitZahtjev = "INSERT INTO zahtjevZaKreiranje values('default','$naziv','$opis','$url','$userfile_name','$korisnik','$vrstaOglasa')";
            $veza->updateDB($upitZahtjev);
            $upitDnevnik = "INSERT INTO dnevnikRada values('default','$korisnik','$pomakVremena','zahtjev za kreiranje','Korisnik $korime je kreirao zahtjev za kreiranje oglasa')";
            $veza->updateDB($upitDnevnik);
            $veza->zatvoriDB();
        }
    }
}

function azurirajZahtjevZaKreiranje() {
    if (array_key_exists("azurirajZahtjevVrstaSlanjePodataka", $_POST)) {
        $naziv = $_POST["azurirajZahtjevNaziv"];
        $opis = $_POST["azurirajZahtjevOpis"];
        $url = $_POST["azurirajZahtjevURL"];
        $vrstaOglasa = $_POST["azurirajZahtjevVrsta"];
        $idkorisnik = $_SESSION["idkorisnik"];
        $id = $_POST["id"];
        $validan = 1;
        $output = "";
        if (empty($naziv)) {
            $validan = 0;
            $output .= "Polje naziv je prazno!<br>";
        }
        if (empty($opis)) {
            $validan = 0;
            $output .= "POlje opis je prazno!<br>";
        }
        if (empty($url)) {
            $validan = 0;
            $output .= "Polje url je prazno!<br>";
        }
        if (empty($vrstaOglasa)) {
            $validan = 0;
            $output .= "Polje vrsta oglasa je prazno!<br>";
        }
        if ($output != "") {
            return $output;
        }
        if ($validan == 1) {
            $veza = new Baza();
            $veza->spojiDB();
            $pomakVremena = dohvatiVrijemePHP();
            $upitAzuriranje = "UPDATE zahtjevZaKreiranje set naziv='$naziv',opis='$opis',url='$url',vrstaOglasa_idvrstaOglasa='$vrstaOglasa' where idzahtjevZaKreiranje='$id'";
            $upitDnevnik = "INSERT into dnevnikRada values('default',$idkorisnik,'$pomakVremena','Ažuriranje zahtjeva za kreiranje','Korisnik {$_SESSION["korisnicko_ime"]} je izmijenio zahtjev za kreiranje $id')";
            $veza->updateDB($upitAzuriranje);
            $veza->updateDB($upitDnevnik);
            $veza->zatvoriDB();
        }
    }
}

function rezervacijaSobePopisHotela() {
    $upit = "";
    $tipKorisnika = $_SESSION["tipKorisnika"];
    if ($tipKorisnika == 1) {
        $upit = "SELECT idsoba,opis from soba";
    } else {
        $upit = "SELECT soba.idsoba,soba.opis FROM korisnik,moderira,hotel,soba where korisnik.idkorisnik=moderira.korisnik_idkorisnik and moderira.hotel_idhotel=hotel.idhotel and hotel.idhotel=soba.hotel_idhotel and korisnik.idkorisnik={$_SESSION["idkorisnik"]}";
    }
    $veza = new Baza();
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $polje = array();
    while ($red = $rezultat->fetch_assoc())
        $polje[] = $red;
    $veza->zatvoriDB();
    return $polje;
}

function rezervacijaSobeRegistriraniKorisnici() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT * from korisnik where tipKorisnika_idtipKorisnika=3";
    $rezultat = $veza->selectDB($upit);
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    $veza->zatvoriDB();
    return $polje;
}

function rezervacijaSobeRezerviraj() {
    if (array_key_exists("rezervacijaSobeSlanjePodataka", $_POST)) {
        $output = "";
        $validan = 1;
        $soba = $_POST["soba"];
        $korisnik = $_POST["korisnik"];
        $datumDolaska = $_POST["datumDolaska"];
        $datumOdlaska = $_POST["datumOdlaska"];
        if (empty($soba)) {
            $validan = 0;
            $output .= "Polje soba je prazno!<br>";
        }
        if (empty($korisnik)) {
            $validan = 0;
            $output .= "Polje korisnik je prazno!<br>";
        }
        if (empty($datumDolaska)) {
            $validan = 0;
            $output .= "Polje datum dolaska je prazno!<br>";
        }
        if (empty($datumOdlaska)) {
            $validan = 0;
            $output .= "Polje datum odlaska je prazno!<br>";
        }
        if ($output != "") {
            return $output;
        }
        if ($validan == 1) {
            $pomakVremena = dohvatiVrijemePHP();
            $veza = new Baza();
            $veza->spojiDB();
            $upitRezervacija = "INSERT INTO rezervacija values('default','$soba','$korisnik','$datumDolaska','$datumOdlaska')";
            $upitDnevnik = "INSERT INTO dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','rezervacija','Korisnik {$_SESSION["korisnicko_ime"]} je napravio rezervaciju sobe $soba za korisnika $korisnik')";
            $veza->updateDB($upitDnevnik);
            $veza->updateDB($upitRezervacija);
            $veza->zatvoriDB();
        }
    }
}

function azurirajPozicijuDohvatiSvePozicije() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT * FROM pozicija";
    $rezultat = $veza->selectDB($upit);
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    $veza->zatvoriDB();
    return $polje;
}

function azurirajPozicijuAzuriraj() {
    if (array_key_exists("azurirajPozicijuslanjePodataka", $_POST)) {
        $output = "";
        $validan = 1;
        $naziv = $_POST["azurirajPozicijuNaziv"];
        $opis = $_POST["azurirajPozicijuOpis"];
        $sirina = $_POST["azurirajPozicijuSirina"];
        $visina = $_POST["azurirajPozicijuVisina"];
        if (empty($naziv)) {
            $validan = 0;
            $output .= "Polje naziv je prazno!<br>";
        }
        if (empty($opis)) {
            $validan = 0;
            $output .= "Polje opis je prazno!<br>";
        }
        if (empty($sirina)) {
            $validan = 0;
            $output .= "Polje širina je prazno!<br>";
        }
        if ($sirina <= 50 || $sirina > 500) {
            $validan = 0;
            $output .= "Unesi vrijednost između 0 i 500!<br>";
        }
        if ($visina <= 50 || $visina > 500) {
            $validan = 0;
            $output .= "Unesi vrijednost između 0 i 500!<br>";
        }
        if ($output != "") {
            return $output;
        }
        if ($validan == 1) {
            $idPozicija = $_POST["azurirajPozicijuId"];
            $idkorisnik = $_SESSION["idkorisnik"];
            $korime = $_SESSION["korisnicko_ime"];
            $pomakVremena = dohvatiVrijemePHP();
            $veza = new Baza();
            $veza->spojiDB();
            $upitAzuriraj = "UPDATE pozicija set naziv='$naziv',opis='$opis',sirina='$sirina',visina='$visina' where idpozicija='$idPozicija'";
            $upitDnevnik = "INSERT into dnevnikRada VALUES('default','$idkorisnik','$pomakVremena','Ažuriranje pozicije','Korisnik $korime je ažurirao poziciju $idPozicija')";
            $veza->updateDB($upitAzuriraj);
            $veza->updateDB($upitDnevnik);

            $veza->zatvoriDB();
        }
    }
}

function kreirajPoziciju() {
    if (array_key_exists("krePozSlanje", $_POST)) {
        $output = "";
        $naziv = $_POST["krePozNaziv"];
        $opis = $_POST["krePozOpis"];
        $sirina = $_POST["krePozSirina"];
        $visina = $_POST["krePozVisina"];
        if (empty($naziv)) {
            $output .= "Polje naziv je prazno!<br>";
        }
        if (empty($opis)) {
            $output .= "Polje opis je prazno!<br>";
        }
        if (empty($sirina)) {
            $output .= "Polje širina je prazno!<br>";
        }
        if ($sirina < 100 || $sirina > 500 || $visina < 100 || $visina > 500) {
            $output .= "Unesi dimenzije između 100 i 500!<br>";
        }
        if (empty($visina)) {
            $output .= "Polje visina je  prazno!<br>";
        }
        if ($output != "") {
            return $output;
        }
        $pomakVremena = dohvatiVrijemePHP();
        $korisnicko_ime = $_SESSION["korisnicko_ime"];
        $idkorisnik = $_SESSION["idkorisnik"];
        $veza = new Baza();
        $veza->spojiDB();
        $upitPozicija = "INSERT INTO pozicija values('default','$naziv','$opis','$sirina','$visina')";
        $upitDnevnik = "INSERT INTO dnevnikRada values('default','$idkorisnik','$pomakVremena','Kreiranje pozicije','Korisnik $korisnicko_ime je kreirao novu poziciju')";
        $veza->updateDB($upitDnevnik);
        $veza->updateDB($upitPozicija);
        $veza->zatvoriDB();
    }
}

function dohvatiModeratore() {
    $veza = new Baza();
    $upit = "SELECT idkorisnik,ime,prezime from korisnik where tipKorisnika_idtipKorisnika=2";
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function dohvatiPozicije() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT idpozicija,naziv from pozicija";
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function dodijeliPoziciju() {
    if (array_key_exists("dodPozSlanje", $_POST)) {
        $output = "";
        $moderator = $_POST["dodPozMod"];
        $pozicija = $_POST["dodPozPoz"];
        if (empty($moderator)) {
            $output .= "Polje moderator je prazno!<br>";
        }
        if (empty($pozicija)) {
            $output .= "Polje pozicija je prazno!<br>";
        }
        if ($output != "") {
            return $output;
        }
        $pomakVremena = dohvatiVrijemePHP();
        $korisnicko_ime = $_SESSION["korisnicko_ime"];
        $idkorisnik = $_SESSION["idkorisnik"];
        $veza = new Baza();
        $veza->spojiDB();
        $upitDodijeli = "INSERT INTO zaduzen values('$pozicija','$moderator')";
        $upitDnevnik = "INSERT INTO dnevnikRada values('default','$idkorisnik','$pomakVremena','Dodijela pozicije','Korisnik $korisnicko_ime je dodijelio poziciju $pozicija moderatoru $moderator')";
        $veza->updateDB($upitDnevnik);
        $veza->updateDB($upitDodijeli);
        $veza->zatvoriDB();
    }
}

function dohvatiStranice() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT idstranica,naziv from stranica";
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function dohvatiOglase() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT oglas.idoglas,zahtjevZaKreiranje.naziv FROM oglas,zahtjevZaKreiranje WHERE zahtjevZaKreiranje.idzahtjevZaKreiranje=oglas.zahtjevZaKreiranje_ID";
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function kreirajLokacijuKreiraj() {
    if (array_key_exists("kreLokSlanje", $_POST)) {
        $output = "";
        $pozicija = $_POST["kreLokPoz"];
        $stranica = $_POST["kreLokStr"];
        $oglas = $_POST["kreLokOgl"];
        if (empty($pozicija)) {
            $output .= "Polje pozicija je prazno!<br>";
        }
        if (empty($stranica)) {
            $output .= "Polje stranica je prazno!<br>";
        }
        if (empty($oglas)) {
            $output .= "Polje oglas je prazno!<br>";
        }
        if ($output != "") {
            return $output;
        }
        $pomakVremena = dohvatiVrijemePHP();
        $idkorisnik = $_SESSION["idkorisnik"];
        $korime = $_SESSION["korisnicko_ime"];
        $veza = new Baza();
        $veza->spojiDB();
        $upitLokacija = "INSERT INTO lokacija values('$pozicija','$stranica','$oglas')";
        $upitDnevnik = "INSERT into dnevnikRada values('default','$idkorisnik','$pomakVremena','Kreiranje lokacije','Korisnik $korime je izradio novu lokaciju na poziciji $pozicija, stranici $stranica na kojoj se nalazi oglas $oglas')";
        $veza->updateDB($upitDnevnik);
        $veza->updateDB($upitLokacija);
        $veza->zatvoriDB();
    }
}

function dohvatiTipoveLogova() {
    $veza = new Baza();
    $upit = "SELECT distinct tip_loga FROM dnevnikRada";
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    $veza->zatvoriDB();
    return $polje;
}

function dohvatiPozicijeKorisnika() {
    $veza = new Baza();

    $upit = "";
    if ($_SESSION["tipKorisnika"] == 1) {
        $upit = "select * from pozicija";
    } else {
        $upit = "SELECT * from pozicija,zaduzen,korisnik where pozicija.idpozicija=zaduzen.pozicija_idpozicija and zaduzen.korisnik_idkorisnik=korisnik.idkorisnik and korisnik.idkorisnik='{$_SESSION["idkorisnik"]}'";
    }
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function kreirajVrstuOglasa() {

    if (array_key_exists("kreVrsOglSlanje", $_POST)) {
        $output = "";
        $brzina = $_POST["kreVrsOglBrzina"];
        $trajanje = $_POST["kreVrsOglTrajanje"];
        $pozicija = $_POST["kreVrsOglPozicija"];
        $cijena = $_POST["kreVrsOglCijena"];
        $naziv = $_POST["kreVrsOglNaziv"];
        if (empty($brzina)) {
            $output .= "Polje brzina izmjene je prazno!<br>";
        }
        if ($brzina <= 0) {
            $output .= "Unesi pozitivan broj u polje brzina!<br>";
        }
        if (empty($trajanje)) {
            $output .= "Polje trajanje je prazno!<br>";
        }
        if ($trajanje <= 0) {
            $output .= "Unesi pozitivan broj u polje tranjanje!<br>";
        }
        if (empty($pozicija)) {
            $output .= "Polje pozicija je prazno!<br>";
        }
        if (empty($cijena)) {
            $output .= "Polje cijena je prazno!<br>";
        }
        if ($cijena <= 0) {
            $output .= "Unesi pozitivan broj u polje cijena!<br>";
        }
        if (empty($naziv)) {
            $output .= "Polje naziv je prazno!<br>";
        }
        if ($output != "") {
            return $output;
        }
        $pomakVremena = dohvatiVrijemePHP();
        $veza = new Baza();
        $upitDnevnik = "INSERT into dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Kreiranje vrste oglasa','Korisnik {$_SESSION["korisnicko_ime"]} je kreirao vrstu oglasa pod nazivom $naziv')";
        $upitVrstaOglasa = "INSERT INTO vrstaOglasa values('default','$brzina','$trajanje','$pozicija','$cijena','$naziv')";
        $veza->spojiDB();
        $veza->updateDB($upitDnevnik);
        $veza->updateDB($upitVrstaOglasa);
        $veza->zatvoriDB();
    }
}

function obrisiGrad() {
    if (array_key_exists("obrisiGrad", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $veza = new Baza();
        $veza->spojiDB();
        $upit = "DELETE FROM grad where postanski_broj='{$_POST["grad"]}'";
        $upitDnevnik = "INSERT into dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Brisanje grada','Korisnik {$_SESSION["korisnicko_ime"]} obrisao je grad {$_POST["grad"]}')";
        $veza->updateDB($upit);
        $veza->updateDB($upitDnevnik);
        $veza->zatvoriDB();
    }
}

function dohvatiHotele() {
    $veza = new Baza();
    $upit = "SELECT * FROM hotel";
    $veza->spojiDB();
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $redovi = array();
    while ($red = $rezultat->fetch_assoc()) {
        $redovi[] = $red;
    }
    return $redovi;
}

function obrisiHotel() {
    if (array_key_exists("hotelSlanje", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $upitBrisanje = "DELETE From hotel where idhotel='{$_POST["hotel"]}'";
        $upitDnevnik = "INSERT INTO dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Brisanje hotela','Korisnik {$_SESSION["korisnicko_ime"]} obrisao je hotel {$_POST["hotel"]}')";
        $veza = new Baza();
        $veza->spojiDB();
        $veza->updateDB($upitBrisanje);
        $veza->updateDB($upitDnevnik);
        $veza->zatvoriDB();
    }
}

function dohvatiKorisnike() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT idkorisnik,korisnicko_ime from korisnik";
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function dohvatiGradove() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT * from grad";
    $rezultat = $veza->selectDB($upit);
    $polje = array();
    $veza->zatvoriDB();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    return $polje;
}

function obrisiKorisnika() {
    if (array_key_exists("korisnikBrisi", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $veza = new Baza();
        $veza->spojiDB();
        $upitBrisanje = "DELETE from korisnik where idkorisnik='{$_POST["brisiKor"]}'";
        $upitDnevnik = "INSERT INTO dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Brisanje korisnika','Korisnik {$_SESSION["korisnicko_ime"]} obrisao je korisnika {$_POST["brisiKor"]}')";
        $veza->updateDB($upitBrisanje);
        $veza->updateDB($upitDnevnik);
        $veza->zatvoriDB();
    }
}

function dohvatiSobe() {
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "SELECT * FROM soba";
    $rezultat = $veza->selectDB($upit);
    $veza->zatvoriDB();
    $polje = array();
    while ($red = $rezultat->fetch_assoc())
        $polje[] = $red;
    return $polje;
}

function obrisiSobu() {
    if (array_key_exists("sobaBrisi", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $veza = new Baza();
        $upitBrisanje = "DELETE from soba where idsoba='{$_POST["sobaB"]}'";
        $upitDnevnik = "INSERT Into dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Brisanje sobe','Korisnik {$_SESSION["korisnicko_ime"]} je obrisao sobu {$_POST["sobaB"]}')";
        $veza->spojiDB();
        $veza->updateDB($upitBrisanje);
        $veza->updateDB($upitDnevnik);
        $veza->zatvoriDB();
    }
}

function obrisiVrstuOglasa() {
    if (array_key_exists("vrstaOglasaBrisi", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $veza = new Baza();
        $upitBrisanje = "DELETE FROM vrstaOglasa WHERE idvrstaOglasa='{$_POST["vrstaOglasaB"]}'";
        $upitDnevnik = "INSERT INTO dnevnikRada VALUES('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Brisanje vrste oglasa','Korisnik {$_SESSION["korisnicko_ime"]} je obrisao vrstu oglasa {$_POST["vrstaOglasaB"]}')";
        $veza->spojiDB();
        $veza->updateDB($upitBrisanje);
        $veza->updateDB($upitDnevnik);
        $veza->zatvoriDB();
    }
}

function navigacijaGradAdmin() {
    if (isset($_SESSION["tipKorisnika"])) {
        if ($_SESSION["tipKorisnika"] == 1) {
            return '<a href="kreirajGrad.php">Kreiraj grad</a><a href="obrisiGrad.php">Obriši grad</a><a href=azurirajGrad.php>Ažuriraj grad</a>';
        }
    }
}

function navigacijaHotelAdmin() {
    if (isset($_SESSION["tipKorisnika"])) {
        if ($_SESSION["tipKorisnika"] == 1) {
            return '<a href="kreirajHotel.php">Kreiraj hotel</a><a href="obrisiHotel.php">Obriši hotel</a>';
        }
    }
}

function navigacijaSoba() {
    if (isset($_SESSION["tipKorisnika"])) {
        $output = "";
        if ($_SESSION["tipKorisnika"] == 1 || $_SESSION["tipKorisnika"] == 2) {
            $output .= '<a href="kreirajSobu.php">Kreiraj sobu</a><a href="rezervacijaSobe.php">Rezervacija sobe</a><a href="azurirajRezervaciju.php">Ažuriraj rezervaciju</a>';
        }
        if ($_SESSION["tipKorisnika"] == 1) {
            $output .= '<a href="obrisiSobu.php">Obriši sobu</a>';
        }
        return $output;
    }
}

function konfiguracijaIKorisnici() {
    if (isset($_SESSION["tipKorisnika"])) {
        if ($_SESSION["tipKorisnika"] == 1) {
            return'<li class="dropdown">
            <a href="" class="dropbtn">Konfiguracija</a>
            <div class="dropdown-content">
                <a href="brojRedovaTablice.php">Tablice</a>
                <a href="trajanjeAktivacijskiKod.php">Aktivacijski kod</a>
                <a href="brojKrivihUnosa.php">Krivi unosi</a>
                <a href="dohvatiVrijeme.php">Dohvati vrijeme</a>
                <a href="azurirajPoziciju.php">Ažuriraj poziciju</a>
                <a href="dnevnik.php">Dnevnik rada</a>
                <a href="resetUvjeteKorištenja.php">Resetiraj uvjete korištenja</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="" class="dropbtn">Korisnici</a>
            <div class="dropdown-content">
                <a href="otkljucajKorisnika.php">Otključaj korisnika</a>
                <a href="blokiranjeKorisnika.php">Blokiraj korisnika</a>
                <a href="kreiranjeModeratora.php">Kreiraj moderatora</a>
                <a href="dodjelaKorisnikuHotel.php">Dodjela moderatora hotelu</a>
                <a href="obrisiKorisnika.php">Obriši korisnika</a>
                <a href="dodijeliPoziciju.php">Dodijeli poziciju</a>
            </div>
        </li>';
        }
    }
}

function Statistike() {
    if (isset($_SESSION["tipKorisnika"])) {
        if ($_SESSION["tipKorisnika"] == 1) {
            return '<li class="dropdown">
            <a href="" class="dropbtn">Statistike</a>
            <div class="dropdown-content">
                <a href="statistikaKlikova.php">Statistika klikova</a>
                <a href="topListaKorisnika.php">Statistika korisnika</a>
                <a href="statistikaVrstaOglasaPozicija.php">Statistika vrsta i pozicija</a>
            </div>
        </li>';
        }
    }
}

function korisniciLokacije() {
    if (isset($_SESSION["tipKorisnika"])) {
        $output = "";
        if ($_SESSION["tipKorisnika"] == 1 || $_SESSION["tipKorisnika"] == 2) {
            $output = '<li class="dropdown">
            <a href="" class="dropbtn">Lokacije</a>
            <div class="dropdown-content">
                <a href="kreirajLokaciju.php">Kreiraj lokaciju</a>
           ';
        }

        $output .= ' </div> </li>';
        return $output;
    }
}

function korisniciPozicija() {
    if (isset($_SESSION["tipKorisnika"])) {
        if ($_SESSION["tipKorisnika"] == 1) {
            return'<li class="dropdown">
            <a href="" class="dropbtn">Pozicije</a>
            <div class="dropdown-content">
                <a href="kreirajPoziciju.php">Kreiraj poziciju</a>

            </div>
        </li>';
        }
    }
}

function korisniciOglasi() {
    if (isset($_SESSION["tipKorisnika"])) {
        $output = "";
        if ($_SESSION["tipKorisnika"] == 3 || $_SESSION["tipKorisnika"] == 2 || $_SESSION["tipKorisnika"] == 1) {
            $output .= '<li class="dropdown">
            <a href="" class="dropbtn">Oglasi</a>
            <div class="dropdown-content">
            <a href="zahtjevBlokirajOglas.php">Blokiraj oglas</a>
            <a href="mojiZahtjevi.php">Moji zahtjevi</a>
            <a href="pregledVrstaOglasa.php">Vrste oglasa</a>
            <a href="kreirajZahtjevZaKreiranje.php">Zahtjev za kreiranje</a>
            <a href="mojaStatistikaOglasa.php">Moja statistika klikova</a>';
        }
        if ($_SESSION["tipKorisnika"] == 2 || $_SESSION["tipKorisnika"] == 1) {
            $output .= '<a href="kreirajVrstuOglasa.php">Kreiraj vrstu oglasa</a>'
                    . '<a href="popisBlokiranje.php">Popis zahtjeva za blokiranje</a>'
                    . '<a href="popisZahtjevaZaKreiranjeZaModeratora.php">Zahtjevi na mojim pozicijama</a>';
        }
        if ($_SESSION["tipKorisnika"] == 1) {
            $output .= '<a href="obrisiVrstuOglasa.php">Obriši vrstu oglasa</a>';
        }

        $output .= "</div>
        </li>";
        return $output;
    }
}

function dohvatiRezervacije() {
    $pomakVremena = dohvatiVrijemePHP();
    $veza = new Baza();
    $veza->spojiDB();
    $upit = "select distinct rezervacija.* from rezervacija,soba,hotel,moderira,korisnik where rezervacija.soba_idsoba=soba.idsoba and soba.hotel_idhotel=hotel.idhotel and hotel.idhotel=moderira.hotel_idhotel and moderira.korisnik_idkorisnik=korisnik.idkorisnik and rezervacija.datum_dolaska>='$pomakVremena'";
    $rezultat = $veza->selectDB($upit);
    $polje = array();
    while ($red = $rezultat->fetch_assoc()) {
        $polje[] = $red;
    }
    $veza->zatvoriDB();
    return $polje;
}

function azurirajGrad() {
    if (array_key_exists("azurirajGradSlanje", $_POST)) {
        $naziv = $_POST["azurirajGradNaziv"];
        $postBroj = $_POST["azurirajGradPostBroj"];
        if (!empty($naziv)) {
            $pomakVremena = dohvatiVrijemePHP();
            $upitDnevnik = "INSERT into dnevnikRada values('default','{$_SESSION["idkorisnik"]}','$pomakVremena','Ažuriranje grada','Korisnik {$_SESSION["korisnicko_ime"]} je ažurirao grad pod poštanskim brojem $postBroj')";
            $upit = "Update grad set naziv='$naziv' where postanski_broj='$postBroj'";
            $veza = new Baza();
            $veza->spojiDB();
            $veza->updateDB($upit);
            $veza->updateDB($upitDnevnik);
            $veza->zatvoriDB();
        }
    }
}
