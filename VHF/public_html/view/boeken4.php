<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionBoeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/functies_boeken.php';

session_start();

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

// array namen radio buttons
$arr_radio_check = array(0=>'lakenhuur', 'eindschoonmaak', 'kinderbenodigheden', 'huisdier');

// referer
$referer = $_SERVER['HTTP_REFERER'];

if (isset($_GET['huis_id']))
{
    $huis_id = $_GET['huis_id'];   
    
    // CONTROLE VARIABELEN
	if (!(is_numeric($huis_id)))
	{
		header('location:'.$absroot);
		exit();
	}
    
    // bestaand huis
    $sql = "SELECT code FROM tblHuizen WHERE huis_id = $huis_id AND archief = 0";
    $result = $db->query($sql);
    if ($result->size() == 0)
    {
		header('location:'.$absroot);
		exit();        
    }  

    // referer
    $referer = $_SERVER['HTTP_REFERER'];
    // vanaf verschillende pagina's tot deze pagina
    $pos = strpos($referer, '/uw-gegevens');
    
    // vanaf verschillende pagina's tot deze pagina
    if ($pos !== false)
    {
        $_SESSION['refer3'] =  $referer;
    }
}

if (isset($_POST))
{
    $aanhef = $_POST['aanhef'];
	$voorletters = $_POST['voorletters'];
	$tussenvoegsel = $_POST['tussenvoegsel'];
	$achternaam = $_POST['achternaam'];
	$hh_gb_dag = $_POST['hh_gb_dag'];
	$hh_gb_maand = $_POST['hh_gb_maand'];
	$hh_gb_jaar = $_POST['hh_gb_jaar'];
	$adres = $_POST['adres'];
	$nummer = $_POST['nummer'];
	$postcode = $_POST['postcode'];
	$woonplaats = $_POST['woonplaats'];
	$land = $_POST['land'];
	$tel_prive = $_POST['tel_prive'];
	$email = $_POST['email'];
	$gsm = $_POST['gsm'];
    $opmerkingen = $_POST['opmerkingen'];
    
    // stripslashes 
    $achternaam = stripslashes($achternaam);
	$voorletters = stripslashes($voorletters);
	$tussenvoegsel = stripslashes($tussenvoegsel);
	$adres = stripslashes($adres);
	$woonplaats = stripslashes($woonplaats);
    $opmerkingen = stripslashes($opmerkingen);
    
    // geen opmerkingen?
    if ($opmerkingen == '')
        $opmerkingen = 'geen';
    
    // opslag sessie	
	$_SESSION['klant'] = new SessionBoeken();	
	$_SESSION['klant']->AddKlant($aanhef, $voorletters, $tussenvoegsel, $achternaam, $hh_gb_dag, $hh_gb_maand, $hh_gb_jaar, $adres, $nummer, $postcode, $woonplaats, $land, $email, $tel_prive, $gsm, $opmerkingen);

    // medehuurders
    $personen = $_SESSION['items']->getItem('personen');
    $aantal_mede = $personen - 1;
    
    // er zijn medehuurders
    if ($aantal_mede != 0)
    {
        if (isset($_POST['arr_naam'])) $arr_naam = $_POST['arr_naam'];
        if (isset($_POST['arr_tv'])) $arr_tv = $_POST['arr_tv'];
        if (isset($_POST['arr_vl'])) $arr_vl = $_POST['arr_vl'];
        if (isset($_POST['arr_geslacht'])) $arr_geslacht = $_POST['arr_geslacht'];
        if (isset($_POST['arr_dag'])) $arr_dag = $_POST['arr_dag'];		
        if (isset($_POST['arr_maand'])) $arr_maand = $_POST['arr_maand'];
        if (isset($_POST['arr_jaar'])) $arr_jaar = $_POST['arr_jaar'];
        
        // session medehuurders
        $_SESSION['medehuurder'] = new SessionBoeken();
        $arr_naam = array_map(stripslashes, $arr_naam);
        $arr_tv = array_map(stripslashes, $arr_tv);
        $arr_vl = array_map(stripslashes, $arr_vl);            
        $_SESSION['medehuurder']->AddMedehuurder($arr_naam, $arr_tv, $arr_vl, $arr_geslacht, $arr_dag, $arr_maand, $arr_jaar);
    }
}

// url voor back-button
if (isset($_SESSION['refer3']))
    $referer = $_SESSION['refer3'];

// session met gegevens huis en data boeking
if (isset($_SESSION['boeking']))
{
    $huis_id = $_SESSION['boeking']->get_huis_id();
    $code = $_SESSION['boeking']->get_code();
    $tmp_a = $_SESSION['boeking']->get_tmp_a();
    $tmp_v = $_SESSION['boeking']->get_tmp_v();
    $aankomst = $_SESSION['boeking']->get_aankomst();
    $vertrek = $_SESSION['boeking']->get_vertrek();
    $foto = $_SESSION['boeking']->get_foto();
    $huisplaats = $_SESSION['boeking']->get_huisplaats();    
    $aantal_dagen = $_SESSION['boeking']->get_aantal_dagen();  
}
else
{
    header('location:'.$absroot);
	exit();
}

// gegevens hoofdhuurder
if (isset($_SESSION['klant']))
{
	$aanhef = $_SESSION['klant']->get_aanhef();
	$voorletters = $_SESSION['klant']->get_voorletters();
	$tussenvoegsel = $_SESSION['klant']->get_tussenvoegsel();
	$achternaam = $_SESSION['klant']->get_achternaam();
	$hh_gb_dag = $_SESSION['klant']->get_hh_gb_dag();
	$hh_gb_maand = $_SESSION['klant']->get_hh_gb_maand();
	$hh_gb_jaar = $_SESSION['klant']->get_hh_gb_jaar();
	$adres = $_SESSION['klant']->get_adres();
	$nummer = $_SESSION['klant']->get_nummer();
	$postcode = $_SESSION['klant']->get_postcode();
	$woonplaats = $_SESSION['klant']->get_plaats();
	$land = $_SESSION['klant']->get_land();
	$tel_prive = $_SESSION['klant']->get_tel_prive();
	$email = $_SESSION['klant']->get_email();
	$gsm =  $_SESSION['klant']->get_gsm();
	$opmerkingen = $_SESSION['klant']->get_opmerking();
} 
else
{
    header('location:'.$absroot);
	exit();
}

// sessie met niet optionele bbk, komen onder de prijsopgave te staan
if (isset($_SESSION['nietoptioneel']))
{
    $arr_bkk_niet_opt = $_SESSION['nietoptioneel']->get_arr_bkk_niet_opt();
}

// sessie met optionele bbk, komen onder de prijsopgave te staan
if (isset($_SESSION['optioneel']))
{
    $arr_bkk_opt = $_SESSION['optioneel']->get_arr_bkk_opt();
}

// sessie 
if (isset($_SESSION['items']))
{
    // reisgezelschap
    $personen = $_SESSION['items']->getItem('personen');
    
    // welke polis
    $val_annulering = $_SESSION['items']->getItem('val_annulering');  
    $val_schade_woning = $_SESSION['items']->getItem('val_schade_woning');  
    if ($val_annulering == 'nee')
        $val_annulering = 'Ik heb geen annuleringsverzekering afgesloten.';
    if ($val_schade_woning == 'nee')
        $val_schade_woning = 'Ik heb zelf een verzekering die schade aan het vakantiehuis dekt.';
    
    // formfields radiobuttons en checkbox  
    $z = count($arr_radio_check);   
    for ($x = 0; $x < $z; $x++)  
    {
        $name = 'ch_'.$arr_radio_check[$x];
        $$name = $_SESSION['items']->getItem($name); 
        
        $name = 'val_'.$arr_radio_check[$x];
        $$name = $_SESSION['items']->getItem($name); 
    }
} 
else
{
    header('location:'.$absroot);
	exit();
}

// header
require_once 'view/header.php';

$menuaction = $absroot.'/boeken/'.$huis_id.'/reisgezelschap';
$urlboeken = $absroot.'/boeken/'.$huis_id.'/voltooid';
?>

<div id="main-container" class="container">
<!-- RIGHT -->
<div id="book-right">
    <!-- book_content -->
    <div id="book_content">
        <ul id="book_nav">
            <li class="first"><a href="<?php echo $menuaction; ?>">1 REISGEZELSCHAP</a></li>
            <li><a class="active" href="#">2 VERZEKERINGEN</a></li>
            <li><a href="#">3 UW GEGEVENS</a></li>
            <li class="last"><a href="#">4 CONTROLE/BEVESTIGING</a></li>
        </ul>
        <h2>4 CONTROLE/BEVESTIGING</h2>
        <div class="controle">
        <h2>Uw gegevens <a href="">wijzigen</a></h2>
        <table>
            <colgroup>
                <col style="width: 180px">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <td>Aantal personen:</td>
                <td><?php echo $personen; ?></td>
            </tr>
<?php
// huisdieren
if ($ch_huisdier == 1)
{   
    if ($val_huisdier != 'nee')
        $tekst = 'ja '.$val_huisdier;
    else
        $tekst = 'nee';
?>
    <tr>
        <td>Huisdier:</td>
        <td><?php echo $tekst; ?></td>
    </tr>
<?php
}
?>
    </tbody>
</table>
</div>
<div class="controle">
<h2>Hoofdhuurder</h2> 
<table>
    <colgroup>
        <col style="width: 180px">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <td>Naam:</td><td><?php echo $voorletters;
RIGHT;
        if ($tussenvoegsel != '')
        {
?>
            <?php echo $tussenvoegsel; 
        }
?>         

    <?php echo $achternaam; ?></td> 
    </tr>
    <tr>
        <td>Straat/nummer:</td>
        <td><?php echo $adres.' '.$nummer; ?></td>
    </tr>
    <tr>
        <td>Postcode/plaats:</td>
        <td><?php echo $postcode.' '.$woonplaats; ?></td>
    </tr>
    <tr>
        <td>Land:</td>
        <td><?php echo $land; ?></td>
    </tr>
    <tr>
        <td>Geboortedatum:</td>
        <td><?php echo $hh_gb_dag.'-'.$hh_gb_maand.'-'.$hh_gb_jaar; ?></td>
    </tr>
    <tr>
        <td>E-mail:</td>
        <td><?php echo $email; ?><span class="msg">Is uw e-mail adres correct?</span></td>
    </tr>
    <tr>
        <td>Tel. priv&eacute;:</td>
        <td><?php echo $tel_prive; ?></td>
    </tr>
    <tr>
        <td>Tel. mobiel:</td>
        <td><?php echo $gsm; ?></td>
    </tr>
    </tbody>
</table>
</div>
<div class="controle">
<h2>Medehuurders</h2> 
<table>
    <colgroup>
        <col style="width: 180px">
        <col>
    </colgroup>
    <tbody>
<?php
     if ($aantal_mede == 0)
     {
?>
        <tr>
            <td colspan="2">Geen</td>
        </tr>
<?php
     }
     else
     {
        // medehuurders
        for ($x = 0; $x < $aantal_mede; $x++)
        { 
            $naam = $arr_vl[$x] . ' ';
            if ($arr_tv[$x] != '')
                $naam .= $arr_tv[$x] . ' ';
			$naam .= $arr_naam[$x]; 
            
            $geboortedatum = $arr_dag[$x].'-'.$arr_maand[$x].'-'.$arr_jaar[$x];
?>
            <tr>
                <td><?php echo $naam; ?></td>
                <td><?php echo $arr_vw_geslacht[$x]; ?></td>
                <td><?php echo $geboortedatum; ?></td>
            </tr>
<?php
        }
     }
?>
    </tbody>
</table>
</div>
<div class="controle">
<h2>Verzekeringen en opties</h2> 
<table>
    <colgroup>
        <col style="width: 180px">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <td>All risk Annuleringsverzekering:</td>
        <td><?php echo $val_annulering; ?></td>
    </tr>
    <tr>
        <td>Verzekering schadewoning:</td>
        <td><?php echo $val_schade_woning; ?></td>
    </tr>
<!-- eindschoonmaak -->
<?php
if ($ch_eindschoonmaak == 1)
{    
?>
    <tr>
        <td>Eindschoonmaak:</td>
        <td><?php echo $val_eindschoonmaak; ?></td>
    </tr>
<?php
}
// lakenhuur
if ($ch_lakenhuur == 1)
{    
?>
    <tr>
        <td>Lakenhuur:</td>
        <td><?php echo $val_lakenhuur; ?></td>
    </tr>
<?php
}
// kinderbenodigheden
if ($ch_kinderbenodigheden == 1)
{
?>
    <tr>
        <td>Kinderbenodigheden:</td>
        <td><?php echo $val_kinderbenodigheden; ?></td>
    </tr>
<?php
}
?>
    </tbody>
</table>
</div>
<div class="controle">
<h2>Opmerkingen</h2> 
<table>
    <tbody>
    <colgroup>
        <col style="width: 180px">
        <col>
    </colgroup>
    <tr>
        <td>Opmerkingen:</td>
        <td><?php echo $opmerkingen; ?></td>
    </tr>
    </tbody>
</table>
</div>
<br class="clear">
<button onclick="document.location.href='<?php echo $referer; ?>';" type="button" id="previous" name="previous">Terug naar stap 2</button>
<button onclick="document.location.href='<?php echo $urlboeken; ?>';" value="" id="next" name="next">Stap 4: Boeken</button>
</div> 
<?php
/** LEFT */
if (isset($_SESSION['kolom-links']))
{
    echo $_SESSION['kolom-links'];
}

?>
</div> <!-- #main-container -->

<?php require_once 'view/footer.php'; ?>