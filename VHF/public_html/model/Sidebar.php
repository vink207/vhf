<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

error_reporting(E_ALL);

/**
 * names en labels voor sidebar
 */

class Sidebar
{
    private $soorthuis = array();
    private $faciliteiten = array();
    private $kinderen = array();
    private $extraopties = array();
    private $array = array();
    
	public function __construct()
	{   
   		$this->db = Db::init();
        
        $this->invoke();
    }
    
    private function invoke()
    {
        // names en labels tabel sidebar
        $sql = "SELECT id, categorie FROM sidebar_categorie ORDER BY rangorde ASC";
        $stmt = $this->db->query($sql);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {		
            extract($row);
            
            $sql = "SELECT name, label FROM sidebar WHERE categorie_id = '$id' AND actief = 1 ORDER BY rank ASC";
            $stmt2 = $this->db->query($sql);
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
            {		
                extract($row2);
                
                $data = array('label'=>$label, 'name'=>$name);
                
                /**
                if ($categorie == 'faciliteit')
                {
                    array_push($this->faciliteiten , $data);
                }
                elseif ($categorie == 'kinderen')
                {
                    array_push($this->kinderen , $data);
                }
                elseif ($categorie == 'soorthuis')
                {
                    array_push($this->soorthuis , $data);
                }
                elseif ($categorie == 'extraopties')
                {
                    array_push($this->extraopties , $data);
                } 
                */
                $this->array[$categorie][] = $data;   
            }
        }
    }
    
    public function getFaciliteiten()
    {
        return $this->faciliteiten;
    }
    
    public function getKinderen()
    {
        return $this->kinderen;
    }
    
    public function getExtraOpties()
    {
        return $this->extraopties;
    }
    
    public function getSoortHuis()
    {
        return $this->soorthuis;
    }
    
    public function getCheckbox()
    {
        return $this->array;
    }
    
}

?>