<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

error_reporting(E_ALL);

/**
 * favorieten/nieuw
 * regionaam, code, max.personen, vanaf prijs en img
 * quote nieuw
 */

class Home
{
    private $nieuw = array();
    private $favoriet = array();
    
	public function __construct()
	{   
   		$this->db = Db::init();
        
        $this->getDataNieuw();
        $this->getDataFavoriet();
    }
    
    public function getDataNieuw()
    {        
        // data huizen homepagen
        $sql = "SELECT hz.huis_id, hz.code, hz.aantal2, st.streek_naam, IFNULL(qu.quote, '') AS quote      
                FROM tblHuizen hz 
                INNER JOIN tblDept_streek ds 
                ON hz.dept_id = ds.dept_id 
                INNER JOIN tblStreek st 
                ON ds.streek_id = st.streek_id 
                LEFT OUTER JOIN quotes qu 
                ON hz.huis_id = qu.huis_id 
                WHERE hz.nieuw = 1 
                AND hz.zichtbaar = 0 
                LIMIT 0,2";
        
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        
        if ($size != 0)
        {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {		
                extract($row);
                
                // vanaf prijs huis
                $minprijs = $this->getMinPrijs($huis_id);
                
                // hoofdfoto
                $foto = $this->getFoto($huis_id);
                
                $data = array('huisID'=>$huis_id, 'code'=>$code, 'prijs'=>$minprijs, 'aantal'=>$aantal2, 'img'=>$foto, 'regio'=>$streek_naam, 'quote'=>$quote);
                
                array_push($this->nieuw, $data);
            }
        }
    }
    
    public function getDataFavoriet()
    {        
        // data huizen homepagen
        $sql = "SELECT hz.huis_id, hz.code, hz.aantal2, st.streek_naam     
                FROM tblHuizen hz 
                INNER JOIN tblDept_streek ds 
                ON hz.dept_id = ds.dept_id 
                INNER JOIN tblStreek st 
                ON ds.streek_id = st.streek_id 
                WHERE hz.favoriet = 1 
                AND hz.zichtbaar = 0 
                LIMIT 0,3";
        
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        
        if ($size != 0)
        {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {		
                extract($row);
                
                // vanaf prijs huis
                $minprijs = $this->getMinPrijs($huis_id);
                
                // hoofdfoto
                $foto =  $this->getFoto($huis_id);
                
                $data = array('huisID'=>$huis_id, 'code'=>$code, 'prijs'=>$minprijs, 'aantal'=>$aantal2, 'img'=>$foto, 'regio'=>$streek_naam);
                
                array_push($this->favoriet, $data);
            }
        }
    }
    
    // min prijs huis
    private function getMinPrijs($huis_id)
    {   
        $jaar = date('Y');
        $tbl = 'tblPrijzen'.$jaar;
        $minprijs = 0;
        $sql = "SELECT MIN(prijs) AS minprijs 
                FROM $tbl  
                WHERE huis_id = $huis_id";
                
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        if ($size != 0)
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $stmt->closeCursor();	
            
            return $minprijs;
        }
    }
    
    // min prijs huis
    private function getFoto($huis_id)
    {
        $sql = "SELECT foto 
                FROM tblFotos 
                WHERE huis_id = $huis_id 
                AND unvisible = 0
                ORDER BY volgorde ASC LIMIT 0, 1";
                
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        if ($size != 0)
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $stmt->closeCursor();	
            
            return $foto;
        }
    }
    
    // getters public
    public function getFavoriet()
    {
        return $this->favoriet;
    }
    public function getNieuw()
    {
        return $this->nieuw;
    }
}

?>