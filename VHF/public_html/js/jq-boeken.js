/*
 * document ready
 */

$(document).ready(function()
{    
    // enquete hoe heeft u ons gevonden
    $('#hoekwam').change(function()
    {
        var name = $(this).attr("name");
        var value = $(this).val();
        console.log(name);
        var dataObj = {};
        dataObj[name]=value;
        
        var div = 'msg-hoekwam';  
        
        // ajax
        var scriptname = 'enquete.php';
        ajax_handler(dataObj, div, scriptname);  
    });
    
    // selectboxen reisgezelschap
    $('#form1 div.jqTransformSelectWrapper ul li a').click(function()
    {
         var name = $(this).closest('div').parent().attr("id");
         var value = $('#'+name+' div.jqTransformSelectWrapper span').text();
         console.log(name+': '+value);
        
         var dataObj = {};
         dataObj[name]=value;
        
         var div = 'huis_periode_aantal';
         //console.log(name+': '+value);
        
         // ajax
         var scriptname = 'bk_update.php';
         ajax_handler(dataObj, div, scriptname);  
    })
    
    /* radio polis */
	$('#form1 .verzekering').change(function()
	{
        var name = $(this).attr("name");
        var value = $(this).val();
        var dataObj = {};
        dataObj[name]=value;
        
        var div = 'kosten'; 
        
        // ajax
        var scriptname = 'bk_update.php';
        ajax_handler(dataObj, div, scriptname);  
	});
    
    /* lakenhuur, eindschoonmaak, kinderbenodigheden, huisdier */
	$('#form1 .optioneel').click(function()
	{        
        var dataObj = {};
        var name = $(this).attr("name"); 
        var value = $(this).val();
        
        var div = 'optioneel';
        
        if (name == 'huisdier'){
            var div = 'infohuisdier';
        }
        console.log(name+value);
        dataObj[name]=value;  
        
        // ajax
        var scriptname = 'bk_update.php';
        ajax_handler(dataObj, div, scriptname);  
	});
});
/*
 * ajax call
 */
function ajax_handler(dataObj, div, scriptname)
{
	var ajaxUrl = ABSROOT+'/model/ajax/'+scriptname;	
    
    // in geval enquete de slectbox verwijderen
    if (scriptname == 'enquete.php')
    {
        $('#frm-enquete').hide();
        console.log(scriptname);
    }	
	
	// run ajax call
	var ajaxResponse = $.ajax(
	{
		url: ajaxUrl,
		// only accept html
		dataType: 'html',
		data: dataObj,
		type: 'POST',
		// success handler
		success: function (ajaxReturn)
		{
			if (ajaxReturn == null)
			{
				updateContentBoeken('<p>Oops... something went wrong: could not retrieve search results.</p>');
				return false;
			}
			// decode html
			//var decoded = $("<div/>").html(ajaxReturn.content).text();
			
			// update content
			updateContentBoeken(ajaxReturn, div);
		},
		// error handler
		error: function (jqXHR, textStatus, errorThrown)
		{
			// debug modus
			updateContentBoeken('<p>Error: The AJAX-call was unsuccesfull or no valid JSON-data was returned.<br /><br /><strong>- Status:</strong> '+textStatus+'<br /><strong>- Error:</strong> '+errorThrown+'</p>');
			return false;
			
			// production
			//updateContentBoeken('<p>Oops... something went wrong: error while processing search results.</p>');	
		}
	});
}
/*
 * updates the content container
 */
function updateContentBoeken(contentHtml, div)
{
	var selector = '#'+div;
    
    if (contentHtml == null)
	{
		contentHtml = '<p>Error: no html-data received for search results.</p>';	
	}
	
	// fade old content out, update content and fade in
	$(selector).fadeOut('fast', function()
	{
		$(selector).html(contentHtml);
		$(selector).fadeIn('fast');
	});
	
	return true;
}


/**
 * validation
 * stap2 boeken
 */
$('#form2').submit(function(event)
{
    // selectboxen geboortedatum medehuurders
    if ( $('[name^=arr_dag]').length > 0 )
    {
        var le = $('[name^=arr_dag]').length;
        
        for (var i = 0; i < le; i++)
        {
            var dag = $('[name=arr_dag\\['+i+'\\]]').val();
            var maand = $('[name=arr_maand\\['+i+'\\]]').val(); 
            var jaar = $('[name=arr_jaar\\['+i+'\\]]').val(); 
            
            $('[name=arr_dag\\['+i+'\\]], [name=arr_maand\\['+i+'\\]], [name=arr_jaar\\['+i+'\\]]').css('border', '1px solid #A3C1DB');
            if (dag == '' || maand == '' || jaar == '')
            {
                event.preventDefault();
                $('[name=arr_dag\\['+i+'\\]], [name=arr_maand\\['+i+'\\]], [name=arr_jaar\\['+i+'\\]]').css('border', '1px solid red');
            }
        }
    }
    
    // telefoon mobiel of vast verplicht
    var flag_telefoon = 0;
    var flag_gsm = 0;
    var flag_tel_prive = 0;
    var gsm = $('#gsm').val();
    var tel_prive = $('#tel_prive').val();
    if ( gsm == '' && tel_prive == '')
    {
        event.preventDefault();
    }
    else
    {
        var reg_tel = /^[0-9]{10,}$/;
        
        if (tel_prive != '')
        {
            flag_telefoon = 1;
            var val_tel = $('#tel_prive').val();
            // trim 
            val_tel = val_tel.trim();
            // spaces verwijderen
            val_tel = val_tel.split(' ').join('');
            // streepjes verwijderen
            val_tel = val_tel.split('-').join('');
                
            if (reg_tel.test(val_tel) == false) 
            {
                event.preventDefault();
                $('input[name=tel_prive]').addClass('border_red');
                flag_tel_prive = 1;
            }
        }
        
        if (gsm != '')
        {
            flag_telefoon = 1;
            var val_gsm = $('#gsm').val(); 
            // trim 
            val_gsm = val_gsm.trim();
            // spaces verwijderen
            val_gsm = val_gsm.split(' ').join('');
            // streepjes verwijderen
            val_gsm = val_gsm.split('-').join('');
                
            if (reg_tel.test(val_gsm) == false) 
            {
                event.preventDefault();
                $('input[name=gsm]').addClass('border_red');
                flag_gsm = 1;
            }
        } 
    }
    
    
    $('[type=text]').each(function() 
    {
        var name = $(this).attr('name');
            
        if(!$(this).val().length && name != 'tussenvoegsel' && name != 'arr_tv[]' && name != 'gsm' && name != 'tel_prive') 
        {
            event.preventDefault();
            $(this).addClass('border_red');
        }
        else
        {
            $(this).removeClass('border_red');
            
            
            if ( flag_telefoon == 0)
            {
                $('input[name=gsm]').addClass('border_red');
                $('input[name=tel_prive]').addClass('border_red');                
            }
            else
            {
                if (flag_gsm == 1)
                    $('input[name=gsm]').addClass('border_red');
                if (flag_tel_prive == 1)
                    $('input[name=tel_prive]').addClass('border_red');
            }
            
            if (name == 'email')
            {
                var val_email = $('#email').val();
                var reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                if (reg.test(val_email) == false) 
                {
                    event.preventDefault();
                    $(this).addClass('border_red');
                }
            }
        }        
    });
});