<?php

ob_start();
$naslov = "Registracija";
include "_header.php";

function nesto() {
    if (array_key_exists("regslanjePodataka", $_POST)) {
        $pomakVremena = dohvatiVrijemePHP();
        $output = "";
        $pravilanUnos = 1;
        if (empty($_POST["regime"])) {
            $output = $output . "Polje Ime je prazno!<br>";
            $pravilanUnos = 0;
        }
        if (empty($_POST["regprez"])) {
            $output = $output . "Polje Prezime je prazno!<br>";
            $pravilanUnos = 0;
        }
        if (empty($_POST["regkorime"])) {
            $output = $output . "Polje Korisničko ime je prazno!<br>";
            $pravilanUnos = 0;
        }
        if (empty($_POST["regwebadresa"])) {
            $output = $output . "Polje E - mail je prazno!<br>";
            $pravilanUnos = 0;
        }
        if (!preg_match("/^[a-zA-z0-9][a-zA-z0-9]+[.]?[a-zA-z0-9]+@{1}[a-zA-z0-9]+[.]{1}[a-zA-Z0-9]{1,}[a-zA-Z0-9]$/", $_POST["regwebadresa"])) {
            $output . $output . "Polje E - mail nije dobrof formata!<br>";
            $pravilanUnos = 0;
        }
        if (empty($_POST["reglozinka1"])) {
            $output = $output . "Polje Lozinka je prazno!<br>";
            $pravilanUnos = 0;
        }
        if (empty($_POST["reglozinka2"])) {
            $output = $output . "Polje Ponovi lozinku je prazno!<br>";
            $pravilanUnos = 0;
        }
        if ($_POST["regCAPTCHA"] !== $_POST["popunaCAPTCHA"]) {
            $output = $output . "Polje CAPTCHA nije pravilno uneseno!<br>";
            $pravilanUnos = 0;
        }
        if ($_POST["reglozinka1"] !== $_POST["reglozinka2"]) {
            $output = $output . "Lozinke nisu iste!<br>";
            $pravilanUnos = 0;
        }
        if ($output != "") {
            return $output;
        }
        $veza = new Baza();
        $veza->spojiDB();
        $sql = "SELECT COUNT(*) as brojKorisnika FROM korisnik where korisnicko_ime='{$_POST["regkorime"]}'";
        $rezultat = $veza->selectDB($sql);
        $veza->zatvoriDB();
        $brojRedaka = mysqli_fetch_assoc($rezultat);
        $dadada = $brojRedaka["brojKorisnika"];
        $aktivacijskiKod = registracijaAktivacijskiKod();
        if ($dadada == 0) {
            $aktivacijskiKod = registracijaAktivacijskiKod();
            $sol = sha1(time());
            $kripitiranaLozinka = sha1($sol . '-' . $_POST["reglozinka1"]);
            $veza->spojiDB();
            $sql = "INSERT INTO korisnik VALUES('default','{$_POST["regime"]}','{$_POST["regprez"]}',3,'{$_POST["regkorime"]}','{$_POST["reglozinka1"]}','$kripitiranaLozinka','{$_POST["regwebadresa"]}','default','default','$aktivacijskiKod',DATE_ADD('$pomakVremena',INTERVAL {$_SESSION["trajanjeAktivacijskogKoda"]} hour),'default')";
            $veza->updateDB($sql);
            if (!$veza->pogreskaDB()) {
                $to = $_POST["regwebadresa"];
                $subject = "Aktivacijski kod";
                $txt = "Aktivacijski kod je: " . $aktivacijskiKod;
                $headers = "From: jmarijano@foi.hr";
                mail($to, $subject, $txt, $headers);

                $korisnik = "SELECT idkorisnik from korisnik where korisnicko_ime='{$_POST["regkorime"]}'";
                $korisnikRezultat = $veza->selectDB($korisnik);
                $redKorisnik = $korisnikRezultat->fetch_assoc();
                $idKorisnik = $redKorisnik["idkorisnik"];
                $dnevnikSQL = "INSERT INTO dnevnikRada VALUES('default','$idKorisnik','$pomakVremena','registracija','Registran je korisnik {$_POST["regkorime"]} ')";
                $veza->updateDB($dnevnikSQL);
                $veza->zatvoriDB();
                header("Location:potvrdiAktivacijskiKod.php");
            }
        } else {
            return "Korisnicko ime već postoji!";
        }
    }
}

$smarty->assign("rezulatRegistracijaKlasaIme", registracijaKlasaIme());
$smarty->assign("rezultatRegKlasaPrez", registracijaKlasaPrezime());
$smarty->assign("rezultatRegKlasaKorime", registracijaKlasaKorime());
$smarty->assign("rezultatRegKlasaMail", registracijaKlasaMail());
$smarty->assign("rezultatRegKlasaLoz1", registracijaKlasaLozinka1());
$smarty->assign("rezultatRegKlasaLoz2", registracijaKlasaLozinka2());
$smarty->assign("rezultatregCap", registracijacaptcha());
$smarty->assign("rezultatNesto", nesto());
$smarty->display("templates/registracija.tpl");
$smarty->display("templates/_footer.tpl");
?>