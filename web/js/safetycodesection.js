
  $('.select2-dropdown').select2();
     

     var Get_Sf_sec = function (sf_id, sf_code)
     {
          var parentid =0;
           $('#appcase-app_case_sf_code_id').val(sf_id);
             if(sf_code == 0)
         {
          parentid = $('#appcase-app_case_sf_code_id').val();
           $("#osha-subsection2").html("<option value=''> -Select applicable safety code subsection- </option>");
            $("#osha-subsection1").html("<option value=''> -Select applicable safety code section- </option>");
       
             executeAjax
                (
                        //getBaseURL()+"/ajax/get-saftety-code?parentcode=" + parentid
                        "/ajax/get-saftety-code?parentcode=" + parentid
                        ).done(function (r) {
            if (r.length != 0) {
                var options = "<option value=''> -Select applicable safety code section-</option>";
                for (var index in r) {
                    options += "<option value='" + index.trim() + "'>" + r[index] + "</option>";
                }
                $("#osha-subsection1").html(options);
            } else {
                $("#osha-subsection1").html("<option value=''> -Select applicable safety code section- </option>");
            }
            return;
        });

        }else if(sf_code == 1)
         {
          parentid = $('#appcase-app_case_sf_code_id').val();
         
             executeAjax
                (
                        //getBaseURL()+"/ajax/get-saftety-code?parentcode=" + parentid
                        "/ajax/get-saftety-code?parentcode=" + parentid
                        ).done(function (r) {
            if (r.length != 0) {
                var options = "<option value=''> -Select applicable safety code subsection- </option>";
                for (var index in r) {
                    options += "<option value='" + index.trim() + "'>" + r[index] + "</option>";
                }
                $("#osha-subsection2").html(options);
            } else {
                $("#osha-subsection2").html("<option value=''> -Select applicable safety code subsection- </option>");
            }
            return;
        });

        }

     }

 /*   $('#osha-section').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        params: {
        'parentcode':0,
        },
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#appcase-app_case_sf_code_id').val(suggestion.data);
        }
    }).blur(function() {
    if($('#osha-section').val().length == 0){
         $('#osha-section').val('');
        }        
    })
    .focus(function() {
    if($('#osha-section').val().length == 0){
         $('#osha-section').val('');
        }        
    });


    $('#osha-subsection1').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        params: {
        'parentcode':function() {
            return $('#appcase-app_case_sf_code_id').val();
         },
        },
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#appcase-app_case_sf_code_id').val(suggestion.data);
        }
    }).blur(function() {
    if($('#osha-subsection1').val().length == 0){
         $('#osha-subsection1').val('');
        }        
    })
    .focus(function() {
    if($('#osha-section').val().length == 0){
         $('#osha-subsection1').val('');
        }        
    });


    $('#osha-subsection2').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        params: {
        
         'parentcode':function() {
            return $('#appcase-app_case_sf_code_id').val();
         },
        },
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#appcase-app_case_sf_code_id').val(suggestion.data);
        }
    }).blur(function() {
    if($('#osha-subsection2').val().length == 0){
         $('#osha-subsection2').val('');
        }        
    })
    .focus(function() {
    if($('#osha-subsection2').val().length == 0){
         $('#osha-subsection2').val('');
        }        
    });*/

function getBaseURL() {
    var arr = window.location.href.split("/web/");
    arr[0]+="/web/";
    return arr[0];
}
