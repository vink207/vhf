<?php
/**
 * @author Lambert Portier
 * @copyright 2014
 * model/Selectbox.php
 */
 
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionSearch.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/Datum.php';

error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Selectbox
{
    private $streek = '';
    private $aankomst = '';
    private $aantalpersonen = 0;
    private $weken = 0;
    private $subject = 'huis';
    
	public function __construct()
	{   
   		$this->db = Db::init();
        
        if(class_exists('SessionSearch.php'))
        {
            echo 'debugging';
        }
        
        if(isset($_SESSION['input_search']))
        {
            $this->streek = $_SESSION['input_search']->get_streek();
            $this->aankomst = $_SESSION['input_search']->get_aankomst();
            $this->aantalpersonen = $_SESSION['input_search']->get_aantalpersonen();
            $this->weken = $_SESSION['input_search']->get_weken();
        }
    }
    
    /**
     * selectbox regio
     */
    public function getOptionStreek()
{       $sql = "SELECT streek_id, streek_naam FROM tblStreek ORDER BY streek_naam ASC";																			
    	$stmt = $this->db->query($sql);
																			
    	$options = <<<SELECT
        <option value="">Regio</option>
SELECT;
									
    	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
    	{																
    		extract($row);						
    																			
    		$options .= <<<SELECT
            <option value="s$streek_id"
SELECT;
		
    		// selected bestaat uit letter en nummer
    		$vgl = 's' .$streek_id;
    		
            if ($vgl == $this->streek){
    		  $options .= <<<SELECT
              selected
SELECT;
		    } 
        			
		    $options .= <<<SELECT
            >$streek_naam (xx_streek)</option>
SELECT;
		
    		$sql_ = "SELECT dpt.dept_naam, dpt.dept_id 
    				 FROM tblDepts dpt 
    				 INNER JOIN tblDept_streek ds 
    				 ON ds.dept_id = dpt.dept_id 
    				 WHERE ds.streek_id = '$streek_id'";
    				 
    		$stmt_ = $this->db->query($sql_);
            $aantal_streek = 0;		
    		while ($row_ = $stmt_->fetch())
            {																
    			extract($row_);	
                
                $aantal = 0;
    			
    			if ($this->subject == 'huis')
                {
                    // hoeveel huizen in dept
        			$sql_count = "SELECT COUNT(*) AS aantal 
                                  FROM tblHuizen 
                                  WHERE zichtbaar  = 0 
                                  AND archief  = 0 
                                  GROUP BY dept_id 
                                  HAVING dept_id = '$dept_id'";					
        		
                }
                else // aanbiedingen/last minutes
                {
                    $sql_count = "SELECT COUNT(d.dept_id) AS aantal
        					      FROM tblAanbieding_dev a
        					      INNER JOIN tblHuizen h
        					      ON a.huis_id = h.huis_id
        					      INNER JOIN tblDepts d
        					      ON h.dept_id = d.dept_id
        					      WHERE h.dept_id = '$dept_id' 
                                  AND a.actief = 1 ";
                     if ($this->subject == 'lm') 
                     {
                        $sql_count .= "AND lastminute == 1";
                     }			
        			 
                     $sql_count .= "GROUP BY d.dept_id";	
                }
                
                $result_count = $this->db->query($sql_count);
                $size = $result_count->rowCount();
                if ($size != 0)
                {
                    $row_count = $result_count->fetch();
                    extract($row_count);
                    $str = '('.$aantal.')';
                }
                else
                {
                    $aantal = 0;
                    $str = '';
                }
                
    			$options .= <<<SELECT
                <option value="d$dept_id"
SELECT;
    			// selected bestaat uit letter en nummer
    			$vgl = 'd' .$dept_id;
    						
    			if ($vgl == $this->streek){
    			 $options .= <<<SELECT
                 selected
SELECT;
			    } 
						
    			$options .= <<<SELECT
                >$dept_naam $str</option>            
SELECT;
                $aantal_streek += $aantal;
            }
		    // xx_streek vervangen door $aantal_streek
            $options = str_replace('xx_streek',$aantal_streek, $options);
        }
        
        $stmt->closeCursor();
        return $options;
    }
    
    /**
     * selectbox aankomstdagen
     */     
     public function getOptionAankomst()
     {        
        // instance object datum voor formateren vn timestamp          
        $dt = new Datum();
        
        $weekdag = date('w');
    	
    	$tmp = time();
    	
    	$sec_dag = 24*60*60;
        $sec_week = 7*24*60*60;
    		
    	switch ($weekdag) 
        {		
    		case 1: $tmp += (5 * $sec_dag);break;
    		case 2: $tmp += (4 * $sec_dag);break;
    		case 3: $tmp += (3 * $sec_dag);break;
    		case 4: $tmp += (2 * $sec_dag);break;
    		case 5: $tmp += (1 * $sec_dag);break;
    		case 6: $tmp += (0 * $sec_dag);break;
    		case 0: $tmp += (6 * $sec_dag);break;		
    	}
    
    	$options = <<<SELECT
            <option value="">Aankomstdatum</option>
SELECT;
    	
    	for ($x = 0; $x < 52; $x++)
        {		
    		$datum = date("Y-m-d", $tmp);
            
            // datum formateren 
            $dt->setTmp($tmp);
            // string dag kort / dag / string maand kort / jaar
            $dt->setFormat('dkdmlj');
            $strdatum = $dt->invoke();
            
    		$options .= <<<SELECT
            <option value="$datum"
SELECT;
    		if ($this->aankomst == $datum)
            {
                $options .= <<<SELECT
                 selected="selected"
SELECT;
            }
    		
    		$options .= <<<SELECT
            >$strdatum</option>
SELECT;
    		
    		$tmp += $sec_week;		
    	}
        return $options;
    }
    
    /**
     * selectbox aantal personen
     */     
    public function getOptionPersonen()
    {        
        $options = '<option value="0"';if ($this->aantalpersonen == '0') $options .= ' selected="selected"'; $options .= '>Aantal personen</option>
    				<option value="2-4"'; if ($this->aantalpersonen == '2-4') $options .= ' selected="selected"'; $options .= '>2-4</option>
    				<option value="4-6"'; if ($this->aantalpersonen == '4-6') $options .= ' selected="selected"'; $options .= '>4-6</option>
    				<option value="6-8"'; if ($this->aantalpersonen == '6-8') $options .= ' selected="selected"'; $options .= '>6-8</option>
    			    <option value="8-12"'; if ($this->aantalpersonen == '8-12') $options .= ' selected="selected"'; $options .= '>8-12</option>
    				<option value="12-12"'; if ($this->aantalpersonen == '12-12') $options .= ' selected="selected"'; $options .= '>Meer dan 12 personen</option>';
        return $options;
    }
    
    /**
     * selectbox duur / aantal weken
     */
    public function getOptionWeken()
    {
    	$options = <<<SELECT
                <option value="0">Verblijfsduur</option>
SELECT;
        
        	for ($x = 1; $x < 5; $x++)
        	{
        		$options .= <<<SELECT
                <option value="$x"
SELECT;
        		if ($x == $this->weken)
                {
                    $options .= <<<SELECT
                     selected="selected"
SELECT;
                }
        	
        		$options .= <<<SELECT
                >
SELECT;
        	
        		if ($x != 1)
                {
                    $options .= <<<SELECT
                $x weken
SELECT;
                }
                else
                {
                    $options .= <<<SELECT
                $x week
SELECT;
                }
        															
        		$options .= <<<SELECT
                </option>
SELECT;
            }
        
        return $options;
    }
}

?>