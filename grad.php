<?php

$naslov = "Popis gradova";
include "_header.php";
$smarty->assign("oglasi", prikazOglasa(basename($_SERVER['PHP_SELF'])));
$smarty->display("templates/grad.tpl");
$smarty->display("templates/_footer.tpl");
?>
