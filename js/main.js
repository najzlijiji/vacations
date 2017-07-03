$(document).ready(function(){
    var urlVars=getUrlVars();
    if(typeof urlVars['controller'] !== 'undefined' && urlVars['action'] !== 'undefined'){
        var p='',
            f='';
        if('page' in urlVars){
            p=urlVars['page']?urlVars['page']:"";
        }
        if('date' in urlVars){
            f=urlVars['date']?urlVars['date']:"";
        }
        load_page(urlVars['controller'],urlVars['action'],p,f);
    }
    else{
        load_page('vacations','approved');
    }
});

function pending_requests(data){
    if(data>0){
        $('li#pending span').text(data);
    }
    else{
        $('li#pending span').text('');
    }
}


function load_page(controller,action,p=false,f=false){
    $("ul#menu li.active").removeClass("active");
    $("li#"+action).addClass("active");
    var p=p?"&page="+p:"";
    var f=f?"&date="+f:"";
    $("#content").load('routes.php?controller='+controller+'&action='+action+p+f,function(responseTxt,statusTxt){
        if(statusTxt=="success"){
            window.history.pushState('?controller='+controller+'&action='+action+p+f, null, '?controller='+controller+'&action='+action+p+f);
            document.title=action+" "+controller;
            if($("#datepicker").length){
                datePicker();
            }
        }
    });
    if(action=='pending'){
    }
}

function request_vacation(userId,startDate,endDate){
    $.post("routes.php?action=requestVacation",
    {
        userId: userId,
        startDate: startDate,
        endDate: endDate
    },
    function(data, status){
        if(data=='Success'){
        	load_page('vacations','approved');
        }
        else{
            $("#errortxt").text(data);
            $("#error").modal();
        }           
    });
}

function approve_vacation(vacationId,userId,page){
    $.post("routes.php?action=approveVacation",
    {
        vacationId: vacationId,
        userId: userId
    },
    function(data, status){
        if(data=='Success'){
            load_page('vacations','approved',page);
        }
        else{
            $("#errortxt").text(data);
            $("#error").modal();
        }
    });
}

function reject_vacation(vacationId){

    $.post("routes.php?action=rejectVacation",
    {
        vacationId: vacationId
    },
    function(data, status){
        load_page('vacations','approved');
    });
}

function remove_vacation(vacationId,userId){
    $.post("routes.php?action=cancelVacation",
    {
        vacationId: vacationId,
        userId: userId
    },
    function(data, status){
    	$('#confirm').modal('hide');
    	$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
    	var page=$("ul.pagination li.active a").data('id');
        load_page('vacations','approved',page);
    });
}

$(document).on("click", "#removeButton", function () {
     var reId = $(this).data('id');
     var info = $(this).data('info');
     var userId = $(this).data('userid');
     $("#modalinfo").text(info);
     $("#btnyes").attr("onclick","remove_vacation("+reId+","+userId+");");
});

function datePicker() {
    var dateFormat = "yy-mm-dd",
      from = $("#from")
        .datepicker({
          dateFormat: dateFormat,
          firstDay: 1,
          changeMonth: true,
          numberOfMonths: 2
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#to" ).datepicker({
        dateFormat: dateFormat,
        firstDay: 1,
        changeMonth: true,
        numberOfMonths: 2
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
 
      return date;
    }
};

function submitcheck(){
    if(missingDateCheck()){
        request_vacation($("#name").val(),$("#from").val(),$("#to").val());
        event.preventDefault();
    }
}

function applyFilter(){
    if(missingDateCheck()){
        var action=$("ul#menu li.active").attr('id');
        var filter=$("input#from").val()+","+$("input#to").val();
        var page=$("ul.pagination li.active a").data('id');
        load_page('vacations',action,1,filter);
    }
}

function missingDateCheck(){
    if($("#from").val()=="" || $("#to").val()==""){
        if($("#from").val()==""){
            $("#fromerror").addClass("has-error");
            $("#fromerror").change(function(){
                $("#fromerror").removeClass("has-error");
            });
        }
        if($("#to").val()==""){
            $("#toerror").addClass("has-error");
            $("#toerror").change(function(){
                $("#toerror").removeClass("has-error");
            });
        }
        return false;
    }
    else{
        return true;
    }
}

function adduser(){
    var name = $("#name").val(),
        days = $("#days").val();
    if(name=="" || days==""){
        if(name==""){
            $("#nameerror").addClass("has-error");
            $("#nameerror").change(function(){
                $("#nameerror").removeClass("has-error");
            });
        }
        if(days==""){
            $("#dayserror").addClass("has-error");
            $("#dayserror").change(function(){
                $("#dayserror").removeClass("has-error");
            });
        }
        return false;
    }
    else{
        event.preventDefault();
        $.post("routes.php?action=addUser",
        {
            name: name,
            days: days
        },
        function(data, status){
            if(data=="Success"){
                load_page('vacations','approved');
            }
            else{
                $("#errortxt").text(data);
                $("#error").modal();
            }
        });        
    }
}

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}