<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionBoeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/PolisTemp.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/functies/functies_boeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionSearch.php';

session_start();

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

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

    // referer
    $referer = $_SERVER['HTTP_REFERER'];
    // vanaf verschillende pagina's tot deze pagina
    $pos = strpos($referer, '/verzekering');
    
    // vanaf verschillende pagina's tot deze pagina
    if ($pos !== false)
    {
        $_SESSION['refer2'] =  $referer;
    }
}

// url voor back-button
if (isset($_SESSION['refer2']))
    $referer = $_SESSION['refer2'];

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
    $verblijfsduur = $_SESSION['boeking']->get_verblijfsduur();  
}

// sessie 
if (isset($_SESSION['items']))
{
    // reisgezelschap
    $volwassen = $_SESSION['items']->getItem('volwassen');
    $kinderen = $_SESSION['items']->getItem('kinderen');
    $peuters = $_SESSION['items']->getItem('peuters');
    $aantal_kinderen = $kinderen + $peuters;
    if ($aantal_kinderen == 0){$aantal_kinderen = '';}
        
    // vaste bedragen
    $huur = $_SESSION['items']->getItem('huur');
    // welke polis
    $val_annulering = $_SESSION['items']->getItem('val_annulering');  
    $val_schade_woning = $_SESSION['items']->getItem('val_schade_woning');  
    
    // radio buttons polis
    if ($val_annulering == 'ja')
    {
        $ch_annulering_ja = 'checked';
        $ch_annulering_nee = '';
    }
    elseif ($val_annulering == 'nee')
    {
        $ch_annulering_ja = '';
        $ch_annulering_nee = 'checked';    
    }
    else
    {    
        $ch_annulering_ja = '';
        $ch_annulering_nee = ''; 
    }
       
    // poliskosten berekenen
    if ($huur != 0)
    {
        $pl = new PolisTemp($huur, $val_annulering, $val_schade_woning);
        $premie_schade = $pl->premie_schade;
        $premie_annulering = $pl->premie_annulering;       
        $assurantie = $pl->assurantie;                               
        $poliskosten = $pl->poliskosten;
    }
    else
    {
        $premie_schade = 0;
        $premie_annulering = 0;
        $assurantie = 0;
        $poliskosten = 0;
    }    
    // kosten polis
    $_SESSION['items']->setItem('premie_schade', $premie_schade);
    $_SESSION['items']->setItem('premie_annulering', $premie_annulering);
    $_SESSION['items']->setItem('assurantie', $assurantie);
    $_SESSION['items']->setItem('poliskosten', $poliskosten);
    
    // poliskosten  
    $val_annulering = $_SESSION['items']->getItem('val_annulering');  
    $val_schade_woning = $_SESSION['items']->getItem('val_schade_woning'); 
    $premie_schade = $_SESSION['items']->getItem('premie_schade');
    $premie_annulering = $_SESSION['items']->getItem('premie_annulering');
    $assurantie = $_SESSION['items']->getItem('assurantie');
    $poliskosten = $_SESSION['items']->getItem('poliskosten');
    // totaal bedrag 
    $totaal = $huur;
    if ($val_annulering == 'ja' || $val_schade_woning == 'ja')
        $totaal += $premie_schade + $premie_annulering + $assurantie + $poliskosten;
    // bedragen formateren
    $huur_format = number_format($huur, 2, ',', '.');
    $premie_schade_format = number_format($premie_schade, 2, ',', '.');
    $premie_annulering_format = number_format($premie_annulering, 2, ',', '.');
    $assurantie_format = number_format($assurantie, 2, ',', '.');
    $poliskosten_format = number_format($poliskosten, 2, ',', '.');
    $totaal_format = number_format($totaal, 2, ',', '.');
    
    // huisdier
    $val_huisdier = $_SESSION['items']->getItem('val_huisdier');
    $ch_huisdier = $_SESSION['items']->getItem('ch_huisdier');
        
    // geen huursom bekend, prijs op aanvraag
    if ($huur == 0){$huur_format = 'op aanvraag';}        
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
    
        if ($premie_annulering != 0)
        {
            $kosten .= <<<EOD
            <tr>
                <td>All risk Annuleringsverzekering:</td>
                <td>&euro;</td>
                <td align="right">$premie_annulering_format</td>
            </tr>
EOD;
        }
            
        if ($premie_schade != 0)
        {
            $kosten .= <<<EOD
            <tr>
                <td>Verzekering schade woning:</td>
                <td>&euro;</td>
                <td align="right">$premie_schade_format</td>
            </tr>
EOD;
        }
            
        if ($premie_schade != 0 || $premie_annulering != 0)
        {
            $kosten .= <<<EOD
            <tr>
                <td>Assurantiebelasting:</td>
                <td>&euro;</td>
                <td align="right">$assurantie_format</td>
            </tr>
            <tr>
                <td>Poliskosten:</td>
                <td>&euro;</td>
                <td align="right">$poliskosten_format</td>
            </tr>
EOD;
        } 
        
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


//  optionele bkk gerelateerd aan de geselecteerde radiobuttons / checkbox huisdier
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
    foreach ($arr_bkk_opt as $key => $value) 
    { 
        // als checkbox waarde 1 heeft tonen
        $name = 'val_'.$key;
        $val = $_SESSION['items']->getItem($name); 
        if ($val == 'ja')
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
            if ($name == 'val_eindschoonmaak' && $val == 'nee')
            {  
                $optioneel .= <<<EOD
                <tr>
                    <td class="bull">&bull;</td>
                    <td>De eindschoonmaak verzorgt u zelf.</td>
                 </tr>
EOD;
            }
            // uitzondering lakenhuur; ook bij keuze nee een tekst tonen: Lakens en (keuken)handdoeken neemt u zelf mee.
            if ($name == 'val_lakenhuur' && $val == 'nee')
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

// niet optionele bkk
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
$seo->setTitle('boeken/verzekeringen');

// header
require_once 'view/header.php';

$action = $absroot.'/boeken/'.$huis_id.'/uwgegevens';
$menuaction = $absroot.'/boeken/'.$huis_id.'/reisgezelschap';
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
        <h2>2 VERZEKERINGEN</h2>
        <form id="form1" method="post" action="<?php echo $action; ?>" enctype="application/x-www-form-urlencoded">
            <fieldset id="fieldset-formVerzekering">
                <p>Wilt u een verzekering afsluiten?</p>
                <p>U kunt kiezen voor een <strong>All risk annuleringsverzekering</strong></p>
                <dt><label class="required">All risk Annuleringsverzekering</label></dt>
                  <dd>
                    <label><input type="radio" class="verzekering" name="annulering" id="annulering-ja" value="ja" <?php echo $ch_annulering_ja; ?>>ja</label>                    
                    <label><input type="radio" class="verzekering" name="annulering" id="annulering-nee" value="nee" <?php echo $ch_annulering_nee; ?>>nee</label>                     
                 </dd>
            <hr>
                  <dt><label><strong>Verzekering Schade vakantiewoning (verplicht)</strong></label> </dt>
                  <dd>1,5% van de huursom + &euro; 3,50 poliskosten + 21% assurantiebelasting over de premie &eacute;n de poliskosten. U bent verzekerd voor &euro; 2500,-.</dd>
                  <dd>* bij afsluiten van beide verzekeringen betaalt u 1 keer poliskosten.</dd>
             </fieldset>
             <hr> 
             <p>Verzekeringen worden aangeboden door de Europeesche Verzekeringen. Lees hier de voorwaarden.</p>   
             <p>* bij afsluiten van beide verzekeringen betaalt u 1 keer poliskosten.
             <button onclick="document.location.href='<?php echo $referer; ?>';" type="button" id="previous" name="previous">STAP 1</button>
             <input type="submit" value="STAP 3" id="next" name="next">
             <!-- blok heeft u een vraag -->
             <div>
                <h2>HEEFT U EEN VRAAG?</h2>
                telefoon +31 (024) 373 99 86 <br />
                email info@vakantiehuisfrnakrijk.nl <br />
                <br />
                We zijn bereikbaar op maandag t/m vrijdag van 9.00 tot 17.00 uur.<br>
                Mails worden ook buiten kantooruren beantwoord.
             </div>
        </form>
    </div>
    <!-- book_content -->
</div>
<?php

/** LEFT */
$left = <<<LEFT
    <div id="book-left">
        <div id="book_details">
            <h2>UW BOEKING</h2>
            <img src="http://www.vakantiehuisfrankrijk.nl/huis_img/$foto" width="258" height="200" alt="foto $code" border="0" />
            <h3>$code <span>$huisplaats</span></h3>            
            <div id="huis_periode_aantal">$huis_periode_aantal</div>
            <hr/>
            <h4>Kostenoverzicht</h4>
            <div id="kosten">$kosten</div>
LEFT;
// niet optionele zaken; inclusief of zelf meebrengen of doen
if (isset($niet_optioneel))
{
    $left .= <<<LEFT
     <hr />
     <h4>Ter plaatse te voldoen</h4>
     <div id="niet_optioneel">$niet_optioneel</div>
LEFT;
}

// opties te kiezen door klant
if (isset($optioneel))
{
    $left .= <<<LEFT
        <div id="optioneel">$optioneel</div> 
LEFT;
}

// info huisdieren
$left .= <<<LEFT
        <hr />
        <div id="infohuisdier">$infohuisdier</div>
    </div>            
    <!-- /book_details -->
</div>
<!-- /book_left -->
LEFT;
echo $left;
    
/**
 * kolom links opslaan in sessie
 */
$_SESSION['kolom-links'] = $left;
?>
</div> <!-- #main-container -->

<?php require_once 'view/footer.php'; ?>