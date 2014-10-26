<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 * view/footer.php
 */
$base = $_SERVER['HTTP_HOST'];

// default, variablen worden geladen op result.php
if (!(isset($jsonhousesresult)))
{
    $divresult = 0;
    $zoomresult = 0;
    $centerxresult = 0;
    $centeryresult = 0;
    $jsonhousesresult = 0;
}

$footer = <<<EOD
<div id="footer-container" class="container">
    <footer class="inner-container">
        <div>
          <nav>
             <ul>
                <li><strong>Meer informatie</strong></li>
                <li><a href="#" title="Contact">&bullet; Contact</a></li>
                <li><a href="#" title="Veelgestelde vragen">&bullet; Veelgestelde vragen</a></li>
                <li><a href="#" title="Huurvoorwaarden">&bullet; Huurvoorwaarden</a></li>
                <li><a href="#" title="Disclaimer">&bullet; Disclaimer</a></li>
            </ul>
           </nav>
        </div>
        <div>
          <nav>                
             <ul>
                <li><strong>Vakantiehuizen</strong></li>
                <li><a href="#" title="Vakantiehuis aan zee">&bullet; Vakantiehuis aan zee</a></li>
                <li><a href="#" title="Vakantiehuis Frankrijk">&bullet; Vakantiehuis Frankrijk</a></li>
                <li><a href="#" title="Vakantiehuis Bretagne">&bullet; Vakantiehuis Bretagne</a></li>
                <li><a href="#" title="Vakantiehuis Normandi&euml;">&bullet; Vakantiehuis Normandi&euml;</a></li>
                <li><a href="#" title="Vakantiehuis Noord-Frankrijk">&bullet; Vakantiehuis Noord-Frankrijk</a></li>
                <li><a href="#" title="Vakantiehuis Atlantische kust">&bullet; Vakantiehuis Atlantische kust</a></li>
                <li><a href="#" title="Vakantiehuis Loire-streek">&bullet; Vakantiehuis Loire-streek</a></li>
                <li><a href="#" title="Vakantiehuis Franse Ardennen ">&bullet; Vakantiehuis Franse Ardennen</a></li>
            </ul>
          </nav>
        </div>
        <div class="footer-meerinfo">
        <p><strong>Heeft u een vraag?</strong><br />Voor meer informatie bel +31 (0)24 373 99 86<br />of mail naar <strong>info@vakantiehuisfrankrijk.nl</strong></p>
        <img src="http://$base/images/logosgr.png" alt="SGR" /><img src="http://$base/images/europeesche.png" alt="SGR" />
        </div>
        <div class="footer-links">
             <ul>
                <li class="first"><a href="#" title="Libert&eacute; vakantiehuizen"><img src="http://$base/images/logo.png" alt="Libert&eacute; vakantiehuizen" width="85" /></a></li>
                <li><a href="#" title="Vakantiehuis Frankrijk blog"><img src="http://$base/images/blog.jpg" alt="Blog" /></a></li>
                <li><a href="#" title="Vakantiehuis Frankrijk Twitter"><img src="http://$base/images/twitter.jpg" alt="Twitter" /></a></li>
                <li><a href="#" title="Vakantiehuis Frankrijk Facebook"><img src="http://$base/images/facebook.jpg" alt="Facebook" /></a></li>
            </ul>              
        </div>                
    </footer>
</div><!--/footer-container -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="http://$base/js/jqtransformplugin/jquery.jqtransform.js" ></script>
<!--
<script type="text/javascript">
  $(function(){
    //$('#main-container select').addClass( 'jqTransformHidden' );
    $('form').jqTransform({imgPath:'http://$base/js/jqtransformplugin/img/'});
    //$('#main-container select').show()
  });
</script>
-->
	<script language="javascript">
		$(function(){
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
		});
	</script>
<script src="http://$base/js/jq-sidebar.js"></script>
<script src="http://$base/js/jq-search.js"></script>
<script src="http://$base/js/jq-boeken.js"></script>
<script src="http://$base/js/jq-calender.js"></script>
<script type="text/javascript">
function initialize(div, intzoom, centerx, centery, jsonhouses) 
{ 
  if (div !== undefined)
  {  
      var mapOptions = {
        zoom: intzoom,
        center: new google.maps.LatLng(centerx,centery)
      }
      var map = new google.maps.Map($('#'+div)[0], mapOptions);
    
      setMarkers(map, jsonhouses);
  }
}

/**
 * Data for the markers consisting of a name, a LatLng and a zIndex for
 * the order in which these markers should display on top of each
 * other.
 */

function setMarkers(map, locations) {
  // Add markers to the map

  // Marker sizes are expressed as a Size of X,Y
  // where the origin of the image (0,0) is located
  // in the top left of the image.

  // Origins, anchor positions and coordinates of the marker
  // increase in the X direction to the right and in
  // the Y direction down.
  var image = {
    // This marker is 20 pixels wide by 32 pixels tall.
    size: new google.maps.Size(26, 27),
    // The origin for this image is 0,0.
    origin: new google.maps.Point(0,0),
    // The anchor for this image is the base of the flagpole at 0,32.
    anchor: new google.maps.Point(0, 32)
  };
  // Shapes define the clickable region of the icon.
  // The type defines an HTML &lt;area&gt; element 'poly' which
  // traces out a polygon as a series of X,Y points. The final
  // coordinate closes the poly by connecting to the first
  // coordinate.
  var shape = {
      coords: [1, 1, 1, 20, 18, 20, 18 , 1],
      type: 'poly'
  };
  
  var infowindow = new google.maps.InfoWindow({maxWidth: '175'});
  
 

  var marker, i;
  
  for (var i = 0; i < locations.length; i++) {
    var myLatLng = new google.maps.LatLng(locations[i][1], locations[i][2]);
    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        shape: shape,
        "icon": 'http://vakantiehuisfrankrijk.nl/dev/images/maps-marker.png',
        title: locations[i][0],
        zIndex: locations[i][4]
    });
    
    google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][3]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
}
// googlemaps header aanroepen
$('#zoekopkaart').click(function()
{   
    $('#header-search-formulier').css('display', 'none');
    $('#header-search-kaart').css('display', 'block');
    initialize('$div', $zoom, $centerx, $centery, $jsonhouses);
});

$('#zoekopformulier').click(function()
{
    $('#header-search-formulier').css('display', 'block');
    $('#header-search-kaart').css('display', 'none');
});
 

</script>
</body>
</html>
EOD;
echo $footer;

/**
 * 

<script type="text/javascript" src="http://$base/js/requiered/jquery.js" ></script>
*/
?>
