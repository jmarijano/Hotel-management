<?php

$naslov = "Kreiraj poziciju";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezKreiranja", kreirajPoziciju());
$smarty->display("templates/kreirajPoziciju.tpl");
$smarty->display("templates/_footer.tpl");


