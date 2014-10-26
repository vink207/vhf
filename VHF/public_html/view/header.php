<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 * view/header.php
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionSearch.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/Selectbox.php';

// coordinaten Google Maps
require_once $_SERVER['DOCUMENT_ROOT'].'/model/GMaps.php';
$gm = new GMaps();
$gm->setTodo(1);
$gm->invoke();
$latlng = $gm->getLatLng();
$arrcode = $gm->getCode();
$arrcontent = $gm->getContent();
$zindex = 1;
$zoom = 10;
$div = 'map-canvas';
$max_lat = 0;
$max_long = 0;
foreach($arrcode AS $key => $huiscode)
{
    $coord = $latlng[$key];
    $arrcoord = explode(',',$coord);  
    $lat = floatval($arrcoord[0]);   
    $long = floatval($arrcoord[1]);    
    $info = $arrcontent[$key]; 
    $houses[] = array($huiscode, $lat, $long, $info, $zindex);
    $max_lat += $lat;
    $max_long += $long;
    $zindex++;
}
$jsonhouses = json_encode($houses);

// center kaart
$centery = $max_long/($zindex - 1);
$centerx = $max_lat/($zindex - 1);

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

$sb = new Selectbox();
$optionstreek = $sb->getOptionStreek();
$optionaankomst = $sb->getOptionAankomst();
$optionaantalpersonen = $sb->getOptionPersonen();
$optionweken = $sb->getOptionWeken();
$base = $_SERVER['HTTP_HOST'];

$title = $seo->getTitle();
$description = $seo->getDescription();
$action = $seo->getAction();
$class = $seo->getClass();

$ru = $_SERVER['REQUEST_URI'];
$array = explode('/', $ru);
$item = $array[1];
if ($item == '')
{
    $bodyclass = 'home';
}
else
{
    $bodyclass = $item;
    if(strpos($item,'-') !== FALSE)
    {
        $bodyclass = substr($item, 0, strpos($item,'-'));
    }    
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $title; ?></title>
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://<?php echo $base; ?>/js/jqtransformplugin/jqtransform.css" type="text/css" media="all" />    
    <link rel="stylesheet" href="http://<?php echo $base; ?>/css/style.css">
    <link rel="stylesheet" href="http://<?php echo $base; ?>/css/colorbox.css" />
    <link href='http://fonts.googleapis.com/css?family=Dosis:300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <script type="text/javascript">var ABSROOT = "<?php echo $absroot; ?>";</script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1CLzRMUYjfzPHhP5--U8SsJ3f7JfSG4k"></script>
    
</head>
<body class="<?php echo $bodyclass; ?>">
<div id="header-container" class="container">
    <div id="header-top" class="inner-container">
        <a href="/" class="logo" title="Liberté vakantiehuizen "><img src="http://<?php echo $base; ?>/images/logo.png" alt="Libert&eacute; vakantiehuizen Frankrijk" /></a>
        <div id="navsearch">
        <nav>
            <ul>
                <li><a href="/" title="Home">home</a>|</li>
                <li><a href="#" title="over ons">over ons</a>|</li>
                <li><a href="#" title="contact">contact</a>|</li>
                <li><a href="#" title="veelgestelde vragen">veelgestelde vragen</a>|</li>
                <li><a href="#" title="inloggen">inloggen</a></li>
            </ul>
        </nav>
        <form id="frm-top" name="frm-top" class="jqtransformdone">
            <input type="text" class="text" name="search" onfocus="if(this.value == 'Zoek op trefwoord, regio of code') { this.value = ''; }" value="Zoek op trefwoord, regio of code" />
            <input type="submit" class="submit" value="&nbsp;" />
        </form>
        </div><!-- /navsearch -->
        <span class="meerinfo">Voor meer informatie bel +31(0)24 373 99 86 of mail <strong>info@vakantiehuisfrankrijk.nl</strong></span>
    </div><!-- /header-top-->
</div><!-- /header-container -->
<div id="header-search-formulier" class="container">
    <div class="select-container">
        <div id="search-container">
              <h3>Zoek een vakantiehuis</h3>
              <form class="<?php echo $class; ?>" method="POST" action="http://<?php echo $base; ?>/<?php echo $action; ?>" id="frm-header" name="frm-header">
                <select name="streek">
                    <?php echo $optionstreek; ?>
                </select> 
                <select name="aankomst">
                    <?php echo $optionaankomst; ?>
                </select> 
          		<select name="aantalpersonen">
                    <?php echo $optionaantalpersonen; ?>
                </select>                  
          		<select name="weken">
                    <?php echo $optionweken; ?>
                </select>  
              <br style="clear: both;" />               
          </div>     
          <div id="search-balk">
            <div>
                <input type="submit" name="submit" id="headersearch" value="ZOEKEN" /></form>
                <a href="#" title="Zoek op kaart" id="zoekopkaart">Of zoek op de <span>kaart</span></a>
                <ul>
                    <li><a href="#" title="Vakantiehuis Frankrijk blog"><img src="http://<?php echo $base; ?>/images/blog.jpg" alt="Blog" /></a></li>
                    <li><a href="#" title="Vakantiehuis Frankrijk Twitter"><img src="http://<?php echo $base; ?>/images/twitter.jpg" alt="Twitter" /></a></li>
                    <li><a href="#" title="Vakantiehuis Frankrijk Facebook"><img src="http://<?php echo $base; ?>/images/facebook.jpg" alt="Facebook" /></a></li>
                </ul>              
            </div>
          </div>       
    </div><!--/select-container-->
</div><!-- /header-search -->
<div id="header-search-kaart" class="container">
    
            <div id="map-canvas"></div>     
            <div id="canvas-search-balk">
                <div>
                    <a href="#" title="Zoek op formulier" id="zoekopformulier">terug naar <span>zoeken</span></a>
                    <ul>
                        <li><a href="#" title="Vakantiehuis Frankrijk blog"><img src="http://<?php echo $base; ?>/images/blog.jpg" alt="Blog" /></a></li>
                        <li><a href="#" title="Vakantiehuis Frankrijk Twitter"><img src="http://<?php echo $base; ?>/images/twitter.jpg" alt="Twitter" /></a></li>
                        <li><a href="#" title="Vakantiehuis Frankrijk Facebook"><img src="http://<?php echo $base; ?>/images/facebook.jpg" alt="Facebook" /></a></li>
                    </ul>              
                </div>
            </div> 
</div><!-- /header-search -->
<div id="header-menu" class="container">
     <nav class="inner-container">
        <ul>
            <li><a href="#" title="Bestemmingen">Bestemmingen</a></li>
            <li><a href="#" title="Aanbiedingen">Aanbiedingen</a></li>
            <li><a href="#" title="Last minutes">Last minutes</a></li>
            <li class="last"><a href="#" title="Bezienswaardigheden">Bezienswaardigheden</a></li>
        </ul>
    </nav>
</div><!--/header-menu-->
<div id="kruimelpad" class="container">
  <ul>
    <li><a href="/" title="Homepage">Home</a></li>
    <li>><a href="#" title="Homepage">Subpagina</a></li>
    <li>><a href="#" title="Homepage">Subpagina</a></li>
    <li class="current">><a href="#" title="Homepage">Huidige pagina</a></li>
  </ul>
  <a href="#" title="Terug" id="kp-back-link">« Terug naar zoekresultaat</a>
 </div><!--/kruimelpad-->

