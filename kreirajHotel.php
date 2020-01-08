<?php

$naslov = "Kreiraj hotel";
include "_header.php";
$smarty->assign("uloga", administrator());
$smarty->assign("rezKreirajHotelUnos", kreirajHotelUnos());
$smarty->assign("rezPopuniGradove", popuniGradove());
$smarty->display("templates/kreirajHotel.tpl");
$smarty->display("templates/_footer.tpl");
?>


