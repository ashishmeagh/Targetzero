/**********************************************************************************************************************/
/*                                                                                                                    */
/* Properties                                                                                                         */
/*                                                                                                                    */
/**********************************************************************************************************************/
var scrollbarList = [];

/**********************************************************************************************************************/
/*                                                                                                                    */
/* Events                                                                                                             */
/*                                                                                                                    */
/**********************************************************************************************************************/

(function(){
    //Get saved layout type from LocalStorage
    var layoutStatus = localStorage.getItem('ma-layout-status');
    if (layoutStatus == 1) {
        $('body').addClass('sw-toggled');
        $('#tw-switch').prop('checked', true);
    }else{
        $('body').removeClass('sw-toggled');
        $('#tw-switch').removeProp('checked');
    }

    $('body').on('change', '#toggle-width input:checkbox', function(){
        if ($(this).is(':checked')) {
            $('body').addClass('toggled sw-toggled');
            localStorage.setItem('ma-layout-status', 1);
        }
        else {
            $('body').removeClass('toggled sw-toggled');
            localStorage.setItem('ma-layout-status', 0);
        }
    });
})();

/**
 * Document Ready
 */
$(document).ready( function()
{

    // Init Components
    initSidebar();
    initInputs();
    initDatePicker();
    initDateTimePicker();
    initMultiSelect();
    initButtonAnimation();
    initTooltip();
    initPopover();
    initDefaultValues();
    initErrorControl();
    initPriorityOrder();
    initPasswordCheckbox();
    initTrimDescription();

    initCreateButtonsCompatibility();

    initJobsiteImport();
    initContractorImport();
    initUserImport();

    initDuplicatedContractorCheck();
    initDuplicatedUserCheck();
    initPreventMultipleComments();

    initDashboard();
    initContractorJobsiteValidation();

    // Scrollbars
    //scrollbarList.push("html");
    //scrollbarList.push("#sidebar");
    initScrollbar();
    greetings();
    initSidebarClicks();
   // initAutoUserName();/*Auto User name is diasabled*/
   DisableUserName();


});

/**
 * Window Resize
 */
$(window).resize(function()
{
    resizeCallback();
});

/**
 * Resize Callback
 */
function resizeCallback()
{
    // Scrollbars
    resizeScrollbar();
}

/**********************************************************************************************************************/
/*                                                                                                                    */
/* Components & UI                                                                                                    */
/*                                                                                                                    */
/**********************************************************************************************************************/
/**
 * says hi after login
 */
function greetings(){
    var prev_url = document.referrer;
    var login_regex = /login\/index/;
    var match = login_regex.exec(prev_url);
    var name = $(".user_name" ).text();
    if(match){
        notify( "info", "Welcome, "+name+"!" );
    }
}


/**
 * Init Sidebar
 */
function initSidebar()
{

    (function(){
        //Toggle
        $('body').on('click', '#menu-trigger, #chat-trigger', function(e){
            e.preventDefault();
            var x = $(this).data('trigger');

            $(x).toggleClass('toggled');
            $(this).toggleClass('open');

            //Close opened sub-menus
            $('.sub-menu.toggled').not('.active').each(function(){
                $(this).removeClass('toggled');
                $(this).find('ul').hide();
            });

            $('.profile-menu .main-menu').hide();

            if (x == '#sidebar') {
                $elem = '#sidebar';
                $elem2 = '#menu-trigger';

                $('#chat-trigger').removeClass('open');

                if (!$('#chat').hasClass('toggled')) {
                    $('#header').toggleClass('sidebar-toggled');
                }
                else {
                    $('#chat').removeClass('toggled');
                }
            }

            if (x == '#chat') {
                $elem = '#chat';
                $elem2 = '#chat-trigger';

                $('#menu-trigger').removeClass('open');

                if (!$('#sidebar').hasClass('toggled')) {
                    $('#header').toggleClass('sidebar-toggled');
                }
                else {
                    $('#sidebar').removeClass('toggled');
                }
            }

            //When clicking outside
            if ($('#header').hasClass('sidebar-toggled')) {
                $(document).on('click', function (e) {
                    if (($(e.target).closest($elem).length === 0) && ($(e.target).closest($elem2).length === 0)) {
                        setTimeout(function(){
                            $($elem).removeClass('toggled');
                            $('#header').removeClass('sidebar-toggled');
                            $($elem2).removeClass('open');
                        });
                    }
                });
            }
        })

        //Submenu
        $('body').on('click', '.sub-menu > a', function(e){
            e.preventDefault();
            $(this).next().slideToggle(200);
            $(this).parent().toggleClass('toggled');
        });
    })();


    // Add Body Class


    //
    //$("#header").addClass("sidebar-toggled");
    //if( $("section#login")[0] )
    //{
    //    $("body").addClass("login-container");
    //}
    //else
    //{
    //    $("#header").addClass("sidebar-toggled");
    //    $("body").addClass("toggled sw-toggled");
    //}
    //
    //if(sessionStorage.getItem("activeMenuItem")){
    //    var menu_id = sessionStorage.getItem("activeMenuItem");
    //    $(".main-menu").find("[data-menu='" + menu_id + "']").addClass("toggled" ).children().css("display", "block");
    //}
    //
    //// Collapse Buttons
    //$(".sub-menu > a").click(function(e)
    //{
    //    e.preventDefault ();
    //    $(".sub-menu .toggled").removeClass("toggled").children().css("display", "none");
    //    $(this).next().slideToggle(200, resizeScrollbar);
    //    $(this).parent().toggleClass("toggled");
    //});
}
/**
 * Init Sidebarclick
 */
function initSidebarClicks()
{
    // Collapse Buttons
    $(".sub-menu").click(function()
    {
        var menu_id = $(this).data("menu");
        if($(this).hasClass("toggled")){
            //add to local storage
            sessionStorage.setItem("activeMenuItem", menu_id);
        }else{
            //remove from local storage
            sessionStorage.removeItem("activeMenuItem");
        }
    });
    return;
}
/**
 * Init initAutoUserName
 */
function initAutoUserName()
{
    $("#user-email" ).focusout(function(){
        if($("#user-email" ).val() && !$("#user-user_name" ).val()){
            var mail = $("#user-email" ).val();
            var username = mail.split("@");
            var username = username[0];
            $("#user-user_name" ).val( username );
        }
    })
    return;
}

/**
 * Disable Username and Password
 */
function DisableUserName()
{
   /* $("#user-role_id" ).change(function(){

     $('.field-user-user_name > div.help-block').empty();
     $('.field-user-password > div.help-block').empty();
     $('.field-user-password').removeClass('has-error');
     $('.field-user-user_name').removeClass('has-error');     
       $("#user-user_name" ).val('');
       $("#user-password" ).val('');
        if($("#user-role_id" ).val() > 6 ){
            $("#user-user_name" ).prop('disabled', true);
            if($("#Isnewrecord" ).val() == 1)
               $("#user-password" ).prop('disabled', true);
        }else{
             $("#user-user_name" ).prop('disabled', false);
             if($("#Isnewrecord" ).val() == 1)
            $("#user-password" ).prop('disabled', false);
        }
    })
    return;*/
}


/**********************************************************************************************************************/
/*                                                                                                                    */
/* Scrollbars                                                                                                         */
/*                                                                                                                    */
/**********************************************************************************************************************/
/**
 * Init Scrollbar
 */
function initScrollbar()
{
    for( var i=0, l=scrollbarList.length; i<l; i++ )
    {
        configureScrollbar(scrollbarList[i]);
    }
    resizeScrollbar();
    if( $(".lastChanges")[0] ){
        $ ( ".lastChanges" ).niceScroll ( {
            cursorcolor:       "rgba(0,0,0,0.5)",
            cursorborder:      0,
            cursorborderradius:"3px",
            cursorwidth:       "6px",
            bouncescroll:      false,
            mousescrollstep:   100,
            autohidemode: false
        } );
    }
}

/**
 * Configure Scrollbar
 * @param $selector
 */
function configureScrollbar($selector)
{

    $ ( $selector ).niceScroll ( {
        cursorcolor:       "rgba(0,0,0,0.5)",
        cursorborder:      0,
        cursorborderradius:"3px",
        cursorwidth:       "6px",
        bouncescroll:      false,
        mousescrollstep:   100
    } );
}

/**
 * Resize Scrollbar
 */
function resizeScrollbar()
{
    for( var i=0, l=scrollbarList.length; i<l; i++ )
    {
        $(scrollbarList[i]).getNiceScroll().resize();
    }
}

/**********************************************************************************************************************/
/*                                                                                                                    */
/* Forms                                                                                                              */
/*                                                                                                                    */
/**********************************************************************************************************************/
/**
 * Init Inputs
 */
function initInputs()
{
    // Add Class FG - Line
    $.each( $(".form-control"), function($i, $obj)
    {
        $($obj).parent().addClass("fg-line");
    });

	// Detele "fg-line" class on search forms index
	$.each( $('.filters td'), function($i, $obj){
		$($obj).removeClass('fg-line');
	});

    // Add / Remove Animated Border
    if( $(".fg-line")[0] )
    {
        $("body").on("focus", ".form-control", function()
        {
            $(this).closest(".fg-line").addClass("fg-toggled");
        });

        $("body").on("blur", ".form-control", function()
        {
            var p = $(this).closest(".form-group");
            var i = p.find(".form-control").val();
            if( p.hasClass("fg-float") )
            {
                if( i.length == 0 )
                {
                    $(this).closest(".fg-line").removeClass("fg-toggled");
                }
            }
            else
            {
                $(this).closest(".fg-line").removeClass("fg-toggled");
            }
        });
    }

    // Add Border For Pre Valued FG - Flot Textfeilds
    if( $(".fg-float")[0] )
    {
        $( ".fg-float .form-control" ).each( function()
        {
            var i = $(this).val();
            if( !i.length == 0 )
            {
                $(this).closest(".fg-line").addClass("fg-toggled");
            }
        });
    }
}

/**
 * Init Date Picker
 */
function initDatePicker()
{
	if( $('.dashboard-date-picker').length != 0 )
	{
        // Init Date Picker
        $(".dashboard-date-picker").datetimepicker
        ({
            useCurrent: true,
            format: "MMMM DD, YYYY",
			maxDate: new Date(),
            keepOpen: true
        });

        // Set Default Date
		//$(".incident-date-picker").val($defaultDate);
        return;
	}
	if( $('.incident-date-picker').length != 0 )
	{
		//$defaultDate = $(".incident-date-picker").val();

        // Init Date Picker
        $(".incident-date-picker").datetimepicker
        ({
            useCurrent: true,
            format: "MMMM DD, YYYY",
			maxDate: new Date(+new Date() + 1000)
        });

        // Set Default Date
		//$(".incident-date-picker").val($defaultDate);
        return;
	}

    if( $(".date-picker").length != 0 )
    {
        // Default Date
        $defaultDate = $(".date-picker").val();

        // Init Date Picker
        $(".date-picker").datetimepicker
        ({
            useCurrent: false,
            format:     "MMMM DD, YYYY"
        });

        // Set Default Date
        $(".date-picker").val($defaultDate);
        return;
    }
}

/**
 *
 */
function initDateTimePicker()
{
	if( $(".time-picker").length != 0 )
	{
		//Init Date Time Picker
		$(".time-picker").datetimepicker({
    	    format: 'LT'
    	});
	}
}

/**
 * Init Multi Select
 */
function initMultiSelect()
{
    if( $("#user-jobsite-select")[0] )
    {
        $('#user-jobsite-select').multiSelect
        ({
            //selectableHeader: "<div class='ms-custom-header'>All Jobsites</div>",
            //selectionHeader: "<div class='ms-custom-header'>My Jobsites</div>",

            afterSelect: function($val)
            {
                //alert("Select value: "+$val);
            },
            afterDeselect: function($val)
            {
                //alert("Deselect value: "+$val);
            }
        });

        // Add Scrollbar
        configureScrollbar(".ms-selectable");
        configureScrollbar(".ms-selection");
    }
}
/**
 * Init Button Animation
 */
function initButtonAnimation()
{
    $('.material-button-trigger').hover(function(){
        $('.btn').addClass('animacionVer');
    })
    $('.material-button-container').mouseleave(function(){
        $('.btn').removeClass('animacionVer');
    })
}
/**
 * Init Tooltips
 */
function initTooltip()
{
    $('[data-toggle="tooltip"]').tooltip();
}
/**
 * Init Popover
 */
function initPopover()
{
    $('[data-toggle="popover"]').popover();
}
/**
 * Init default values
 */
function initDefaultValues()
{
    if($("#input-safety-code-display")[0]){
        if($("#input-safety-code-display").val() == ""){
            $("#input-safety-code-display").val('-Choose a safety code-');
        }
    }
    if($(".user-view")[0]){
        if(!$("#user-jobsite li")[0]){
            $("#user-jobsite" ).append("<li>No jobsites assigned</li>")
        }
    }
   if($("#input-affected_employee-display")[0]){
        if($("#input-affected_employee-display").val() == ""){
            $("#input-affected_employee-display").val('-Choose an affected employee-');
        }
    } 
}

/**
 * Init error control at create
 */
function initErrorControl()
{
    if($(".app-case-create")[0]){
        $("#input-safety-code-display" ).change(function(){
            validateSafetyCodeError();
        });
        
        $("#input-affected_employee-display" ).change(function(){           
          validateAffectedEmployeeError();
      });
      
      /* $("#affected-user-display" ).change(function(){           
          validateAffectedError();
      }); */
      
        $("#w0" ).submit(function(){
            validateSafetyCodeError();
             validateAffectedEmployeeError();
        });
      
    }
}

function validateSafetyCodeError(){
  if($("#input-safety-code-display").val() == "" || $("#input-safety-code-display").val() == "-Choose a safety code-"){
      $(".field-appcase-app_case_sf_code_id" ).next().addClass('has-error');
      $("#input-safety-code-display" ).next().text("Safety code cannot be blank.");
  }else{
      $(".field-appcase-app_case_sf_code_id" ).next().removeClass('has-error').addClass("has-success");
      $("#input-safety-code-display" ).next().text("");
  }
}

/*function validateAffectedError(){
  debugger;
  if($("#affected-user-display").val() == "" || $("#affected-user-display").val() == "-Choose an affected employee-"){
      $(".field-appcase-app_case_sf_code_id" ).next().addClass('has-error');
      $("#affected-user-display" ).next().text("Affected employee cannot be blank.");
  }else{
      $(".field-appcase-app_case_sf_code_id" ).next().removeClass('has-error').addClass("has-success");
      $("#affected-user-display" ).next().text("");
  }
} */

function validateAffectedEmployeeError(){
  if($("#input-affected_employee-display").val() == "" || $("#input-affected_employee-display").val() == "-Select the affected employee ID number or name-"){
      $(".field-appcase-affected_user_id" ).next().addClass('has-error');
      $("#input-affected_employee-display" ).next().text("Affected employee cannot be blank.");
  }else{
      $(".field-appcase-affected_user_id" ).next().removeClass('has-error').addClass("has-success");
      $("#input-affected_employee-display" ).next().text("");
  }
}

/**
 * Init priority order
 */
function initPriorityOrder()
{
    if($(".app-case-create")[0]){
        var select = $("#appcase-app_case_priority_id option");
        $(select[1]).insertAfter( $( select[ 3 ] ) );
    }
}
/**
 * Init checkbox status
 */
function initPasswordCheckbox()
{
    if($(".passwordChange")[0]){
        $("#user-password" ).attr("disabled", true);
        $(".passwordChange" ).click(function(){
            if( $("#user-password" ).attr("disabled") ){
                $("#user-password" ).removeAttr("disabled");
            }else{
                $("#user-password" ).attr("disabled", true);
            }
        })
    }
}
/**
 * Init trim description
 */
function initTrimDescription()
{
    $(".app-case-index .truncate").each (function () {
        if ($(this).text().length > 150){
            $(this).text($(this).text().substring(0,150) + '...')
        };
    });
}
/**
 * Init create buttons
 */
function initCreateButtonsCompatibility()
{
    $(".material-button-container .material-btn").each (function () {
        $(this).click(function(){
            href = $(this).children().attr("href");
            window.location.href = href;
        });
    });
}

function initJobsiteImport()
{
    if($(".import-jobsite")[0]){
        $("input:file").change(function (){
            var fileName = $(this).val();
            fileName = fileName.split("\\");
            fileName = fileName.pop();
            $("label[for='file-upload']" ).html(fileName);
            //$(".filename").html(fileName);
        });

        $("#w0" ).submit(function(e){
            var fileName = $("input:file").val();
            if( !fileName ){
                e.preventDefault();
                return false;
            }
            $(".preloader-transparent" ).css("display", "block");
            setInterval(function(){
                if ($.cookie("fileLoading")) {//testing man testing man me asignar proyectos y levanto bugs
                    switch($.cookie("fileErrors"))
                    {
                        case "all":
                            // clean the cookie for future downoads
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            //redirect
                            //location.href = getBaseURL() + "/import/jobsite?r=success";
                            //hide preloader
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Jobsites import",
                                text:              "Import has failed. For more information, open the downloaded excel below.",
                                type:              "error",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                        case "some":
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Jobsites import",
                                text:              "Import has been completed with some errors. For more information, open the downloaded excel below.",
                                type:              "info",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                        case "none":
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Jobsites import",
                                text:              "Import has been completed successfully. For more information, open the downloaded excel below.",
                                type:              "success",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                    }
                }
            },500);
        });
    }

}
function initContractorImport()
{
    if($(".import-contractor")[0]){
        $("input:file").change(function (){
            var fileName = $(this).val();
            fileName = fileName.split("\\");
            fileName = fileName.pop();
            $("label[for='file-upload']" ).html(fileName);
            //$(".filename").html(fileName);
        });

        $("#w0" ).submit(function(e){
            var fileName = $("input:file").val();
            if( !fileName ){
                e.preventDefault();
                return false;
            }
            $(".preloader-transparent" ).css("display", "block");
            setInterval(function(){
                if ($.cookie("fileLoading")) {
                    switch($.cookie("fileErrors"))
                    {
                        case "all":
                            // clean the cookie for future downoads
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            //redirect
                            //location.href = getBaseURL() + "/import/jobsite?r=success";
                            //hide preloader
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Contractors import",
                                text:              "Import has failed. For more information, open the downloaded excel below.",
                                type:              "error",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                        case "some":
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Contractors import",
                                text:              "Import has been completed with some errors. For more information, open the downloaded excel below.",
                                type:              "info",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                        case "none":
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Contractors import",
                                text:              "Import has been completed successfully. For more information, open the downloaded excel below.",
                                type:              "success",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                    }
                }
            },500);
        });
    }

}
function initUserImport()
{
    if($(".import-user")[0]){
        $("input:file").change(function (){
            var fileName = $(this).val();
            fileName = fileName.split("\\");
            fileName = fileName.pop();
            $("label[for='file-upload']" ).html(fileName);
            //$(".filename").html(fileName);
        });

        $("#w0" ).submit(function(e){
            var fileName = $("input:file").val();
            if( !fileName ){
                e.preventDefault();
                return false;
            }
            $(".preloader-transparent" ).css("display", "block");
            setInterval(function(){
                if ($.cookie("fileLoading")) {
                    switch($.cookie("fileErrors"))
                    {
                        case "all":
                            // clean the cookie for future downoads
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            //redirect
                            //location.href = getBaseURL() + "/import/jobsite?r=success";
                            //hide preloader
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Users import",
                                text:              "Import has failed. For more information, open the downloaded excel below.",
                                type:              "error",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                        case "some":
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Users import",
                                text:              "Import has been completed with some errors. For more information, open the downloaded excel below.",
                                type:              "info",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                        case "none":
                            $.removeCookie("fileLoading");
                            $.removeCookie("fileErrors");
                            $(".preloader-transparent" ).css("display", "none");
                            swal ( {
                                title:             "Users import",
                                text:              "Import has been completed successfully. For more information, open the downloaded excel below.",
                                type:              "success",
                                confirmButtonColor:"#FF6319",
                                confirmButtonText: "OK",
                                closeOnConfirm:    false
                            }, function(){
                                location.reload();
                            });
                            break;
                    }
                }
            },500);
        });
    }

}

/**
 * Init initDuplicatedCheck
 */
function initDuplicatedContractorCheck()
{
    if($(".contractor-create")[0]){

        $("#create-new-contractor").modal({show: false});
        var override_duplicated = 0;
        $("#contractor-form" ).submit(function(event){
            var contractor_name = $("#contractor-contractor").val();
            var existe = checkDuplicatedContractors ( contractor_name );
            var valida = $( '#contractor-form' ).yiiActiveForm('submitForm');
            if(valida && existe && !override_duplicated){
                event.preventDefault();
                $("#create-new-contractor .cancel" ).on("click", function( e ){
                    override_duplicated = 0;
                    $("#create-new-contractor" ).modal('hide');
                    e.preventDefault();
                    return false;
                });
                $("#create-new-contractor .submit" ).on("click", function( e ){
                    override_duplicated = 1;
                    $("#contractor-form").submit();
                    return true;
                });
                $("#create-new-contractor").modal({show: true});
                return false;
            }else if(valida || override_duplicated){
                return true;
            }else{
                event.preventDefault();
            }
        });
    }
}

function initDuplicatedUserCheck(){
    if($(".user-create")[0]){
        $("#create-new-user").modal({show: false});
        var override_duplicated = 0;
        $("#user-form" ).submit(function(event){
            var first_name = $("#user-first_name").val();
            var last_name = $("#user-last_name").val();
            var emp_id = $("#user-employee_number").val();
            var jobsite = $("#user-jobsite-select").val();
            var IsAduser = $("input[type='radio'][name='wtuser-rb']:checked").val();
            var existe = false;

            if(first_name != '' && last_name != '' && emp_id != '' && IsAduser == 0)
              existe = checkDuplicatedUsers( first_name, last_name, emp_id );
            
            if(jobsite == null){
                existe = false;
                $('#user-jobsite-error').removeClass('hidden');
                event.preventDefault();
                return false;
            }else{
                $('#user-jobsite-error').addClass('hidden');
            }
                

            var valida = $( '#user-form' ).yiiActiveForm('submitForm');
            
            if(valida && existe && !override_duplicated){
                event.preventDefault();
                $("#create-new-user .cancel" ).on("click", function( e ){
                    override_duplicated = 0;
                    $("#create-new-user" ).modal('hide');
                    e.preventDefault();
                    return false;
                });
                $("#create-new-user .submit" ).on("click", function( e ){
                    override_duplicated = 1;
                    $("#user-form").submit();
                    return true;
                });
                $("#create-new-user").modal({show: true});
                return false;
            }else if(valida || override_duplicated){
                return true;
            }else{
                event.preventDefault();
            }
        });
    }
    else{
         $("#user-form" ).submit(function(event){
         var jobsite = $("#user-jobsite-select").val();
            if(jobsite == null){
               $('#user-jobsite-error').removeClass('hidden');
                event.preventDefault(); 
                return false;       
            }else{
                $('#user-jobsite-error').addClass('hidden');
            }
        });
    }
}

function initPreventMultipleComments(){
    if($(".app-case-view")[0]){
        $(".app-case-view form" ).submit(function(){
            $(this).find("button[type='submit']").prop('disabled',true);
            //$(".app-case-view button[type='submit']" ).attr('disabled','disabled');
        });
    }
}

function validateUSPhoneNumber(elementValue){
var phoneNumberPattern = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
return phoneNumberPattern.test(elementValue);
}

/**
 * Init dashboard graphs
 */
function initDashboard()
{
    if($(".dashboard-index")[0]){

        $(window).resize(
            function(){
                var options = [];
                options["type"] = $("#type-filter").val();
                options["from"] = $("#from-date").val();
                options["to"] = $("#to-date").val();
                options["scale"] = $("input[type='radio'][name='scale']:checked").val();
                options["jobsite"] = $("#jobsite-filter").val();
                options["subjobsite"] = $("#subjobsite-filter").val();
                options["building"] = $("#building-filter").val();
                options["status"] = $("#status-filter").val();
                options["contractor"] = $("#contractor-filter").val();
                options["trade"] = $("#trade-filter").val();
                options["area"] = $("#area-filter").val();
                options["floor"] = $("#floor-filter").val();
                options["reportType"] = $("#report-type-filter").val();
                options["reportTopic"] = $("#report-topic-filter").val();
                options["recordable"] = $("#recordable-incidents-filter").val();
                options["injuryType"] = $("#injury-type-filter").val();
                options["bodyPart"] = $("#body-part-filter").val();
                options["lostTime"] = $("#lost-time-filter").val();
                options["is_dart"] = $("#dart-filter").val();
                options["dayWeek"] = $("#day-week-filter").val();
                options["timeDayFrom"] = $("#value-lower").text();
                options["timeDayTo"] = $("#value-upper").text();
                options["createdby"] = $("#createdby-filter").val();
                options["affectedby"] = $("#affectedby-filter").val();
                filterGraphs(options);
            }
        );

        var from = $("#from-date").attr('placeholder');
        $("#from-date").val(from);
        $("#from-date").val(from);
        var to = $("#to-date").attr('placeholder');
        $("#to-date").val(to);

        $("#type-filter").change(function(r){
            var type = $(this).val();
            if( type == 3){
                $(".incident-related-filters" ).slideDown();
            }else{
                $(".incident-related-filters" ).slideUp();
            }
        });

        var options = [];
        $(".btn-filter").on("click", function(){
            options["type"] = $("#type-filter").val();
            options["from"] = $("#from-date").val();
            options["to"] = $("#to-date").val();
            options["scale"] = $("input[type='radio'][name='scale']:checked").val();
            options["jobsite"] = $("#jobsite-filter").val();
            options["subjobsite"] = $("#subjobsite-filter").val();
            options["building"] = $("#building-filter").val();
            options["status"] = $("#status-filter").val();
            options["contractor"] = $("#contractor-filter").val();
            options["trade"] = $("#trade-filter").val();
            options["area"] = $("#area-filter").val();
            options["floor"] = $("#floor-filter").val();
            options["reportType"] = $("#report-type-filter").val();
            options["reportTopic"] = $("#report-topic-filter").val();
            options["recordable"] = $("#recordable-incidents-filter").val();
            options["injuryType"] = $("#injury-type-filter").val();
            options["bodyPart"] = $("#body-part-filter").val();
            options["lostTime"] = $("#lost-time-filter").val();
            options["is_dart"] = $("#dart-filter").val();
            options["dayWeek"] = $("#day-week-filter").val();
            options["timeDayFrom"] = $("#value-lower").text();
            options["timeDayTo"] = $("#value-upper").text();
            options["createdby"] = $("#createdby-filter").val();
            options["affectedby"] = $("#affectedby-filter").val();
            filterGraphs(options);
        });

    }
}

function initContractorJobsiteValidation(){

    if($(".contractor-update")[0]){
        var jobsites = new Array;
        $("#user-jobsite-select option:selected" ).each(function(item){
            jobsites.push($(this).attr("value"));
        });
        sessionStorage.setItem('contractorJobsite', JSON.stringify(jobsites));

        $ ( "#contractor-form button" ).on ( "click", function ( event )
        {
            var previousJobsites = JSON.parse(sessionStorage.getItem('contractorJobsite'));

            var actualJobsites = new Array;
            $("#user-jobsite-select option:selected" ).each(function(item){
                actualJobsites.push($(this).attr("value"));
            });
            var is_same = 1;
            for(var i=0;i<previousJobsites.length;i++){
                if($.inArray(previousJobsites[i], actualJobsites) == -1){
                    is_same = 0
                }
            }

            if(previousJobsites.toString() === actualJobsites.toString()) {
                is_same = 1;
            }


            if(!is_same){
                swal ( {
                    title:             "Caution",
                    text:              "If you remove a jobsite, all users from this contractor will lose access to that jobsite.",
                    type:              "warning",
                    showCancelButton:  true,
                    cancelButtonText:  "Cancel",
                    confirmButtonColor:"#FF6319",
                    confirmButtonText: "Continue",
                    closeOnConfirm:    true
                }, function ()
                {
                    $ ( '#contractor-form' ).yiiActiveForm ( 'submitForm' );
                } );
            }else{
                $ ( '#contractor-form' ).yiiActiveForm ( 'submitForm' );
            }
            return false;
        } );
    }
}









/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
/**********************************************************************************************************************/
init_app = function()
{
    /*
     * Layout
     */
    (function ()
    {
        //Get saved layout type from LocalStorage
        var layoutStatus = localStorage.getItem ( 'ma-layout-status' );
        if ( layoutStatus == 1 )
        {
            $ ( 'body' ).addClass ( 'sw-toggled' );
            $ ( '#tw-switch' ).prop ( 'checked', true );
        }

        $ ( 'body' ).on ( 'change', '#toggle-width input:checkbox', function ()
        {
            if ( $ ( this ).is ( ':checked' ) )
            {
                $ ( 'body' ).addClass ( 'toggled sw-toggled' );
                localStorage.setItem ( 'ma-layout-status', 1 );
            }
            else
            {
                $ ( 'body' ).removeClass ( 'toggled sw-toggled' );
                localStorage.setItem ( 'ma-layout-status', 0 );
            }
        } );
    }) ();

    $ ( document ).ready ( function ()
    {
        /*
         * Top Search
         */
        (function ()
        {
            $ ( 'body' ).on ( 'click', '#top-search > a', function ( e )
            {
                e.preventDefault ();

                $ ( '#header' ).addClass ( 'search-toggled' );
            } );

            $ ( 'body' ).on ( 'click', '#top-search-close', function ( e )
            {
                e.preventDefault ();

                $ ( '#header' ).removeClass ( 'search-toggled' );
            } );
        }) ();

        /*
         * Sidebar
         */
        (function ()
        {
            //Toggle
            $ ( 'body' ).on ( 'click', '#menu-trigger, #chat-trigger', function ( e )
            {
                e.preventDefault ();
                var x = $ ( this ).data ( 'trigger' );

                $ ( x ).toggleClass ( 'toggled' );
                $ ( this ).toggleClass ( 'open' );

                //Close opened sub-menus
                $ ( '.sub-menu.toggled' ).not ( '.active' ).each ( function ()
                {
                    $ ( this ).removeClass ( 'toggled' );
                    $ ( this ).find ( 'ul' ).hide ();
                } );

                $ ( '.profile-menu .main-menu' ).hide ();

                if ( x == '#sidebar' )
                {
                    $elem = '#sidebar';
                    $elem2 = '#menu-trigger';

                    $ ( '#chat-trigger' ).removeClass ( 'open' );

                    if ( !$ ( '#chat' ).hasClass ( 'toggled' ) )
                    {
                        $ ( '#header' ).toggleClass ( 'sidebar-toggled' );
                    }
                    else
                    {
                        $ ( '#chat' ).removeClass ( 'toggled' );
                    }
                }

                if ( x == '#chat' )
                {
                    $elem =
                        '#chat';
                    $elem2 =
                        '#chat-trigger';

                    $ ( '#menu-trigger' ).removeClass ( 'open' );

                    if ( !$ ( '#sidebar' ).hasClass ( 'toggled' ) )
                    {
                        $ ( '#header' ).toggleClass ( 'sidebar-toggled' );
                    }
                    else
                    {
                        $ ( '#sidebar' ).removeClass ( 'toggled' );
                    }
                }

                //When clicking outside
                if ( $ ( '#header' ).hasClass ( 'sidebar-toggled' ) )
                {
                    $ ( document ).on ( 'click', function ( e )
                    {
                        if ( ($ ( e.target ).closest ( $elem ).length === 0) && ($ ( e.target ).closest ( $elem2 ).length === 0) )
                        {
                            setTimeout ( function ()
                            {
                                $ ( $elem ).removeClass ( 'toggled' );
                                $ ( '#header' ).removeClass ( 'sidebar-toggled' );
                                $ ( $elem2 ).removeClass ( 'open' );
                            } );
                        }
                    } );
                }
            } )

            //Submenu
            $ ( 'body' ).on ( 'click', '.sub-menu > a', function ( e )
            {
                e.preventDefault ();
                $ ( this ).next ().slideToggle ( 200 );
                $ ( this ).parent ().toggleClass ( 'toggled' );
            } );
        }) ();

        /*
         * Clear Notification
         */
        $ ( 'body' ).on ( 'click', '[data-clear="notification"]', function ( e )
        {
            e.preventDefault ();

            var x = $ ( this ).closest ( '.listview' );
            var y = x.find ( '.lv-item' );
            var z = y.size ();

            $ ( this ).parent ().fadeOut ();

            x.find ( '.list-group' ).prepend ( '<i class="grid-loading hide-it"></i>' );
            x.find ( '.grid-loading' ).fadeIn ( 1500 );

            var w = 0;
            y.each ( function ()
            {
                var z = $ ( this );
                setTimeout ( function ()
                {
                    z.addClass ( 'animated fadeOutRightBig' ).delay ( 1000 ).queue ( function ()
                    {
                        z.remove ();
                    } );
                }, w +=
                    150 );
            } )

            //Popup empty message
            setTimeout ( function ()
            {
                $ ( '#notifications' ).addClass ( 'empty' );
            }, (z * 150) + 200 );
        } );

        /*
         * Dropdown Menu
         */
        if ( $ ( '.dropdown' )[ 0 ] )
        {
            //Propagate
            $ ( 'body' ).on ( 'click', '.dropdown.open .dropdown-menu', function ( e )
            {
                e.stopPropagation ();
            } );

            $ ( '.dropdown' ).on ( 'shown.bs.dropdown', function ( e )
            {
                if ( $ ( this ).attr ( 'data-animation' ) )
                {
                    $animArray =
                        [];
                    $animation =
                        $ ( this ).data ( 'animation' );
                    $animArray =
                        $animation.split ( ',' );
                    $animationIn =
                        'animated ' + $animArray[ 0 ];
                    $animationOut =
                        'animated ' + $animArray[ 1 ];
                    $animationDuration =
                        ''
                    if ( !$animArray[ 2 ] )
                    {
                        $animationDuration =
                            500; //if duration is not defined, default is set to 500ms
                    }
                    else
                    {
                        $animationDuration =
                            $animArray[ 2 ];
                    }

                    $ ( this ).find ( '.dropdown-menu' ).removeClass ( $animationOut )
                    $ ( this ).find ( '.dropdown-menu' ).addClass ( $animationIn );
                }
            } );

            $ ( '.dropdown' ).on ( 'hide.bs.dropdown', function ( e )
            {
                if ( $ ( this ).attr ( 'data-animation' ) )
                {
                    e.preventDefault ();
                    $this =
                        $ ( this );
                    $dropdownMenu =
                        $this.find ( '.dropdown-menu' );

                    $dropdownMenu.addClass ( $animationOut );
                    setTimeout ( function ()
                    {
                        $this.removeClass ( 'open' )

                    }, $animationDuration );
                }
            } );
        }

        /*
         * Calendar Widget
         */
        if ( $ ( '#calendar-widget' )[ 0 ] )
        {
            (function ()
            {
                $ ( '#calendar-widget' ).fullCalendar ( {
                    contentHeight:'auto',
                    theme:        true,
                    header:       {
                        right: '',
                        center:'prev, title, next',
                        left:  ''
                    },
                    defaultDate:  '2014-06-12',
                    editable:     true,
                    events:       [
                        {
                            title:    'All Day',
                            start:    '2014-06-01',
                            className:'bgm-cyan'
                        },
                        {
                            title:    'Long Event',
                            start:    '2014-06-07',
                            end:      '2014-06-10',
                            className:'bgm-orange'
                        },
                        {
                            id:       999,
                            title:    'Repeat',
                            start:    '2014-06-09',
                            className:'bgm-lightgreen'
                        },
                        {
                            id:       999,
                            title:    'Repeat',
                            start:    '2014-06-16',
                            className:'bgm-blue'
                        },
                        {
                            title:    'Meet',
                            start:    '2014-06-12',
                            end:      '2014-06-12',
                            className:'bgm-teal'
                        },
                        {
                            title:    'Lunch',
                            start:    '2014-06-12',
                            className:'bgm-gray'
                        },
                        {
                            title:    'Birthday',
                            start:    '2014-06-13',
                            className:'bgm-pink'
                        },
                        {
                            title:    'Google',
                            url:      'http://google.com/',
                            start:    '2014-06-28',
                            className:'bgm-bluegray'
                        }
                    ]
                } );
            }) ();
        }

        /*
         * Weather Widget
         */
        if ( $ ( '#weather-widget' )[ 0 ] )
        {
            $.simpleWeather ( {
                location:'Austin, TX',
                woeid:   '',
                unit:    'f',
                success: function ( weather )
                {
                    html =
                        '<div class="weather-status">' + weather.temp + '&deg;' + weather.units.temp + '</div>';
                    html +=
                        '<ul class="weather-info"><li>' + weather.city + ', ' + weather.region + '</li>';
                    html +=
                        '<li class="currently">' + weather.currently + '</li></ul>';
                    html +=
                        '<div class="weather-icon wi-' + weather.code + '"></div>';
                    html +=
                        '<div class="dash-widget-footer"><div class="weather-list tomorrow">';
                    html +=
                        '<span class="weather-list-icon wi-' + weather.forecast[ 2 ].code + '"></span><span>' + weather.forecast[ 1 ].high + '/' + weather.forecast[ 1 ].low + '</span><span>' + weather.forecast[ 1 ].text + '</span>';
                    html +=
                        '</div>';
                    html +=
                        '<div class="weather-list after-tomorrow">';
                    html +=
                        '<span class="weather-list-icon wi-' + weather.forecast[ 2 ].code + '"></span><span>' + weather.forecast[ 2 ].high + '/' + weather.forecast[ 2 ].low + '</span><span>' + weather.forecast[ 2 ].text + '</span>';
                    html +=
                        '</div></div>';
                    $ ( "#weather-widget" ).html ( html );
                },
                error:   function ( error )
                {
                    $ ( "#weather-widget" ).html ( '<p>' + error + '</p>' );
                }
            } );
        }

        /*
         * Todo Add new item
         */
        if ( $ ( '#todo-lists' )[ 0 ] )
        {
            //Add Todo Item
            $ ( 'body' ).on ( 'click', '#add-tl-item .add-new-item', function ()
            {
                $ ( this ).parent ().addClass ( 'toggled' );
            } );

            //Dismiss
            $ ( 'body' ).on ( 'click', '.add-tl-actions > a', function ( e )
            {
                e.preventDefault ();
                var x = $ ( this ).closest ( '#add-tl-item' );
                var y = $ ( this ).data ( 'tl-action' );

                if ( y == "dismiss" )
                {
                    x.find ( 'textarea' ).val ( '' );
                    x.removeClass ( 'toggled' );
                }

                if ( y == "save" )
                {
                    x.find ( 'textarea' ).val ( '' );
                    x.removeClass ( 'toggled' );
                }
            } );
        }

        /*
         * Auto Hight Textarea
         */
        if ( $ ( '.auto-size' )[ 0 ] )
        {
            $ ( '.auto-size' ).autosize ();
        }

        /*
         * Custom Scrollbars
         */
        function scrollbar ( className, color, cursorWidth )
        {
            $ ( className ).niceScroll ( {
                cursorcolor:       color,
                cursorborder:      0,
                cursorborderradius:0,
                cursorwidth:       cursorWidth,
                bouncescroll:      true,
                mousescrollstep:   100
            } );
        }

        //Scrollbar for HTML but not for login page
        if ( !$ ( '.login-content' )[ 0 ] )
        {
            scrollbar ( 'html', 'rgba(0,0,0,0.3)', '5px' );
        }

        //Scrollbar Tables
        if ( $ ( '.table-responsive' )[ 0 ] )
        {
            scrollbar ( '.table-responsive', 'rgba(0,0,0,0.5)', '5px' );
        }

        //Scrill bar for Chosen
        if ( $ ( '.chosen-results' )[ 0 ] )
        {
            scrollbar ( '.chosen-results', 'rgba(0,0,0,0.5)', '5px' );
        }

        //Scroll bar for tabs
        if ( $ ( '.tab-nav' )[ 0 ] )
        {
            scrollbar ( '.tab-nav', 'rgba(0,0,0,0.2)', '2px' );
        }

        //Scroll bar for dropdowm-menu
        if ( $ ( '.dropdown-menu .c-overflow' )[ 0 ] )
        {
            scrollbar ( '.dropdown-menu .c-overflow', 'rgba(0,0,0,0.5)', '0px' );
        }

        //Scrollbar for rest
        if ( $ ( '.c-overflow' )[ 0 ] )
        {
            scrollbar ( '.c-overflow', 'rgba(0,0,0,0.5)', '5px' );
        }

        /*
         * Profile Menu
         */
        $ ( 'body' ).on ( 'click', '.profile-menu > a', function ( e )
        {
            e.preventDefault ();
            $ ( this ).parent ().toggleClass ( 'toggled' );
            $ ( this ).next ().slideToggle ( 200 );
        } );





        /*
         * Audio and Video
         */
        if ( $ ( 'audio, video' )[ 0 ] )
        {
            $ ( 'video,audio' ).mediaelementplayer ();
        }

        /*
         * Custom Select
         */
        if ( $ ( '.selectpickers' )[ 0 ] )
        {
            $ ( '.selecstpicker' ).selectpicker ();
        }

        /*
         * Tag Select
         */
        if ( $ ( '.tag-select' )[ 0 ] )
        {
            $ ( '.tag-select' ).chosen ( {
                width:                '100%',
                allow_single_deselect:true
            } );
        }

        /*
         * Input Slider
         */
        //Basic
        //if ( $ ( '.input-slider' )[ 0 ] )
        //{
        //    $ ( '.input-slider' ).each ( function ()
        //    {
        //        var isStart = $ ( this ).data ( 'is-start' );
        //
        //        $ ( this ).noUiSlider ( {
        //            start:isStart,
        //            range:{
        //                'min':0,
        //                'max':100
        //            }
        //        } );
        //    } );
        //    //$('.input-slider').Link('lower').to('-inline-<div class="is-tooltip"></div>');
        //}
        //
        ////Range slider
        //if ( $ ( '.input-slider-range' )[ 0 ] )
        //{
        //    $ ( '.input-slider-range' ).noUiSlider ( {
        //        start:  [
        //            30,
        //            60
        //        ],
        //        range:  {
        //            'min':0,
        //            'max':100
        //        },
        //        connect:true
        //    } );
        //}
        //
        ////Range slider with value
        //if ( $ ( '.input-slider-values' )[ 0 ] )
        //{
        //    $ ( '.input-slider-values' ).noUiSlider ( {
        //        start:    [
        //            45,
        //            80
        //        ],
        //        connect:  true,
        //        direction:'rtl',
        //        behaviour:'tap-drag',
        //        range:    {
        //            'min':0,
        //            'max':100
        //        }
        //    } );
        //
        //    $ ( '.input-slider-values' ).Link ( 'lower' ).to ( $ ( '#value-lower' ) );
        //    $ ( '.input-slider-values' ).Link ( 'upper' ).to ( $ ( '#value-upper' ), 'html' );
        //}

        /*
         * Input Mask
         */
        if ( $ ( 'input-mask' )[ 0 ] )
        {
            $ ( '.input-mask' ).mask ();
        }

        /*
         * Color Picker
         */
        if ( $ ( '.color-picker' )[ 0 ] )
        {
            $ ( '.color-picker' ).each ( function ()
            {
                $ ( '.color-picker' ).each ( function ()
                {
                    var colorOutput = $ ( this ).closest ( '.cp-container' ).find ( '.cp-value' );
                    $ ( this ).farbtastic ( colorOutput );
                } );
            } );
        }

        /*
         * HTML Editor
         */
        if ( $ ( '.html-editor' )[ 0 ] )
        {
            $ ( '.html-editor' ).summernote ( {
                height:150
            } );
        }

        if ( $ ( '.html-editor-click' )[ 0 ] )
        {
            //Edit
            $ ( 'body' ).on ( 'click', '.hec-button', function ()
            {
                $ ( '.html-editor-click' ).summernote ( {
                    focus:true
                } );
                $ ( '.hec-save' ).show ();
            } )

            //Save
            $ ( 'body' ).on ( 'click', '.hec-save', function ()
            {
                $ ( '.html-editor-click' ).code ();
                $ ( '.html-editor-click' ).destroy ();
                $ ( '.hec-save' ).hide ();
                notify ( 'Content Saved Successfully!', 'success' );
            } );
        }

        //Air Mode
        if ( $ ( '.html-editor-airmod' )[ 0 ] )
        {
            $ ( '.html-editor-airmod' ).summernote ( {
                airMode:true
            } );
        }

        /*
         * Date Time Picker
         */

        //Date Time Picker
        if ( $ ( '.date-time-picker' )[ 0 ] )
        {
            $ ( '.date-time-picker' ).datetimepicker ();
        }

        //Time
        if ( $ ( '.time-picker' )[ 0 ] )
        {
            $ ( '.time-picker' ).datetimepicker ( {
                format:'LT'
            } );
        }

        //Date
        if ( $ ( '.date-picker' )[ 0 ] )
        {
            $defaultDate =
                $ ( '.date-picker' ).val ();
            $ ( '.date-picker' ).datetimepicker ( {
                useCurrent:false,
                format:    "YYYY-MM-DD HH:MM:SS"
            } ).val ( $defaultDate );
        }

        /*
         * Form Wizard
         */

        if ( $ ( '.form-wizard-basic' )[ 0 ] )
        {
            $ ( '.form-wizard-basic' ).bootstrapWizard ( {
                tabClass:'fw-nav'
            } );
        }

        /*
         * Bootstrap Growl - Notifications popups
         */
        function notify ( message, type )
        {
            $.growl ( {
                message:message
            }, {
                type:         type,
                allow_dismiss:false,
                label:        'Cancel',
                className:    'btn-xs btn-inverse',
                placement:    {
                    from: 'top',
                    align:'right'
                },
                delay:        2500,
                animate:      {
                    enter:'animated fadeIn',
                    exit: 'animated fadeOut'
                },
                offset:       {
                    x:20,
                    y:85
                }
            } );
        };

        //Welcome Message (not for login page)
        if ( !$ ( '.login-content' )[ 0 ] )
        {
            //notify('Welcome back Mallinda Hollaway', 'inverse');
        }

        /*
         * Waves Animation
         */
        (function ()
        {
            var wavesList = [ '.btn' ];

            for ( var x = 0; x < wavesList.length; x++ )
            {
                if ( $ ( wavesList[ x ] )[ 0 ] )
                {
                    if ( $ ( wavesList[ x ] ).is ( 'a' ) )
                    {
                        $ ( wavesList[ x ] ).not ( '.btn-icon, input' ).addClass ( 'waves-effect waves-button' );
                    }
                    else
                    {
                        $ ( wavesList[ x ] ).not ( '.btn-icon, input' ).addClass ( 'waves-effect' );
                    }
                }
            }

            setTimeout ( function ()
            {
                if ( $ ( '.waves-effect' )[ 0 ] )
                {
                    Waves.displayEffect ();
                }
            } );
        }) ();

        /*
         * Lightbox
         */
        if ( $ ( '.lightbox' )[ 0 ] )
        {
            $ ( '.lightbox' ).lightGallery ( {
                enableTouch:true
            } );
        }

        /*
         * Link prevent
         */
        $ ( 'body' ).on ( 'click', '.a-prevent', function ( e )
        {
            e.preventDefault ();
        } );

        /*
         * Collaspe Fix
         */
        if ( $ ( '.collapse' )[ 0 ] )
        {

            //Add active class for opened items
            $ ( '.collapse' ).on ( 'show.bs.collapse', function ( e )
            {
                $ ( this ).closest ( '.panel' ).find ( '.panel-heading' ).addClass ( 'active' );
            } );

            $ ( '.collapse' ).on ( 'hide.bs.collapse', function ( e )
            {
                $ ( this ).closest ( '.panel' ).find ( '.panel-heading' ).removeClass ( 'active' );
            } );

            //Add active class for pre opened items
            $ ( '.collapse.in' ).each ( function ()
            {
                $ ( this ).closest ( '.panel' ).find ( '.panel-heading' ).addClass ( 'active' );
            } );
        }

        /*
         * Tooltips
         */
        if ( $ ( '[data-toggle="tooltip"]' )[ 0 ] )
        {
            $ ( '[data-toggle="tooltip"]' ).tooltip ();
        }

        /*
         * Popover
         */
        if ( $ ( '[data-toggle="popover"]' )[ 0 ] )
        {
            $ ( '[data-toggle="popover"]' ).popover ();
        }

        /*
         * Message
         */

        //Actions
        if ( $ ( '.on-select' )[ 0 ] )
        {
            var checkboxes = '.lv-avatar-content input:checkbox';
            var actions = $ ( '.on-select' ).closest ( '.lv-actions' );

            $ ( 'body' ).on ( 'click', checkboxes, function ()
            {
                if ( $ ( checkboxes + ':checked' )[ 0 ] )
                {
                    actions.addClass ( 'toggled' );
                }
                else
                {
                    actions.removeClass ( 'toggled' );
                }
            } );
        }

        if ( $ ( '#ms-menu-trigger' )[ 0 ] )
        {
            $ ( 'body' ).on ( 'click', '#ms-menu-trigger', function ( e )
            {
                e.preventDefault ();
                $ ( this ).toggleClass ( 'open' );
                $ ( '.ms-menu' ).toggleClass ( 'toggled' );
            } );
        }

        /*
         * Login
         */
        if ( $ ( '.login-content' )[ 0 ] )
        {
            //Add class to HTML. This is used to center align the logn box
            $ ( 'html' ).addClass ( 'login-content' );

            $ ( 'body' ).on ( 'click', '.login-navigation > li', function ()
            {
                var z = $ ( this ).data ( 'block' );
                var t = $ ( this ).closest ( '.lc-block' );

                t.removeClass ( 'toggled' );

                setTimeout ( function ()
                {
                    $ ( z ).addClass ( 'toggled' );
                } );

            } )
        }

        /*
         * Fullscreen Browsing
         */
        if ( $ ( '[data-action="fullscreen"]' )[ 0 ] )
        {
            var fs = $ ( "[data-action='fullscreen']" );
            fs.on ( 'click', function ( e )
            {
                e.preventDefault ();

                //Launch
                function launchIntoFullscreen ( element )
                {

                    if ( element.requestFullscreen )
                    {
                        element.requestFullscreen ();
                    }
                    else if ( element.mozRequestFullScreen )
                    {
                        element.mozRequestFullScreen ();
                    }
                    else if ( element.webkitRequestFullscreen )
                    {
                        element.webkitRequestFullscreen ();
                    }
                    else if ( element.msRequestFullscreen )
                    {
                        element.msRequestFullscreen ();
                    }
                }

                //Exit
                function exitFullscreen ()
                {

                    if ( document.exitFullscreen )
                    {
                        document.exitFullscreen ();
                    }
                    else if ( document.mozCancelFullScreen )
                    {
                        document.mozCancelFullScreen ();
                    }
                    else if ( document.webkitExitFullscreen )
                    {
                        document.webkitExitFullscreen ();
                    }
                }

                launchIntoFullscreen ( document.documentElement );
                fs.closest ( '.dropdown' ).removeClass ( 'open' );
            } );
        }

        /*
         * Clear Local Storage
         */
        if ( $ ( '[data-action="clear-localstorage"]' )[ 0 ] )
        {
            var cls = $ ( '[data-action="clear-localstorage"]' );

            cls.on ( 'click', function ( e )
            {
                e.preventDefault ();

                swal ( {
                    title:             "Are you sure?",
                    text:              "All your saved localStorage values will be removed",
                    type:              "warning",
                    showCancelButton:  true,
                    confirmButtonColor:"#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm:    false
                }, function ()
                {
                    localStorage.clear ();
                    swal ( "Done!", "localStorage is cleared", "success" );
                } );
            } );
        }
    } );
};
