
/**
 * execute an ajax petition by url
 * @param $url
 */
function executeAjax( $url ){
    return $.ajax
    ({
        url: $url,
        type: "POST",
        dataType: "JSON"
    });
}

function executeAjaxGET( $url ){
    return $.ajax
    ({
        url: $url,
        type: "GET"
    });
}

function checkDuplicatedContractors( contractor_name )
{
    var url =  "/ajax/check-duplicated-contractors?contractor=" + contractor_name ;
    var existe = 0;
    $.ajax
    ({
        url: url,
        type: "POST",
        async: false,
        dataType: "JSON",
        success: function(response)
        {
            existe = response.length > 0 ? 1 : 0;
            if(existe){
                var html ="<table class='table table-hover'>";
                html +="<thead><tr><th>Status</th><th>Vendor number</th><th>Contractor</th><th>Created</th><th>Updated</th><th>Swap</th></tr></thead>";
                html +="<tbody>";
                for(var i = 0; i < response.length; i++){
                    html +="<tr data-key='" + response[i].id + "'>";
                    html +="<td class='active-column'>";
                    html += response[i].is_active == 1 ? "<i class='md md-check'>": "<i class='md md-close'>";
                    html += "</td>";
                    html +="<td class='additional_information'>";
                    html += response[i].vendor_number ? response[i].vendor_number : "(not set)";
                    html += "</td>";
                    html +="<td class='additional_information'>" + response[i].contractor + "</td>";
                    created = response[i].created;
                    html +="<td class='date-column'>" + created.split(" ")[0] + "</td>";
                    updated = response[i].updated;
                    html +="<td class='date-column'>" + updated.split(" ")[0] + "</td>";
                    html +="<td class='table-action-button'><a href='" + getBaseURL() + "contractor/update?id=" + response[i].id + "'><i class='md md-swap-horiz view-case'></i></a></td>";
                    html +="</tr>";
                }
                html +="</tbody>";
                html +="</table>";
                $("#similarContractors" ).html(html);
                $ ( "#similarContractors").niceScroll ( {
                    cursorcolor:       'rgba(0,0,0,0.5)',
                    cursorborder:      0,
                    cursorborderradius:0,
                    cursorwidth:       '5px',
                    bouncescroll:      true,
                    mousescrollstep:   80
                } );
            }
        }
    });
    return existe;
}
function checkDuplicatedUsers( first_name, last_name, emp_id )
{
    var url = "/ajax/check-duplicated-users?firstname=" + first_name + "&lastname=" + last_name + "&empid=" + emp_id;
    var existe = 0;
    $.ajax
    ({
        url: url,
        type: "POST",
        async: false,
        dataType: "JSON",
        success: function(response)
        {
            existe = response.length > 0 ? 1 : 0;
            if(existe){
                var html ="<table class='table table-hover'>";
                html +="<thead><tr><th>Status</th><th>Name</th><th>Emp. ID</th><th>Contractor</th><th>Assigned jobsites</th><th>Created</th><th>Last login</th><th></th></tr></thead>";
                html +="<tbody>";
                for(var i = 0; i < response.length; i++){
                    html +="<tr data-key='" + response[i].id + "'>";
                    html +="<td class='active-column'>";
                    html += response[i].is_active == 1 ? "<i class='md md-check'>": "<i class='md md-close'>";
                    html += "</td>";
                    html +="<td class='name-column'>";
                    html += response[i].first_name + " " + response[i].last_name;
                    html += "</td>";
                    html +="<td class='username-column'>";
                    html += response[i].employee_number ? response[i].employee_number : " ";
                    html += "</td>";
                    html +="<td class='username-column'>";
                    html += response[i].contractor;
                    html += "</td>";
                    html +="<td class='assigned-jobsites'>";
                    html += response[i].jobsites;
                    html += " ";
                    html += "</td>";
                    created = response[i].created;
                    html +="<td class='date-column'>" + created.split(" ")[0] + "</td>";
                    last_login = response[i].last_login;
                    html +="<td class='date-column'>" + last_login.split(" ")[0] + "</td>";
                    html +="<td class='table-action-button'><a href='" + getBaseURL() + "user/update?id=" + response[i].id + "'><i class='md md-swap-horiz view-case'></i></a></td>";
                    html +="</tr>";
                }
                html +="</tbody>";
                html +="</table>";
                $("#similarUsers" ).html(html);
                $ ( "#similarUsers"    ).niceScroll ( {
                    cursorcolor:       'rgba(0,0,0,0.5)',
                    cursorborder:      0,
                    cursorborderradius:0,
                    cursorwidth:       '5px',
                    bouncescroll:      true,
                    mousescrollstep:   80
                } );
            }
        }
    });
    return existe;
}

function getOffenderUsersView(controllerUrl)
{
    var url = controllerUrl;
    var existe = 0;
    $.ajax
    ({
        url: url,
        type: "POST",
        async: false,
        dataType: "JSON",
        success: function(response)
        {
            existe = response.length > 0 ? 1 : 0;
            if(existe){
                var html ="<table class='table table-hover'>";
                html +="<thead><tr><th>Issue Id</th><th>Issue Type</th></tr></thead>";
                html +="<tbody data-link='row' class='rowlink'>";
                for(var i = 0; i < response.length; i++){
                    html +="<tr data-key='" + response[i].id + "'>";
                    html +="<td class='id-column' >";
                    html +="<a href='/web/app-case/view?id=" + response[i].id + "' target='_blank'>";
                    html += response[i].id;
                    html += "</td>";
                    html +="<td class='type-column'>";
                    html += response[i].issue_type;
                     html += "</a>";
                      html += "</td>";
                    //html +="<td class='table-action-button'><a href='" + getBaseURL() + "app-case/view?id=" + response[i].id + "'><i class='md md-swap-horiz view-case'></i></a></td>";
                    html +="</tr>";
                }
                html +="</tbody>";
                html +="</table>";
                $("#offenderuserandissues" ).html(html);
                $ ("#offenderuserandissues").niceScroll ( {
                    cursorcolor:       'rgba(0,0,0,0.5)',
                    cursorborder:      0,
                    cursorborderradius:0,
                    cursorwidth:       '5px',
                    bouncescroll:      true,
                    mousescrollstep:   80
                    
                } );
            }
        }
    });
    return existe;
}
function getBaseURL() {
    // var arr = window.location.href.split("/web/");
    // arr[0]+="/web/";
    // return arr[0];
    var base_url = window.location.origin;
    return base_url;
}

/**
 * On Change Dropdown
 * Call this function on dropdown with relation change event
 * @param $url
 * @param $dropdownId
 * @param $label
 * @param $name
 * @param $callback
 * @param $callbackParams
 * @param $from
 */
function onChangeDropdown($url, $dropdownId, $label, $name, $callback, $callbackParams, $from)
{
    $.ajax
    ({
        url: $url,
        type: "POST",
        dataType: "JSON",
        success: function($response)
        {
            $($dropdownId).empty();
            for( var i=0, l=$response.length; i<l; i++)
            {
                if($name === "employee_number")
                {
                    var label = $response[i][$name] + " - " + $response[i]["first_name"] + " " + $response[i]["last_name"];
                    $($dropdownId).append("<option value='"+$response[i].id+"'>"+ label +"</option>");
                }
                else
                {
                    $($dropdownId).append("<option value='"+$response[i].id+"'>"+ $response[i][$name] +"</option>");
                }
            }
            if($from === "fromDashboard"){
                $($dropdownId).prepend("<option value='all'>-All-</option>");
                $($dropdownId).val("all");
            }else if($from === "buildingChange" && $response.length === 0){
                if($label == "floor" || $label == "Floor"  ) {
                   $($dropdownId).prepend("<option value=''>-Select the floor-</option>");                
                }else {
                   $($dropdownId).prepend("<option value=''>-Select a "+$label+"-</option>");                 
                }
                $($dropdownId).val("");
            }
            else if ($from == "area" && i == 0){
                $($dropdownId).prepend("<option value=''>-</option>");
            }
            else if ($from == "area" && i > 0){
                $($dropdownId).prepend("<option value=''>-Select an "+$label+"-</option>");
                $($dropdownId).val("");
            }else if(i===0 && $response.length === 0)
            {
                $($dropdownId).append("<option>-</option>");
            }else
            {

               if($label == "floor" || $label == "Floor"  ) {
                   $($dropdownId).prepend("<option value=''>-Select the floor-</option>");                
                }else {
                   $($dropdownId).prepend("<option value=''>-Select a "+$label+"-</option>");
                }

                $($dropdownId).val("");
            }

            if($callback)
            {
                $callback.apply(undefined, $callbackParams);
            }
        }
    });
}
/**
 * Select from dropdown
 * Call this function on dropdown with relation change event
 * @param $url
 * @param $dropdownId
 * @param $foremanUrl
 */
function selectFromDropdown($url, $dropdownId, $foremanUrl)
{
    $.ajax
    ({
        url: $url,
        type: "POST",
        dataType: "JSON",
        success: function($response)
        {
            if($response == 0){
                $($dropdownId ).val($($dropdownId +"option:first" ).val());
            }else{
                contractor_id = $response[0]["id"];
                $($dropdownId ).val(contractor_id);

                //No aplico el .change() porque me cambiaria los usuarios, asi que llamo el cambio de foreman a parte

                //onChangeDropdown
                //(
                //    $foremanUrl + contractor_id,
                //    "#foreman-id-select",
                //    "Foreman",
                //    "employee_number",
                //    null,
                //    []
                //);

            }
        }
    });
}

/**
 * Get Safety Code Tree View
 * Generate list of Safety Codes
 * @param $url
 * @param $containerId
 * @param $treeViewContainerId
 * @param $inputIdDisplay
 * @param $inputIdHide
 */
function getSafetyCodeTreeView($url, $containerId, $treeViewContainerId, $inputIdDisplay, $inputIdHide)
{
    $.ajax
    ({
        url: $url,
        type: "POST",
        dataType: "JSON",
        cache: true,
        success: function($response)
        {
            // Open Modal
            $($containerId).modal();

            // Clear container & create tree container
            $($treeViewContainerId).empty();
            $($treeViewContainerId).append("<div id='tree-view-container'><\/div>");

            // Create Tree View
            var $tree = $("#tree-view-container").tree
            ({
                closedIcon: $("<i class='md md-add'><\/i>"),
                openedIcon: $("<i class='md md-expand-less'><\/i>"),
                data: $response,
                autoOpen: false,
                dragAndDrop: false,
                autoscroll: false
            });
            // Add Scrollbar
            configureScrollbar("#tree-view-container");


            // Open node selection
            var $node = $tree.tree('getNodeById', $($inputIdHide).val());
            if($node != undefined)
            {
                $tree.tree('selectNode', $node);
                $tree.tree('openNode', $node.parent);
            }

            // bind 'tree.select' event
            $("#tree-view-container").bind
            (
                'tree.select',
                function(event)
                {
                    $("#tree-view-container" ).css("height", "-=1");
                    if (event.node)
                    {
                        var node = event.node;
                        $($inputIdHide).val(node.id);
                        $($inputIdDisplay).val(node.name);
                        $("#input-safety-code-display").attr( "data-original-title", "Selected safety code:");
                        $("#input-safety-code-display").attr( "data-content", node.name);
                    }
                    else
                    {
                        $($inputIdHide).val("");
                        $($inputIdDisplay).val("");
                        $("#input-safety-code-display").attr( "data-original-title", "");
                        $("#input-safety-code-display").attr( "data-content", "");
                    }
                }
            );
        }
    });

}


/**
 * Get User by Contractor ID
 * Generate Dropdown of User to Reassign Issue
 * @param $url
 * @param $containerId
 * @param $dropdownContainerId
 * @param $buttonContainerId
 * @param $app_case_id
 * @param $current_element_id
 */
 function getUsersByContractorDropdown( $url, $containerId, $dropdownContainerId, $buttonContainerId, $app_case_id, $current_element_id ){
	
	$.ajax
    ({
        url: $url,
        type: "POST",
        dataType: "JSON",
        success: function($response)
        {
            // Open Modal
            $($containerId).modal();
			
            // Clear container & dropdown & create
            $($dropdownContainerId).empty(); $('#btn-reassign').remove();
            $($dropdownContainerId).prepend("<select id='user-by-contractor-id' class='selectpicker' data-live-search='true' data-show-subtext='true'></select>");
			
			// Fill select with users
			$.each($response, function( index, value ) {
				$('.selectpicker').append('<option value="'+value.id+'" data-subtext="'+value.first_name+' '+value.last_name+'">'+value.employee_number+' - </option>');
			});
			
			// Create button 
			$($buttonContainerId).append('<button id="btn-reassign" type="button" class="btn btn-link disabled" data-dismiss="modal" onclick="reassignUserCreator('+$app_case_id+','+$current_element_id+');">Reassign</button>');
			
			// Init selecte piker
			var $selectpicker = $('.selectpicker').selectpicker();
			
			// Set creator selected default
			$('.selectpicker').selectpicker('val', $("#"+$current_element_id).data('current-owner')); 
			
			// Set disabled button "Reassign" if select value and creator id are diferent
			if( $('.selectpicker').val() != $("#"+$current_element_id).data('current-owner') ){
				$('#btn-reassign').removeClass('disabled');
			}else{
				$('#btn-reassign').addClass('disabled');
			}
			
			// bind 'change' event
            $(".selectpicker").bind
            (
                'change',
                function(event)
				{
					if( $(this).val() != $("#"+$current_element_id).data('current-owner') ){
						$('#btn-reassign').removeClass('disabled');
					}else{
						$('#btn-reassign').addClass('disabled');
					}
					
				}
            );
        }
    });
	
 }


function notify( type, text ){
    $.growl({
        icon: "fa fa-comments",
        title: text,
        message: '',
        url: ''
    },{
        element: 'body',
        type: type,
        allow_dismiss: false,
        placement: {
            from: "bottom",
            align: "left"
        },
        offset: {
            x: 10,
            y: 50
        },
        spacing: 10,
        z_index: 1031,
        delay: 2500,
        timer: 1000,
        url_target: '_blank',
        mouse_over: false,
        animate: {
            enter: "animated fadeInUp",
            exit: "animated fadeOutDown"
        },
        icon_type: 'class',
        template: '<div data-growl="container" class="alert" role="alert">' +
                 '<button type="button" class="close" data-growl="dismiss">' +
                 '<span aria-hidden="true">&times;</span>' +
                 '<span class="sr-only">Close</span>' +
                 '</button>' +
                 '<span data-growl="icon"></span>' +
                 '<span data-growl="title"></span>' +
                 '<span data-growl="message"></span>' +
                 '<a href="#" data-growl="url"></a>' +
        '</div>'
    });
};  

function getNewsflash ( $url, $user_id, $count_container, $notification_container, $remove_newsflash_url, $base_path )
{
    $.ajax
    ( {
        url:     $url + $user_id,
        type:    "POST",
        dataType:"JSON",
        success: function ( $response )
        {
            if ( $response.quantity[ 0 ].quantity > 0 )
            {
                $ ( ".lv-header" ).text ( "Newsflash" ).append ( "<ul class='actions-basic'> <li class='dropdown'> <a href='' data-clear='notification' class='notification-icon'> <i class='md md-done-all'></i> </a> </li> </ul>" );
                $ ( $count_container ).append ( "<i class='tmn-counts'>" + $response.quantity[ 0 ].quantity + "</i>" );
                for ( var i = 0; i < $response.notifications.length; i++ )
                {
                    var id = $response.notifications[ i ].id;
                    var title = $response.notifications[ i ].jobsite;
                    var description = $response.notifications[ i ].additional_information;
                    var date = $response.notifications[ i ].created;
                    var base_path = getBasePath();
                    var href_url = base_path + "app-case/view?id=" + id + "&from=newsflash";
                    var img_src = "";
                    switch ( $response.notifications[ i ].app_case_type_id )
                    {
                        case "1":
                            img_src = $base_path + "/img/IssueType-1.png";
                            break;
                        case "2":
                            img_src = $base_path + "/img/IssueType-2.png";
                            break;
                        case "3":
                            img_src = $base_path + "/img/IssueType-3.png";
                            break;
                        case "4":
                            img_src = $base_path + "/img/IssueType-4.png";
                            break;
                    }
                    $ ( $notification_container ).append ( "<a class='lv-item' href='" + href_url + "' data-id=" + id + "> <div class='media'> <div class='pull-left'> <img class='lv-img-sm'  alt='' src=" + img_src + "> </div> <div class='media-body'> <div class='lv-title'>" + title + " - <span>" + date + "</span> </div> <small class='lv-small'>" + description + "</small> </div> </div> </a>" );
                }
                configureScrollbar ( $notification_container );
                $ ( ".notification-icon" ).click ( function ( e )
                {
                    e.preventDefault ();
                    $.ajax
                    ( {
                        url:     $remove_newsflash_url,
                        type:    "POST",
                        dataType:"JSON",
                        success: function ( $response )
                        {
                            $ ( ".lv-header" ).text ( "No newsflash" );
                            $ ( ".lv-body" ).empty ();
                            $ ( ".tmn-counts" ).remove ();
                        }
                    } )
                } )
            }
        }
    } );
}

function getBasePath() {
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/web') + 5);
    pathName = loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
    return pathName;
}

function getAffectedEmployeeByContractorDropdown( $url, $containerId, $dropdownContainerId, $buttonContainerId, $current_element_id,$inputIdHide, $inputIdDisplay){
	$.ajax
    ({
        url: $url,
        type: "POST",
        dataType: "JSON",
        success: function($response)
        {
            // Open Modal
            $($containerId).modal();	
            // Clear container & dropdown & create
            $($dropdownContainerId).empty(); $('#btn-select').remove();
            $($dropdownContainerId).prepend("<select id='user-by-contractor-id' class='selectpicker' data-live-search='true' data-show-subtext='true'></select>");
			//debugger;
			// Fill select with users
			$.each($response, function( index, value ) {
				//$('.selectpicker').append('<option value="'+value.id+'" data-subtext="'+value.first_name+' '+value.last_name+'">'+value.employee_number+' - </option>');
               $('.selectpicker').append('<option value="'+value.id+'" data-subtext="'+value.employee_number+' - '+value.first_name+' '+value.last_name+'"> </option>');
            });
			
			// Create button 
			$($buttonContainerId).append('<button id="btn-select" type="button" class="btn btn-link" data-dismiss="modal" onclick="onSelectAffectedEmployee();">select</button>');
			
			// Init selecte piker and load data 
			 $('.selectpicker').selectpicker();
                       
                       //get selected employee using id
                         var selected_employee = $('#hdnAffectedUsr').val();
                         //show checkmark for selected value in drop down
                        if($current_element_id !== "-Select the affected employee ID number or name-")
                        {
                          $('.selectpicker').selectpicker('val',selected_employee); 
                        }

                if($current_element_id != null)         
               var currentelement = $current_element_id.split("-");
                if($('.selectpicker').val() != null)
                var pickerelement = $('.selectpicker').val().split("-");        
           if($('.selectpicker').val() != null && currentelement[0].trim() != ""){
			// Set disabled button "Reassign" if select value and creator id are diferent
			if( $('.selectpicker').val() != $("#"+$current_element_id).data('current-owner') ){
				//$('#btn-select').removeClass('disabled');
			}else{
				//$('#btn-select').addClass('disabled');
			}
        }
			
			// bind 'change' event
            $(".selectpicker").bind
            (
                'change',
                function(event)
				{
                                    if(event)
                                    {
                                      //  var id =  $('#user-by-contractor-id').val();
                                       //var $selectedEmployee =$('#user-by-contractor-id option:selected').attr('data-subtext');
                                     
                                     // $($inputIdHide).val(id);
                                      // $($inputIdDisplay).val($selectedEmployee);
                                      // $("#input-affected_employee-display").attr( "data-original-title", "Selected affected employee code:");
                                      // $("#input-affected_employee-display").attr("data-content", $selectedEmployee);
                                    }

                if($(this).val() != null && currentelement[0].trim() != ""){               
					if( $(this).val() != $("#"+$current_element_id).data('current-owner') ){
						//$('#btn-select').removeClass('disabled');
					}else{
						//$('#btn-select').addClass('disabled');
					}
                }
					
				}
            );
        }
    });
	
 }