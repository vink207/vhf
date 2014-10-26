<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 */

/** 
 * selectbox aantal personen
 */
function selectAantal($name, $aantal, $nul)
{    
	$select = <<<SELECT
    <div id="$name">
    <select style="width: 50px; padding: 4px" name="$name">
SELECT;

    if ($nul == 1)
    {
        $select .= '<option value="0"></option>';
    }
	
	for ($i = 1; $i < 21; $i++)
	{
		$select .= <<<SELECT
        <option value="$i"
SELECT;
		
		if ($i == $aantal) $select .= ' selected="selected"'; 
		
		$select .= <<<SELECT
        >$i</option>
SELECT;
	}			
	$select .= '</select></div>';
    return $select;
}

/**
 * formulier reserveren
 */
function selectMaatHuisdier($val)
{
    $select = <<<SELECT
    <div id="huisdier">
           <select class="optioneel" name="huisdier" size="1" style="width: 80px;">
               <option value="nee">geen</option>
	           <option value="klein"
SELECT;
    if ($val == 'klein')
    {
      $select .= <<<SELECT
        selected="selected"
SELECT;
    }    
    $select .= <<<SELECT
    >klein</option>
    <option value="middel"
SELECT;
    if ($val == 'middel')
    {
        $select .= <<<SELECT
        selected="selected"
SELECT;
    }
    $select .= <<<SELECT
    >middel</option>
	<option value="groot"
SELECT;
    if ($val == 'groot')
    {
        $select .= <<<SELECT
        selected="selected"
SELECT;
    }
    $select .= <<<SELECT
    >groot</option>
    </select></div>
SELECT;
	return $select;			
}

function selectGeslacht($val)
{
    $select = <<<SELECT
           <select id="aanhef" name="aanhef" size="1" style="width: 80px;">
	           <option value="Mevr."
SELECT;
    if ($val == 'Mevr.')
    {
      $select .= <<<SELECT
        selected="selected"
SELECT;
    }    
    $select .= <<<SELECT
    >vrouw</option>
    <option value="Dhr."
SELECT;
    if ($val == 'Dhr.')
    {
        $select .= <<<SELECT
        selected="selected"
SELECT;
    }
    $select .= <<<SELECT
    >man</option>
    </select>
SELECT;
	return $select;			
}

/**
 * formulier reserveren
 */
function selectGbdatum($dag, $maand, $jaar)
{         
    /**  array maanden */         
    $maandNaam = array(1=>"januari","februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"); 
        
    $select = <<<SELECT
    <select name="hh_gb_dag" size="1" style="width: 50px;">
    <option value=""></option>
SELECT;
		
    for ($i = 1; $i < 32; $i++) 
    { 
        $select .= <<<SELECT
		<option value="$i"
SELECT;
      	if ($dag != 0) 
        {	
            if ($dag == $i)
            {
                $select .= <<<SELECT
                selected="selected"
SELECT;
            } 
		}
        $select .= <<<SELECT
		>$i</option>
SELECT;
    } 
	$select .= <<<SELECT
    </select>
    <select name="hh_gb_maand" size="1" style="width: 100px;">
        <option value=""></option>
SELECT;
		
    for($i = 1; $i < 13; $i++) 
    { 
        $select .= <<<SELECT
		<option value="$i"
SELECT;
        if($maand != 0) 
        { 
            if($maand == $i)
            {
                $select .= <<<SELECT
                selected="selected"
SELECT;
            }           		
		}
        $select .= <<<SELECT
		>$maandNaam[$i]</option>
SELECT;
    }
    $eindJaar = date("Y")+1;
	$startJaar = $eindJaar - 95;
	$select .= <<<SELECT
    </select>
    <select name="hh_gb_jaar" size="1" style="width: 70px;">
    <option value=""></option>
SELECT;

    for ($i = $startJaar; $i < $eindJaar; $i++)
    {
        $select .= <<<SELECT
        <option value="$i"
SELECT;
        if ($jaar != 0) 
        {
            if ($jaar == $i)
            {
                $select .= <<<SELECT
                selected="selected"
SELECT;
            }           		
		}
        $select .= <<<SELECT
        >$i</option>
SELECT;
    }    
	$select .= <<<SELECT
    </select>
SELECT;
return $select;
}

/**
 * formulier reserveren
 * geboortedatum medehuurder
 */
function selectGbdatumMede($x, $dag, $maand, $jaar)
{         
    /**  array maanden */         
    $maandNaam = array(1=>"jan","feb", "mrt", "apr", "mei", "jun", "jul", "aug", "sep", "okt", "nov", "dec"); 
    $select = <<<SELECT
    <select name="arr_dag[$x]" size="1" style="width: 50px;">
    <option value=""></option>
SELECT;
		
    for ($i = 1; $i < 32; $i++) 
    { 
        $select .= <<<SELECT
		<option value="$i"
SELECT;
      	if ($dag != 0) 
        {	
            if ($dag == $i)
            {
                $select .= <<<SELECT
                selected="selected"
SELECT;
            } 
		}
        $select .= <<<SELECT
		>$i</option>
SELECT;
    } 
	$select .= <<<SELECT
    </select>
    <select name="arr_maand[$x]" size="1" style="width: 60px;">
        <option value=""></option>
SELECT;
		
    for($i = 1; $i < 13; $i++) 
    { 
        $select .= <<<SELECT
		<option value="$i"
SELECT;
        if($maand != 0) 
        { 
            if($maand == $i)
            {
                $select .= <<<SELECT
                selected="selected"
SELECT;
            }           		
		}
        $select .= <<<SELECT
		>$maandNaam[$i]</option>
SELECT;
    }
    $eindJaar = date("Y")+1;
	$startJaar = $eindJaar - 95;
	$select .= <<<SELECT
    </select>
    <select name="arr_jaar[$x]" size="1" style="width: 60px;">
    <option value=""></option>
SELECT;

    for ($i = $startJaar; $i < $eindJaar; $i++)
    {
        $select .= <<<SELECT
        <option value="$i"
SELECT;
        if ($jaar != 0) 
        {
            if ($jaar == $i)
            {
                $select .= <<<SELECT
                selected="selected"
SELECT;
            }           		
		}
        $select .= <<<SELECT
        >$i</option>
SELECT;
    }    
	$select .= <<<SELECT
    </select>
SELECT;
return $select;
}

function selectLand($land)
{
    $landen = array(0=>"Nederland","Belgi&euml;", "Frankrijk", "Duitsland", "Engeland");
    $z = count($landen);
    
    $select = <<<SELECT
    <select name="land" size="1" style="width: 100px;">
SELECT;
            
    for ($x = 0; $x < $z; $x++)
    {
        $select .= <<<SELECT
        <option value="{$landen[$x]}"
SELECT;
        if ($land == $landen[$x])
        {
            $select .= <<<SELECT
                selected="selected"
SELECT;
        }
        $select .= <<<SELECT
        >{$landen[$x]}</option>
SELECT;
    }
    $select .= <<<SELECT
            </select>
SELECT;
return $select;
} 

/**
 * 1 digit vervangen door twee
 */
function oneToTwo($string)
{
	if (strlen($string) == 1) $string = '0' . $string;
	return $string;
}
/**
 * formateren dubbele namen
 */
function format($str)
{
	//  eerste naam
	// spaties voor en achter weg 
	$str = trim($str);
	// punten verwijderen
	$str = str_replace('.', '', $str);
    $str = ucfirst($str);
	
	// dubbele naam met - en/of spaties
	// streepje
	$pos = strpos($str, '-');
	if ($pos !== false)
	{
		$array = explode('-', $str);
		$z = count($array);
		
		for ($x = 0; $x < $z; $x++)
		{ 
			$item = $array[$x];
			$item = trim($item);
			$first = strtoupper( substr($item, 0, 1) );
			$item = $first . substr($item, 1);
			$arr_item[] = $item;
		}
		
		$string = implode($arr_item, '-');
	}
	
	$pos = strpos($string, ' ');
	if ($pos !== false)
	{
		$array = explode(' ', $string);
		// ieder item in array voorzien van een hoofdletter, bij meer dan twee items alleen de eerste en laatste
		$z = count($array);
		for ($x = 0; $x < $z; $x++)
		{ 
			$item = $array[$x];
			
			if ($z < 3)
			{
				$first = strtoupper( substr($item, 0, 1) );
				$item = $first . substr($item, 1);
			}
			elseif(strlen($item) > 3)
			{
				$first = strtoupper( substr($item, 0, 1) );
				$item = $first . substr($item, 1);
			}
			$arr_item_[] = $item;
		}
		
		$string = implode($arr_item_, ' ');
	}
	
	return $string;
}

/**
 * code E-commerce tracking
 */
 function getCodeEcomm($db, $klant_id)
 {
    $sql = "SELECT tmp.prijs, hz.code, dpt.dept_naam, str.streek_naam
        FROM tblTemp tmp 
        INNER JOIN tblHuizen hz 
        ON tmp.huis_id = hz.huis_id 
        INNER JOIN tblDepts dpt 
        ON hz.dept_id = dpt.dept_id 
        INNER JOIN tblDept_streek dptstr 
        ON dpt.dept_id = dptstr.dept_id 
        INNER JOIN tblStreek str 
        ON dptstr.streek_id = str.streek_id 
        WHERE tmp.klant_id = $klant_id"; 
        
        $result = $db->query($sql);
        $row = $result->fetch();
        extract($row);
        $dept_naam = addslashes($dept_naam);
        $streek_naam = addslashes($streek_naam);
        $result->free_result();
        // opbouw code voor GA Ecomm
        $code_ga_track_ecomm = <<<EOD
         _gaq.push(['_addTrans',
        '$klant_id',           // order ID - required
        '',                    // affiliation or store name
        '$prijs',             // total - required
        '',                    // tax
        '',                    // shipping
        '$dept_naam',          // city
        '$streek_naam',        // state or province
        'Frankrijk'            // country
        ]);
EOD;
    $code_ga_track_ecomm .= <<<EOD
     _gaq.push(['_addItem',
    '$klant_id',    // order ID - required
    '$code',        // SKU/code - required
    '',             // product name
    '',             // category or variation
    '$prijs',       // unit price - required
    '1'             // quantity - required
    ]);
EOD;
    $code_ga_track_ecomm .= <<<EOD
    _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
EOD;
    $code_ga_track_ecomm .= "\n";
    
    return $code_ga_track_ecomm;
}
?>