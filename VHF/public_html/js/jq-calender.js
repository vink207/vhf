// declare global variables
var selectedDates = [];
var ocDays = [];
var ocDaysTime = [];
var ocDaysArrival = [];
var ocDaysDeparture = [];
var start_date = '';

// fill array occupies days with records database
fetchOcDays();

$(function() {

   var cur = -1, prv = -1;
   
   $('#jrange div')
      .datepicker({              
        minDate:0,
        showOn: 'both',
		dateFormat: 'dd/mm/yy',
        numberOfMonths: 3,
        onChangeMonthYear: fetchOcDays,
        beforeShowDay: function ( date ) 
        {            
            // timestamp mlsec
            var dt = date.getTime();
            
            // Y-m-d
            var y = date.getFullYear();            
            var m = date.getMonth();
            m++;
            var d = date.getDate();
            var day = y+'-'+m+'-'+d;
            
            
            // weekday
            var wkd = date.getDay();
            
            // dag
            var n = date.toDateString(); 
            
            // flags
            var flag_ocday = 0;
            var flag_arrival = 0;
            var flag_departure = 0;
            var flag_range = 0; 
            
            // dates between arrival and departure    
            for (var i = 0; i < ocDays.length; i++) 
            {                
                if (ocDays[i] == day) 
                {
                    flag_ocday = 1;
                }
            }
            // dates arrival
            for (var i = 0; i < ocDaysArrival.length; i++) 
            {                
                if (ocDaysArrival[i] == day) 
                {
                    flag_arrival = 1;
                }
            }
            // dates departure
            for (var i = 0; i < ocDaysArrival.length; i++) 
            {                
                if (ocDaysDeparture[i] == day) 
                {
                    flag_departure = 1;
                }
            }
            
            // check availability
            var bool = false;
            for (var i = 0; i < ocDays.length; i++) 
            {
                if (ocDaysTime[i] >= Math.min(prv, cur) && ocDaysTime[i] <= Math.max(prv, cur)) 
                { 
                    bool = true;
                }
            }
            if (bool == false)
            {
                if ( (date.getTime() >= Math.min(prv, cur) && date.getTime() <= Math.max(prv, cur)) )
                    flag_range = 1;
            }
            
            if (flag_ocday == 1)
            {
                return [false, 'geboekt', 'niet beschikbaar']; // [0] = true | false if this day is selectable, [1] = class to add, [2] = tooltip to display
            }
            else if (flag_range == 1)
            {
                return [true, ( (date.getTime() >= Math.min(prv, cur) && date.getTime() <= Math.max(prv, cur)) ? 'date-range-selected' : '')];
            }
            else if (flag_arrival == 1)
            {
                return [true, 'aankomst', '']; // [0] = true | false if this day is selectable, [1] = class to add, [2] = tooltip to display
            } 
            else if (flag_departure == 1)
            {
                return [true, 'vertrek', '']; // [0] = true | false if this day is selectable, [1] = class to add, [2] = tooltip to display
            }            
            else if (wkd == 6)
            {
                return [true, 'saturday', ''];
            }
            else
            {
                return [true, '','beschikbaar'];
            }           
        },
        onSelect: function ( dateText, inst ) 
        {
            var d1, d2;
            
            prv = cur;            
            
            // timestamp selected date
            cur = (new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay)).getTime();
            
            if ( prv == -1 || prv == cur )
            {
                prv = cur;
                $('#jrange input').val( dateText );
            } 
            else 
            {
                // check availability
                var bool = false;
                for (var i = 0; i < ocDays.length; i++) 
                {
                    if (prv < cur)
                    {
                        if (ocDaysTime[i] >= prv && ocDaysTime[i] <= cur) 
                        { 
                            bool = true;
                        }
                    }
                    else
                    {
                        if (ocDaysTime[i] >= cur && ocDaysTime[i] <= prv) 
                        { 
                            bool = true;
                        }
                        
                    }
                }
                if (bool == false)
                {                    
                    // di = lowest value of two numbers
                    //d1 = $.datepicker.formatDate( 'dd/mm/yy', new Date(Math.min(prv,cur)), {} );
                    d1 = Math.min(prv,cur);
                    // d2 = highest value of two numbers
                    //d2 = $.datepicker.formatDate( 'dd/mm/yy', new Date(Math.max(prv,cur)), {} );
                    d2 = Math.max(prv,cur);
                    $('#jrange input').val( d1+'-'+d2 );
                    
                    // ajax call
                    send_input();
                }
                else
                {                    
                    $('#jrange input').val('');
                    $('#prices').html('');
                }
            }
        }
    });
});

// query for occupied (booked) days in datepicker
function fetchOcDays(year, month)
{     
    // if a month and year were supplied, build a start_date in yyyy-mm-dd format
    if (year != undefined && month != undefined) {
      start_date = year +'-';
      start_date += month +'-';
      start_date += '1';
    }
    
    var huisID = $('#huisID').val();  
    $.ajax(
    {
        url: ABSROOT+"/model/ajax/fetchocdays.php?start_date="+ start_date +"&id="+huisID,
        dataType: 'json',
        async: false,
        success: function(data)
        {
            // empty array occupied days
            ocDays.length = 0;
            ocDaysTime.length = 0;
            // days
            $.each(data.days, function(index, value) {
                ocDays.push(value); // add this date to the ocDays array 
            });
            // timestamps
            $.each(data.timestamps, function(index, value) {
                ocDaysTime.push(value); // add this date to the ocDaysTime array 
            });
            // arrivals
            $.each(data.arrivals, function(index, value) {
                ocDaysArrival.push(value); // add this date to the ocDaysArrival array 
            });
            // departures
            $.each(data.departures, function(index, value) {
                ocDaysDeparture.push(value); // add this date to the ocDaysDeparture array 
            });
        }
    });
}

function send_input()
{
    var range = $('#jrange input').val();
    var huisID = $('#huisID').val(); 
    
    $.ajax(
    {
        url: ABSROOT+"/model/ajax/bk_default.php?range="+ range +"&id="+huisID,
        async: false,
        success: function(ajaxReturn)
        {
            $('#prices').html(ajaxReturn);
        }      
    });
}