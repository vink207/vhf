<?php
/**
 * @author Lambert Portier
 * @copyright 2014
 * view/vakantiehuis.php
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Vakantiehuis.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Reacties.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/OptiesHuis.php';

error_reporting(E_ALL);

if (isset($_GET['code']))
{
    $code  = $_GET['code'];  
    
    //data huis
    $vk = new Vakantiehuis();
    $vk->setCode($code); 
    $huis_id = $vk->getHuisID();
    $vk->getDataHuis();
    $datahuis = $vk->getData();
    if ($datahuis['aantal2'] != $datahuis['aantal1'])
    {
        $pers = $datahuis['aantal1'] . ' - ' . $datahuis['aantal2']; 
    }
    else
    {
        $pers = $datahuis['aantal1']; 
    }
    
    // faciliteiten
    $vk->getFaciliteiten();
    $faciliteiten = $vk->getArrFaciliteit();
    
    // fotos
    $fotos = $vk->getFotos();
    
    //extra opties
    $oh = new OptiesHuis($huis_id);
    $opties = $oh->getOpties();
    
    //reacties
    $re = new Reacties();
    $re->setHuisID($huis_id);
    $re->reactieHuisID();
    $datum = $re->getDatum();
    $naam = $re->getNaam();
    $id = $re->getID();
    $reactie = $re->getReactie();
    $dept = $re->getDept();
    $aantal_reacties = count($datum);
    
    // vergelijkbare huizen
    $vk->getAlikes();
    $alikefotos = $vk->getAlikeFoto();
    $alikepers = $vk->getAlikePers();
    $alikeprijs = $vk->getAlikePrijs();
    $alikecode = $vk->getAlikeCode();
    $alikeurl = $vk->getAlikeUrl();
    $alikes = count($alikecode);

    // seo
    require_once 'utilities/Seo.php';
    $seo = new Seo();
    $seo->setTitle($datahuis['code']);

require_once 'header.php';
?>
<div id="main-container" class="container">
    <div id="content-right">
        <div class="blok">
               <h2>VAKANTIEHUIS <?php echo $datahuis['code']; ?></h2>
                  <div class="huis-images">
                  <?php
                  foreach ($fotos as $foto)
                  {
                  ?>
                      <img src="http://www.vakantiehuisfrankrijk.nl/thumbnails/<?php echo $foto; ?>" />
                  <?php
                  }
                  ?>
                  <a href="" class="button">Bewaar</a>
                <p>icoontjes media</p>
                </div><!--/huis-images-->
              <div class="blok-inhoud">
                <h2><?php echo $datahuis['dept_naam']; ?>, <?php echo $datahuis['streek_naam']; ?></h2>
                <?php echo $datahuis['beschrijving']; ?>
                <br />
                <div class="result-bottom">
                        <span class="rating">7,9</span>
                <ul>
                    <li><?php echo $pers; ?>. personen</li>
                    <li><?php echo $datahuis['slaapkamers']; ?> slaapkamers</li>
                <?php                
                foreach ($faciliteiten as $faciliteit)
                {
                ?>
                    <li><?php echo $faciliteit; ?></li>
                <?php
                }
                ?>  
                </ul>
                </div><!--/result-bottom-->
                
                </div><!--/blok-inhoud-->

            </div><!-- /blok-->
            <div class="blok">
            <h2>BESCHIKBAARHEID EN PRIJZEN</h2>
              <div class="blok-inhoud">            
                <p>
                    <strong>Belangrijk:</strong> de huurperiode loopt <u>altijd van zaterdag tot zaterdag in juni, juli en augustus &eacute;n de vakantieperiodes</u>. Soms is een afwijkende boeking mogelijk (bv. van maandag-vrijdag). Wij vragen u in dat geval ons <a href="<?php echo $absroot; ?>/contact">een e-mail te sturen</a>.
                    <br />
                    De beschikbaarheid is actueel. Binnen 2 werkdagen ontvangt u van ons een boekingsbevestiging. Mocht het door u geboekte huis niet meer beschikbaar zijn, dan zullen we u dit binnen 2 werkdagen per mail of telefonisch laten weten. In dat geval zullen we u een vergelijkbaar alternatief voorstellen.
                    <br /><a class="ajax" href="<?php echo $absroot; ?>/view/prijzen.php">prijslijst</a>
                </p>
                <div id="legenda" class="with-float">
                    <span><span></span> = beschikbaar</span><span><span id="booked"></span> = Reeds geboekt</span>
                </div>
                 <form class="nojq">
                    <input type="hidden" name="huisID" id="huisID" value="<?php echo $huis_id; ?>">
                    <div id="jrange" class="dates">
                        <input type="hidden" />
                        <div></div>
                    </div>           
                </form>

                <div id="prices"></div> 
            </div><!--/blok-inhoud-->          
           </div><!--/blok--> 
<?php
if ($alikes != 0)
{
?>
            <div class="blok related with-float">
                <h2>vergelijkbare vakantiehuizen</h2>
           <!-- vergelijkbare huizen -->
              <ul class="with-float">
         
<?php
    for ($x = 0; $x < $alikes; $x++)
    {
?>            
                <li>
                <img src="http://www.vakantiehuisfrankrijk.nl/thumbs/<?php echo $alikefotos[$x]; ?>" width="200" border="0"  title="Vakantiehuis Frankrijk <?php echo $alikecode[$x]; ?>" alt="Vakantiehuis Frankrijk <?php echo $alikecode[$x]; ?>" /> 
                 <a class="group1 plus-small" href="http://www.vakantiehuisfrankrijk.nl/huis_img/<?php echo $alikefotos[$x]; ?>">+</a> 
                 <a class="button bekijk" href="<?php echo $alikeurl[$x]; ?>">bekijk</a> 
                 <a class="button boeken" href="<?php echo $alikeurl[$x]; ?>">boeken</a> 
                 <strong><?php echo $datahuis['dept_naam']; ?> <?php echo $alikecode[$x]; ?></strong><br />
                 max. <?php echo $alikepers[$x]; ?> personen <br />
                 vanaf &euro; <?php echo $alikeprijs[$x]; ?> p/w
                </li>
            

<?php    
    }
?>
</ul>
            </div><!--/blok-->
<?php
}
?>        
</div> <!-- #home-right -->

    <div id="content-left">
       <div class="blok">
            <h2>LIGGING</h2>
              <div class="blok-inhoud">
            
            <?php echo $datahuis['ligging']; ?>
            <br /><br /><a href="" class="lees-verder align-left">» Top tien bezienswaardigheden</a><a href="" class="lees-verder align-left">» Top tien activiteiten</a><br />
            </div><!--/blok-inhoud-->

</div><!--/blok-->
        <div class="blok">
            <h2>INDELING</h2>
              <div class="blok-inhoud">            
            <?php echo $datahuis['indeling']; ?>
</div><!--/blok-inhoud-->

</div><!--/blok-->
        <div class="blok">
            <h2>EXTRA OPTIES</h2>
              <div class="blok-inhoud">
            <ul>
<?php
foreach ($opties as $optie)
{
?>
    <li><?php echo $optie; ?></li>
<?php
}
?>            
            </ul>
            
</div><!--/blok-inhoud-->
</div><!--/blok-->
<?php
if ($aantal_reacties != 0)
{
?>
    <div class="reacties">
            <h2>REACTIES EERDERE HUURDERS</h2>
<?php
// eerste 2 reacties tonen
for ($x = 0; $x < 2; $x++)
{
    if (isset($reactie[$x]))
    {
?>    
    <p class="reactie-p"><span class="reactie">"<?php echo $reactie[$x]; ?>"</span>
    <b><?php echo $naam[$x]; ?></b><br />
    <?php echo $code; ?> <?php echo $dept[$x]; ?><br />
    <?php echo $datum[$x]; ?>
    <a href="" class="lees-verder">» lees verder</a>
    <br />
<?php
    }
}

if ($aantal_reacties > 2)
{
?>
    </div><!--/reacties-->
    <a href="" class="ajax lees-alles">» lees alle reacties</a>
<?php
}
}
?>
    </div></div><!-- /content-left -->
</div> <!-- #main-container -->
<?php require_once 'footer.php'; 
}
?>