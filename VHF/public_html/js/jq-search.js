/*
 * document ready
 */
//var absroot = 'http://www.dev.vakantiehuisfrankrijk.nl';
$(document).ready(function()
{
	/*
	 * Reset form
	 */
	$('#frm-sidebar #reset-all').click(function()
	{
		$('#frm-sidebar input:checkbox').each(function()
		{
            $(this).prev().removeClass('jqTransformChecked');
            $(this).attr('checked', false);
		});
        
        $('#afstandzee').val('0-50');
        $('#slider-afstandzee').slider('values', 0, 0);
        $('#slider-afstandzee').slider('values', 1, 50);
        
        $('#slaapkamer').val('1-3');
        $('#slider-slaapkamer').slider('values', 0, 1);
        $('#slider-slaapkamer').slider('values', 1, 3);
        
        $('#slider-slaapkamer').slider('refresh');
        $('#slider-afstandzee').slider('refresh');
        
        // ajax call 
		search_handler();
	});
    
    $('#frm-sidebar span[id^=reset_]').click(function()
    {
        $(this).attr('id');
        var n = $(this).attr('id').indexOf('_');
        var selector = $(this).attr('id').substr(n+1);
        
        $('#'+selector+' input:checkbox').each(function()
		{
            $(this).prev().removeClass('jqTransformChecked');
            $(this).attr('checked', false);
		});        
        
        // ajax call 
		search_handler();
    });

	/*
	 * ajax-call
	 */
	$('#frm-sidebar input:checkbox').click(search_handler);
    
    $('#slider-slaapkamer').slider().on({
        slidechange: function() {
            search_handler();
        }
    });

    $('#slider-afstandzee').slider().on({
        slidechange: function() {
            search_handler();
        }
    });

    $('#slider-prijs').slider().on({
        slidechange: function() {
            search_handler();
        }
    });

    if ($('#frm-header').hasClass('ajax') )
    {
        $('#frm-header').submit(function(e)        
        { 
            e.preventDefault();
            search_handler();
        });
    }    

    $('#resultlijst').click(function()
    {
        $('#search-lijst').css('display', 'block');
        $('#search-kaart').css('display', 'none');
    });
    
    // googlemaps kaart met resultaat zoekactie tonen
    $('#resultkaart').click(function()
    {  console.log('debugging');
        $('#search-lijst').css('display', 'none');
        $('#search-kaart').css('display', 'block');
        var stringhuisid = $('#stringhuisid').text();
        // ajax call
        map_handler(stringhuisid);
    });
});

/*
 * ajax zoekactie
 */
function search_handler()
{	
	var ajaxUrl = ABSROOT+'/model/ajax/search.php';
    
    // googlemaps kaart onzichtbaar maken
    $('#search-kaart').css('display', 'none');
	
	// run ajax call
	var ajaxResponse = $.ajax(
	{
		url: ajaxUrl,
		// only accept json
		dataType: 'JSON',
		data: 
		{
			streek: $('select[name=streek]').val(),
			aantalpersonen: $('select[name=aantalpersonen]').val(),
			aankomst: $('select[name=aankomst]').val(),
			weken: $('select[name=weken]').val(),
			slaapkamer: $('input[name=slaapkamer]').val(),
			afstandzee: $('input[name=afstandzee]').val(),
			slaapkamer: $('input[name=slaapkamer]').val(),
			prijs: $('input[name=prijs]').val(),
			checkbox: $('input:checkbox:checked').serializeArray()
		},
        beforeSend: function(){
            $('#search-lijst').html('<img id="ajax-loader" alt="Loading" src="http://www.vakantiehuisfrankrijk.nl/img/ajax-loader.gif" />');
        },
		type: 'POST',
		// success handler
		success: function (ajaxReturn)
		{
            if (ajaxReturn == null || ajaxReturn.content == null)
			{
				updateContent('<p>Oops... something went wrong: could not retrieve search results.</p>');
				return false;
			}
		
			// decode html
			var decoded = $("<div/>").html(ajaxReturn.content).text();
			
			// update content
			updateContent(decoded);
            
            // update msg aantal gevonden huizen
			updateMsg(ajaxReturn.msgnumber);
            
            // update gebruikte filters
			updateFilters(ajaxReturn.filters);
            
            // map handler
            updateStringHuisID(ajaxReturn.stringhuisid);
		},
		// error handler
		error: function (jqXHR, textStatus, errorThrown)
		{
			// debug modus
			updateContent('<p>Error: The AJAX-call was unsuccesfull or no valid JSON-data was returned.<br /><br /><strong>- Status:</strong> '+textStatus+'<br /><strong>- Error:</strong> '+errorThrown+'</p>');
			return false;
			
			// production
			//updateContent('<p>Oops... something went wrong: error while processing search results.</p>');	
		}
	});
}
/*
 * updates the content container
 */
function updateContent(contentHtml)
{
	if (contentHtml == null)
	{
		contentHtml = '<p>Error: no html-data received for search results.</p>';	
	}
	
	// fade old content out, update content and fade in
	$('#search-lijst').fadeOut('fast', function()
	{
		$('#search-lijst').html(contentHtml);
		$('#search-lijst').fadeIn('fast');
	});
	
	return true;
}

/*
 * update div met tekst aantal gevonden huizen
 */
function updateMsg(msgHtml)
{
	if (msgHtml == null)
	{
		msgHtml = '<p>Error: no msg received for number results.</p>';	
	}
	
	// fade old content out, update content and fade in
	$('#msg-search').fadeOut('fast', function()
	{
		$('#msg-search').html(msgHtml);
		$('#msg-search').fadeIn('fast');
	});
	
	return true;
}

/*
 * update div met tekst aantal gevonden huizen
 */
function updateFilters(filters)
{
	if (filters == null)
	{
		filters = '<p>Error: no msg received for search filters.</p>';	
	}
	
	// fade old content out, update content and fade in
	$('#msg-filters').fadeOut('fast', function()
	{
		$('#msg-filters').html(filters);
		$('#msg-filters').fadeIn('fast');
	});
	
	return true;
}


/*
 * update div met string gevonden huisIDs
 */
function updateStringHuisID(stringhuisid)
{
    $('#stringhuisid').text(stringhuisid);
}

function map_handler(stringhuisid)
{
    var ajaxUrl = ABSROOT+'/model/ajax/gmaps.php';
	
	// run ajax call
	var ajaxResponse = $.ajax(
	{
		url: ajaxUrl,
		// only accept json
		dataType: 'json',
		data: 
		{
			stringhuisid: stringhuisid
		},
        beforeSend: function(){
            $('#search-kaart').html('<img id="ajax-loader" alt="Loading" src="http://www.vakantiehuisfrankrijk.nl/img/ajax-loader.gif" />');
        },
		type: 'POST',
		// success handler
		success: function (ajaxReturn)
		{
			initialize(ajaxReturn.divmap, ajaxReturn.zoom, ajaxReturn.centerx, ajaxReturn.centery, ajaxReturn.jsonhouses);
		},
		// error handler
		error: function (jqXHR, textStatus, errorThrown)
		{
			// debug modus
			$('#search-lijst').html('<p>Error: The AJAX-call was unsuccesfull or no valid JSON-data was returned.<br /><br /><strong>- Status:</strong> '+textStatus+'<br /><strong>- Error:</strong> '+errorThrown+'</p>');
			return false;
		}
	});
} 