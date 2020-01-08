<?php

session_start();
require_once './baza.class.php';
require_once './funkcije.php';
require_once './vanjske_biblioteke/smarty-3.1.32/libs/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = "templates";
$smarty->compile_dir = "templates_c";
$smarty->compile_check = true;
$smarty->assign("rezPretvoriuHTTPS", pretvoriuHTTPS());
$smarty->assign("trajanjeAkKoda", trajanjeAktivacijskogKoda());
$smarty->assign("rezLosePrijave", brojLosihPrijava());
$smarty->assign("paginacijaTablica", tablicaPaginacija());
$smarty->assign('naslov', $naslov);
$smarty->assign("rezultatFunkcijeProvjeraSesije", provjeraSesije());

$smarty->display("templates/_header.tpl");
include './navigacija.php';
