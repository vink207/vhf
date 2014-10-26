<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Datum.php';

error_reporting(E_ALL);

class Reacties
{
    private $huis_id = 0;
    private $tag = '';
    private $arrid = array();
    private $arrcode = array();
    private $arrreactie = array();    
    private $arrnaam = array();  
    private $arrdeptnaam = array();
    private $arraantal = array();
    private $arrdatum = array();

	public function __construct()
	{   
   		$this->db = Db::init();
        $this->dt = new Datum();
    }
    
    // tags voor homepage en overons
    public function reactieTag()
    {
        if ($this->tag != '')
        {
            if ($this->tag == 'homepage')
            {
                // select id, naam, regio, code, datum en reactie
                $sql = "SELECT re.id, re.opmerking, UNIX_TIMESTAMP(re.datum)AS tmp,
                               dp.dept_naam,
                               hz.code 
                        FROM reactie_huurder_dev re 
                        INNER JOIN tblHuizen hz 
                        ON re.huis_id = hz.huis_id 
                        INNER JOIN tblDepts dp 
                        ON hz.dept_id = dp.dept_id 
                        WHERE re.homepage = 1 
                        AND hz.zichtbaar = 0
                        ORDER BY UNIX_TIMESTAMP(re.datum) DESC 
                        LIMIT 0, 3";
                
                $stmt = $this->db->query($sql);
                $size = $stmt->rowCount();
                if ($size != 0)
                {
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {		
                        extract($row);
                        
                        // datum reactie formateren
                        $this->dt->setTmp($tmp);
                        $this->dt->setFormat('mlj');
                        $datum = $this->dt->invoke();
        			
                        $this->setID($id);
                        $this->setReactie($opmerking);
                        $this->setDatum($datum);
                        $this->setDept($dept_naam);
                        $this->setCode($code);
                    }
        		
                    $stmt->closeCursor();	
                }
            }
            elseif ($this->tag != 'overons')
            {
                // select id, naam, datum en aantal (personen)
                $sql = "SELECT re.id, re.opmerking, UNIX_TIMESTAMP(re.datum)AS tmp,
                                bk.aantal 
                        FROM reactie_huurder re 
                        INNER JOIN tblBoeking bk  
                        ON re.klant_id = bk.klant_id 
                        WHERE re.overons = 1 
                        AND hz.zichtbaar = 0
                        ORDER BY UNIX_TIMESTAMP(re.datum) DESC";
                
                $stmt = $this->db->query($sql);
                $size = $stmt->rowCount();
                if ($size != 0)
                {
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {		
                        extract($row);
                        
                        // datum reactie formateren
                        $this->dt->setTmp($tmp);
                        $this->dt->setFormat('mlj');
                        $datum = $this->dt->invoke();
        			
                        $this->setID($id);
                        $this->setReactie($opmerking);
                        $this->setAantal($aantal);
                    }
        		
                    $stmt->closeCursor();	
                }
            }
            else
            {
                // return false;
                return false;
            }
        }
    }
    
    // reacties vakantiehuispagina
    public function reactieHuisID()
    {
        // select id, naam, regio, code, datum , reactie
        $sql = "SELECT re.id, re.opmerking, UNIX_TIMESTAMP(re.datum)AS tmp,
                       IFNULL(kl.tussenvoegsel, '') AS tussenvoegsel, IFNULL(kl.achternaam, 'naam huurder') AS achternaam, 
                       dp.dept_naam 
                FROM reactie_huurder_dev re 
                LEFT OUTER JOIN tblKlant kl 
                ON re.klant_id = kl.klant_id 
                INNER JOIN tblHuizen hz 
                ON re.huis_id = hz.huis_id 
                INNER JOIN tblDepts dp 
                ON hz.dept_id = dp.dept_id 
                WHERE re.huis_id = $this->huis_id 
                AND re.zichtbaar = 1 
                ORDER BY UNIX_TIMESTAMP(re.datum)";
                
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        if ($size != 0)
        {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {		
                extract($row);
                
                // datum reactie formateren
                $this->dt->setTmp($tmp);
                $this->dt->setFormat('mlj');
                $datum = $this->dt->invoke();
                        
                // naam huurder
                $naam = '';
                if ($tussenvoegsel != '')
                {
                    $naam .= $tussenvoegsel. ' ';
                }
                $naam .= $achternaam;
                
			
                $this->setID($id);
                $this->setReactie($opmerking);
                $this->setDatum($datum);
                $this->setNaam($naam);
                $this->setDept($dept_naam);
            }
		
            $stmt->closeCursor();	
        }
    }
    
    // setters public  
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    public function setHuisID($huis_id)
    {
        $this->huis_id = $huis_id;
    } 
    // setters private
    private function setDatum($datum)
    {
        array_push($this->arrdatum, $datum);
    }   
    private function setNaam($naam)
    {
        array_push($this->arrnaam, $naam);
    }   
    private function setID($id)
    {
        array_push($this->arrid, $id);
    }   
    private function setReactie($opmerking)
    {
        array_push($this->arrreactie, $opmerking);
    }   
    private function setDept($dept_naam)
    {
        array_push($this->arrdeptnaam, $dept_naam);
    }  
    private function setCode($code)
    {
        array_push($this->arrcode, $code);
    }    
    private function setAantal($aantal)
    {
        array_push($this->arraantal, $aantal);
    }
    
    // getters public 
    public function getDatum()
    {
        return $this->arrdatum;
    }   
    public function getNaam()
    {
        return $this->arrnaam;
    }   
    public function getID()
    {
        return $this->arrid;
    }   
    public function getReactie()
    {
        return $this->arrreactie;
    }   
    public function getDept()
    {
        return $this->arrdeptnaam;
    }       
    public function getCode()
    {
        return $this->arrcode;
    }    
    public function getAantal()
    {
        return $this->arraantal;
    }
}

?>