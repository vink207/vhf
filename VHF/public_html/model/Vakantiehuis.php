<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';

error_reporting(E_ALL);

class Vakantiehuis
{
    protected $db;
    private $huis_id;
    private $data = array();
    private $arrfaciliteit = array();
    private $fotos = array();

	public function __construct()
	{
        $this->db = Db::init();
    }
    
    // data tblHuizen
    public function getDataHuis()
    {
        $sql = "SELECT  hz.huisnaam, hz.plaats, hz.code, hz.aantal1, hz.aantal2, hz.beschrijving, hz.ligging, hz.afstand, hz.indeling, hz.latlng, hz.slaapkamers,
                    dpt.deptcode, dpt.dept_naam, 
                    str.streek_naam
		        FROM tblHuizen hz 
                INNER JOIN tblDepts_dev dpt 
                ON hz.dept_id = dpt.dept_id 
                INNER JOIN tblDept_streek_dev dpt_str 
                ON dpt.dept_id = dpt_str.dept_id 
                INNER JOIN tblStreek_dev str 
                ON dpt_str.streek_id = str.streek_id 
                WHERE hz.huis_id = '$this->huis_id'";	
        
        $stmt = $this->db->query($sql);
        
        if ($stmt->rowCount()!= 0)
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            while (list($key, $value) = each($row))
            {
                $this->setData($key, $value);
            }
        }
        else
        {
            // error
        }
    }
    
    // faciliteiten tabel sidebar
    public function getFaciliteiten()
    {
        $sql = "SELECT name, LOWER(label) AS label FROM sidebar WHERE categorie != 'soorthuis' ORDER BY rank ASC";
        $stmt = $this->db->query($sql);
        $stmt->fetch(PDO::FETCH_ASSOC);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);
            
            $sql = "SELECT id FROM sidebar_huis WHERE huis_id = '$this->huis_id' AND $name = 1";
            $stmt2 = $this->db->query($sql);
            if ($stmt2->rowCount() != 0)
            {
                array_push($this->arrfaciliteit, $label);
            }
        }
    }
    
    public function getFotos()
    {
        $sql = "SELECT foto FROM tblFotos WHERE huis_id = '$this->huis_id' AND unvisible = 0 ORDER BY volgorde ASC";        
        $stmt = $this->db->query($sql);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);
            array_push($this->fotos, $foto);
        }
        return $this->fotos;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
        $value = addslashes($code);
        
        $sql = "SELECT huis_id FROM tblHuizen WHERE code = '$value'";
        $stmt = $this->db->query($sql);
        if ($stmt->rowCount()!= 0)
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $this->setHuisID($huis_id);
        }
        else
        {
            // error
        }
    }
    
    private function setData($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    public function getData(){
        return $this->data;
    }
    
    public function getArrFaciliteit()
    {
        return $this->arrfaciliteit;
    }
    
    public function getHuisID()
    {
        return $this->huis_id;
    }
    
    public function setHuisID($huis_id)
    {
        $this->huis_id= $huis_id;
    }
}

?>