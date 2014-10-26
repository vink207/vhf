<?php
class SessionSearch
{
	// selectboxen header
    private $streek = ''; 
	private $aantalpersonen = 0;
	private $aankomst = '';
	private $weken = 0;
    
    // sliders sidebar
	private $slaapkamer = '';
	private $afstandzee = '';
	private $prijs = '';
    
    // cheeckboxen sidebar
	private $arrcheckbox = array();
    
    public function __construct(){        
    }
    
    /**
     * setters
     * var search results
     */
    public function set_streek($streek){
        $this->streek = $streek;
    }
    public function set_aantalpersonen($aantalpersonen){
        $this->aantalpersonen = $aantalpersonen;
    }
    public function set_aankomst($aankomst){
        $this->aankomst = $aankomst;
    }
    public function set_weken($weken){
        $this->weken = $weken;
    }
    public function set_slaapkamer($slaapkamer){
        $this->slaapkamer = $slaapkamer;
    }
    public function set_afstandzee($afstandzee){
        $this->afstandzee = $afstandzee;
    }
    public function set_prijs($prijs){
        $this->prijs = $prijs;
    }
    public function set_arrcheckbox($arrcheckbox){
        $this->arrcheckbox = $arrcheckbox;
    }
    
    /**
     * getters
     * var search results
    */  
	 public function get_streek() {
		   return $this->streek;
	 }
	 public function get_aantalpersonen() {
		   return $this->aantalpersonen;
	 }	    
	 public function get_aankomst() {
		   return $this->aankomst;
	 }		
	 public function get_weken() {
		   return $this->weken;
	 }		
	 public function get_slaapkamer() {
		   return $this->slaapkamer;
	 }		
	 public function get_afstandzee() {
		   return $this->afstandzee;
	 }		
	 public function get_prijs() {
		   return $this->prijs;
	 }
    public function get_arrcheckbox(){
        return $this->arrcheckbox;
    }
}


?>
