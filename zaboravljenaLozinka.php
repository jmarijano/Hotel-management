<?php

$naslov = "Zaboravljena lozinka";
include "_header.php";
$smarty->assign("regnesto", zaboravljenaLozinkaPosaljiMail());
$smarty->display("templates/zaboravljenaLozinka.tpl");
$smarty->display("templates/_footer.tpl");

