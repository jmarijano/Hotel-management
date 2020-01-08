<?php

$naslov = "Popis hotela";
include "_header.php";
$smarty->assign("oglasi", prikazOglasa(basename($_SERVER['PHP_SELF'])));
$smarty->display("templates/hotel.tpl");
$smarty->display("templates/_footer.tpl");
?>


