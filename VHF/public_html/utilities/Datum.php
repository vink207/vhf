<?php
/**
	* timestamps datum omzetten naar leesbaar reeks
*/
	
class Datum {
    
    private $tmp = 0;
    private $dag;
    private $weekdag;
    private $maand;
    private $maandkort;
    private $maandlang;
    private $jaar;
    private $format = '';
    private $datum = '';

	public function __construct()
	{
	}
    
    public function invoke()
    {
        if ($this->tmp != 0)
        {
            $dag = date('d', $this->tmp);
            $weekdag = date('w', $this->tmp);
            $maand = date('n', $this->tmp);
            $maandi = date('m', $this->tmp);
            $jaar = date('Y', $this->tmp);
            $maandkort = $this->getMaandKort($maand); 
            $maandlang = $this->getMaandLang($maand);
            $dagkort = $this->getDagKort($weekdag); 
            $daglang = $this->getDagLang($weekdag);
            
            if ($this->format != '')
            {  
                if($this->format == 'mlj')
                {                
                    $datum = <<<EOD
                    $maandlang $jaar
EOD;
                }
                elseif($this->format == 'dmj')
        		{
                    $datum = <<<EOD
                    $dag-$maandi-$jaar
EOD;
        		}
                elseif($this->format == 'dkdmkj')
                {                
                    $datum = <<<EOD
                    $dagkort $dag $maandkort $jaar
EOD;
                }
                elseif($this->format == 'dkdmlj')
                {                
                    $datum = <<<EOD
                    $dagkort $dag $maandlang $jaar
EOD;
                }
                elseif($this->format == 'dldmlj')
                {                
                    $datum = <<<EOD
                    $daglang $dag $maandlang $jaar
EOD;
                }
                elseif($this->format == 'dmlj')
                {                
                    $datum = <<<EOD
                    $dag $this->maandlang $jaar
EOD;
                }
                else
                {
                    $datum = '';
                }
                return $datum;
            }
        }
    }
    
    // setter timestamp
    public function setTmp($tmp)
    {
        $this->tmp = $tmp;
    }
    
    // setter format
    public function setFormat($format)
    {
        $this->format = $format;
    }	
	
	private function getDagLang($weekdag) 
	{
		switch ($weekdag)
		{
 			case "0":$dagLang = 'zondag';  break;
			case "1":$dagLang = 'maandag'; break;
			case "2":$dagLang = 'dinsdag'; break;
			case "3":$dagLang = 'woensdag'; break;
			case "4":$dagLang = 'donderdag'; break;
			case "5":$dagLang = 'vrijdag'; break;
			case "6":$dagLang = 'zaterdag'; break;
		}
        return $dagLang;
	}
	
	private function getDagKort($weekdag) 
	{
		switch ($weekdag)
		{
 			case "0":$dagKort = 'zo.';  break;
			case "1":$dagKort = 'ma.'; break;
			case "2":$dagKort = 'di.'; break;
			case "3":$dagKort = 'wo.'; break;
			case "4":$dagKort = 'do.'; break;
			case "5":$dagKort = 'vr.'; break;
			case "6":$dagKort = 'za.'; break;
		}
        return $dagKort;
	}	
	
	private function getMaandKort($maand) 
	{
		switch ($maand)
		{
			case "1":$maandKort = 'jan.'; break;
			case "2":$maandKort = 'feb.'; break;
			case "3":$maandKort = 'mrt'; break;
			case "4":$maandKort = 'apr.'; break;
			case "5":$maandKort = 'mei'; break;
			case "6":$maandKort = 'jun.'; break;
			case "7":$maandKort = 'jul.'; break;
			case "8":$maandKort = 'aug.'; break;
			case "9":$maandKort = 'sep.'; break;
			case "10":$maandKort = 'okt.'; break;
			case "11":$maandKort = 'nov.'; break;
			case "12":$maandKort = 'dec.'; break;
		}
        
        return $maandKort;
	}
	
	private function getMaandLang($maand) 
	{
		switch ($maand)
		{
			case "1":$maandLang = 'januari'; break;
			case "2":$maandLang = 'februari'; break;
			case "3":$maandLang = 'maart'; break;
			case "4":$maandLang = 'april'; break;
			case "5":$maandLang = 'mei'; break;
			case "6":$maandLang = 'juni'; break;
			case "7":$maandLang = 'juli'; break;
			case "8":$maandLang = 'augustus'; break;
			case "9":$maandLang = 'september'; break;
			case "10":$maandLang = 'oktober'; break;
			case "11":$maandLang = 'november'; break;
			case "12":$maandLang = 'december'; break;
		}
        return $maandLang;
	}
}

?>