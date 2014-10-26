$(function() {
    $( "#slider-slaapkamer" ).slider({
      range: true,
      orientation: "horizontal",
      min: 1,
      max: 5,
      values: [ 1, 3 ],
      slide: function( event, ui ) {
        $( "#slaapkamer" ).val(ui.values[ 0 ] + "-" + ui.values[ 1 ] );
      }
    });
    $( "#slaapkamer" ).val($( "#slider-slaapkamer" ).slider( "values", 0 ) +
      "-" + $( "#slider-slaapkamer" ).slider( "values", 1 ) );
  });
  
$(function() {
  $( "#slider-afstandzee" ).slider({
      range: true,
      orientation: "horizontal",
      min: 0,
      max: 50,
      values: [ 0, 50 ],
      slide: function( event, ui ) {
        $( "#afstandzee" ).val(ui.values[ 0 ] + "-" + ui.values[ 1 ] );
      }
    });
    $( "#afstandzee" ).val($( "#slider-afstandzee" ).slider( "values", 0 ) +
      "-" + $( "#slider-afstandzee" ).slider( "values", 1 ) );
  });
  
$(function() {
  $( "#slider-prijs" ).slider({
      range: true,
      orientation: "horizontal",
      min: 200,
      max: 1000,
      values: [ 200, 1000 ],
      slide: function( event, ui ) {
        $( "#prijs" ).val(ui.values[ 0 ] + "-" + ui.values[ 1 ] );
      }
    });
    $( "#prijs" ).val($( "#slider-prijs" ).slider( "values", 0 ) +
      "-" + $( "#slider-prijs" ).slider( "values", 1 ) );
  });