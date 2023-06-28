/**
 * Document Ready
 */
$(document).ready( function()
{
  //GetAutoSaveData();
   AutoCreateIssue();

   $("#discardbutton").click(function(){
        $('#discardconfirm-modal').modal('show');
   });

  $("#discardyes-bth").click(function(){
          DeleteDraftissue();
   });
  $("#discardno-bth").click(function(){
        $('#discardconfirm-modal').modal('hide');
   });


   $("#IssueCreateClose").click(function(){
        $("#Pageexitconfirm").modal("show");

   });

   $("#Pageexitconfirmdelete").click(function(){
        DeleteDraftissue();
       
   });

   $("#Pageexitconfirmsave").click(function(){
       var Issuetype = getParameterByName("type");       
       AutoSubmitIssue(Issuetype);
       $("#Pageexitconfirm").modal("hide");
       window.location.href =  getBaseURL();
   });

});


function DeleteDraftissue(){
     var Issuetype = getParameterByName("type");
    if(Issuetype != "" || Issuetype != 'undefined' ){     
     var url = "/ajax/del-draft-issue?typeid=" + Issuetype;
      $.ajax({
        type:"GET",
        url:url,
        success: function(response){
             window.location.href =  getBaseURL();
        }
    });
  }
}


function AutoCreateIssue(){
var timeLeft = 30;   
var Issuetype = getParameterByName("type");
var Timeout = false;

setInterval(function(){ 
  if (timeLeft == 0 && Timeout == false) {
        Timeout = true;
        var success = AutoSubmitIssue(Issuetype);
        if(success)
          timeLeft = 30;
      } else {
        if(timeLeft >= 0){
        $("#autosave").empty().append('Auto Save: '+ timeLeft);
        Timeout = false;
        timeLeft--;
        }
        
      } }, 1000);    

}

function AutoSubmitIssue(Issuetype){
  var IsValidated  = false;
  if($('#appcase-jobsite_id').val() != "" )
    IsValidated = true;
  else if($('#sub-jobsite-id-select').val() != "" )
    IsValidated = true;
  else if($('#appcase-contractor_id').val() != "" )
    IsValidated = true;
  else if($('#input-affected_employee-display').val() != "-Choose an affected employee-" )
    IsValidated = true;
  else if($('#appcase-building_id').val() != "" )
    IsValidated = true;
  else if($('#appcase-floor_id').val() != "" )
    IsValidated = true;
  else if($('#area-id-select').val() != "" )
    IsValidated = true;
  else if($('#appcase-app_case_status_id').val() != "" )
    IsValidated = true;
  else if($('#appcase-app_case_status_id').val() != "" )
    IsValidated = true;
  else if($('#appcase-app_case_priority_id').val() != "" )
    IsValidated = true;
  else if($('#appcase-app_case_priority_id').val() != "" )
    IsValidated = true;
  else if($('#foreman-id-select').val() != "")
     IsValidated = true;
  else if($('#appcase-trade_id').val() != "")  
     IsValidated = true;
  else if($('#appcase-additional_information').text() != "")  
     IsValidated = true;



  if(IsValidated){
     var formData = $("#IssueForm").serialize();
     var url = "/ajax/auto-save?typeid=" + Issuetype;
      $.ajax({
        type:"POST",
        url:url,
        data:formData,//only input
        success: function(response){
             AutoCreateIssue(); 
        }
    });
  }else{
    return true; 
  }
   
}

function GetAutoSaveData(){
  var Issuetype = getParameterByName("type");
    var url = "/ajax/get-auto-savedata?typeid=" + Issuetype;
      $.ajax({
        type:"GET",
        url:url,
        success: function(response){
             var fromdatajson = JSON.parse(response); 
              for (var key in fromdatajson.AppCase) {
                  if (fromdatajson.AppCase.hasOwnProperty(key)) {
                    var val = fromdatajson.AppCase[key];
                    var formkeypair = '"[name = "AppCase["'+key+'"]"]"';
                    $("[name='AppCase["+key+"]'").val(val);
                    console.log(val);
                  }
                }


        }
    });

}


    function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
