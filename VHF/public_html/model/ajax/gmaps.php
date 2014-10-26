<?php
if (isset($_POST['stringhuisid']))
{
    $stringhuisid = $_POST['stringhuisid'];
    $arrhuisid = explode(',', $stringhuisid);
    
    // data GoogleMaps
    require_once $_SERVER['DOCUMENT_ROOT'].'/model/GMaps.php';
    $gm = new GMaps();
    $gm->setArrayHuisID($arrhuisid);
    $gm->invoke();
    $latlng = $gm->getLatLng();
    $code = $gm->getCode();
    $arrcontent = $gm->getContent();
    $zindex = 1;
    $zoom = 9;
    $div = 'search-kaart';
    $max_lat = 0;
    $max_long = 0;
    foreach($code AS $key => $huiscode)
    {
        $coord = $latlng[$key];
        $arrcoord = explode(',',$coord);  
        $lat = floatval($arrcoord[0]);   
        $long = floatval($arrcoord[1]);    
        $info = $arrcontent[$key]; 
        $houses[] = array($huiscode, $lat, $long, $info, $zindex);
        $max_lat += $lat;
        $max_long += $long;
        $zindex++;
    }
    //$jsonhouses = json_encode($houses);
    // center kaart
    $centery = $max_long/($zindex - 1);
    $centerx = $max_lat/($zindex - 1);
    
    $response = array('divmap'=>$div,'zoom'=>$zoom, 'centerx'=>$centerx, 'centery'=>$centery, 'jsonhouses'=>$houses);
    echo json_encode($response);
}
?>