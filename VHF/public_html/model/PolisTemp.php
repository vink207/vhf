<?php 
/**
* Prijs berekening polis europeesche
* polis achade aan woning is verplicht
* 06/05/2010
*/

class PolisTemp 
{
	/**
 	* huursom
	*/
	public $huursom;

	/**
 	* verzekering garantie annulering
	*/
	public $annulering;

	/**
 	* verzekering schade_woning
	*/
	public $schade_woning;
	
	private $pct_schade = 0.015; // 1.5 procent van de huursom 
	private $pct_annulering = 0.07; // 7% van de huursom 
	private $pct_tax = 0.21;
	public $poliskosten = 3.50;
	public $premie_annulering = 0;
	public $premie_schade = 0;
	public $assurantie;
	public $polissom = 0;

	public function __construct($huursom, $annulering, $schade_woning)
	{   
   		$this->huursom = $huursom; 
   		$this->annulering = $annulering;
        $this->schade_woning = $schade_woning;
		
		$this->bereken_premie();
	}

	private function bereken_premie()
	{
		// tax bedrag
        $tax_bedrag  = 0;
        // polis schade aan woning
        if ($this->schade_woning == 'ja')
        {
            $this->premie_schade = $this->pct_schade * $this->huursom;
		    $this->premie_schade = round($this->premie_schade, 2);
            $tax_bedrag += $this->premie_schade;
        }
		else
        {
            $this->premie_schade = 0;
        }
		
		// polis garantie annulering
		if ($this->annulering == 'ja')
		{			
			$this->premie_annulering = $this->pct_annulering * $this->huursom;
			$this->premie_annulering = round($this->premie_annulering, 2);
            $tax_bedrag += $this->premie_annulering;
        }
        else
        {
            $this->premie_annulering = 0;
        }
        
        $tax_bedrag += $this->poliskosten;        
		$this->assurantie = $this->pct_tax * $tax_bedrag;
		$this->assurantie = round($this->assurantie, 2);
		$this->polissom = $this->premie_annulering + $this->premie_schade + $this->poliskosten + $this->assurantie;			
	}
}

?>