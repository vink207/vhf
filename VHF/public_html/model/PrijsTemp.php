<?php
// Berekenen van de regulier en aanbiedingsprijzen van een boeking. Omdat de aanbiedingsperiodes
// binnen de seizoensperiodes kunnen vallen berekenen we eerst de aanbiedingsprijs daarna de reguliere
// prijs voor de aanbiedngsperiode het verschil tussen de reguliere en aanbiedingsprijs trekken we
// later af van de reguliere som over de gehele periode.
// De functie prijsAanbiedingen() berekent de aanbiedingsprijs voor een aanbiedingsperiode 
// De functie prijsRegulier() de reguliere prijs voor een reguliere periode 

 

include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

error_reporting(E_ALL);

class PrijsTemp 
{
	private $huis_id;
	private $tmp_a;
	private $tmp_v;
	private $somaanb = 0;
	private $somregaanb = 0;
	private $som = 0;
	public $huur = 0;
	
	/**
	* vlaggen
	*/
	var $uitvoer;

	public function __construct($huis_id, $tmp_a, $tmp_v)
	{   
   		$this->db = Db::init();
            		
   		$this->huis_id = $huis_id;
		$this->tmp_a = $tmp_a;
		$this->tmp_v = $tmp_v;
   
   		$this->jaar = date("Y", $this->tmp_a);
   
   		$this->tblPrijs = 'tblPrijzen' . $this->jaar;
   		$this->tblSeizoen = 'tblSeizoen' . $this->jaar;
   
   		$this->sec_in_week = 60*60*24*7;
   
   		$this->aantal_weken = round(($this->tmp_v - $this->tmp_a) / $this->sec_in_week);
		
		$this->TableExists();
		
		if ($this->uitvoer != 0) $this->BepaalPrijs(); 
	}
	
	private function TableExists()
	{
		$sql = "SHOW TABLES LIKE '$this->tblPrijs'";
		
		$stmt = $this->db->query($sql);
		
		$size = $stmt->rowCount();
		
		if ($size == 0) 
		{
			$this->uitvoer = 0;
		}
		else 
		{
			$this->uitvoer = 1;
		}
	}

	private function BepaalPrijs()
	{		 
		$this->prijsAanbiedingen();
		 
		$this->prijsRegulier();	
	
		$this->huur = $this->som - ( $this->somregaanb - $this->somaanb );
	}

	private function prijsAanbiedingen() // betreft het aanbiedingen
	{	
		$sql = "SELECT * FROM tblAanbieding WHERE huis_id = $this->huis_id AND actief = 1 ORDER BY van ASC";
	
		$stmt =$this->db->query($sql);
	
		$size  = $stmt->rowCount();
	
		if ($size != 0)
		{ 
			while ($row = $result->fetch())
			{
				extract($row);
	
				$this->tmp_ba = strtotime($van);  // $tmp_van = timestamp  begindatum seizoen 
				$this->tmp_ea = strtotime($tot);  // $tmp_tot = timestamp  einddatum  seizoen 
			
				if ($this->tmp_a == $this->tmp_ba) $this->tmp_a += 1;
				if ($this->tmp_a == $this->tmp_ea) $this->tmp_a += 1;
				if ($this->tmp_v == $this->tmp_ba) $this->tmp_v -= 1;
				if ($this->tmp_v == $this->tmp_ea) $this->tmp_v -= 1;
			
				$weken = round (( $this->tmp_ea - $this->tmp_ba ) / $this->sec_in_week );
		
				// boeking valt binnen 1 aanbiedingsperiode 				
				if ($this->tmp_ba <= $this->tmp_a && $this->tmp_v <= $this->tmp_ea)
				{						
					$this->bedragaanb = $this->aantal_weken * $prijs; 
				 	$this->somaanb += $this->bedragaanb;
				
				 	$this->bedragregaanb = $this->aantal_weken * $volprijs;
				 	$this->somregaanb += $this->bedragregaanb; 				
				}
		
				// aankomst binnen en vertrek buiten de aanbiedingsperiode			
				elseif ($this->tmp_ba <= $this->tmp_a && $this->tmp_v > $this->tmp_ea && $this->tmp_a < $this->tmp_ea) 
				{		
					$weken_in_periode = round (( $this->tmp_ea - $this->tmp_a ) / $this->sec_in_week );
				
					$this->bedragaanb = $weken_in_periode * $prijs;
					$this->somaanb += $this->bedragaanb;
					
					$this->bedragregaanb = $weken_in_periode * $volprijs;
					$this->somregaanb += $this->bedragregaanb;
				}
			
				// aankomst buiten en vertrek binnen de aanbiedingsperiode 		
				elseif ($this->tmp_ba > $this->tmp_a && $this->tmp_v <= $this->tmp_ea && $this->tmp_v > $this->tmp_ba) 
				{		
					$weken_in_periode = round (( $this->tmp_v - $this->tmp_ba ) / $this->sec_in_week );
				
					if ($this->vlagaanb1 != 1)
					{				 
				 		$this->bedragaanb = $weken_in_periode * $prijs;
						$this->somaanb += $this->bedragaanb;
				 		$this->vlagaanb1 = 1;
					
						$this->bedragregaanb = $weken_in_periode * $volprijs;
				 		$this->somregaanb += $this->bedragregaanb;					
					}
				}
			
				// aankomst en vertrek buiten de aanbiedingsperiode 		
				elseif ($this->tmp_ea < $this->tmp_v && $this->tmp_a < $this->tmp_ba) 
				{		
					$weken_in_periode = round (( $this->tmp_ea - $this->tmp_ba ) / $this->sec_in_week );
				
					$this->bedragaanb = $weken_in_periode * $prijs;
					$this->somaanb += $this->bedragaanb;
					
					$this->bedragregaanb = $weken_in_periode * $volprijs;
					$this->somregaanb += $this->bedragregaanb;					 
				}	
			} // eind while 	
		}
		else 
		{
			$vlagaanb2 = 1;  // geen aanbiedingen	
		}
	}

	private function prijsRegulier()
	{		
		$sql = "SELECT pr.prijs, sz.van, sz.tot, pr.seizoens_id 
				FROM $this->tblPrijs pr
				INNER JOIN $this->tblSeizoen sz
				ON pr.seizoens_id = sz.seizoens_id  
				WHERE pr.huis_id = '$this->huis_id'
				ORDER BY van ASC";

		$stmt = $this->db->query($sql);
	
		$size  = $stmt->rowCount();
	
		if ($size != 0)
		{ 
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				extract($row);
	
				$tmp_van = strtotime($van);  // $tmp_van = timestamp  begindatum seizoen 
				$tmp_tot = strtotime($tot);  // $tmp_tot = timestamp  einddatum  seizoen 

				if ($this->tmp_a == $tmp_van) $this->tmp_a += 1;
				if ($this->tmp_a == $tmp_tot) $this->tmp_a += 1;
				if ($this->tmp_v == $tmp_van) $this->tmp_v -= 1;
				if ($this->tmp_v == $tmp_tot) $this->tmp_v -= 1;
		
				// boeking valt binnen 1 periode 				
				if ($tmp_van <= $this->tmp_a && $this->tmp_v <= $tmp_tot)
				{			
				 	$this->bedrag = $this->aantal_weken * $prijs; 
					$this->som += $this->bedrag; 
				 				 
				 	if ($prijs == -2) $this->msg = "Huis niet te huur in deze periode";			
				}
		
				// boeking bestrijkt meerder periodes 			
				// aankomst binnen en vertrek buiten de periode 		
				elseif ($tmp_van <= $this->tmp_a && $this->tmp_v > $tmp_tot && $this->tmp_a < $tmp_tot) 
				{		
					$weken_in_periode = round (( $tmp_tot - $this->tmp_a ) / $this->sec_in_week );
				
					$this->bedrag = $weken_in_periode * $prijs;
					$this->som += $this->bedrag;
					
					if ($prijs == -2) $this->msg = "Huis niet te huur in deze periode";
				}
			
				// aankomst buiten en vertrek binnen de periode 		
				elseif ($tmp_van > $this->tmp_a && $this->tmp_v <= $tmp_tot && $this->tmp_v > $tmp_van) 
				{		
					$weken_in_periode = round (( $this->tmp_v - $tmp_van ) / $this->sec_in_week );
				
					if ($this->vlag1 != 1)
					{				 
						$this->bedrag = $weken_in_periode * $prijs;
						$this->som += $this->bedrag;
						$this->vlag1 = 1;
					
						if ($prijs == -2) $this->msg = "Huis niet te huur in deze periode";
					}
				}
			
				// aankomst en vertrek buiten de periode 
		
				elseif ($tmp_tot < $this->tmp_v && $this->tmp_a < $tmp_van) 
				{		
					$weken_in_periode = round (( $tmp_tot - $tmp_van ) / $this->sec_in_week );
				
					$this->bedrag = $weken_in_periode * $prijs;
				 	$this->som += $this->bedrag;
					
					if ($prijs == -2) $this->msg = "Huis niet te huur in deze periode";
				}
	
			} // eind while 
		} 
		else 
		{	
			$this->uitvoer = 0;  // geen prijzen bekend 
		}	
  	}
}

