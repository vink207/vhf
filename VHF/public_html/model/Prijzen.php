<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

error_reporting(E_ALL);

class Prijzen
{
    protected $db;
    private $huis_id;
    private $arrjaar = array();

	public function __construct()
	{
        $this->db = Db::init();
    }
    
    public function invoke()
    {
        // jaar prijs
        $start_jaar = date('Y');
        $eind_jaar = $start_jaar + 2;
        for ($jaar = $start_jaar; $jaar < $eind_jaar; $jaar++)
        {            
            // prijs/periode huis
            $this->getPrijsPeriode($jaar);
        }   
    }
    
    private function getPrijsPeriode($jaar)
    {           
        $tblPrijs = 'tblPrijzen' . $jaar;
        $tblSeizoen = 'tblSeizoen' . $jaar;
        
        $sql = "SELECT pr.prijs_id, pr.prijs,
                       UNIX_TIMESTAMP(sz.van) AS tmp_van_pr, UNIX_TIMESTAMP(sz.tot) AS tmp_tot_pr 
                FROM $tblPrijs pr 
                INNER JOIN $tblSeizoen sz 
                ON pr.seizoens_id = sz.seizoens_id 
                WHERE pr.huis_id = $this->huis_id 
                ORDER BY UNIX_TIMESTAMP(sz.van) ASC";
                		
		$stmt = $this->db->query($sql);	
        $size = $stmt->rowCount();
        
        if ($size != 0)
        {
            array_push($this->arrjaar, $jaar);
            $this->arrprijs[$jaar] = array();
            $this->arrvan[$jaar] = array();
            $this->arrtot[$jaar] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                extract($row);
            
                $dag_van = date("d", $tmp_van_pr);
                $dag_tot = date("d", $tmp_tot_pr);
                
                $maand_van =  date('m',$tmp_van_pr);
                $maand_tot =  date('m',$tmp_tot_pr);
            
                $str_van = $dag_van.'/'.$maand_van;
                $str_tot = $dag_tot.'/'.$maand_tot;
            
                switch ($prijs)
                {   
                    case "-1": $prijs = 'Prijs op aanvraag'; break;
                    case "-2": $prijs = 'Niet te huur in deze periode'; break;
                    default: $prijs = '&euro; ' . $prijs . ' p/w';            
                }
                
                array_push($this->arrprijs[$jaar], $prijs);
                array_push($this->arrvan[$jaar], $str_van);
                array_push($this->arrtot[$jaar], $str_tot);
            }
        }
    }
    public function getJaar(){
        return $this->arrjaar;
    } 
    public function getPrijs(){
        return $this->arrprijs;
    }    
    public function getVan()
    {
        return $this->arrvan;
    }    
    public function getTot()
    {
        return $this->arrtot;
    }     
    public function setHuisID($huis_id)
    {
        $this->huis_id= $huis_id;
    }
}

?>