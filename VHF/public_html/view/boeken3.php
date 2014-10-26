<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/SessionBoeken.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/functies_boeken.php';

session_start();

// base
$absroot = 'http://'.$_SERVER['HTTP_HOST'];

// referer
$referer = $_SERVER['HTTP_REFERER'];

if (isset($_GET['huis_id']))
{
    $huis_id = $_GET['huis_id'];   
    
    // CONTROLE VARIABELEN
	if (!(is_numeric($huis_id)))
	{
		header('location:'.$absroot);
		exit();
	}

    // referer
    $referer = $_SERVER['HTTP_REFERER'];
    // vanaf verschillende pagina's tot deze pagina
    $pos = strpos($referer, '/verzekering');
    
    // vanaf verschillende pagina's tot deze pagina
    if ($pos !== false)
    {
        $_SESSION['refer2'] =  $referer;
    }
}

// url voor back-button
if (isset($_SESSION['refer2'])){$referer = $_SESSION['refer2'];}

// session hoofdhuurder
if (isset($_SESSION['klant']))
{
	$aanhef = $_SESSION['klant']->get_aanhef();
	$voorletters = $_SESSION['klant']->get_voorletters();
	$tussenvoegsel = $_SESSION['klant']->get_tussenvoegsel();
	$achternaam = $_SESSION['klant']->get_achternaam();
	$hh_gb_dag = $_SESSION['klant']->get_hh_gb_dag();
	$hh_gb_maand = $_SESSION['klant']->get_hh_gb_maand();
	$hh_gb_jaar = $_SESSION['klant']->get_hh_gb_jaar();
	$adres = $_SESSION['klant']->get_adres();
	$nummer = $_SESSION['klant']->get_nummer();
	$postcode = $_SESSION['klant']->get_postcode();
	$woonplaats = $_SESSION['klant']->get_plaats();
	$land = $_SESSION['klant']->get_land();
	$tel_prive = $_SESSION['klant']->get_tel_prive();
	$email = $_SESSION['klant']->get_email();
	$gsm =  $_SESSION['klant']->get_gsm();
	$opmerkingen = $_SESSION['klant']->get_opmerking();
} 
else 
{
	$aanhef = '';
	$voorletters = '';
	$tussenvoegsel = '';
	$achternaam = '';
	$hh_gb_dag = '1';
	$hh_gb_maand = '1';
	$hh_gb_jaar = '1960';
	$adres = '';
	$nummer = '';
	$postcode = '';
	$plaats = "";
	$land = '';
	$tel_prive = '';
	$email = '';
	$gsm = '';
    $opmerkingen = '';
}

// selectboxen hoofdhuurder
$select_gbdatum = selectGbdatum($hh_gb_dag, $hh_gb_maand, $hh_gb_jaar);
$select_land = selectLand($land);
$select_geslacht = selectGeslacht($aanhef);

// header
require_once 'view/header.php';

$action = $absroot.'/boeken/'.$huis_id.'/controle/bevestiging';
$menuaction = $absroot.'/boeken/'.$huis_id.'/reisgezelschap';
?>

<div id="main-container" class="container">
<!-- RIGHT -->
<div id="book-right">
    <!-- book_content -->
    <div id="book_content">
        <ul id="book_nav">
            <li class="first"><a href="<?php echo $menuaction; ?>">1 REISGEZELSCHAP</a></li>
            <li><a class="active" href="#">2 VERZEKERINGEN</a></li>
            <li><a href="#">3 UW GEGEVENS</a></li>
            <li class="last"><a href="#">4 CONTROLE/BEVESTIGING</a></li>
        </ul>
        <h2>3 UW GEGEVENS</h2>
        <form id="form1" method="post" action="<?php echo $action; ?>" enctype="application/x-www-form-urlencoded">
            <fieldset id="fieldset-formVerzekering">
                <p>Vul hieronder uw gegevens in</p>
                <dt><label>Geslacht</label></dt>
		          <dd>$select_geslacht</dd> 
                <dt><label>Naam</label></dt>
		          <dd>
		              <input placeholder="voorletters" name="voorletters" id="voorletters" value="<?php echo $voorletters; ?>" size="12" type="text"> 
                      <input placeholder="tv" name="tussenvoegsel" id="tussenvoegsel" value="<?php echo $tussenvoegsel; ?>" size="12" type="text">   
                      <input placeholder="tv" name="achternaam" id="achternaam" value="<?php echo $achternaam; ?>" size="24" type="text">  
                  </dd> 
                  <dt><label>Geboortedatum</label></dt>
                    <dd><?php echo $select_gbdatum; ?></dd>
                  <dt><label>Straat / huisnummer</label></dt>
                    <dd>
                    <input placeholder="straatnaam" name="adres" id="adres" value="<?php echo $adres; ?>" size="30" type="text">
                    <input placeholder="huisbr" name="nummer" value="<?php echo $nummer; ?>" id="huisnummer" size="10" type="text">
                </dd>
                <dt><label>Postcode</label></dt>
	            <dd>
                  <input maxlength="6" name="postcode" id="postcode" value="<?php echo $postcode; ?>" size="10" type="text">
                </dd>
                <dt><label>Woonplaats</label></dt>
	            <dd>
		          <input name="woonplaats" value="<?php echo $woonplaats; ?>" id="woonplaats" size="30" type="text">
                </dd>
                <dt><label>Land</label></dt>
		          <dd><?php echo $select_land; ?></dd>
                <dt><label>E-mailadres</label></dt>
                    <dd><input name="email" value="<?php echo $email; ?>" id="email" size="30" type="text"></dd>
                <dt><label>Telefoon</label></dt>
                    <dd><input name="tel_prive" id="tel_prive" value="<?php echo $tel_prive; ?>" size="30" type="text"></dd>
                <hr>
                <p>Medehuurders</p>
                <p>Vul hieronder de gegevens in van de personen die met u mee op vakantie gaan.</p>
<?php
        
$volwassen = $_SESSION['items']->getItem('volwassen');
$kinderen = $_SESSION['items']->getItem('kinderen');
$peuters = $_SESSION['items']->getItem('peuters');
$aantal_mede = ($volwassen + $kinderen + $peuters)- 1;

if ($aantal_mede == 0)
{
?>
        Geen medehuurders
<?php
}
else
{
    if (isset($_SESSION['medehuurder']))
    {
        $arr_naam = $_SESSION['medehuurder']->get_arr_naam();
        $arr_tv = $_SESSION['medehuurder']->get_arr_tv();
        $arr_vl = $_SESSION['medehuurder']->get_arr_vl();
        $arr_geslacht = $_SESSION['medehuurder']->get_arr_geslacht();
        $arr_dag = $_SESSION['medehuurder']->get_arr_dag();
        $arr_maand = $_SESSION['medehuurder']->get_arr_maand();
        $arr_jaar = $_SESSION['medehuurder']->get_arr_jaar();
        
        // komt aantal items in sessie overeen met aantal medehuurders
        $aantal_sessie = count($arr_naam);
        $diff = $aantal_mede - $aantal_sessie;
        if ($diff > 0)
        {
            // nieuwe lege items aan sessie toevoegen
            for ($i = $aantal_mede; $i < $diff; $i++)
            {
                $_SESSION['medehuurder']->AddMedehuurder('', '', '', '', '');
            }
            
            // nieuwe lege items aan array toevoegen
            for ($i = $aantal_mede; $i < $diff; $i++)
            {
                array_push($arr_naam, "");
                array_push($arr_tv, "");
                array_push($arr_vl, "");
                array_push($arr_geslacht, "");   
                array_push($arr_dag, "");  
                array_push($arr_maand, "");        
                array_push($arr_jaar, "");            
            }
            $_SESSION['medehuurder']->AddMedehuurder($arr_naam, $arr_tv, $arr_vl, $arr_geslacht, $arr_dag, $arr_maand, $arr_jaar);
        }
    }
    else
    {
        for ($x = 0; $x < $aantal_mede; $x++)
        {
            $arr_naam[$x] = '';
            $arr_tv[$x] = '';
            $arr_vl[$x] = '';
            $arr_geslacht[$x] = '';
            $arr_dag[$x] = '';
            $arr_maand[$x] = '';
            $arr_jaar[$x] = '';
        }
    }
?>
    <!-- tableheader -->
    <table style="width: 620px">
        <colgroup>
            <col width="">
            <col width="">
            <col width="">
            <col width="">
            <col width="">
        </colgroup>
        <tbody>
        <tr>
            <th>Geslacht</th>
            <th>Voorletters</th>
            <th>Tussenvoegsel</th>
            <th>Achternaam</th>
            <th>Geboortedatum</th>
        </tr>
<?php
    for ($x = 0; $x < $aantal_mede; $x++)
    {            
        if ($arr_geslacht[$x] == "M")
            $m_check = ' selected';
        else
            $m_check = '';
                
        if ($arr_geslacht[$x] == "V")
            $v_check = ' selected';
        else
            $v_check = '';
            
        // selectboxen geboortedatum
        $select = selectGbdatumMede($x, $arr_dag[$x], $arr_maand[$x], $arr_jaar[$x]);            
?>
        <tr>
            <td>
                <select name="arr_geslacht[]">
                    <option value="M" <?php echo $m_check; ?>>Man</option>
                    <option value="V" <?php echo $v_check; ?>>Vrouw</option>
                </select>
            </td>
            <td><input name="arr_vl[]" value="<?php echo $arr_vl[$x]; ?>" size="8" type="text" class="voorletters" /></td>
            <td><input name="arr_tv[]" value="<?php echo $arr_tv[$x]; ?>" size="8" type="text" class="tussenvoegsel" /></td>
            <td><input name="arr_naam[]" value="<?php echo $arr_naam[$x]; ?>" size="25" type="text" class="achternaam" /></td>
            <td><?php echo $select; ?></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>
<?php
}
?>
            <button onclick="document.location.href='<?php echo $referer; ?>';" type="button" id="previous" name="previous">STAP 2</button>
             <input type="submit" value="STAP 4" id="next" name="next" />
             <!-- blok heeft u een vraag -->
             <div>
                <h2>HEEFT U EEN VRAAG?</H2>
                telefoon +31 (024) 373 99 86 <br>
                email info@vakantiehuisfrnakrijk.nl <br>
                <br>
                We zijn bereikbaar op maandag t/m vrijdag van 9.00 tot 17.00 uur.<br>
                Mails worden ook buiten kantooruren beantwoord.
             </div>
        </form>
    </div>
    <!-- book_content -->
</div>
<?php
/** LEFT */
if (isset($_SESSION['kolom-links']))
{
    echo $_SESSION['kolom-links'];
}

?>
</div> <!-- #main-container -->

<?php require_once 'view/footer.php'; ?>