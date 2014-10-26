<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 * Genereer prijs boeking, niet-optionele bijkomende kosten en verplichte bkk
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionBoeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/PolisTemp.php';

session_start();

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

// POST
if (isset($_POST))
{
    foreach ($_POST as $key => $value) 
    {      
        if ($key == 'volwassen' || $key == 'kinderen' || $key == 'peuters') 
        {
            // update session item
            $_SESSION['items']->setItem($key, $value);
            
            // div huis_periode_aantal genereren
            $volwassen = $_SESSION['items']->getItem('volwassen');
            $kinderen = $_SESSION['items']->getItem('kinderen');
            $peuters = $_SESSION['items']->getItem('peuters');
            $aankomst = $_SESSION['boeking']->get_aankomst();
            $vertrek = $_SESSION['boeking']->get_vertrek();
            $verblijfsduur = $_SESSION['boeking']->get_verblijfsduur();
            
            
            $aantal_kinderen = $kinderen + $peuters;
            if ($aantal_kinderen == 0){$aantal_kinderen = '';}                
            
            $response = <<<EOD
            <table class="first book_data">
                <colgroup>
                    <col widht="120">
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
        }
        elseif ($key == 'schade_woning' || $key == 'annulering') 
        {
            $_SESSION['items']->setItem('val_'.$key, $value);
            
            // polis berekenen
            $val_annulering = $_SESSION['items']->getItem('val_annulering');
            $val_schade_woning = $_SESSION['items']->getItem('val_schade_woning');
            
            $huur = $_SESSION['items']->getItem('huur');
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
            
            // opslaan in sessie
            $_SESSION['items']->setItem('premie_schade', $premie_schade);
            $_SESSION['items']->setItem('premie_annulering', $premie_annulering);
            $_SESSION['items']->setItem('assurantie', $assurantie);
            $_SESSION['items']->setItem('poliskosten', $poliskosten);
                         
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
        
            // geen huursom bekend, prijs op aanvraag
            if ($huur == 0)
                $huur_format = 'op aanvraag';
            
            // opbouw response
            if ($huur != 0)
            {
                $response = <<< EOD
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
                    $response .= <<<EOD
                    <tr>
                        <td>All risk Annuleringsverzekering:</td>
                        <td>&euro;</td>
                        <td align="right">$premie_annulering_format</td>
                    </tr>
EOD;
                }
            
                if ($premie_schade != 0)
                {
                    $response .= <<<EOD
                    <tr>
                        <td>Verzekering schade woning:</td>
                        <td>&euro;</td>
                        <td align="right">$premie_schade_format</td>
                    </tr>
EOD;
                }
            
                if ($premie_schade != 0 || $premie_annulering != 0)
                {
                    $response .= <<<EOD
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
        
                $response .= <<<EOD
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
                $response = <<< EOD
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
        }       
        elseif($key == 'lakenhuur' || $key == 'eindschoonmaak' || $key == 'kinderbenodigheden')
        {
            // update item value radiobutton
            $_SESSION['items']->setItem('val_'.$key, $value);
            
            // opbouw response
            $response = <<<EOD
            <table>
            <colgroup>
                <col width="12px">
                <col>
            </colgroup>
            <tbody>
EOD;
            // arr_bkk_opt  staan voor alle optionele bkk/faciliteiten de bijhorende informatie; keys lakenhuur etc.
            $arr_bkk_opt = $_SESSION['optioneel']->get_arr_bkk_opt();
            
            foreach ($arr_bkk_opt as $key => $value) 
            { 
                // als checkbox waarde ja heeft tonen
                $name = 'val_'.$key;
                $val = $_SESSION['items']->getItem($name);
                
                if ($val == 'ja')
                {                     
                    $response .= <<<EOD
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
                        $response .= <<<EOD
                        <tr>
                            <td class="bull">&bull;</td>
                            <td>De eindschoonmaak verzorgt u zelf.</td>
                        </tr>
EOD;
                    }
                    // uitzondering lakenhuur; ook bij keuze nee een tekst tonen: Lakens en (keuken)handdoeken neemt u zelf mee.
                    if ($name == 'val_lakenhuur' && $val == 'nee')
                    {                    
                        $response .= <<<EOD
                        <tr>
                            <td class="bull">&bull;</td>
                            <td>Lakens en (keuken)handdoeken neemt u zelf mee.</td>
                        </tr>
EOD;
                    }
                }
            } 
            $response .= <<<EOD
                </tbody>
            </table>
EOD;
        }
        elseif ($key == 'huisdier')
        {
            // update item value radiobutton
            $_SESSION['items']->setItem('val_'.$key, $value);
            
            // info huisdier
            if ($value != 'nee')
            {
                $teksthuisdier = $_SESSION['items']->getItem('huisdier_opt');
                
                // opbouw response                
                $response = <<<EOD
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
            else
            {
                $response = '';
            }
        }
    }
    echo $response;
}



?>