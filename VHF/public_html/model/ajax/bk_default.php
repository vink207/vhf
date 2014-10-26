<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 * Genereer prijs boeking, niet-optionele bijkomende kosten en verplichte bkk
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/PrijsTemp.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Datum.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionBoeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionSearch.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

session_start();

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

// bestaande sessions opruimen
if (isset($_SESSION['items']))
    unset($_SESSION['items']);
    
if (isset($_SESSION['boeking']))
    unset($_SESSION['boeking']);

// verplichte kosten
if (isset($_SESSION['nietoptioneel']))
    unset($_SESSION['nietoptioneel']);
 
// optionele kosten   
if (isset($_SESSION['optioneel']))
    unset($_SESSION['optioneel']);
    
$_SESSION['items'] = new SessionBoeken();
$_SESSION['boeking'] = new SessionBoeken();
$_SESSION['nietoptioneel'] = new SessionBoeken(); 
$_SESSION['optioneel'] = new SessionBoeken(); 
$_SESSION['testhuisdier'] = new SessionBoeken(); 

// Instance DB
$db = Db::init();

if (isset($_GET['id']))
{
    $huis_id = $_GET['id'];
    $range =  $_GET['range'];
    
    // timestamps aankomst/vertrek; string -> array
    $array = explode('-', $range);
    
    // javascript timestamp in milliseconden omzetten naar secondes
    $tmp_a = $array[0]/1000;
    $tmp_v = $array[1]/1000;
    
    // aantal dagen
    $aantal_dagen = ($tmp_v - $tmp_a) / (24*60*60);
    $verblijfsduur = $aantal_dagen. ' dagen (';
    $aantal_dagen--;
    $verblijfsduur .= $aantal_dagen.' nachten)';
        
    // string aankomst / vertrek
    $dt = new Datum();
    $dt->setTmp($tmp_a);
    $dt->setFormat('dmj');
    $aankomst = $dt->invoke();
    $dt->setTmp($tmp_v);
    $dt->setFormat('dmj');
    $vertrek = $dt->invoke();
    
    // weekdagen
    $wkday_aankomst = date ('N', $tmp_a);
    $wkday_vertrek = date ('N', $tmp_v);
    
    if ($wkday_aankomst == 6 && $wkday_vertrek == 6)
    {
        // huurprijs boeking
        $pt = new PrijsTemp($huis_id, $tmp_a, $tmp_v);
        $uitvoer = $pt->uitvoer; 
    }
    else
    {
       $uitvoer = 0; 
    }
    
    // poliskosten default 0
    $_SESSION['items']->setItem('polis_schade_woning', 0);
    $_SESSION['items']->setItem('polis_annulering', 0);
    
    // default settings personen
    $_SESSION['items']->setItem('volwassen', 1);
    $_SESSION['items']->setItem('kinderen', 0);    
    $_SESSION['items']->setItem('peuters', 0);
    
    // sommige huizen worden niet verhuurd in laagseizoen prijs = -2
    // in dit geval genereert class PrijsTemp een msg
    if ($uitvoer != 0)
    {
	   $huur = $pt->huur;
                    
        if ($huur != '-2')
        { 
            $huur_ = number_format($huur, 2, ',', '.');
            $str_huur = '&euro; '.$huur_;  
        }
        else
        {
            $msg_huur = 'Prijs op aanvraag. U heeft niet van zaterdag - zaterdag geboekt. Soms is een afwijkende boeking inderdaad mogelijk. <u>Echter niet in juli en augustus &eacute;n de vakantieperiodes.</u>';
            $str_huur = '';
            $huur = 0;
        }
    }
    else
    {        
        $msg_huur = 'Prijs op aanvraag. U heeft niet van zaterdag - zaterdag geboekt. Soms is een afwijkende boeking inderdaad mogelijk. <u>Echter niet in juli en augustus &eacute;n de vakantieperiodes.</u>';
        $str_huur = '';
        $huur = 0;
    }
    
    $_SESSION['items']->setItem('huur', $huur);
    
    
    /**
     * bijkomende kosten en data huis 
     * verplichte bkk voor eindschoonmaak -> prijsverplicht; 
     * inclusieve bkk: eindschoonmaak5, lakenhuur4, lakenhuur5
     * optionele bkk: eindschoonmaak2, eindschoonmaak3, lakenhuur2, lakenhuur3, lakenhuur6, kinderbedje
     * niet optioneel: GWL: 
     */
    $sql = "SELECT ak.*,
                   ft.foto, 
                   hz.code, hz.plaats AS huisplaats, hz.huisdier, hz.aantal2   
            FROM tblAddKosten ak 
            INNER JOIN tblHuizen hz 
            ON ak.huis_id = hz.huis_id 
            INNER JOIN tblFotos ft 
            ON hz.huis_id = ft.huis_id 
            WHERE ak.huis_id = $huis_id 
            AND ft.unvisible = 0
            AND ft.volgorde = 0";
            
    $stmt = $db->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    
    // gwl 
    if ($gwl != '')
    {
        $gwl_inc = 'Water, gas en elektr.: ';
        
        if ($gwl != 'opties' && $gwl != 'inclusief') 
            $gwl_inc .= $gwl;
        elseif ($gwl == 'inclusief')
            $gwl_inc .= $gwl;
        else
            $gwl_inc .= $optiesgwl;
            
        $arr_bkk_niet_opt[] = $gwl_inc;
    }   
    
    // inclusief
    // lakenhuur
    if ($lakenhuur5 != '')
    {
        $arr_bkk_niet_opt[] = $lakenhuur5;
    }
    elseif ($lakenhuur4 != '')
    {
        $arr_bkk_niet_opt[] = $lakenhuur4;
    }
    elseif($lakenhuur1 != '' && $lakenhuur2 == '' && $lakenhuur3 == ''  && $lakenhuur4 == '' && $lakenhuur5 == '' && $lakenhuur6 == '')
    {
        $arr_bkk_niet_opt[] = $lakenhuur1;
    }
    
    // eindschoonmaak
    if ($prijsverplicht != 0)
    {
        $prijsverplicht = number_format($prijsverplicht, 2, ',', '.');
        $prijsverplicht = '&euro; '. $prijsverplicht;
        $_SESSION['items']->setItem('prijsverplichtschoonmaak', $prijsverplicht);
        $arr_bkk_niet_opt[] = 'Eindschoonmaak verplicht '.$prijsverplicht;
    }
    elseif ($eindschoonmaak5 != '')
    {
        $arr_bkk_niet_opt[] = 'Eindschoonmaak inclusief';
    }
    elseif($eindschoonmaak1 != '' && $eindschoonmaak2 == '' && $eindschoonmaak3 == '' && $eindschoonmaak4 == '' && $eindschoonmaak6 == '')
    {
        $arr_bkk_niet_opt[] = $eindschoonmaak1;
    }
    
    // borg
    if ($bedragborg != 0)
    {
        $bedragborg = number_format($bedragborg, 2, ',', '.');
        $_SESSION['items']->setItem('bedragborg', $bedragborg);
        $arr_bkk_niet_opt[] = 'Borg &euro; '.$bedragborg.'. U voldoet de borgsom contant aan de eigenaar. Deze ontvangt u voor vertrek weer terug.';
    }
    else
    {
        if ($borg == 'opties')
        {
            $arr_bkk_niet_opt[] = 'Borg '.$optiesborg;
        }
    }
    
    // formfields
    $ch_lakenhuur = 0;
    $ch_eindschoonmaak = 0;
    $ch_kinderbenodigheden = 0;
    $ch_huisdier = 0;
    
    // values
    $val_lakenhuur = 'nee';
    $val_eindschoonmaak = 'nee';
    $val_kinderbenodigheden = 'nee';
    $val_huisdier = 'nee';
    
    // Lakenhuur
    if ($lakenhuur2 != '' || $lakenhuur3 != '' || $lakenhuur6 != '')
    {        
        if ($lakenhuur2 != '' && $prijsset != '') 
		{
          $ch_lakenhuur = 1;
          $prijsset = number_format($prijsset, 2, ',', '.');
		  $lakenhuur_opt = 'Lakens te huur: &euro; ' . $prijsset . ' per set. (keuken)handdoeken neemt u zelf mee.';
		}        
        elseif ($lakenhuur3 != '') 
		{
          $ch_lakenhuur = 1;
		  $lakenhuur_opt = 'Lakens te huur: ';
		  $prijs1pers = number_format($prijs1pers, 2, ',', '.');
		  $prijs2pers = number_format($prijs2pers, 2, ',', '.');
		  if ($prijs1pers != '') $lakenhuur_opt .= '&euro; ' . $prijs1pers . ' (1 pers. bed)';
		  if ($prijs2pers != '') $lakenhuur_opt .= ' / &euro; ' . $prijs2pers . ' (2 pers. bed).';
		  $lakenhuur_opt .= ' (keuken)handdoeken neemt &nbsp;&nbsp;u zelf mee.';
		}
        elseif ($lakenhuur6 != '') // extra opties
		{
          $ch_lakenhuur = 1;				
		  $optieslaken = str_replace('€', '&euro', $optieslaken);
		  $lakenhuur_opt = 'Lakens te huur: ' . $optieslaken;
		}
        $arr_bkk_opt['lakenhuur'] = $lakenhuur_opt;	        
    }      
    $_SESSION['items']->setItem('ch_lakenhuur', $ch_lakenhuur);
    $_SESSION['items']->setItem('val_lakenhuur', $val_lakenhuur);
    
    // eindschoonmaak
    if ($eindschoonmaak2 != '' || $eindschoonmaak3 != '')
    {        
        if($eindschoonmaak2 != '')
		{
          $ch_eindschoonmaak = 1;  				
		  $prijsoptie1 = number_format($prijsoptie1, 2, ',', '.');
          $_SESSION['items']->setItem('prijsoptie', $prijsoptie1);
		  $eindschoonmaak_opt = 'Eindschoonmaak: &euro; ' . $prijsoptie1 . '.';
		}
        elseif($eindschoonmaak3 != '')
		{
          $ch_eindschoonmaak = 1; 			
		  $prijsoptie2 = number_format($prijsoptie2, 2, ',', '.');
          $_SESSION['items']->setItem('prijsoptie', $prijsoptie2);
		  $eindschoonmaak_opt = 'Eindschoonmaak: &euro; ' . $prijsoptie2 . ' per uur ';
		  if ($aantal_uren != '') $eindschoonmaak_opt .= '(ongeveer '  . $aantal_uren . ' uur).';
		}
        $arr_bkk_opt['eindschoonmaak'] = $eindschoonmaak_opt;
    }         
    $_SESSION['items']->setItem('ch_eindschoonmaak', $ch_eindschoonmaak);
    $_SESSION['items']->setItem('val_eindschoonmaak', $val_eindschoonmaak);
    
    // kinderbenodigheden
    $kbgratis = 'gratis';
    if ($kinderbedje != 'geen')
    {
        $ch_kinderbenodigheden = 1;
        
        $kinderbenodigheden_opt = 'Babybenodigheden: ';
				
        if ($kinderbedje != '')
		{					
            $bedje = true;
					
            if ($kinderbedje == 'vast' || $kinderbedje == 'camping')
            {						
                $kinderbenodigheden_opt .= $aantal_kinderbedje . ' x bedje';   
					
                if ($prijskinderbedje != 0) 
                {
                    $prijskinderbedje = number_format($prijskinderbedje, 2, ',', '.');
                    $kinderbenodigheden_opt .= ' &euro; ' . $prijskinderbedje;
                    if ($prijs_per_week == 1) {$kinderbenodigheden_opt .= ' p/w';}
                    $kbgratis = '';
                }
            }
        }
		
        if ($kinderstoel != 'geen')
        {			
            if ($bedje) $kinderbenodigheden_opt .= ', ';
                $stoel = true;
					
            if ($kinderstoel == 'inclusief')
            {
                $kinderbenodigheden_opt .= $aantal_kinderstoel . ' x stoeltje';
            }
            elseif ($kinderstoel == 'anders') 
            {
                $prijskinderstoel = number_format($prijskinderstoel, 2, ',', '.');
                $kinderbenodigheden_opt .= $aantal_kinderstoel . ' x stoeltje &euro; ' . $prijskinderstoel;
                if ($prijs_per_week == 1) {$kinderbenodigheden_opt .= ' p/w';}
                $kbgratis = '';
            }
        }
		
        if ($kinderbad != 'geen')
        {		
            $bad = true;
					
            if ($bedje || $stoel) $kinderbenodigheden_opt .= ', ';					
					
			if ($kinderbad == 'inclusief')
			{
                $kinderbenodigheden_opt .= $aantal_kinderbad . ' x badje';
			}		
			elseif ($kinderbad == 'geen') 
			{
			    $kinderbenodigheden_opt .= 'badje niet aanwezig';
			}
			elseif ($kinderbad == 'anders') 
			{
			     $prijskinderbad = number_format($prijskinderbad, 2, ',', '.');
			     $kinderbenodigheden_opt .= $aantal_kinderbad . ' x badje &euro; ' . $prijskinderbad;
			     if ($prijs_per_week == 1) {$kinderbenodigheden_opt .= ' p/w';}
                 $kbgratis = '';
			}		
        }				
				
		$kinderbenodigheden_opt .= '.';
        if ($kinderbedje == 'inclusief_fixe' || $kinderbedje == 'inclusief_pliant' || $kinderstoel == 'inclusief' || $kinderbad == 'inclusief')
        {
            $kinderbenodigheden_opt .= ' Gratis.';
        } 
        $arr_bkk_opt['kinderbenodigheden'] = $kinderbenodigheden_opt;
    }        
    $_SESSION['items']->setItem('ch_kinderbenodigheden', $ch_kinderbenodigheden);
    $_SESSION['items']->setItem('val_kinderbenodigheden', $val_kinderbenodigheden);
    $_SESSION['items']->setItem('kbgratis', $kbgratis);
    
    // huisdier toegestaan 
    if ($huisdier == 'ja')
    {
        $ch_huisdier = 1;
        
        $huisdieren_opt = 'Huisdier: ';
		
        if ($prhuisdier == 'inclusief')
		{
		  $huisdieren_opt .= ' toegestaan, geen extra kosten.';									
		}		
		elseif ($prhuisdier == 'borg')
		{
		  $borg_huisdier = number_format($borg_huisdier, 2, ',', '.');
		  $huisdieren_opt .= ' borgsom &euro; ' .$borg_huisdier. ' per huisdier';
		}
		elseif ($prhuisdier == 'bijbetaling')
		{
		  $bijbetaling_huisdier = number_format($bijbetaling_huisdier, 2, ',', '.');
		  $huisdieren_opt .= ' toegestaan. Er wordt &euro; ' . $bijbetaling_huisdier . ' gevraagd ' .$termijn_bijbetaling.'.';
		}
		elseif ($prhuisdier == 'extra_schoonmaak')
		{
		  $extra_schoonmaak_huisdier = number_format($extra_schoonmaak_huisdier, 2, ',', '.');
		  $huisdieren_opt .= ' toegestaan. Totaal verplichte schoonmaakkosten &euro; ' . $extra_schoonmaak_huisdier . '.';
		}
					
		if ($txthuisdier != '')
		{
		  $txthuisdier = str_replace('€', '&euro', $txthuisdier);
		  $huisdieren_opt .= ' ' . $txthuisdier;
		}
        $_SESSION['items']->setItem('huisdier_opt', $huisdieren_opt);
    } 
    else
    {
        $_SESSION['items']->setItem('huisdier_niet_opt', 'Huisdieren niet toegestaan');
    } 
    
    $_SESSION['items']->setItem('ch_huisdier', $ch_huisdier);         
    $_SESSION['items']->setItem('val_huisdier', $val_huisdier); 
    
    // html prijzen
    $html = <<<EOD
    <div id="tekst">
        <h3>Prijsopgave</h3>
            <a href="$absroot/boeken/$huis_id/reisgezelschap" class="button">NU BOEKEN</a>

        <table cellspacing="5">
            <tr>
                <td><span class="prijs-title">Aankomstdatum:</span> <span class="prijs-flex">$aankomst</span></td>
                <td><span class="prijs-title">Verblijfsduur:</span> <span class="prijs-flex">$verblijfsduur</span></td>
            </tr>
            <tr>
                <td><span class="prijs-title">Vertrekdatum:</span> <span class="prijs-flex">$vertrek</span></td>
                <td><span class="prijs-title">Huurprijs:</span> <span class="prijs-flex">$str_huur</span></td>
            </tr>
EOD;
    if (isset($msg_huur))
    {
        $html .= <<<EOD
        <tr>
            <td colspan="4">$msg_huur</td>
        </tr>
EOD;
    }
    $html .= <<<EOD
        </table>
</div>
EOD;
}

// data huis en boeking opslaan in object SessionBoeken
$_SESSION['boeking'] = new SessionBoeken();
$_SESSION['boeking']->AddBoeking($huis_id, $code, $tmp_a, $tmp_v, $aankomst, $vertrek, $verblijfsduur, $foto, $huisplaats, $huisdier, $aantal2);


// reisgezelscahp default 2 volwassenen
$_SESSION['items']->setItem('volwassen', '2');

// polis radiobuttons default annulering ja en schade ja; bedragen polis op 0
// radiobuttons tonen
$_SESSION['items']->setItem('ch_annulering', 0);
$_SESSION['items']->setItem('ch_schade_woning', 1);
// value 
$_SESSION['items']->setItem('val_annulering', 'nee');
$_SESSION['items']->setItem('val_schade_woning', 'ja');

// info niet optionele kosten
if (isset($arr_bkk_niet_opt))
{
    $_SESSION['nietoptioneel']->AddBkkNietOptioneel($arr_bkk_niet_opt);
}
// info optionele kosten
if (isset($arr_bkk_opt))
{
    $_SESSION['optioneel']->AddBkkOptioneel($arr_bkk_opt);
}

echo $html;
?>