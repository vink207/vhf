<?php

/**
 * @author Lambert Portier
 * @copyright 2013
 */
 
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/ztwt.php'; 
 
// ajax_date.php
$arr_days = array();
$arr_timestamp = array();
$arr_arrival = array();
$arr_departure = array();

$db = Db::init();

if (isset($_GET['id']))
{
    $id = $_GET['id'];
    $start_date =  $_GET['start_date'];
    
    // if date is provided, use it, otherwise default to first last month
    $start_date = (!empty($start_date)) ? addslashes($start_date) : date('Y-m-d');
    
    // problem onchange year/month calender
    $today = date('Y-m-d');   
    $showdayfirstmonth = 1;         
    if ($start_date == $today)
    {
        $showdayfirstmonth = 0;
    }
    
    // timestamp day
    $tmp_d = 24*60*60;
    // timestamp hour
    $tmp_h = 60*60;
    
    // timestamp startdate
    $start_tmp = strtotime($start_date);
    
    // aanvang en eind zomertijd
    $jaar = date('Y', $start_tmp);
    $zt = ztwt(3, $jaar);
    $wt = ztwt(10, $jaar);
    
    // timestamp 0 or empty default
    if ($start_tmp == 0 OR $start_tmp == '')
        $start_tmp = time();
    
    // bookings 
    $sql = "SELECT aankomst, vertrek, UNIX_TIMESTAMP(aankomst) AS tmp_a, UNIX_TIMESTAMP(vertrek) AS tmp_v  
            FROM tblBoeking
            WHERE huis_id = $id 
            AND UNIX_TIMESTAMP(vertrek) >= $start_tmp 
            AND status = 1          
            AND annulering = 0
            ORDER BY aankomst ASC";
            
    $stmt = $db->query($sql);    
    
    if ($stmt->rowCount() != 0)
    {
            
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {		
            extract($row);	
            
            // aanpassen timestamp wanneer aankomstdag dag ingang van zomertijd of wintertijd is
            if ($aankomst == $zt)
            {
                //$tmp_a -= $tmp_h; uitgezet 02/05/2014
            }
            if ($aankomst == $wt)
            {
                $tmp_a += $tmp_h;
            }
            
            // dagen boeking opslaan in array
            if ($tmp_a < $start_tmp)
            {
                $tmp_a = $start_tmp; 
                
                if ($showdayfirstmonth == 1)
                    $tmp_a -= $tmp_d;
            }
                
            $x = round(($tmp_v - $tmp_a)/$tmp_d);
        
            for ($i = 0; $i <= $x; $i++)
            {   
                // aankomst, begin loop
                if ($i == 0 && $tmp_a != $start_tmp)
                {
                    $arr_arrival[] = date('Y-n-j',($tmp_a));
                }
                
                // php timestamp met 1000 vermenigvuldigt tbv javascript
                if ($i > 0 && $i < $x )
                {
                    $arr_days[] = date('Y-n-j',($tmp_a + ($i * $tmp_d)));
                    $arr_timestamp[] = ($tmp_a + ($i * $tmp_d))*1000; 
                }
                
                // vertrek, eind loop
                if ($i == $x)
                {
                    $arr_departure[] = date('Y-n-j',($tmp_a + ($i * $tmp_d)));
                }
            }
        }
        
        // bij aansluitende boekingen de vertrekdatum en aankomstdatum toevoegen aan array's
        $z = count($arr_days);
        for ($i = 0; $i < $z; $i++)
        {
            if ($i != 0)
            {            
                if ( $arr_timestamp[$i]/1000 - $arr_timestamp[$i-1]/1000 == 2*$tmp_d)
                {
                    $newday = date('Y-n-j', ($arr_timestamp[$i]/1000)-$tmp_d);
                    $newtmp = $arr_timestamp[$i]-($tmp_d*1000);
                    array_push($arr_days, $newday);
                    array_push($arr_timestamp, $newtmp);
                    
                    // aankomst verwijderen uit array
                    $key = array_search($newday, $arr_arrival);
                    unset($arr_arrival[$key]);
                    
                    // vertrek verwijderen uit array
                    $key = array_search($newday, $arr_departure);
                    unset($arr_departure[$key]);
                }
            }
        }
    }    
    
    $arr_response = array('days'=>$arr_days, 'timestamps'=>$arr_timestamp, 'arrivals'=>$arr_arrival, 'departures'=>$arr_departure);


    header('Content-type: application/json');
    echo json_encode($arr_response);
}
?>