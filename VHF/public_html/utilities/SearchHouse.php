<?php
include_once('Db.php');
include_once ('SessionSearch.php');
include_once ('Datum.php');

/**
 * @author Lambert Portier
 * @copyright 2014
 * utilities/SearchHouse.php
 */
 
class SearchHouse {	
    
	protected $db;
    private $streek = ''; // dX of sX
    private $aantalpersonen = 0;
    private $aankomst = '';
    private $weken = 0; 
    private $slaapkamer = '';
    private $afstandzee = '';
    private $checkbox = array();
    private $subject = '';
    private $huisID = array();
    private $content = '';
    private $msg;
    private $searchfilters;
	
	public function __construct()
	{
		$this->db = Db::init();
        
        /**
         * getters SessionSearch
         */   
         $this->streek = $_SESSION['input_search']->get_streek(); 
    	 $this->aantalpersonen = $_SESSION['input_search']->get_aantalpersonen();
    	 $this->aankomst = $_SESSION['input_search']->get_aankomst();
    	 $this->weken = $_SESSION['input_search']->get_weken(); 
    	 $this->slaapkamer = $_SESSION['input_search']->get_slaapkamer();
    	 $this->afstandzee = $_SESSION['input_search']->get_afstandzee();
    	 $this->arrcheckbox = $_SESSION['input_search']->get_arrcheckbox();
         $this->count_checkbox = count($this->arrcheckbox);
         
         // filters opslaan in string
         $this->getFilters();
         
         // zoekactie
         $this->invoke();
    }
    
    /**
     * opbouw string met zoektermen voor weergaven result
     */
    private function getFilters()
    {        
        $this->searchfilters = '';
       
        if ($this->streek != '')
        {
            $id = substr($this->streek, 1);
            
            if (substr($this->streek, 0, 1) == 's')
            {
                $this->streek_id = $id;
                $sql = "SELECT streek_naam AS name FROM tblStreek WHERE streek_id = '$id'";
            }                        
            else
            {
                $this->dept_id = $id;
                $sql = "SELECT dpt.dept_naam AS name FROM tblDepts dpt WHERE dpt.dept_id = '$id'";
            }  
          
            $komma = 1;
        }
        
        if (isset($komma))
        {
            $name = $this->get_name($sql);
            $this->searchfilters .= $name;
        }
        
        /** aantal personen */
        if ($this->aantalpersonen != '' && $this->aantalpersonen != 0)
        {                
            if (isset($komma))
            {
                $this->searchfilters .= ', ';
            } 
            if ($this->aantalpersonen != '12-12')
            {
                $this->searchfilters .= $this->aantalpersonen . ' pers.';
            }                
            else
            {
                $this->searchfilters .= 'vanaf 12 pers.';
            }                    
            $komma = 1;  
        }
        
        /** aankomst */
        if ($this->aankomst != '')
        {
            $dt = new Datum();
            $tmp = strtotime($this->aankomst);
            $dt->setTmp($tmp);
            $datum = $dt->setFormat('dkdmlj');
            if (isset($komma))
            {
                $this->searchfilters .= ', ';
            } 
            $this->searchfilters .= $datum.' ';
            
            if($this->weken == 1 || $this->weken == 0)
            {
                $this->searchfilters .= ', 1 week';
            }
            else 
            {
               $this->searchfilters .= ', '.$this->weken.' weken'; 
            }
            $komma = 1; 
        }         
        
        /** checkboxen */
        if ($this->count_checkbox != 0)
        {
            if (isset($komma)) $this->searchfilters .= ', ';
            
            for ($x = 0; $x < $this->arrcheckbox; $x++)
            {
                $str = $this->formatFac($this->arrcheckbox[$x]);
                $this->searchfilters .= $str.', ';
            }
            $komma = 1; 
            // laatste komma weg
            $this->searchfilters = substr($this->searchfilters, 0, strlen($this->searchfilters)-2); 
        }       
        $this->searchfilters = 'Zoekfilters: '.$this->searchfilters;
    }
    
    /**
     * naam streek of regio
     */
    private function get_name($sql)
    {
        $stmt = $this->db->query($sql);
	    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $name = ucfirst($name);
        return $name; 
    }
    
    /**
     * aanpassen namen checkboxen voor weergave zoekfilters
     */
    private function formatFac($str)
    {
        $str = str_replace('_', ' ',$str);
        $str = str_replace('minder', 'minder-',$str);
        return $str;
    }
    
    private function invoke()
    {
    	// base
    	$this->base = 'http://'.$_SERVER['HTTP_HOST'];
        
        // streek of departement sX / dX
        if ($this->streek != '')
        {
            $id = substr($this->streek, 1);
                    
            if (substr($this->streek, 0, 1) == 's')
            {
                $this->streek_id = $id;
            }                        
            else
            {
                $this->dept_id = $id;
            }  
    	}
        
    	// aankomst omzetten naar timestamp
    	if ($this->aankomst != '')
        {
            $this->tmp_van = strtotime($this->aankomst);
        }    		
    	else
        {
            $this->tmp_van = 0;
        }	
    	
    	// opslag zoektermen
    	$this->storeSearchItems();
	
		/**
		 * sliders/selectboxen
		 * aantal personen
         * slaapkamers
         * afstandzee
         * 
		 */
		$this->get_select_box();
	
		/**
		 * huizen bij streek/dept
		 */
		if ($this->streek != '')
		{
            $this->get_dept();
        }
		else
        {
            $this->get_array_huis_id();
        }
    	
    	/**
    	 * controle beschikbaarheid
    	 */
    	if (isset($this->huisID))
    	{
    		$z = count($this->huisID);
    		if ($z != 0)
    		{
    			if ($this->aankomst != '')
    			{
    				for ($x = 0; $x < $z; $x++)
    				{
    				    $this->huis_id = $this->huisID[$x];
    					$this->beschikbaarheid();
    					if ($this->bool == 'true') // key verwijderen
    					{
    					   // val verwijderen, laat een lege plek achter
    					   unset($this->huisID[$x]);
    				    }
    				}
   				}
    	
				/** array_values; lege plekken in array verwijderen */
				$this->huisID = array_values($this->huisID);
				$this->number = count($this->huisID);
                
                /** vanaf prijs */
                $this->getPrijs();            
            }
	   }
           		 
   		/**
   		* query genereren die voor uiteindelijk resultaat zorgt
   		*/
   		if ($this->number != 0)
   		{
   		   $this->mk_result_sql();
   		}
   		else
   		{
   		   $this->number = 0;
   		}
   		 
   		/**
   		* msg aantal gevonden huizen
   		*/
   		$this->getMsg();
    }
    
    /**
     * opslag zoekitems
     */
    private function storeSearchItems() 
    {
        $sql = 'INSERT INTO searchitems ';
        $fields = '(streek, aantalpersonen, aankomst, weken, slaapkamer, afstandzee,';
        $values = '(:streek, :aantalpersonen, :aankomst, :weken, :slaapkamer, :afstandzee,';
        $array = array(':streek' => $this->streek,
                       ':aantalpersonen' => $this->aantalpersonen,
                       ':aankomst' => '$this->aankomst',
                       ':weken' => $this->weken,  
                       ':slaapkamer' => '$this->slaapkamer',
                       ':afstandzee' => '$this->afstandzee');
        
        // checked checkboxen toevoegen aan query string
        if ($this->count_checkbox != 0)
        {    
            for ($x = 0; $x < $this->count_checkbox; $x++)
            {
                $field = $this->arrcheckbox[$x];
                $fields .= $field.',';
                $values .= ':'.$field.',';
                $array[$field] = 1;
            }
        }
        // laatste komma verwijderen uit $fields en $values
        $fields = substr($fields, 0, strlen($fields)-1);
        $values = substr($values, 0, strlen($values)-1);
        
        // sluithaakjes toevoegen aan $fields en $values
        $fields .= ')';
        $values .= ')';
        
        // string query opbouwen
        $sql .= $fields. ' VALUES '. $values; 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($array);
    }
    
    /**
    * selectboxen
    */
    private function get_select_box()
    {
        $this->sql_select_box = '';	
        
        /** aantal personen	*/
        if ($this->aantalpersonen != 0)
        {
            $pos = strpos($this->aantalpersonen, "-");	
            $pos2 = $pos + 1;
            $this->minpers = substr($this->aantalpersonen, 0, $pos);
            $this->maxpers = substr($this->aantalpersonen, $pos2);
            // vanaf 12 pers. is de waarde van min gelijk aan max
            if ($this->minpers != $this->maxpers)
            {
                $this->sql_select_box .= "AND hz.aantal1 >= '$this->minpers' AND hz.aantal2 <= '$this->maxpers' ";
            }
            else
            {
                $this->sql_select_box .= "AND ( 
					(hz.aantal1 <= '$this->minpers' AND hz.aantal2 >= '$this->maxpers') 
						OR
					(hz.aantal1 >= '$this->minpers' AND hz.aantal2 >= '$this->maxpers')
					)";
		    }
        }
        /** slaapkamers	*/
        if ($this->slaapkamer != '') 
        {
            $min_slaapkamer = substr($this->slaapkamer, 0, strpos($this->slaapkamer,'-'));
            $max_slaapkamer = substr($this->slaapkamer, strpos($this->slaapkamer,'-')+1);
            $this->sql_select_box .= " AND hz.slaapkamers BETWEEN $min_slaapkamer AND $max_slaapkamer";
        }
        /** afstand zee	*/
        if ($this->afstandzee != '') 
        {		 	
            $min_afstandzee = substr($this->afstandzee, 0, strpos($this->afstandzee,'-'));
            $max_afstandzee = substr($this->afstandzee, strpos($this->afstandzee,'-')+1);
            $this->sql_select_box .= " AND hz.afstandz BETWEEN $min_afstandzee AND $max_afstandzee";
        }
    }
    
    /**
    * dept + huizen selecteren voor streek
    */
    private function get_dept()
    {
	   /** 
	   * variable streek staat in selectbox streek
       * twee varianten bijv. d2 en s2: resp dept_id = 2, streek_id = 2
	   */	
       if (isset($this->streek_id))
       {            
            $sql = "SELECT dept_id FROM tblDept_streek_dev WHERE streek_id = '$this->streek_id'";
            $stmt = $this->db->query($sql);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $this->deptID[] = $dept_id;
            }     
            $stmt->closeCursor(); 
        }	
        else
        {
            $this->deptID[] = $this->dept_id;
        }
        
        /** huisID opslaan in array */
        $sql = "SELECT hz.huis_id FROM tblHuizen_dev hz ";
        
        /** checkboxen sidebar */
        if ($this->count_checkbox != 0)
        {
            $sql .= " INNER JOIN sidebar_huis sb ON hz.huis_id = sb.huis_id ";
        }  
        
        $sql .= "WHERE hz.dept_id IN (";
        $z = count($this->deptID);
        for ($x = 0; $x < $z; $x++)
        {
            $sql .= $this->deptID[$x]. ',';
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        $sql .= ") AND hz.archief = '0' AND hz.zichtbaar = '0' ";
        
        /** checkboxen sidebar */
        if ($this->count_checkbox != 0)
        {
            for ($x = 0; $x < $this->count_checkbox; $x++)
            {
                $fieldname = $this->arrcheckbox[$x];
                $sql .= " AND sb.$fieldname = '1'";
            }
        }
	    
        /** selectboxen m.u.v. prijs en beschikbaarheid */
        if ($this->sql_select_box != '')
        {
            $sql .= $this->sql_select_box. ' ';
        }
        
        $stmt = $this->db->query($sql);
            
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);
            array_push($this->huisID, $huis_id);
        }	
        $stmt->closeCursor(); 
    }
    
    /**
    * huis-id selecteren in geval er nog geen array huisID is aangemaakt
    */
    private function get_array_huis_id()
    {
        $sql = "SELECT hz.huis_id FROM tblHuizen_dev hz ";
        
        /** faciliteiten */
        if ($this->count_checkbox != 0)
        {
            $sql .= " INNER JOIN sidebar_huis sb ON hz.huis_id = sb.huis_id ";
        } 
        
        /** selectboxen */            
        if ($this->sql_select_box != '')
        {
            $sql .= $this->sql_select_box. ' ';
        }
        
        /** checkboxen sidebar */
        if ($this->count_checkbox != 0)
        {
            for ($x = 0; $x < $this->count_checkbox; $x++)
            {
                $fieldname = $this->arrcheckbox[$x];
                $sql .= " AND sb.$fieldname = '1'";
            }
        }
        
        /** zichtbaar en niet in archief */
        $sql .= " AND hz.archief = 0 AND hz.zichtbaar = 0";
    
        /** eerste AND in sql vervangen door WHERE */
        $pos = strpos($sql, 'AND');
        if ($pos !== FALSE) 
        {
            $sql = substr_replace($sql, 'WHERE', $pos, 3);
        }
        
        $stmt = $this->db->query($sql);
        $size = $stmt->rowCount();
            
        if ($size != 0)
        {            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                extract($row);		
                array_push($this->huisID, $huis_id);
            }
            $stmt->closeCursor();
        }
    }
    
    /**
    * beschikbaarheid
    */
    private function beschikbaarheid()
    {	
        $sec_week = 24*60*60*7;  
        if ($this->weken == 0) 
        {
            $this->weken = 1;
        }   
        $this->tmp_tot = $this->tmp_van + ($this->weken * $sec_week);
        $jaar = date("Y", $this->tmp_van);    
        $vorig_jaar = $jaar - 1;

       $this->bool = "false";       
	   
       $sql = "SELECT UNIX_TIMESTAMP(aankomst) AS tmp_tbl_aankomst, UNIX_TIMESTAMP(vertrek) AS tmp_tbl_vertrek 
                FROM tblBoeking 
                WHERE huis_id = '$this->huis_id' 
                AND annulering = '0' 
                AND YEAR(aankomst) >= '$jaar' 
                AND status = '1'";

        $stmt = $this->db->query($sql);	
        $size = $stmt->rowCount();
		
        if ($size != 0) 
        {				
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {	
                extract($row);
                
                if ($this->tmp_van == $this->tmp_tot) $this->bool = "true";
                if($this->tmp_tot < $this->tmp_van) $this->bool = "true";                
                
                if ($this->tmp_van <= $tmp_tbl_aankomst and $this->tmp_tot >= $tmp_tbl_vertrek)
                {
                    $this->bool = "true";
                }                     
		 				 
                for($i = $this->tmp_van; $i < $this->tmp_tot; $i += 86400)
                {				
                    if ($i > $tmp_tbl_aankomst and $i < $tmp_tbl_vertrek)
                    {
                        $this->bool = "true";
                    }                         
                }
            }		
            $stmt->closeCursor();
        } 
     }

    /**
    * vanaf prijs voor ieder huis
    */
    private function getPrijs()
    {	
        // m.i.v. 29 november de prijzen van het volgend jaar tonen
        // m.i.v. 1 januari de prijzen van het huidig jaar tonen
        $jaar = date("Y");
        $volgend_jaar = $jaar+1;
        $tmp_nov = mktime(0,0,1,11,28,$jaar);
        $tmp_jan = mktime(0,0,1,1,1,$volgend_jaar);
        $tmp_now = time();
        if ($tmp_now < $tmp_nov) 
        {
            $tbl_jaar = $jaar;
        }            
        elseif ($tmp_now > $tmp_nov && $tmp_now < $tmp_jan) 
        {
            $tbl_jaar = $volgend_jaar;
        }                    
        else
        {
            $tbl_jaar = $jaar;
        } 
        $tblPrijs = 'tblPrijzen' . $tbl_jaar;
        
        $stmt = $this->db->prepare( "SELECT MIN(prijs) AS prijs FROM $tblPrijs WHERE huis_id = :huis_id AND prijs != -2 AND prijs != -1 GROUP BY huis_id" );
        foreach( $this->huisID as $huis_id ) 
        {
            $stmt->execute( array( ':huis_id' => $huis_id ) );
            $row = $stmt->fetch( PDO::FETCH_ASSOC );
            extract($row);
            $stmt->closeCursor();
            $this->arrprijs[$huis_id][] = $prijs;
        }
    }
    /**
     * boodschap over zoekresultaat
     */
    private function getMsg()
    {        
        if ($this->number != 0) // huizen gevonden
        {
            if ($this->number == 1)
            {
	           $this->msg = $this->number . ' vakantiehuis gevonden ';
            }
            elseif ($this->number > 1)
            {
	           $this->msg = $this->number . ' vakantiehuizen gevonden ';
            }
        }
        else
        {
	       $this->msg =  'Geen vakantiehuizen gevonden ';
        }
        
        if ($this->streek != '')
        {        
            if (isset($this->dept_id))
            {
                $sql = "SELECT dp.dept_naam AS dept_naam, st.streek_naam AS streek_naam  
                        FROM tblDepts_dev dp 
                        INNER JOIN tblDept_streek_dev ds 
                        ON dp.dept_id = ds.dept_id 
                        INNER JOIN tblStreek_dev st 
                        ON ds.streek_id = st.streek_id 
                        WHERE dp.dept_id = $this->dept_id";
            }
            else
            {
                $sql = "SELECT streek_naam AS streek_naam FROM tblStreek_dev WHERE streek_id = $this->streek_id";
            }
        
            $this->msg .= 'in ';
            
            $stmt = $this->db->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            if (isset($this->dept_id))
            {
                $dept_naam = html_entity_decode($dept_naam);
                $streek_naam = html_entity_decode($streek_naam);
                $dept_naam = utf8_decode($dept_naam);
                $streek_naam = utf8_decode($streek_naam);
                $this->msg .= '<span style="text-transform:uppercase;">'. $dept_naam .' ('.$streek_naam.')</span>';
            }
            else
            {
                $streek_naam = html_entity_decode($streek_naam);
                $streek_naam = utf8_decode($streek_naam);
                $this->msg .= '<span style="text-transform:uppercase;">'. $streek_naam . '</span>';
            }
            
            $stmt->closeCursor();
        }
    }
    
    /**
    * selectie data tblHuizen_dev, thumb	
    */	
    private function mk_result_sql()
    {
        $this->result_sql = "SELECT hz.code, hz.huis_id, hz.latlng, hz.plaats, hz.slaapkamers, hz.aantal1, hz.aantal2, hz.afstandz, hz.huisdier, hz.beschrijving_result, hz.plaats,
                                    dpt.dept_id AS deptID, dpt.dept_naam, dpt.depturl,
                                    str.streek_naam, str.streek_id AS streekID, str.streekurl,
                                    sb.internet, sb.huisdier_toegestaan   
                            FROM tblHuizen_dev hz 
                            INNER JOIN tblDepts_dev dpt 
                            ON hz.dept_id = dpt.dept_id 
                            INNER JOIN tblDept_streek_dev dpt_str 
                            ON hz.dept_id = dpt_str.dept_id 
                            INNER JOIN tblStreek_dev str 
                            ON dpt_str.streek_id = str.streek_id 
                            INNER JOIN sidebar_huis sb 
                            ON hz.huis_id = sb.huis_id 
                            WHERE hz.huis_id IN (";
       
       if (isset($this->huisID))
       {
            if ($this->number != 0)
            {		
                for ($x = 0; $x < $this->number; $x++)
                {	
                    $this->result_sql .= $this->huisID[$x];		
                    $this->result_sql .= ',';
                }
	
                $this->result_sql = substr($this->result_sql, 0, strlen($this->result_sql) - 1);	
                $this->result_sql .= ") AND hz.archief = '0' AND hz.zichtbaar = '0' ORDER BY dpt.dept_naam, hz.label ASC";
            }
        }
        else
        {
            $this->number = 0;
        }
    }     
    
    /**
     * selectie content html
     */
    public function getResultsHTML()
    {
        $this->content .= <<<EOD
EOD;

        // query data search-result
        if ($this->number != 0)
        {
            $stmt = $this->db->query($this->result_sql);
        
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                extract($row);
            
	           // URL naar details vakantiehuis
	           $url_vakantiehuis = '/vakantiehuis-'.$streekurl.'/'.$depturl.'-'.$code;
		
	           // FOTO	
	           $sql = "SELECT foto 
                       FROM tblFotos 
                       WHERE huis_id = $huis_id 
                       AND unvisible = 0
                       ORDER BY volgorde ASC LIMIT 0, 1";
	           
               $stmt_ft = $this->db->query($sql);
	
	           $size = $stmt_ft->rowCount();	
	          
               if ($size != 0)
	           {
		          $row_ft = $stmt_ft->fetch(PDO::FETCH_ASSOC);
		          extract($row_ft);	
		          $stmt_ft->closeCursor();
	           }
	           else
	           {
		          $foto = "no_img_klst.jpg";
	           }						
	           
               // aantal personen
	           if ($aantal2 != $aantal1) 
               {
                  $pers = $aantal1 . ' - ' . $aantal2; 
               }
	           else  
               {
                  $pers = $aantal1;
               }
        
               // vanaf prijs
               $prijs = $this->arrprijs[$huis_id][0];
        
//                $streek_naam = html_entity_decode($streek_naam);
                $streek_naam = utf8_decode($streek_naam);
	           // item huis met info
	           $this->content .= <<<EOD
                <div>
                    <div>

                        $plaats ($streek_naam)<br />
                        $code<br />
                        (vanaf $prijs,- p/w) <br />
                        $beschrijving_result <br />
                        <a href="$url_vakantiehuis"> >> lees verder</a> <br />
                        <ul>
                            <li>- $pers personen</li>
                            <li>- $slaapkamers slaapkamers</li>
                        </ul> 
EOD;
                // huisdieren toegestaan
                if ($internet == 1)
                {
                    $this->content .= <<<EOD
                        <ul>
                            <li>- internet</li>
                        </ul>
EOD;
                }
                // internet
                if ($huisdier_toegestaan == 1)
                {
                    $this->content .= <<<EOD
                        <ul>
                            <li>- huisdieren</li>
                            <li>toegestaan</li>
                        </ul>
EOD;
                }
                
                // data voor GoogleMaps
                
                $this->content .= <<<EOD
                    </div>
                    <div>
                        <img src="http://www.vakantiehuisfrankrijk.nl/huis_img/$foto" border="0"  title="Vakantiehuis Frankrijk $plaats, $streek_naam" alt="Vakantiehuis Frankrijk $plaats, $streek_naam" />
                        <a href="$url_vakantiehuis">Bekijk</a>
                        <a href="$url_vakantiehuis">Boeken</a>                    
                    </div>
                  </div>
EOD;
            }
            $stmt->closeCursor();
        }
    }
    /**
     * return data json-format
     */
    public function getResponseJSON()
    {         
        $this->content = utf8_encode($this->content);
        $this->content = htmlentities($this->content);
        $stringhuisid = implode(',', $this->huisID);
        $this->response = array('search_count'=>$this->number,'content'=>$this->content, 'filters'=>$this->searchfilters, 'msg'=>$this->msg, 'stringhuisid'=>$stringhuisid);
        return json_encode($this->response);
    }
    
    
    /**
     * return data html
     */
    public function getResponseHTML()
    { 
        $this->content = utf8_encode($this->content);
        $stringhuisid = implode(',', $this->huisID);
        $this->response = array('search_count'=>$this->number, 'content'=>$this->content, 'filters'=>$this->searchfilters, 'msg'=>$this->msg, 'stringhuisid'=>$stringhuisid);
        return $this->response;
    }
} 




?>