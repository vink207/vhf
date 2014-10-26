<?php
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU Library General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Copyright (C) 2001 by Edward Rudd <eddie@omegaware.com>

class SessionBoeken 
{	
	// KLANT
    private  $aanhef;
	private  $voorletters;
	private  $tussenvoegsel;
	private  $achternaam;
	private  $hh_gb_dag;
	private  $hh_gb_maand;
	private  $hh_gb_jaar;
	private  $adres;
	private  $nummer;
	private  $postcode;
	private  $plaats;
	private  $land;
	private  $email;
	private  $tel_prive;
	private  $gsm;
    private  $opmerking;
	
	// MEDEHUURDERS
    private  $arr_naam;
	private  $arr_vl;
	private  $arr_tv;
	private  $arr_geslacht;
	private  $arr_dag;
	private  $arr_maand;
	private  $arr_jaard;
	
    // BOEKING / DETAILS HUIS
	private  $huis_id;
	private  $code;
    private  $foto;
    private  $huisplaats;
    private  $huisdier;
	private  $tmp_a;
	private  $tmp_v;
    private  $verblijfsduur;
    private  $aantal2;
    
    // TEKST NIET OPTIONELE BIJKOMENDE KOSTEN
    public $arr_bkk_niet_opt;
    
    // TEKST OPTIONELE BIJKOMENDE KOSTEN
    public $arr_bkk_opt;
    
    // ARRAY DATA
    public $data;
    
    
    public function setItem($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    public function getItem($name)
    {
        return $this->data[$name];
    }
    
    // array met items ophalen
    public function deleteItem($name)
    {
        unset($this->data[$name]);
    }
	
	public function AddKlant($aanhef, $voorletters, $tussenvoegsel, $achternaam, $hh_gb_dag, $hh_gb_maand, $hh_gb_jaar, $adres, $nummer, $postcode, $plaats, $land, $email, $tel_prive, $gsm, $opmerking) 
    {      	
		$this->achternaam = $achternaam;
		$this->voorletters = $voorletters;
		$this->tussenvoegsel = $tussenvoegsel;
		$this->aanhef = $aanhef;
		$this->hh_gb_dag = $hh_gb_dag;
		$this->hh_gb_maand = $hh_gb_maand;
		$this->hh_gb_jaar = $hh_gb_jaar;
		$this->adres = $adres;
		$this->nummer = $nummer;
		$this->postcode = $postcode;
		$this->plaats = $plaats;
		$this->land = $land;
		$this->tel_prive = $tel_prive;
		$this->gsm = $gsm;
		$this->email = $email;
		$this->opmerking = $opmerking;
	}
	
	public function AddBoeking($huis_id, $code, $tmp_a, $tmp_v, $aankomst, $vertrek, $verblijfsduur, $foto, $huisplaats, $huisdier, $aantal2)
    {
		$this->huis_id = $huis_id;
		$this->code = $code;
		$this->tmp_a = $tmp_a;
		$this->tmp_v = $tmp_v;
		$this->aankomst = $aankomst;
		$this->vertrek = $vertrek;
		$this->verblijfsduur = $verblijfsduur;
		$this->foto = $foto;
		$this->huisplaats = $huisplaats; 
		$this->huisdier = $huisdier;  // toegestaan ja/nee
        $this->aantal2 = $aantal2; 
	}	
	
	public function AddMedehuurder($arr_naam, $arr_tv, $arr_vl, $arr_geslacht, $arr_dag, $arr_maand, $arr_jaar)
    {			
		$this->arr_naam = $arr_naam;
		$this->arr_tv = $arr_tv;
		$this->arr_vl = $arr_vl;
		$this->arr_geslacht = $arr_geslacht;
		$this->arr_dag = $arr_dag;
		$this->arr_maand = $arr_maand;
		$this->arr_jaar = $arr_jaar;
	}
    
    /**
     * teksten die bij de formuliervelden horen die na activering in rechterkolom (prijsopgave) worden getoond
     * opslag in array
     */
    public function AddBkkOptioneel(array $arr_bkk_opt)
    { 
        $this->arr_bkk_opt = $arr_bkk_opt;
    }
    
    /**
     * info bkk die niet optioneel kunnen zijn bij gegeven huis
     */
    public function AddBkkNietOptioneel(array $arr_bkk_niet_opt)
    {   
        $this->arr_bkk_niet_opt = $arr_bkk_niet_opt;
    }
		
	// ophalen data boeking 
	public function get_tmp_a() {
	 	   return $this->tmp_a;
	}
	public function get_tmp_v() {
	 	   return $this->tmp_v;
	}
	public function get_aankomst() {
	 	   return $this->aankomst;
	}
	public function get_vertrek() {
	 	   return $this->vertrek;
	}
	public function get_huis_id() {
	 	   return $this->huis_id;
	}
	public function get_code() {
	 	   return $this->code;
	}
	public function get_foto() {
	 	   return $this->foto;
	}
	public function get_verblijfsduur() {
	 	   return $this->verblijfsduur;
	}
	public function get_huisplaats() {
	 	   return $this->huisplaats;
	}
	public function get_huisdier() {
	 	   return $this->huisdier;
	} 
	public function get_aantal2() {
	 	   return $this->aantal2;
	} 
	 
	 //ophalen data klant   
	 public function get_aanhef() {
		   return $this->aanhef;
	 }   
	 public function get_voorletters() {
		   return $this->voorletters;
	 }  
	 public function get_tussenvoegsel() {
		   return $this->tussenvoegsel;
	 }	
	 public function get_achternaam() {
		   return $this->achternaam;
	 } 
	 public function get_hh_gb_dag() {
		   return $this->hh_gb_dag;
	 } 
	 public function get_hh_gb_maand() {
		   return $this->hh_gb_maand;
	 } 
	 public function get_hh_gb_jaar() {
		   return $this->hh_gb_jaar;
	 } 
	 public function get_adres() {
		   return $this->adres;
	 }		
	 public function get_nummer() {
		   return $this->nummer;
	 }		
	 public function get_postcode() {
		   return $this->postcode;
	 }
	 public function get_plaats() {
		   return $this->plaats;
	 }		
	 public function get_land() {
		   return $this->land;
	 }	
	 public function get_tel_prive() {
		   return $this->tel_prive;
	 }			
	 public function get_email() {
		   return $this->email;
	 }			
	 public function get_gsm() {
		   return $this->gsm;
	 }
	 public function get_opmerking() {
		   return $this->opmerking;
	 }
     
     // MEDEHUURDERS
	 public function get_arr_naam() {
		   return $this->arr_naam;
	 }
	 public function get_arr_vl() {
		   return $this->arr_vl;
	 }
	 public function get_arr_tv() {
		   return $this->arr_tv;
	 }
	 public function get_arr_geslacht() {
		   return $this->arr_geslacht;
	 }
	 public function get_arr_dag() {
		   return $this->arr_dag;
	 }
	 public function get_arr_maand() {
		   return $this->arr_maand;
	 }
	 public function get_arr_jaar() {
		   return $this->arr_jaar;
	 }
     
     // data niet-optionele kosten
     public function get_arr_bkk_niet_opt()
     {
        return $this->arr_bkk_niet_opt;
     }
     
     // data optionele kosten
     public function get_arr_bkk_opt()
     {
        return $this->arr_bkk_opt;
     }
}


?>
