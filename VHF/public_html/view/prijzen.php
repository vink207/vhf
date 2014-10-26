<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 */


include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Prijzen.php';

error_reporting(E_ALL);

$pr = new Prijzen();

$huis_id = 4;
$pr->setHuisID($huis_id);
$pr->invoke();
$arrjaar = $pr->getJaar();
$arrprijs = $pr->getPrijs();
$arrvan = $pr->getVan();
$arrtot = $pr->getTot();

$cj = count($arrjaar);

$html = <<<HTML
<div>
HTML;
// loop jaren
for ($x = 0; $x < $cj; $x++)
{
    $html .= <<<HTML
    <div>
        <h2>Prijzen $arrjaar[$x]</h2>
HTML;
    
    // loop jaren prijzen en periodes
    $cp = count($arrprijs[$arrjaar[$x]]);
    
    for ($i = 0; $i < $cp; $i++)
    {
        $html .= <<<HTML
        <p>{$arrvan[$arrjaar[$x]][$i]} - {$arrtot[$arrjaar[$x]][$i]} <strong>{$arrprijs[$arrjaar[$x]][$i]}</strong></p>
HTML;
    }
    $html .= '</div>
            </div>';
}
echo $html;
?>