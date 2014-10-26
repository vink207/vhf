<?php
// favorieten en nieuwe huizen
require_once 'model/Home.php';
$home = new Home();
$datanieuw = $home->getNieuw();
$datafavoriet = $home->getFavoriet();

// data reacties
require_once 'model/Reacties.php';
$re = new Reacties();
$re->setTag('homepage');
$re->reactieTag();
$datum = $re->getDatum();
$naam = $re->getNaam();
$id = $re->getID();
$reactie = $re->getReactie();
$dept = $re->getDept();
//GEEFT ERROR OP HOMEPAGE WANNEER DEZE NIET UITGEVINKT STAAT $codes = $re->getCode();

// seo
require_once 'utilities/Seo.php';
$seo = new Seo();

require_once 'view/header.php';

error_reporting(E_ALL);

?>

<div id="main-container" class="container">
   <div id="home-right">
          <h3>Zoeken</h3>
       <p><img src="images/kaart-voorbeeld.jpg" /></p>

       <h3>Waarom Libert&eacute; vakantiehuizen</h3>
          <Ul>
          <li>Karakteristieke &eacute;n betaalbare vakantiehuizen</li> 
          <li>Klanttevredenheid: 8,7 </li> 
          <li>12,5 jaar ervaring</li>  
          <li>G&eacute;&eacute;n boekingskosten </li> 
          <li>Persoonlijk en klantvriendelijk</li>   
          </ul>
       <!-- reacties huurders -->
       <h3>Reacties eerdere huurders</h3>
       <?php
       $reacties = '';
       while (list($key, $val) = each($datum)) 
       {   
       ?>
       <p class="reactie-p"><span class="reactie">"<?php echo $reactie[$key]; ?>"</span><br />
            <?php echo $dept[$key]; ?> -  <? //php echo $codes[$key];  ##LAMBERT DEZE VARIABLE GEEFT ERROR ?> <br />
            <?php echo $datum[$key]; ?>
            <a href="#" title="#">&raquo Lees verder</a></p>
       <?php } ?>
    </div><!--/home-right-->
    <div id="home-left">
        <!-- hier komen de favorieten -->
          <div class="house-container favorieten">      
          <h4>Favorieten</h4>
        <?php
        // 3 fotos
        $favorieten = '';
        foreach($datafavoriet AS $array)
        {
        ?> 
            <div class="house"><?php echo '<img src="http://'.$base.'/images/favorieten/'.$array['img'].'">'; ?>
                <p class="specs">
               <?php echo $array['regio']; ?><br />
               <?php echo $array['code']; ?><br />
               max. <?php echo $array['aantal']; ?> pers. - vanaf <?php echo $array['prijs']; ?>,- p/w</p>
               <a title="#" href="#" class="arrow"></a><a title="#" href="#" class="plus"></a><a title="#" href="#" class="bekijk"></a><a title="#" href="#" class="boeken"></a>
            </div>
        <?php
        }
        ?>
        
        </div><!-- house-container -->
        
        <?php
        // hier komen de 2 nieuwe huizen
        // fotos en details en quotes VHF
        
        foreach($datanieuw AS $array)
        {
        ?>
        <div class="house-container">
            <h4>Nieuw</h4>
            <div class="house">
            <?php echo '<img src="http://'.$base.'/images/huis/'.$array['img'].'">'; ?>
                <p class="specs">
                <?php echo $array['regio']; ?><br />
                <?php echo $array['code']; ?><br />
                max. <?php echo $array['aantal']; ?> pers. - vanaf <?php echo $array['prijs']; ?>,- p/w </p>
                <a title="#" href="#" class="arrow"></a><a title="#" href="#" class="plus"></a><a title="#" href="#" class="bekijk"></a><a title="#" href="#" class="boeken"></a></div>
                <div class="onder">
                <span class="quote"><?php echo $array['quote']; ?></span>               
                </div>
            </div><!-- house-container -->
        <?php
        }
        ?>
    </div><!--/home-left-->
</div> <!-- #main-container -->

<?php require_once 'view/footer.php'; ?>


