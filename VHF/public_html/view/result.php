<?php
/**
 * @author Lambert Portier
 * @copyright 2014
 */
require_once '../utilities/SessionSearch.php';
require_once '../model/SearchHouse.php';
require_once '../utilities/SessionCheck.php';

if (isset($_POST) && !(empty($_POST)))
{    
    if (isset($_POST['streek'])) {$streek = $_POST['streek'];} 
    if (isset($_POST['aantalpersonen'])) {$aantalpersonen = $_POST['aantalpersonen'];} 
    if (isset($_POST['aankomst'])) {$aankomst = $_POST['aankomst'];}
    if (isset($_POST['weken'])) {$weken = $_POST['weken'];}
    
    // store search var in session object
    $_SESSION['input_search'] = new SessionSearch();
    $_SESSION['input_search']->set_streek($streek);
    $_SESSION['input_search']->set_aantalpersonen($aantalpersonen);
    $_SESSION['input_search']->set_aankomst($aankomst);
    $_SESSION['input_search']->set_weken($weken);
    
    // nodig om de resubmit te voorkomen, $_POST worden niet gecached door browsers
    header('Location: '.$base.'/results');
}

$search = new SearchHouse();
$search->getResultsHTML();
$arr_response = $search->getResponseHTML();
$content = $arr_response['content'];
$sidebar_counts = $arr_response['sidebar_counts'];
$msgnumber = $arr_response['msgnumber'];
$filters = $arr_response['filters'];
$stringhuisid = $arr_response['stringhuisid'];

// seo
require_once 'utilities/Seo.php';
$seo = new Seo();
$seo->setTitle('zoekresultaten');
$seo->setClass('ajax');
$seo->setSidebarCounts($sidebar_counts);

require_once 'header.php';
?>
<div id="main-container" class="container">
    <div id="search-right">
        <h1 id="msg-search" class="search-results"><?php echo $msgnumber; ?></h1>  
        <!--<h2 id="msg-filters"><?php echo $filters; ?></h2>-->
        <div id="knoppen">
        <select><option>Sorteer op: <b>standaard</b></option><option>Sorteer op: <b>prijs</b></option></select>
            <span id="resultlijst" class="button">Lijst</span>  
            <span id="resultkaart" class="button">Kaart</span>
        </div>
        <div id="search-lijst"><?php echo $content; ?></div>
        <div id="search-kaart"></div>
    </div>
    <div id="search-left"><?php require_once 'sidebar.php'; ?></div>
</div> <!-- #main-container -->
<div id="stringhuisid"><?php echo $stringhuisid; ?></div>
<?php require_once 'footer.php'; ?>
