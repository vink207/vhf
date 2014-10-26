<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 * utilities/Seo.php
 */
 
class Seo
{    
    private $title = 'Vakantiehuis Frankrijk | Libert&eacute; Vakantiehuizen';
    private $description = 'Libert&eacute; biedt betaalbare, karakteristieke vakantiehuizen aan in Normandi&euml;, Bretagne. Vind uw ideale huis! Liberte biedt meer dan 400 vakantiewoningen aan de westkust van Frankrijk aan.';
    private $class = '';
    private $action = 'results';
    private $sidebar_counts = array();
    
    public function __construct()
    {
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function setClass($class)
    {
        $this->class = $class;
    } 
    
    public function setAction($action)
    {
        $this->action = $action;
    }  
    
    public function setSidebarCounts($sidebar_counts)
    {
        $this->sidebar_counts = $sidebar_counts;
    }    
    
    public function getTitle()
    {
        return $this->title;
    } 
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    public function getSidebarCounts()
    {
        return $this->sidebar_counts;
    }
}

?>