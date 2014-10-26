<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 */

// ingang zomertijd laatste zaterdag van maart
// einde zomertijd laatste zaterdag van oktober

function ztwt($maand, $jaar)
{
    $dagenInMaand = date("t", mktime(0, 0, 0, $maand, 1, $jaar));
    $tmp = mktime(0, 0, 0, $maand, $dagenInMaand, $jaar);
    $weekdag = date('w', $tmp);
    $tmp_d = 24*60*60;
    // zes is zaterdag
    switch($weekdag)
    {
        case 0:
            $dag = $dagenInMaand-1;
            break;
        case 1:
            $dag = $dagenInMaand-2;
            break;
        case 2:
            $dag = $dagenInMaand-3;
            break;
        case 3:
            $dag = $dagenInMaand-4;
            break;
        case 4:
            $dag = $dagenInMaand-5;
            break;
        case 5:
            $dag = $dagenInMaand-6;
            break;
        case 6:
            $dag = $dagenInMaand;
            break;
    }    
            
    $d = date('Y-m-d', mktime(0, 0, 0, $maand, $dag, $jaar)); 
    return $d; 
}
?>