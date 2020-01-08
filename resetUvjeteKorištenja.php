<?php

$naslov = "Resetiraj uvjete korištenja";
include '_header.php';
$smarty->assign("uloga", administrator());
$smarty->display("templates/resetUvjeteKorištenja.tpl");
$smarty->display("templates/_footer.tpl");

