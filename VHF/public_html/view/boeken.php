<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionBoeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/functies/functies_boeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionSearch.php';

session_start();

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

if (isset($_GET['huis_id']))
{
    $huis_id = $_GET['huis_id'];    
    
    // CONTROLE VARIABELEN
	if (!(is_numeric($huis_id)))
	{
		header('location:'.$absroot);
		exit();
	}

    // referer
    $referer = $_SERVER['HTTP_REFERER'];
    // vanaf verschillende pagina's tot deze pagina
    $pos = strpos($referer, '/vakantiehuis');
    if ($pos !== false)
    {
        $_SESSION['refer1'] =  $referer;
    }
}

// url voor back-button
if (isset($_SESSION['refer1']))
    $referer = $_SESSION['refer1'];

// session met gegevens huis en data boeking
if (isset($_SESSION['boeking']))
{
    $huis_id = $_SESSION['boeking']->get_huis_id();
    $huiscode = $_SESSION['boeking']->get_code();
    $tmp_a = $_SESSION['boeking']->get_tmp_a();
    $tmp_v = $_SESSION['boeking']->get_tmp_v();
    $aankomst = $_SESSION['boeking']->get_aankomst();
    $vertrek = $_SESSION['boeking']->get_vertrek();
    $foto = $_SESSION['boeking']->get_foto();
    $huisplaats = $_SESSION['boeking']->get_huisplaats();    
    $verblijfsduur = $_SESSION['boeking']->get_verblijfsduur();    
    $aantal2 = $_SESSION['boeking']->get_aantal2(); 
}

// sessie 
if (isset($_SESSION['items']))
{
    // reisgezelschap
    $volwassen = $_SESSION['items']->getItem('volwassen');
    $kinderen = $_SESSION['items']->getItem('kinderen');
    $peuters = $_SESSION['items']->getItem('peuters');
    $aantal_kinderen = $kinderen + $peuters;
    if ($aantal_kinderen == 0)
        $aantal_kinderen = '';
    
    // vaste bedragen
    $huur = $_SESSION['items']->getItem('huur');
        
    // totaal bedrag 
    $totaal = $huur;
    // bedragen formateren
    $huur_format = number_format($huur, 2, ',', '.');
    $totaal_format = number_format($totaal, 2, ',', '.');
    
    // geen huursom bekend, prijs op aanvraag
    if ($huur == 0)
    {
        $huur_format = 'op aanvraag';
    }        
    
    // formfields radiobuttons en checkbox values  
    // array namen radio en checkboxes
    $arr_radio_check = array(0=>'lakenhuur', 'eindschoonmaak', 'kinderbenodigheden', 'huisdier', 'schade_woning', 'annulering');
    $z = count($arr_radio_check);   
    for ($x = 0; $x < $z; $x++)  
    {
        $name = 'ch_'.$arr_radio_check[$x];
        $$name = $_SESSION['items']->getItem($name); 
        
        $name = 'val_'.$arr_radio_check[$x];
        $$name = $_SESSION['items']->getItem($name); 
    }
}

// sessie met niet optionele bbk; komt bij vaste kosten te staan
if (isset($_SESSION['nietoptioneel']))
{
    $arr_bkk_niet_opt = $_SESSION['nietoptioneel']->get_arr_bkk_niet_opt();
}

// sessie met optionele bbk; komt bij vaste kosten te staan
if (isset($_SESSION['optioneel']))
{
    $arr_bkk_opt = $_SESSION['optioneel']->get_arr_bkk_opt();
}

// selectboxen aantal volwassen
$select_aantal_volwassenen = selectAantal('volwassen', $volwassen, 0);
$select_aantal_kinderen = selectAantal('kinderen', $kinderen, 1);
$select_aantal_peuters = selectAantal('peuters', $peuters, 1);

// radio buttons polis
if ($val_annulering == 'ja')
{
    $ch_annulering_ja = 'checked';
    $ch_annulering_nee = '';
}
else
{
    $ch_annulering_ja = '';
    $ch_annulering_nee = 'checked';    
}
if ($val_schade_woning == 'ja')
{
    $ch_schade_woning_ja = 'checked';
    $ch_schade_woning_nee = '';
}
else
{
    $ch_schade_woning_ja = '';
    $ch_schade_woning_nee = 'checked';    
}

// blok huis met foto
$huis_periode_aantal = <<<EOD
    <table class="first book_data">
        <colgroup>
            <col width="120">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <td>Aankomst:</td>
                <td align="right">$aankomst</td>
            </tr>
            <tr>
                <td>Vertrekdatum:</td>
                <td align="right">$vertrek</td>
            </tr> 
            <tr>
                <td>Verblijfsduur:</td>
                <td align="right">$verblijfsduur</td>
            </tr> 
            <tr>
                <td>Aantal volwassenen:</td>
                <td align="right">$volwassen</td>
            </tr>  
            <tr>
                <td>Aantal kinderen:</td>
                <td align="right">$aantal_kinderen</td>
            </tr>
        </tbody>
    </table>
EOD;
// kostenoverzivht
if ($huur != 0)
{
    $kosten = <<< EOD
        <table class="book_prices">
            <colgroup>
                <col width="190">
                <col>
                <col>
            </colgroup>
            <tbody>
                <tr class="rp-first">
                    <td>Huurprijs:</td>
                    <td>&euro;</td>
                    <td align="right">$huur_format</td>
                </tr>
EOD;
        
    $kosten .= <<<EOD
        <tr>
            <td class="totaal">Totaal</td>
            <td class="totaal">&euro;</td>
            <td align="right" class="totaal">$totaal_format</td>
        </tr>
    </tbody>
</table>
EOD;
}
else // huur op aanvraag
{
    $kosten = <<< EOD
        <table class="book_prices">
            <colgroup>
                <col width="100">
                <col>
             </colgroup>
             <tbody>
                <tr class="rp-first">
                <td>Huurprijs:</td>
                <td align="right">$huur_format</td>
             </tr>
        </tbody>
    </table>
EOD;
}

//  optionele bijkomende kosten
$optioneel = '';
if (isset($arr_bkk_opt))
{    
    $optioneel = <<<EOD
            <table>
            <colgroup>
                <col width="12px">
                <col>
            </colgroup>
            <tbody>
EOD;
    // key = lakenhuur, eindschoonmaak en kinderbenodigheden
    foreach ($arr_bkk_opt as $key => $value) 
    { 
        // als checkbox waarde 1 heeft tonen
        $name = 'val_'.$key;
        if ($$name == 'ja')
        {   
            $optioneel .= <<<EOD
            <tr>
                <td class="bull">&bull;</td>
                <td>$value</td>
             </tr>
EOD;
        }
        else
        {
            // uitzondering eindschoonmaak; ook bij keuze nee een tekst tonen: De eindschoonmaak verzorgt u zelf.
            if ($name == 'val_eindschoonmaak' && $val_eindschoonmaak == 'nee')
            {  
                $optioneel .= <<<EOD
                <tr>
                    <td class="bull">&bull;</td>
                    <td>De eindschoonmaak verzorgt u zelf.</td>
                 </tr>
EOD;
            }
            // uitzondering lakenhuur; ook bij keuze nee een tekst tonen: Lakens en (keuken)handdoeken neemt u zelf mee.
            if ($name == 'val_lakenhuur' && $val_lakenhuur == 'nee')
            {  
                $optioneel .= <<<EOD
                <tr>
                    <td class="bull">&bull;</td>
                    <td>Lakens en (keuken)handdoeken neemt u zelf mee.</td>
                 </tr>
EOD;
            }
        }
    } 
    $optioneel .= <<<EOD
            </tbody>
            </table>
EOD;
}

// niet optionele bijkomende kosten
if (isset($arr_bkk_niet_opt))
{
    $z = count($arr_bkk_niet_opt);
    
    $niet_optioneel = <<<EOD
            <table>
            <colgroup>
                <col width="12px">
                <col>
            </colgroup>
            <tbody>
EOD;
    for ($x = 0; $x < $z; $x++)
    {
        $niet_optioneel .= <<<EOD
            <tr>
                <td class="bull">&bull;</td>
                <td>$arr_bkk_niet_opt[$x]</td>
             </tr>
EOD;
    } 
    $niet_optioneel .= <<<EOD
            </tbody>
            </table>
EOD;
}
$niet_optioneel .= <<<EOD
EOD;

// info huisdier
if ($ch_huisdier == 1)
{
    if ($val_huisdier != 'nee')
        $teksthuisdier = $_SESSION['items']->getItem('huisdier_opt');
    else
        $teksthuisdier = '';
} 
else
{
    $teksthuisdier = $_SESSION['items']->getItem('huisdier_niet_opt');
}
$infohuisdier = '';
if ($teksthuisdier != '')
{
   $infohuisdier = <<<EOD
    <table>
        <colgroup>
            <col width="12px">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <td class="bull">&bull;</td>
            <td>$teksthuisdier</td>
        </tr>
        </tbody>
    </table>
EOD;
}

// seo
require_once 'utilities/Seo.php';
$seo = new Seo();
$seo->setTitle('boeken/reisgezelschap');

// header
require_once 'view/header.php';

$action = $absroot.'/boeken/'.$huis_id.'/verzekeringen';
?>

<div id="main-container" class="container boeken col2right">

<!-- RIGHT -->
<div id="right">
    <!-- book_content -->
    <div id="book_content">
        <ul id="book_nav">
            <li class="first active"><a href="#">1 REISGEZELSCHAP</a></li>
            <li><a href="#">2 VERZEKERINGEN</a></li>
            <li><a href="#">3 UW GEGEVENS</a></li>
            <li class="last"><a href="#">4 BEVESTIGING</a></li>
        </ul>
        <div class="blok">
        <h2>1 REISGEZELSCHAP</h2>
        <form id="form1" method="post" action="<?php echo $action; ?>" enctype="application/x-www-form-urlencoded" class="jqTransformhidden">
            <fieldset id="fieldset-formReisgezelschap">
                <p>Met hoeveel personen verblijft u in het huis</p>
                <dt><label for="formHuurders-aantalVolwassen" class="required">Aantal volwassenen</label></dt>
                    <dd>
                        <?php echo $select_aantal_volwassenen; ?>
                    </dd>
                <dt><label for="formHuurders-aantalKinderen" class="required">Aantal kinderen (3 tot 17 jaar)</label></dt>
                    <dd>
                         <?php echo $select_aantal_kinderen; ?>
                    </dd>
                <dt><label for="formHuurders-aantalKinderen" class="required">Aantal kinderen (tot 3 jaar)</label></dt>
                    <dd>
                         <?php echo $select_aantal_peuters; ?>
                    </dd>
                <dt><label for="formHuurders-huisdier" class="required">Huisdieren</label></dt>
<?php
if ($ch_huisdier == 1)
{
    // selectbox grootte huisdier
    $select_grootte = selectMaatHuisdier($val_huisdier);
?>
                    <dd>
                        <?php echo $select_grootte; ?>
                    </dd>
<?php
}
else
{
?>                  
                    <dd>
                        niet toegestaan
                    </dd>
<?php
}
?>
             <p>* In het gekozen vakantiehuis kunt u met max. $aantal2 personen verblijven (<strong>kinderen tot 3 jaar niet meegerekend</strong>)
             </fieldset>
             <hr />
             <fieldset id="fieldset-formVerzekering">
                <legend>Ter plaatse te voldoen</legend>
             </fieldset>
             <fieldset id="fieldset-formFaciliteiten">
                <legend>Extra opties</legend>
<?php
// lakenhuur
if ($ch_lakenhuur == 1)
{
    // radio buttons
    if ($val_lakenhuur == 1)
    {
        $ch_lakenhuur_ja = 'checked';
        $ch_lakenhuur_nee = '';
    }
    else
    {
        $ch_lakenhuur_ja = '';
        $ch_lakenhuur_nee = 'checked';    
    }
?>   
    <dt><label class="required">Lakenhuur</label></dt>
    <dd>
        <label><input type="radio" class="optioneel" name="lakenhuur" id="lakenhuur-ja" value="ja" <?php echo $ch_lakenhuur_ja; ?>>Ja</label>
        <label><input type="radio" class="optioneel" name="lakenhuur" id="lakenhuur-nee" value="nee" <?php echo $ch_lakenhuur_nee; ?>>Nee</label>
    </dd>
<?php
}
// eindschoonmaak
if ($ch_eindschoonmaak == 1)
{
    // radio buttons
    if ($val_eindschoonmaak == 'ja')
    {
        $ch_eindschoonmaak_ja = 'checked';
        $ch_eindschoonmaak_nee = '';
    }
    else
    {
        $ch_eindschoonmaak_ja = '';
        $ch_eindschoonmaak_nee = 'checked';    
    }
    
    $prijsoptie = $_SESSION['items']->getItem('prijsoptie');
?>    
    <dt><label class="required">Schoonmaak<br />(&euro; <?php echo $prijsoptie; ?>)</label></dt>
    <dd>
        <label><input type="radio" class="optioneel" name="eindschoonmaak" id="eindschoonmaak-ja" value="ja" <?php echo $ch_eindschoonmaak_ja; ?>>Ja</label>  
        <label><input type="radio" class="optioneel" name="eindschoonmaak" id="eindschoonmaak-nee" value="nee" <?php echo $ch_eindschoonmaak_nee; ?>>Nee</label>
    </dd>
<?php
}
// kinderbenodigheden
if ($ch_kinderbenodigheden == 1)
{
    // radio buttons
    if ($val_kinderbenodigheden == 'ja')
    {
        $ch_kinderbenodigheden_ja = 'checked';
        $ch_kinderbenodigheden_nee = '';
    }
    else
    {
        $ch_kinderbenodigheden_ja = '';
        $ch_kinderbenodigheden_nee = 'checked';    
    }
    
    $kbgratis = $_SESSION['items']->getItem('kbgratis');
?>
    
    <dt><label class="required">Kinderbenodigheden
<?php
     if ($kbgratis != '')
     {
?>
    <br />($kbgratis)
<?php
     }
?>
    </label></dt>
    <dd>
        <label><input type="radio" class="optioneel" name="kinderbenodigheden" id="kinderbenodigheden-ja" value="ja"<?php echo $ch_kinderbenodigheden_ja; ?>>Ja</label>             
        <label><input type="radio" class="optioneel" name="kinderbenodigheden" id="kinderbenodigheden-nee" value="nee"<?php echo $ch_kinderbenodigheden_nee; ?>>Nee</label>
   </dd>
<?php
}
?>
    </fieldset>
    </div><!-- /blok-->
    <input type="submit" value="STAP 2" id="next" name="next">
        </form>
        </div><!--/right-->
    </div>
    <!-- book_content -->
    <!-- LEFT  -->
    <div id="left">
        <div id="book_details">
            <h2>UW BOEKING</h2>
            <div class="blok">
            <img src="http://www.vakantiehuisfrankrijk.nl/huis_img/<?php echo $foto; ?>" width="280"  alt="foto $huiscode" border="0" />
            <h3>$huiscode <span><?php echo $huisplaats; ?></span></h3>            
            <div id="huis_periode_aantal"><?php echo $huis_periode_aantal; ?></div>
            </div><!--/blok-->
            <div class="blok">
            <h4>Kostenoverzicht</h4>
            <div id="kosten"><?php echo $kosten; ?></div>
            </div><!--/blok-->
    <!-- niet optionele zaken; inclusief of zelf meebrengen of doen -->
<?php
if (isset($niet_optioneel))
{
?>
     <div class="blok">
     <h4>Ter plaatse te voldoen</h4>
     <div id="niet_optioneel"><?php echo $niet_optioneel; ?></div>
<?php
}

// optionele zaken
if (isset($optioneel))
{
?>
    <div id="optioneel"><?php echo $optioneel; ?></div> </div><!--/blok-->
<?php
}
?>
<!-- info huisdieren -->
        <div class="blok" style="border-bottom: 0;">
        <div id="infohuisdier"><?php echo $infohuisdier; ?></div></div><!--/blok-->
    </div>            
    <!-- /book_details -->
</div>
<!-- /book_left -->
</div> <!-- #main-container -->

<?php require_once 'view/footer.php'; ?>