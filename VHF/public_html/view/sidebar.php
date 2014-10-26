<?php
/**
 * @author Lambert Portier
 * @copyright 2014
 */

require_once '../model/Sidebar.php';

/**
 * labels en names checkboxen sidebar
 */
$sb = new Sidebar();
$checkbox = $sb->getCheckbox();

/**
 * counts checkbox
 */
$sidebar_counts = $seo->getSidebarCounts();
?>
    <h2>Verfijn je zoekopdracht</h2>
<div>
    <form id="frm-sidebar">
    <div class="filter_item">
      <h3>Aantal slaapkamers</h3>
      <div id="slider_bedroom"></div>
      <input type="hidden" name="bedroom_range" id="bedroom_range">
    </div><!--/filter_item-->
    <div class="filter_item">
      <h3>Afstand tot de zee</h3>
      <div id="slider_distance"></div>
      <input type="hidden" name="distance_range" id="distance_range">
    </div><!--/filter_item-->
    <div class="filter_item">
      <h3>Prijs</h3>
      <div id="slider_price"></div>
      <input type="hidden" name="price_range" id="price_range">
    </div><!-- /filter__item-->
<?php    
foreach($checkbox AS $key=>$categorie)
{  
    $id = str_replace(' ', '',strtolower($key));
?>
    <div class="filter_item">
    <h3><?php echo $key; ?></h3>
    <ul id="<?php echo $id; ?>">
<?php
    foreach($categorie AS $array)
    {
        $temp = array();
        foreach($array AS $name=>$value)
        {                
            $temp[] = $value;
        }
        
        if (!(in_array($temp[1], (array)$arrcheckbox)))
        {
            $checked = '';
        }
        else
        {
            $checked = 'checked="checked"';
        }
?>
        <li><input type="checkbox" class="checkbox" id="" name="<?php echo $temp[1]; ?>" value="<?php echo $temp[1]; ?>" <?php echo $checked; ?> /><?php echo $temp[0]; ?><span class="sidebar_count" id="count_<?php echo $temp[1]; ?>">(<?php echo $sidebar_counts[$temp[1]]; ?>)</span></li>
<?php
    }
?>
        </ul><span style="cursor: pointer;" id="reset_<?php echo $id; ?>" class="btn-wissen">wissen</span> 
 </div><!-- /filter__item-->
 
<?php   
}
?>
    <span style="cursor: pointer;" id="reset-all" class="btn-wissen">alles wissen</span>
 
    </form>
</div>
