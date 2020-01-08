<?php

$naslov = "O autoru";
include "./_header.php";
$smarty->assign("oglasi", prikazOglasa(basename($_SERVER['PHP_SELF'])));
$smarty->display("templates/oAutoru.tpl");
$smarty->display("templates/_footer.tpl");

