<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

error_reporting(E_ALL);

class OptiesHuis {
    
	private $huis_id;
	private $array_opties = array();
	
 	public function __construct($huis_id)
 	{
		$this->db = Db::init();        
   		$this->huis_id = $huis_id;
        
		$this->invoke();
	}
	
	private function invoke()
	{  
  		$sql = "SELECT ak.*, hz.huisdier, hz.dekens_dekbedden FROM tblAddKosten ak 
                INNER JOIN tblHuizen hz 
                ON ak.huis_id = hz.huis_id 
                WHERE ak.huis_id = $this->huis_id";

		$stmt = $this->db->query($sql);
        
	
		if ($stmt->rowCount() != 0)
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			extract($row);
			
			$this->adk = 1; 
            
            if ($dekens_dekbedden != '')
            {    
                $this->setOpties('Beddengoed: '.$dekens_dekbedden.' aanwezig.');
            }
			
			/** LAKENHUUR
			* lakenhuur1: zelf meenemen
			* lakenhuur2 en lakenhuur3: verwijst naar te huur
			* lakenhuur4 en lakenhuur5: verwijst naar inclusief
			* als lakenhuur1 leeg is zijn de lakens inclusief
			*/
		
			if ($lakenhuur4 != '') 
			{		
				$lakenhuur = $lakenhuur4;
			}
			elseif ($lakenhuur5 != '') 
			{		
				$lakenhuur = $lakenhuur5;
			}
			elseif ($lakenhuur2 != '' && $prijsset != '') 
			{
				$prijsset = number_format($prijsset, 2, ',', '.');
				$lakenhuur = 'Lakens te huur: &euro; ' . $prijsset . ' per set. (keuken)handdoeken neemt u zelf mee.';
			}
			elseif ($lakenhuur3 != '') 
			{
				$lakenhuur = 'Lakens te huur: ';
				$prijs1pers = number_format($prijs1pers, 2, ',', '.');
				$prijs2pers = number_format($prijs2pers, 2, ',', '.');
				if ($prijs1pers != '') {$lakenhuur .= '&euro; ' . $prijs1pers . ' (1 pers. bed)';}
				if ($prijs2pers != '') {$lakenhuur .= ' / &euro; ' . $prijs2pers . ' (2 pers. bed).';}
				$lakenhuur .= ' (keuken)handdoeken neemt &nbsp;&nbsp;u zelf mee.';
			}
			elseif ($lakenhuur1 != '' && $lakenhuur6 == '') 
			{
				$lakenhuur = $lakenhuur1;
			}
			elseif ($lakenhuur6 != '') // extra opties
			{				
				$optieslaken = str_replace('€', '&euro', $optieslaken);
				$lakenhuur = 'Lakens te huur: ' . $optieslaken;
			}
            
            $this->setOpties($lakenhuur);			
			
			/** EINDSCHOONMAAK
			* opties: zelf, betaald, verplicht of inclusief
			* soms twee opties, zelf en betaald
			* eindschoonmaak1: zelf
			* eindschoonmaak2: optioneel in combinatie met prijsoptie1
			* eindschoonmaak3: prijs per uur in combinatie met prijsoptie2
			* eindschoonmaak4: verplicht in combinatie met prijsverplicht
			* eindschoonmaak5: inclusief
			* eindschoonmaak6: extra opties
			*/
	
			if ($eindschoonmaak5 != '') // inclusief
			{	
		 		$eindschoonmaak = 'Eindschoonmaak: ' . $eindschoonmaak5;		 
			} 
			elseif ($eindschoonmaak1 != '' && $eindschoonmaak2 == '' && $eindschoonmaak3 == '' && $eindschoonmaak4 == '' && $eindschoonmaak6 == '') // zelf
			{	
				$eindschoonmaak = $eindschoonmaak1;		 
			}	
			elseif($eindschoonmaak2 != '')
			{				
				$prijsoptie1 = number_format($prijsoptie1, 2, ',', '.');
				$eindschoonmaak = 'Eindschoonmaak: &euro; ' . $prijsoptie1 . ' (indien gewenst).';
			}
			elseif($eindschoonmaak3 != '')
			{			
				$prijsoptie2 = number_format($prijsoptie2, 2, ',', '.');
				$eindschoonmaak = 'Eindschoonmaak: &euro; ' . $prijsoptie2 . ' per uur ';
				if ($aantal_uren != '') {$eindschoonmaak .= '(ongeveer '  . $aantal_uren . ' uur) ';}
				$this->eindschoonmaak .= '(indien gewenst).';		
			}
			elseif($eindschoonmaak4 != '')
			{			
				$prijsverplicht = number_format($prijsverplicht, 2, ',', '.');
				$eindschoonmaak = 'Eindschoonmaak: &euro; ' . $prijsverplicht . ' (verplicht).';		
			}
			elseif($eindschoonmaak6 != '')
			{				
				$optieseindschoonmaak = str_replace('€', '&euro', $optieseindschoonmaak);
				$eindschoonmaak = 'Eindschoonmaak: ' . $optieseindschoonmaak;		
			}
            
            $this->setOpties($eindschoonmaak);
			
			/** GAS, WATER EN LICHT
			* opties: inclusief of niet
			*/
			if ($gwl == 'inclusief.')
			{
				$gwl = 'Water, gas en elektr.: inclusief.';
			}
			elseif ($gwl == "Direct a/d eigenaar te voldoen svp (zie huurvoorwaarden).")
			{
				$gwl = 'Water, gas en elektr.: naar verbruik. (zie huurvoorwaarden)';
			}
			elseif ($gwl == 'p/dag. Direct a/d eigenaar te voldoen svp.')
			{	
				$prijsgwl = number_format($prijsgwl, 2, ',', '.');
				$gwl = 'Water, gas en elektr.: &euro; ' . $prijsgwl . 'p/dag.';	
			} 
			elseif ($gwl == 'opties')
			{				
				$optiesgwl = str_replace('€', '&euro;', $optiesgwl);
				$gwl = 'Water, gas en elektr.: ' .$optiesgwl;	
			}
            
            $this->setOpties($gwl);
            
			/** Kinderbenodigheden
			* opties: inclusief, geen, anders in combinatie met prijs
			* kinderbedje, kinderbad en kinderstoel
			*/
			
			if ($kinderbedje != '' || $kinderstoel != '' || $kinderbad != '')
			{						 			
				$kinderbenodigheden = 'Babybenodigheden: ';
				
				if ($kinderbedje != '')
				{					
					$bedje = true;
					
					if ($kinderbedje == 'vast' || $kinderbedje == 'camping')
					{						
						$kinderbenodigheden .= $aantal_kinderbedje . ' x bedje';   
					
                        if ($prijskinderbedje != 0) 
					    {
						  $prijskinderbedje = number_format($prijskinderbedje, 2, ',', '.');
						  $kinderbenodigheden .= ' &euro; ' . $prijskinderbedje;
						  if ($prijs_per_week == 1) {$kinderbenodigheden .= ' p/w';}
					    }
					}
					elseif ($kinderbedje == 'geen') 
					{
						$kinderbenodigheden .= 'bedje niet aanwezig';
					}
				}
		
				if ($kinderstoel != '')
				{			
					if ($bedje) {$kinderbenodigheden .= ', ';}
					$stoel = true;
					
					if ($kinderstoel == 'inclusief')
					{
						$kinderbenodigheden .= $aantal_kinderstoel . ' x stoeltje';
					}						
					elseif ($kinderstoel == 'geen') 
					{
						$kinderbenodigheden .= 'stoeltje niet aanwezig';
					}
					elseif ($kinderstoel == 'anders') 
					{
						$prijskinderstoel = number_format($prijskinderstoel, 2, ',', '.');
						$kinderbenodigheden .= $aantal_kinderstoel . ' x stoeltje &euro; ' . $prijskinderstoel;
						if ($prijs_per_week == 1) {$kinderbenodigheden .= ' p/w';}
					}
				}
		
				if ($kinderbad != '')
				{		
					$bad = true;
					
					if ($bedje || $stoel) {$kinderbenodigheden .= ', ';}					
					
					if ($kinderbad == 'inclusief')
					{
						$kinderbenodigheden .= $aantal_kinderbad . ' x badje';
					}		
					elseif ($kinderbad == 'geen') 
					{
						$kinderbenodigheden .= 'badje niet aanwezig';
					}
					elseif ($kinderbad == 'anders') 
					{
						$prijskinderbad = number_format($prijskinderbad, 2, ',', '.');
						$kinderbenodigheden .= $aantal_kinderbad . ' x badje &euro; ' . $prijskinderbad;
						if ($prijs_per_week == 1) {$kinderbenodigheden .= ' p/w';}
					}		
				}				
				
				$kinderbenodigheden .= '.';
			}
			
			if ($kinderbedje == 'inclusief_fixe' || $kinderbedje == 'inclusief_pliant' || $kinderstoel == 'inclusief' || $kinderbad == 'inclusief') {$kinderbenodigheden .= ' Geen extra kosten.';}

			
			if ($kinderbedje == '' && $kinderstoel == '' && $kinderbad == '')
			{						 			
				$kinderbenodigheden = 'Babybenodigheden: niet aanwezig';
			}
            
            $this->setOpties($kinderbenodigheden);
            
			/** HUISDIEREN
			* opties: als huisdieren zijn toegestaan opties tonen
			*/
	
			if ($huisdier == 'ja') 
			{			
				$huisdieren = 'Huisdier: ';
		
				if ($prhuisdier == 'inclusief')
				{
					$huisdieren .= ' toegestaan, geen extra kosten.';									
				}		
				elseif ($prhuisdier == 'borg')
				{
					$borg_huisdier = number_format($borg_huisdier, 2, ',', '.');
					$huisdieren .= ' borgsom &euro; ' .$borg_huisdier. ' per huisdier';
				}
				elseif ($prhuisdier == 'bijbetaling')
				{
					$bijbetaling_huisdier = number_format($bijbetaling_huisdier, 2, ',', '.');
					$huisdieren .= ' toegestaan. Er wordt &euro; ' . $bijbetaling_huisdier . ' gevraagd ' .$termijn_bijbetaling.'.';
		 		}
				elseif ($prhuisdier == 'extra_schoonmaak')
				{
					$extra_schoonmaak_huisdier = number_format($extra_schoonmaak_huisdier, 2, ',', '.');
					$huisdieren .= ' toegestaan. Totaal verplichte schoonmaakkosten &euro; ' . $extra_schoonmaak_huisdier . '.';
		 		}
					
				if ($txthuisdier != '')
				{
					//$txthuisdier = firstToLower($txthuisdier);
					$txthuisdier = str_replace('€', '&euro', $txthuisdier);
					$huisdieren .= ' ' . $txthuisdier;
				}
			}
			else
			{
				$huisdieren = 'Huisdier: niet toegestaan.';
			}
            
            $this->setOpties($huisdieren);
			
			/**
			* internet, prijs internet  en dvd
			*/
			if ($internet == 'nee' OR $internet == '')
			{
				$internet = 'Internet: niet beschikbaar.';
			}
			elseif ($internet == 'gratis')
			{
				$internet .= 'Internet: WiFi gratis';
			}
			else
			{
				$internet = ucfirst($internet_anders);
			}
			$this->setOpties($internet);
            
			if ($dvd == 'nee' OR $dvd == '')
			{
				$dvd = 'Dvd-speler: niet aanwezig.';
			}
			elseif ($dvd == 'aanvraag')
			{
				$dvd .= 'Dvd-speler: op aanvraag';
			}
			else
			{
				$dvd = 'Dvd-speler: aanwezig.';
			}            
            $this->setOpties($dvd);
			
			/** BIEZONDERHEDEN
			* overige informatie
			*/
			if ($biezonderheden != '')
			{
			  $biezonderheden = str_replace('€', '&euro;', $biezonderheden);
              $this->setOpties($biezonderheden);
			}
			
			$stmt->closeCursor();
		}
		else
		{
			$this->adk = false;
		}	
	}
    
    private function setOpties($value){
        array_push($this->array_opties, $value);
    }
    
    public function getOpties(){
        return $this->array_opties;
    }
}

?>