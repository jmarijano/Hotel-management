<?php

$naslov = "Popis zahtjeva za blokiranjem";
include '_header.php';
$smarty->assign("uloga", moderator());
$smarty->display("templates/popisBlokiranje.tpl");
$smarty->display("templates/_footer.tpl");

