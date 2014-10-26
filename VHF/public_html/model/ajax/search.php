<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/SearchHouse.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionSearch.php';

if (isset($_POST))
{    
    if (isset($_POST['streek'])) $streek = $_POST['streek'];
    if (isset($_POST['aankomst'])) $aankomst = $_POST['aankomst'];
    if (isset($_POST['weken'])) $weken = $_POST['weken'];
    if (isset($_POST['aantalpersonen'])) $aantalpersonen = $_POST['aantalpersonen'];
    if (isset($_POST['slaapkamer'])) $slaapkamer = $_POST['slaapkamer'];
    if (isset($_POST['afstandzee'])) $afstandzee = $_POST['afstandzee'];
    if (isset($_POST['prijs'])) $prijs = $_POST['prijs'];
    
    //var_dump($_POST);
    
    // checkboxen sidebar
    if (isset($_POST['checkbox']))
    {
        if ($_POST['checkbox'] != '')
        {
            foreach($_POST['checkbox'] AS $array)
            {
                if ($array != '')
                {
                    $array_checkbox[] = $array['name'];
                }
            }
        }
        else
        {
            $array_checkbox = array();
        }
    }
    
    // sessie aanmaken
    $_SESSION['input_search'] = new SessionSearch();
    $_SESSION['input_search']->set_streek($streek);
    $_SESSION['input_search']->set_aantalpersonen($aantalpersonen);
    $_SESSION['input_search']->set_aankomst($aankomst);
    $_SESSION['input_search']->set_weken($weken);
    $_SESSION['input_search']->set_slaapkamer($slaapkamer);
    $_SESSION['input_search']->set_afstandzee($afstandzee);
    $_SESSION['input_search']->set_arrcheckbox($array_checkbox);
    
//var_dump($_SESSION['input_search']);

/**
/* object returns results search
*/
    $search = new SearchHouse(); 
    $search->getResultsHTML();
    $arr_response = $search->getResponseJSON();
    echo $arr_response;
}
?>