<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use yii\widgets\MaskedInput;
   
    $user_wt_username_value = "";
    $user_wt_username[] = "";
?>
<style>
   .ms-selected[readonly] { pointer-events: none; opacity: 0.6;}
   .box1 {width:50% !important;}
   .box2 {width:50% !important;}
   .bootstrap-duallistbox-container .buttons{display:none;}

    .box1 .form-control option{    
     font-family: roboto;
     font-size: 13px;
     line-height: 1.42857143;
     color: #5e5e5e;  
     cursor:pointer;  
    }
    .box2 .form-control option[readonly]{
     pointer-events: none; 
     color: rgba(0, 0, 0, 0.3); 
    } 

    .box1 .form-control option:hover{    
      color: #FF6319;  
    }
    .box2 .form-control option{
      cursor:pointer; 
    }
    .ms-container{
        display:none;
    }
    .fg-line.fg-toggled:after {
      width: 0px;
   }
    .control-label {
        height: 30px;
    }

    .col-sm-8 {
        width: 100%;
    }

    html, body {
        width: 100%;
        height: 100%;
        margin: 0px;
        padding: 0px;
        overflow-x: hidden;
    }

.modal-footer {
    border-top: transparent;
}

.modal-header {
    border-bottom: transparent;
}

.modal-dialog {
    width: 375px;
}

.modal-footer {
    padding: 15px;
    text-align: center;
}

.modal-body {
    position: relative;
    padding: 10px;
    text-align: center;
    font-size: 18px;
}

.modal-header {
    padding: 5px;
}

.close {
    float: right;
    font-size: 28px;
}
.modal-header .close {
    margin-top: -12px;
}
.modal-body p {
    margin-top: 19px;
}

.select2-container--default {
    width: 100% !important;
  }

  .read-only-class{
     pointer-events: none;
     color: rgba(0, 0, 0, 0.3); 
     background-color: rgb(175 165 165 / 30%);    
  }


  .autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
.autocomplete-no-suggestion {color: red;}
.circleloader {
    background: url(../img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;}

@media only screen and (max-width:40em){

canvas.jSignature {width: 100% !important;}

div#sig_pad_71 {width: 100% !important;}

div#sig_pad_74 {width: 100% !important;}

.signature-pad-wrapper{width:100% !important;}

}

</style>

<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/bootstrap-duallistbox.css"/>
    <?php $form = ActiveForm::begin(["id" => "newuserreg"]); ?>
<div class="user-form">
    <input type="hidden"  name="User[jobsite]" id="user_jobsite" style="display:none" value="<?=$jobsite_id?>">

<div class="row">
        <div class="col-sm-6">
           <div class="form-group field-contractor fg-line">
            <label class="control-label" for="user-emergency_contact">Company/Compañia</label>

            <select id="contractor_id" class="form-control duallist" name="User[contractor_id]">
              <option value="0">Please Select Company</option> 
              <?php foreach ( $contractor_arr as $key => $value ): ?> 
              <option value="<?=  $value['data'] ?>"><?= $value['value'] ?></option> 
            <?php endforeach; ?>
            </select>
          
            <div class="help-block user-contractor_error"></div>
            </div>
        </div>
       <div class="col-sm-6">
            <div class="form-group field-user-employee_number required fg-line">
            <label class="control-label" for="user-employee_number">Sticker Number/Badge Number<br /> Número de Sticker/ Número de Gafete </label>
            <input type="text" id="user-employee_number" class="form-control" name="User[employee_number]" maxlength="70" aria-required="true">

            <div class="help-block user-employee_number_error"></div>
            </div>
        </div>
        </div>
        <div class="row">
          <div class="col-sm-6 hidden wtuserblock" id='wtuser-yes'>
          
          <div class="form-group field-user-user_name required fg-line">
            <label class="control-label" for="email">Username</label>

            <?= Html::dropDownList('user_name',$user_wt_username_value, $user_wt_username,['prompt' => '-Select the whiting-turner user-','class' => 'form-control select2-dropdown', 'id'=> 'wt-user-list','onchange' => 'Get_wtuser_email(this)']) ?>
             <div class="help-block"></div>
             </div>
           </div> 
          <div class="col-sm-6 hidden wtuserblock" id="emailblock">
            <div class="form-group field-user-email required fg-line">
            <label class="control-label" for="user-email">Email</label>
            <input type="email" id="user-email" class="form-control" name="User[email]" maxlength="70" aria-required="true">

            <div class="help-block user-email_error"></div>
            </div>
        </div>
 
          </div>
	<div class="row">
        <div class="col-sm-6">
           <div class="form-group field-role fg-line">
            <label class="control-label" for="user-role_id">Role/Papel</label>

            <select id="role_id" class="form-control duallist" name="User[role_id]">
              <option value="0" >Please Select Role</option> 
              <?php foreach ( $role_arr as $key => $value ): ?> 
              <option value="<?=  $value['data'] ?>"><?= $value['value'] ?></option> 
            <?php endforeach; ?>
            </select>
          
            <div class="help-block user-role_error"></div>
            </div>
        </div>
           <div class="col-sm-6">
              <div class="form-group field-user-phone_no fg-line">
              <label class="control-label" for="user-phone_no">Phone Number/Número de teléfono
              </label>
              <?php
              echo MaskedInput::widget([
                      'name' => 'User[phone]',
                      'id' => 'user-phone_no',
                      'mask' => '999-999-9999',
                  ]);
              ?>
              <div class="help-block user-phone_no_error"></div>
              </div>
            </div>  
           
        </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-user-first_name required fg-line">
            <label class="control-label" for="user-first_name">First Name/Primer Nombre</label>
            <input type="text" id="user-first_name" class="form-control read-only-class" name="User[first_name]" maxlength="70" aria-required="true" aria-invalid="true" readonly="true">

            <div class="help-block user-first_name_error"></div>
            </div>
        </div>
        <div class="col-sm-6">
           <div class="form-group field-user-last_name required fg-line">
            <label class="control-label" for="user-last_name">Last Name/Apellido</label>
            <input type="text" id="user-last_name" class="form-control read-only-class" name="User[last_name]" maxlength="70" aria-required="true" readonly="true">

            <div class="help-block user-last_name_error"></div>
            </div>
        </div>

    </div>   
        <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-user-emr-contact fg-line">
            <label class="control-label" for="emr-contact">Name of Emergency Contact<br />
            Nombre de un Contacto en Caso de Emergencia
            </label>
            <input type="text" id="emr-contact" class="form-control" name="User[emergency_name]" maxlength="70">

            <div class="help-block user-emr-contact"></div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group field-user-phone fg-line">
            <label class="control-label" for="user-phone">Emergency Contact Phone Number<br />
            Número de Telephono del Contacto de Emergencia
            </label>
            <?php
            echo MaskedInput::widget([
                    'name' => 'User[emergency_contact]',
                    'id' => 'user-phone',
                    'mask' => '999-999-9999',
                ]);
            ?>

            <div class="help-block user-phone_error"></div>
            </div>
        </div>

        </div>
<div class="row">
          <div class="col-sm-6 nonwt-userblock" id="non-wt-emailblock">
              <div class="form-group nonwt-field-user-email required fg-line">
              <label class="control-label" for="nonwt-user-email">Email/Correo electrónico</label>
              <input type="email" id="nonwt-user-email" class="form-control" name="User[email]" maxlength="70" aria-required="true">
              <div class="help-block nonwt-user-email_error"></div>
              </div>
          </div>

          <!-- <div class="col-sm-6">
              <div class="form-group field-user-phone_no fg-line">
              <label class="control-label" for="user-phone_no">Phone Number
              </label>
              <?php
              echo MaskedInput::widget([
                      'name' => 'User[phone]',
                      'id' => 'user-phone_no',
                      'mask' => '999-999-9999',
                  ]);
              ?>
              <div class="help-block user-phone_no_error"></div>
              </div>
            </div> -->
        </div>
        

        <div class="row">
        <div class="col-sm-8">
          <div class="form-group field-user-agree fg-line">
          <label class="checkbox checkbox-inline  m-r-20">
                    <input type="checkbox" value="0" id="user-agree" name="User[agree]">
                    <i class="input-helper"></i>
                    <span style="color: #000000 !important;">I have read (or had read to me) and received Site Safety Orientation from WHITING – TURNER CONTRACTING COMPANY, and am aware of the project Hazards, Rules and Regulations. I fully understand them and agree to follow them. <br/>
Yo, he leído (o me han leído) y recibí la orientación de seguridad del sitio de trabajo de WHITING-TURNER CONTRACTING COMPANY,  y estoy consiente de los peligros del proyecto, reglas y regulaciones. Las entiendo completamente y estoy dispuesto a seguirlas.</span>
                </label>
              <div class="help-block user-agree_error"></div>
              </div>
            </div>
        </div>
         <div class="row">
          <div class="col-sm-6">
          <div class="form-group field-user-ds fg-line"  onselectstart="return false">
          <label class="control-label">Signature</label>     
          <div id="signature-pad" style="border: 1px black solid;" ></div>

          
          <button id="clear" class="pull-right">Clear</button>
          <div class="help-block user-ds_error"></div>

          </div>
        </div>
        </div>




    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right" id="datasubmit">Create</button>
        </div>
    </div>

        </div>

          <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close close_button">&times;</button>
        </div>
        <div class="modal-body">
      <img src="<?php echo Yii::$app->request->baseUrl; ?>/img/icons/Done.png" alt="sign" width="42" height="42">
       <p id="success-msg">You have successfully registered</p>
        </div>
 
        <div class="modal-footer">
          <button type="button" class="btn btn-default close_button" id="close_button">Ok</button>
        </div>
      </div>
      
    </div>
  </div>
            <!-- Modal -->
  <div class="modal fade" id="myModalError" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close close_button_error">&times;</button>
        </div>
        <div class="modal-body">
      <img src="<?php echo Yii::$app->request->baseUrl; ?>/img/icons/cross.png" alt="sign" width="42" height="42">
      <p id="err-msg"></p>
        </div>
 
        <div class="modal-footer">
          <button type="button" class="btn btn-default close_button_error" id="close_button_error">Ok</button>
        </div>
      </div>
      
    </div>
  </div>

 <?php ActiveForm::end(); ?>
 <script language="javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script type="text/javascript">
var allowedcon = [148];
var nonwt_roles = [7,8,10,11,12,13,14,15];
var wt_roles = [1,2,3,4,5,6,18,19];
var extra_roles = [16,7,8,15,19];
autoSearchjobsiturl = "<?=Yii::$app->urlManager->createUrl('/ajax/get-all-contractors-by-jobsite')?>";

$('.select2-dropdown').select2({
    placeholder: "-Select the whiting-turner user-",
    allowClear: true
});
$( document ).ready(function() {
  GetUsers();
  $('#contractor_id option[value=' + 148 + ']').hide();
  wt_roles.forEach(element => {$("#role_id option[value=" + element + "]").attr('disabled', true); $('#role_id option[value=' + element + ']').hide();});
  nonwt_roles.forEach(element => {
  if(element == "10"){
    $("#role_id option[value=" + element + "]").append(' - Dueño de la Compañia');
  }else if(element == "11"){
    $("#role_id option[value=" + element + "]").append(' - Supervisor/Capatas');
  }else if(element == "12"){
    $("#role_id option[value=" + element + "]").append(' - Gerente de Seguridad');
  }else if(element == "13"){
    $("#role_id option[value=" + element + "]").append(' - Gerente del Proyecto');
  }else if(element == "14"){
    $("#role_id option[value=" + element + "]").append(' - Trabajador/Empleado');
  }
  });
  extra_roles.forEach(element => {$("#role_id option[value=" + element + "]").attr('disabled', true); $('#role_id option[value=' + element + ']').hide();});
});

var saveButton = document.getElementById('save');
var cancelButton = document.getElementById('clear');

$('.close_button').on('click', function (e) {
  e.preventDefault();
  location.reload();
});

$('.close_button_error').on('click', function (e) {
  e.preventDefault();
  $('#myModalError').modal('hide');
});

$('#contractor_id').on('change', function (e) {
     var valueSelected = this.value;
	 
     $("#role_id").val('0');
     
     if(allowedcon.includes(parseInt(valueSelected))){     	    
            $('.wtuserblock').removeClass('hidden');
			      $('.nonwt-userblock').addClass('hidden');
            $('#user-first_name').addClass('read-only-class');
            $('#user-last_name').addClass('read-only-class');
            $("#user-first_name").prop("readonly", true);
            $("#user-last_name").prop("readonly", true);
            $("#user-email").prop("readonly", false);
            $("#user-email").attr('disabled', false);
            $("#nonwt-user-email").attr('disabled', true);
            $("#role_id").attr('disabled', false);
            nonwt_roles.forEach(element => $("#role_id option[value=" + element + "]").attr('disabled', true));
            wt_roles.forEach(element => $("#role_id option[value=" + element + "]").attr('disabled', false));
            extra_roles.forEach(element => $("#role_id option[value=" + element + "]").attr('disabled', true));
      } else{
        $('.wtuserblock').addClass('hidden');
         $("#user-email").val('');
         $('#wt-user-list').val('').trigger("change");
         $('#select2-wt-user-list-container').empty().html('-Select the whiting-turner user-');
         $('#select2-wt-user-list-container').attr('title', '-Select the whiting-turner user-');
            $('.nonwt-userblock').removeClass('hidden');
            $('#user-first_name').removeClass('read-only-class');
            $('#user-last_name').removeClass('read-only-class');
            $("#user-first_name").prop("readonly", false);
            $("#user-last_name").prop("readonly", false);
            $("#user-email").attr('disabled', true);
            $("#nonwt-user-email").attr('disabled', false);
            $("#user-first_name").val('');
            $("#user-last_name").val('');
			$("#role_id").attr('disabled', false);
            $("#role_id").val('14');
      }
});

 

 function handleKeyPress(e) {
    let newValue = e.target.value + e.key;
    if (
    // It is not a number nor a control key?
    isNaN(newValue) && 
    e.which != 8 && // backspace
    e.which != 17 && // ctrl
    newValue[0] != '-' || // minus
    // It is not a negative value?
    newValue[0] == '-' &&
    isNaN(newValue.slice(1)))
        e.preventDefault(); // Then don't write it!
}


function validateEmailDomain(Email) {
  var restricteddomain = ['whiting-turner.com'];
    var splitArray = Email.split('@'); // To Get Array

    if (restricteddomain.indexOf(splitArray[1].toLowerCase()) >= 0) {
        return false;
    }
    return true;

}

function validateUSPhoneNumber(elementValue){
var phoneNumberPattern = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
return phoneNumberPattern.test(elementValue);
}
function validateEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
}



</script>


<script type="text/javascript">
jQuery.noConflict()
</script>

<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jSignature/jSignature.min.noconflict.js"></script>

<script>
(function($){

$(document).ready(function() {
  
  // This is the part where jSignature is initialized.
  var $sigdiv = $("#signature-pad").jSignature({'UndoButton':false, color:"#000000"})
  

cancelButton.addEventListener('click', function (event) {
   event.preventDefault();
  $sigdiv.jSignature("reset");
}); 


 var url = "<?php echo Yii::$app->request->baseUrl; ?>/custom-user/create";
        $('#datasubmit').on('click', function (e) {
          e.preventDefault();
          var submit = true;
          $('.help-block').empty();
          $('.fg-line').removeClass('has-error');
          if($('#user-first_name').val() == '' || $('#user-first_name').val() == undefined)
          {
            $('.field-user-first_name').addClass('has-error');
            $('.user-first_name_error').empty().append('First Name cannot be blank. <br/> Primer Nombre no puede estar en blanco.');
            submit = false;
          } 

          if($('#user-last_name').val() == ''|| $('#user-last_name').val()  == undefined)
          {
            $('.field-user-last_name').addClass('has-error');
            $('.user-last_name_error').empty().append('Last Name cannot be blank.<br/> Apellido no puede estar en blanco.');
            submit = false;
          } 

           if($('#contractor_id').val() == 0 ||$('#contractor_id').val() == ''|| $('#contractor_id').val() == undefined)
          {
            $('.field-contractor').addClass('has-error');
            $('.user-contractor_error').empty().append('Please Contact a Whiting Turner members if your company name is not found. <br/> Favor de hacer saber a un miembro del equipo de Whiting-Turner si el nombre de su compañia no aparece');
            submit = false;
          }

		if($('#role_id').val() == 0 ||$('#role_id').val() == ''|| $('#role_id').val() == undefined)
          {
            $('.field-role').addClass('has-error');
            $('.user-role_error').empty().append('Please choose the role.<br/>Por favor, elija el papel.');
            submit = false;
          }
           if($('#user-employee_number').val() == ''|| $('#user-employee_number').val() == undefined)
          {
            $('.field-user-employee_number').addClass('has-error');
            $('.user-employee_number_error').empty().append('Employee Number cannot be blank.<br/> Número de Sticker/ Número de Gafete no puede estar en blanco.');
            submit = false;
          }  

           

           if($('#emr-contact').val() == ''|| $('#emr-contact').val() == undefined)
          {
            $('.field-user-emr-contact').addClass('has-error');
            $('.user-emr-contact').empty().append('Emergency Contact Name cannot be blank.<br/>Nombre de un Contacto en Caso de Emergencia no puede estar en blanco.');
            submit = false;
          } 

          if(allowedcon.includes(parseInt($('#contractor_id').val()))){
            if($('#user-email').val() == ''|| $('#user-email').val() == undefined){
              $('.field-user-email').addClass('has-error');
            $('.user-email_error').empty().append('Email cannot be blank.<br/>El correo electrónico no puede estar en blanco.');
            submit = false;
          }else if(validateEmailDomain($('#user-email').val()))
            {
            $('.field-user-email').addClass('has-error');
            $('.user-email_error').empty().append('Please enter only @whiting-turner.com ');
            submit = false;
            }
            }else {
              if($('#nonwt-user-email').val() == ''|| $('#nonwt-user-email').val() == undefined ){
                if($('#role_id').val() != '14'){
                  $('.nonwt-field-user-email').addClass('has-error');
                  $('.nonwt-user-email_error').empty().append('Email cannot be blank.<br/>El correo electrónico no puede estar en blanco.');
                  submit = false;
                }
              }else if(!validateEmail($('#nonwt-user-email').val())){
                $('.nonwt-field-user-email').addClass('has-error');
                $('.nonwt-user-email_error').empty().append('Please enter valid Email Id.<br/>Ingrese una identificación de correo electrónico válida');
                submit = false;
              }
            }            
		  if($('#user-phone_no').val() != '' && !validateUSPhoneNumber($('#user-phone_no').val())) {
             $('.field-user-phone_no').addClass('has-error');
            $('.user-phone_no_error').empty().append('Please enter valid Phone Number.<br/>Ingrese un número de teléfono válido');
            submit = false;
          }

          if($('#user-phone').val() == '' || (validateUSPhoneNumber($('#user-phone').val()) == false) ){
             $('.field-user-phone').addClass('has-error');
            $('.user-phone_error').empty().append('Please enter valid Emergency Contact Number.<br/>Ingrese un número de contacto de emergencia válido');
            submit = false;
          }
            

          if(!$("#user-agree").prop('checked'))
          {
            $('.field-user-agree').addClass('has-error');
            $('.user-agree_error').empty().append('Please agree the conditions. <br/> Por favor acepte las condiciones');
            submit = false;
          }

          var checkDsData = ($sigdiv.jSignature('getData', 'native').length == 0)?true:false;
          
         
          if(checkDsData){
            $('.field-user-ds').addClass('has-error');
            $('.user-ds_error').empty().append('Please enter the signature.<br/> Por favor ingrese la firma');
            submit = false;
          }

          var DS = $sigdiv.jSignature('getData');
          var data = $('#newuserreg').serializeArray();
           data.push({name: 'digital_signature', value: DS});
          if(submit){

          $.ajax({
            type: 'post',
            url: url,
            data: data,
             beforeSend: function () {
                (jQuery)('#loader').show();
            },
            success: function (data) {
              (jQuery)('#loader').hide();
              if(data == 'submitted'){
                var msg = "You have successfully registered";
                 if(allowedcon.includes(parseInt($('#contractor_id').val()))){
                    msg = "You have successfully registered.<br/> Please check your email for email verification. ";
                 }

                 $('#success-msg').empty().append(msg);
                (jQuery)('#myModal').modal('show');

              }                 
              else if(data == -1){
                   $('.field-user-employee_number').addClass('has-error');
              $('.user-employee_number_error').empty().append('Sticker Number/Badge Number seems to be duplicate.');
              $('#err-msg').empty().append('Sticker Number/Badge Number seems to be duplicate.');
              (jQuery)('#myModalError').modal('show');
              }                               
              else{
                $('#err-msg').empty().append('Internal Error. Please try again');
                (jQuery)('#myModalError').modal('show');
              }

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
              (jQuery)('#loader').hide();
                   
                     $('#err-msg').empty().append('Internal Error. Please try again');
            (jQuery)('#myModalError').modal('show');
                } 
          });


          }


        });

  
});

})(jQuery)


        function GetUsers(){

  executeAjax
            (
                "<?= Yii::$app->urlManager->createUrl('/ajax/get-wt-users') ?>"+  "<?='?isnew=1'?>"
            ).done(function(r){
            if (r.length != 0) {
               
                var options = "<option value=''>-Select the whiting-turner user-</option>";
                for (var index in r) {
                    var selected = '';
                    options += "<option data-valueemail='" + r[index]["email"] + "' data-valuefirstname='" + r[index]["firstname"] + "' data-valuelastname='" + r[index]["lastname"] + "'  value='" + r[index]["username"] + "' "+selected +">" + r[index]["username"] + "</option>";
                }
                $("#wt-user-list").html(options);
                 $("#wt-user-list option[value='undefined']").remove();
            } else {
                $("#wt-user-list").html("<option value=''> -Select the whiting-turner user- </option>");
            }

            });
       
            
}

function Get_wtuser_email(user)
{
   
   $('#user-email').val($("#wt-user-list option:selected").attr("data-valueemail"));
   $('#user-first_name').val($("#wt-user-list option:selected").attr("data-valuefirstname"));
   $('#user-last_name').val($("#wt-user-list option:selected").attr("data-valuelastname"));
   

}

</script>
</div>
