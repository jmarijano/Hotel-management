<?php

$naslov = "Statistika vrsta i pozicija oglasa";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->display("templates/statistikaVrstaOglasaPozicija.tpl");
$smarty->display("templates/_footer.tpl");

