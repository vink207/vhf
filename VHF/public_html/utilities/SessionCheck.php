<?php
require_once 'SessionSearch.php';
session_start();
if (isset($_SESSION['input_search']))
{
    // checkboxen checked sidebar
    $streek = $_SESSION['input_search']->get_streek(); 
	$aantalpersonen = $_SESSION['input_search']->get_aantalpersonen();
	$aankomst = $_SESSION['input_search']->get_aankomst();
	$weken = $_SESSION['input_search']->get_weken(); 
	$slaapkamer = $_SESSION['input_search']->get_slaapkamer();
	$afstandzee = $_SESSION['input_search']->get_afstandzee();
    $arrcheckbox = $_SESSION['input_search']->get_arrcheckbox();
}
else
{
    $streek = ''; 
	$aantalpersonen = 0;
	$aankomst = '';
	$weken = '';
	$slaapkamer = '';
	$afstandzee = '';
    $arrcheckbox = array();
}
?>