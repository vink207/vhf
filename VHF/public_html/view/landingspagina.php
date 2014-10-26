<?php
/**
 * @author Lambert Portier
 * @copyright 2014
 */
require_once '../utilities/SessionSearch.php';
//require_once '../utilities/SearchResult.php';

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
    $_SESSION['input_search']->set_subject('huizen');
    
    // nodig om de resubmit te voorkomen, $_POST worden niet gecached door browsers
    //header('Location: '.$base.'/result.php');
}

if (isset($_GET['route']))
{
    $route = $_GET['route'];
}

/**
$search = new SearchResult();
$search->getSearchFor(); 
$search->getContentHTML();
$search->getResponseHTML();
$arr_response = $search->response;
$content = $arr_response['content'];
*/
// seo
require_once 'utilities/Seo.php';
$seo = new Seo();
$seo->setTitle('zoekresultaten');
$seo->setSubmitID('submit-ajax');

require_once 'header.php';
?>
<div id="main-container" class="container">
    <div id="home-left">
    <?php require_once 'sidebar.php'; ?>
    </div>
</div> <!-- #main-container -->
<?php require_once 'footer.php'; ?>
