function load_page(content,page=false,fil=false){
	$("ul#menu li.active").removeClass("active");
    $("li#"+content).addClass("active");
    var p=page?"&page="+page:"";
    var f=fil?"&date="+fil:"";
    $("#content").load("action.php?content="+content+p+f,function(responseTxt,statusTxt){
        if(statusTxt=="success"){
            window.history.pushState('/index.php?content='+content+p+f, null, '/index.php?content='+content+p+f);
            document.title=content+" Vacations";
            if($("#datepicker").length){
                datePicker();
                if(f!=""){
                    var d=fil.split(",");
                    $("input#from").val(d[0]);
                    $("input#to").val(d[1]);
                }
            }
        }
    });
    if(content=='approve'){
    	pending_requests();
    }
}

function pending_requests(){
	$.get("action.php?content=pending", function(data,status){
		if(data>0){
			$('li#approve span').text(data);
		}
		else{
			$('li#approve span').text('');
		}
	})
}

$(document).ready(function(){
    var urlVars=getUrlVars();
    if(typeof urlVars['content'] !== 'undefined'){
        var p='',
            f='';
        if('page' in urlVars){
            p=urlVars['page']?urlVars['page']:"";
        }
        if('date' in urlVars){
            f=urlVars['date']?urlVars['date']:"";
        }
        load_page(urlVars['content'],p,f);
    }
    else{
        load_page('approved');
    }
	pending_requests();    
});

function request_vacation(user_id,start_date,end_date){
    $.post("action.php?action=addnew",
    {
        user_id: user_id,
        from: start_date,
        to: end_date
    },
    function(data, status){
        if(data==true){
        	pending_requests();
        	load_page('approved');
        }
        else{
            $("#errortxt").text(data);
            $("#error").modal();
        }           
    });
}

function approve_vacation(id,user_id,page){
    $.post("action.php?action=vacationApprove",
    {
        id: id,
        user_id: user_id
    },
    function(data, status){
        if(data==true){
        	pending_requests();
            load_page('approve',page)
        }
        else{
            $("#errortxt").text(data);
            $("#error").modal();
        }
    });
}

function reject_vacation(id){

    $.post("action.php?action=vacationReject",
    {
        id: id
    },
    function(data, status){
    	pending_requests();
        load_page('approve');
    });
}

function remove_vacation(id,userId){
    $.post("action.php?action=vacationRemove",
    {
        id: id,
        user_id: userId
    },
    function(data, status){
    	$('#confirm').modal('hide');
    	$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
    	pending_requests();
    	var page=$("ul.pagination li.active a").data('id');
        load_page('approved',page);
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

function applyFilter(content){
    if(missingDateCheck()){
        var filter=$("input#from").val()+","+$("input#to").val();
        var page=$("ul.pagination li.active a").data('id');
        pending_requests();
        load_page(content,page,filter);
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
        $.post("action.php?action=adduser",
        {
            name: name,
            days: days
        },
        function(data, status){
            if(data==true){
                pending_requests();
                load_page('approved')
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