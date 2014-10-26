<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Vakantiehuis.php';

error_reporting(E_ALL);

class GMaps
{
    private $huisid = 0;
    private $deptid = 0;
    private $streekid = 0;
    private $todo = 0;
    private $arrhuisid = array();  
    private $arrcode = array();  
    private $arrlatlng = array();
    private $arrcontent = array();
    
	public function __construct()
	{   
   		$this->db = Db::init();
        $this->base = 'http://'.$_SERVER['HTTP_HOST'];
    }
    
    public function invoke()
    {
        // query opbouwen afhankelijk van parameters
        $sql = "SELECT hz.latlng, hz.code, hz.huis_id, hz.plaats, dpt.depturl, str.streekurl, str.streek_naam   
                FROM tblHuizen_dev hz 
                INNER JOIN tblDepts_dev dpt 
                ON hz.dept_id = dpt.dept_id 
                INNER JOIN tblDept_streek_dev dpt_str 
                ON hz.dept_id = dpt_str.dept_id 
                INNER JOIN tblStreek_dev str 
                ON dpt_str.streek_id = str.streek_id 
                WHERE hz.zichtbaar = 0 
                AND hz.archief = 0 
                AND hz.latlng !=''";
                
        if ($this->todo != 0)
        {
            $sql .= " ORDER BY code ASC";
        }       
        elseif($this->huisid != 0)
        {
            $sql .= " AND huis_id = $this->huisid";
        }       
        elseif(count($this->arrhuisid != 0))
        {
            $sql .= " AND huis_id IN (";
            foreach($this->arrhuisid AS $value) 
            {
                $sql .= "$value,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ')';
        }
        elseif($this->deptid != 0)
        {
            $sql .= " AND dept_id = $this->deptid";
        }
        elseif($this->streekid != 0)
        {
            $depts = $this->selectDept();
            
            $sql .= " AND dept_id IN ($depts)";
        }
        
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        $str = '';
        if ($size != 0)
        {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {		
                extract($row);
                
                // object vakantiehuis aanmaken
                $vk = new Vakantiehuis();
                $vk->setHuisID($huis_id);
                $fotos = $vk->getFotos();
                $hoofdfoto = $fotos[0];
                $content = <<<EOD
<div class="infowindow">
$code <br />$plaats, $streek_naam
<img src="http://www.vakantiehuisfrankrijk.nl/thumbs/$hoofdfoto" />
<br />
<a href="/vakantiehuis-$streekurl/$depturl-$code">details</a>
</div>
EOD;
                $this->setLatLng($latlng);
                $this->setCode($code);  
                $this->setContent($content);
            }
        }
    }
    
    // select dept_id bij streek_id
    public function selectDept()
    {   
        $sql = "SELECT dept_id
                FROM tblDept_streek   
                WHERE streek_id = $this_streek_id";
                
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
        $str = '';
        if ($size != 0)
        {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {		
                extract($row);
                
                $str .= $dept_id.',';
            }
		
            $str = substr($str, 0, strlen($str)-1);
            
            $stmt->closeCursor();	
            
            return $str;
        }
    }
    
    // setters public  
    public function setHuisID($huisid)
    {
        $this->huisid = $huisid;
    }
    public function setDeptID($deptid)
    {
        $this->deptid = $deptid;
    }
    public function setTodo($todo)
    {
        $this->todo = $todo;
    } 
    public function setArrayHuisID($arrhuisid)
    {
        $this->arrhuisid = $arrhuisid;
    } 
    // setters private
    private function setLatLng($latlng)
    {
        array_push($this->arrlatlng, $latlng);
    }   
    private function setCode($code)
    {
        array_push($this->arrcode, $code);
    }  
    private function setContent($content)
    {
        array_push($this->arrcontent, $content);
    }
    
    // getters public 
    public function getLatLng()
    {
        return $this->arrlatlng;
    }   
    public function getCode()
    {
        return $this->arrcode;
    }  
    public function getArrayHuisID()
    {
        return $this->arrhuisid;
    } 
    public function getContent()
    {
        return $this->arrcontent;
    }  
}

?>