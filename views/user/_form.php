<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$user_wt_email = [];
//get user
$user = app\models\User::find()->where([
	"id" => Yii::$app->session->get("user.id"),
])->one();
$data_contractor = [];
$data_role = \app\helpers\security::getAvailableRoles($model->role_id, $model->isNewRecord);
if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
	
    // $contractors_sp = Yii::$app->db->createCommand("exec [dbo].[Contractors] ")->queryAll();
    // $data_contractor = ArrayHelper::map($contractors_sp, 'id', 'contractor');
    if(isset($model->IsAduser)&& ($model->IsAduser == 1)){
	   $data_contractor = ArrayHelper::map(app\models\Contractor::find()->where(["is_active" => 1, "id"=> 148])->orderBy('contractor')->asArray()->all(), 'id', 'contractor');
	}else{
		$data_contractor = ArrayHelper::map(app\models\Contractor::find()->where(["is_active" => 1])->orderBy('contractor')->asArray()->all(), 'id', 'contractor');

	}
    
} else {
	$data_jobsite = ArrayHelper::map(app\models\Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->distinct()->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
	$data_contractor = app\models\ContractorJobsite::getContractorsForJobsites($data_jobsite);
}

$systemAdminLogged = Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN;
$urlSelectingContractor = 'ajax/get-jobsites-by-contractor-and-user?contractor_id=';
if ($model->contractor_id) {
	
    $contractor_jobsite_sp = Yii::$app->db->createCommand("exec [dbo].[ContractorJobsites] '" . $model->contractor_id . "'")->queryAll();
    $data_contractor_jobsite = ArrayHelper::map($contractor_jobsite_sp, 'id', 'jobsite');
    
    $contractor_user_jobsite_sp = Yii::$app->db->createCommand("exec [dbo].[ContractorUserJobsites] '" . $model->contractor_id . "','" . $user->id . "'")->queryAll();
    $data_contractor_user_jobsite = ArrayHelper::map($contractor_user_jobsite_sp, 'id', 'jobsite');
    
	//If the logged in user has role System Admin, "Available jobsites" list doesn't restrict to those associated to the user.
	if ($systemAdminLogged) {
		$data_contractor_user_jobsite = $data_contractor_jobsite;
	}
} else {
	$data_contractor_jobsite = array();
}

//If the logged in user has role System Admin, "Available jobsites" list doesn't restrict to those associated to the user.
if ($systemAdminLogged) {
	$urlSelectingContractor = 'ajax/get-jobsites-by-contractor?id=';
}
$user_jobsites_selected = ArrayHelper::map(app\models\UserJobsite::find()->where(["user_id" => $model->id])->distinct()->asArray()->all(), 'jobsite_id', 'user_id');
$diffJobsites = app\models\Jobsite::getDifferentJobsite(Yii::$app->session->get("user.id"), $model->contractor_id);
if (!empty($diffJobsites)) {
	foreach ($diffJobsites as $value) {
		$array[] = $value['Id'];
	}
}

$user_wt_username_value = $model->user_name;
$user_wt_username[] = $model->user_name;

$disableemail = ($model->isNewRecord == 1) ? false : true;
$disablecontractor= ($model->isNewRecord == 1) ? false : true;
$disable_wt_contractor= ($model->IsAduser == 1) ? true : false;


if($disablecontractor){
  if((Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_ADMIN) && ($model->IsAduser == 0))
   $disablecontractor = false;
}

$checkedyes = ($model->IsAduser == 1) ? 'checked' : '';
$checkedno = ($model->IsAduser == 0) ? 'checked' : '';

//Disable user name
$usernamereadonly = 'false';
$disabledusername_roles = array(7,8,15);
if ($model->role_id > 6 && !in_array($model->role_id, $disabledusername_roles)) {
	$usernamereadonly = 'true';
}

$disableactivestatus = 'disabled';
if((Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) || ($model->role_id > 6 )   )
   $disableactivestatus = '';

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

    .select2-container--disabled, .select2-container--default {
        width: 100% !important;
    }

    .loader {
        border: 10px solid #f3f3f3; /* Light grey */
        border-top: 10px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        text-align: center;
        margin: auto;
        padding-bottom: 15px;
        }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

  .autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
  .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
  .autocomplete-selected { background: #F0F0F0; }
  .autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
  .autocomplete-group { padding: 2px 5px; }
  .autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
  .circleloader {
    background: url(../img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;
}
.circle-load{
    position: absolute;
    right: 50px;
    top:48px;
    width:5%;
    padding: 23px 18px 3px 4px;
}

/*
    Clearable text inputs
*/
.clearable{
  background: #fff url(http://i.stack.imgur.com/mJotv.gif) no-repeat right -10px center;
  padding: 3px 18px 3px 4px; /* Use the same right padding (18) in jQ! */
  transition: background 0.4s;
}
.clearable.x  { background-position: right 5px center; } /* (jQ) Show icon */
.clearable.onX{ cursor: pointer; } /* (jQ) hover cursor style */
.clearable::-ms-clear {display: none; width:0; height:0;} /* Remove IE default X */



</style>

<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/bootstrap-duallistbox.css"/>

<div class="user-form">
    <?php $form = ActiveForm::begin(['options' => ["id" => "user-form"]]);?>
    <input style="display:none">
    <input type="password" style="display:none">
    <input type="hidden" id="Isnewrecord" value="<?php echo $model->isNewRecord ?>" style="display:none">
    <input type="hidden" id="userid" value="<?php echo $user->id ?>" style="display:none">
    <div class="row">

  <?php if ($model->isNewRecord): ?>
        <div class="col-sm-3 hidden">
        <div class="form-group field-user-email-radio fg-line">
        <label class="control-label" for="user-email">Is this Whiting Turner Employee?</label>
        <input type="radio" class="user-rb-mt-yes" id="wtuser-yes-rb" name="wtuser-rb" value="1" >
        <label for="wtuser-yes-rb">Yes</label>
        <input type="radio" class="user-rb-mt-no" id="wtuser-no-rb" name="wtuser-rb" value="0" checked>
        <label for="wtuser-no-rb">No</label>

        <div class="help-block"></div>
        </div>
            </div>
                 <div class="col-sm-3" id='wtuser-no' style="width: 50%;">
      <?=$form->field($model, 'user_name')->textInput(['maxlength' => 20, 'autocomplete' => 'off', 'readonly' => true,
])?>
            </div>
            <div class="col-sm-3 hidden" id='wtuser-yes' style="width: 25%;">

          <div class="form-group field-user-user_name required fg-line">
            <label class="control-label" for="user-user_name">Username</label>

            <?=Html::dropDownList('user_name', $user_wt_username_value, $user_wt_username, ['prompt' => '-Select the whiting-turner user-', 'class' => 'form-control select2-dropdown', 'id' => 'wt-user-list', 'onchange' => 'Get_wtuser_email(this)'])?>
             <div class="help-block"></div>
             </div>
            </div>

            <div class="col-sm-6">
                <div class = 'WT-dropdown hidden'>
                <?=$form->field($model, 'contractor_id')->dropDownList($data_contractor, ['prompt' => '-Choose a Contractor-',  'onfocus' => "this.setAttribute('PrvSelectedValue',this.value);", 'onchange' => 'contractorIdChange(this.value, this.getAttribute("PrvSelectedValue"), ' . $user->id . ',' . $model->isNewRecord . ')'])?>
                </div>
            </div>
            <div class="col-sm-6  NWT-textbox">
            <label class="control-label" >Contractor</label>
            <div class="m-b-15">
                <input type="text" class="form-control selectpicker clearable"  placeholder="Start typing the contractor"  name="contractor_id" id="nwt-user-contractor_id"/>
            </div>
            </div><span class="circle-load"></span>    
            <?php else: ?>
            <div class="col-sm-3 hidden">
                <div class="form-group field-user-email-rb fg-line">
                <label class="control-label" for="user-email">Is this Whiting Turner Employee?</label>
                <input type="radio" class="user-rb-mt-yes" id="wtuser-yes-rb" name="wtuser-rb" value="1" disabled="<?=$disableemail?>"  <?=$checkedyes?> >
                <label for="wtuser-yes-rb">Yes</label>
                <input type="radio" class="user-rb-mt-no" id="wtuser-no-rb" name="wtuser-rb" value="0" disabled="<?=$disableemail?>"  <?=$checkedno?> >
                <label for="wtuser-no-rb">No</label>
            </div>
                </div>

            <div class="col-sm-3 <?=($model->IsAduser == 1) ? 'hidden' : ' '?>" id='wtuser-no' style="width: 50%;">
            <?=$form->field($model, 'user_name')->textInput(['maxlength' => 20, 'autocomplete' => 'off', ($usernamereadonly)?'disabled':'' ,])?>
            </div>
            <div class="col-sm-3 <?=($model->IsAduser == 0) ? 'hidden' : ' '?>" id='wtuser-yes' style="width: 50%;">
            <label class="control-label" for="email">Username</label>
            <?=Html::dropDownList('email', $user_wt_username_value, $user_wt_username, ['class' => 'form-control select2-dropdown', 'disabled' => $disableemail, 'id' => 'wt-user-list', 'onchange' => 'Get_wtuser_email(this)'])?>
            </div>
            <?php if ($model->IsAduser == 1): ?>
            <div class="col-sm-6">
                <div class = 'WT-dropdown'>
                <?=$form->field($model, 'contractor_id')->dropDownList($data_contractor, ['prompt' => '-Choose a Contractor-', 'onfocus' => "this.setAttribute('PrvSelectedValue',this.value);", 'disabled' => $disable_wt_contractor, 'onchange' => 'contractorIdChange(this.value, this.getAttribute("PrvSelectedValue"), ' . $user->id . ',' . $model->isNewRecord . ')'])?>
                </div>
            </div>
            <?php else: ?>
                <div class="col-sm-6  NWT-textbox">
                <label class="control-label" >Contractor</label>
                <div class="m-b-15">
                    <input type="text" class="form-control selectpicker clearable"  placeholder="Start typing the contractor"  name="contractor_id" id="nwt-user-contractor_id"/>
                </div>
                </div><span class="circle-load"></span>
                <div class="NWT-contractor_id hidden">
                <?=$form->field($model, 'contractor_id')->textInput(['maxlength' => 255, 'disabled' => $disablecontractor, 'type' => 'hidden'])?>
                </div>
            <?php endif;?>
            <?php endif;?>


    </div>
    <div class="row">
        <?php if (((!$model->isNewRecord) && (Yii::$app->session->get('user.role_id') == ROLE_ADMIN) && ($model->id == Yii::$app->session->get('user.id'))) || (($model->role_id == ROLE_ADMIN) && (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN))): ?>
        <div class="col-sm-6">
            <?=$form->field($model, 'role_id')->dropDownList($data_role, ['prompt' => [
                             'text' => '-Choose a Role-',
                             'options'=> ['disabled' => true, 'selected' => true]], 'disabled' => true])?>
            <input type="hidden" name="User[role_id]" value="1">
        </div>
        <?php else: ?>
         <div class="col-sm-6">
            <?=$form->field($model, 'role_id')->dropDownList($data_role, ['prompt' => [
                             'text' => '-Choose a Role-',
                             'options'=> ['disabled' => true, 'selected' => true]], ''])?>
        </div>
        <?php endif;?>

         <div class="col-sm-3">
            <?=$form->field($model, 'first_name')->textInput(['maxlength' => 70])?>
        </div>
        <div class="col-sm-3">
            <?=$form->field($model, 'last_name')->textInput(['maxlength' => 70])?>
        </div>
    </div>

    <div class="row">
       <div class="col-sm-6">
            <?=$form->field($model, 'email')->textInput(['maxlength' => 255])?>
       </div>

       <?php if ($model->isNewRecord): ?>
           <div class="col-sm-6">
                <?=$form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off', 'readonly' => true,
])?>
            </div>
      <?php else: ?>
         <?php if ($model->IsAduser == 0): ?>

             <div class="col-sm-5">
                <?=$form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off',
])?>
            </div>

            <label class="checkbox checkbox-inline col-sm-1 m-b-0 m-t-40">
                <input type="checkbox" value="1"
                       name="User[changePassword]" <?=($model->changePassword == false) ? "" : "checked"?> class="passwordChange" >
                <i class="input-helper"></i>
                   Change
            </label>
            <?php else: ?>
             <div class="col-sm-5">
                <?=$form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off', 'disabled' => true,
])?>
            </div>

            <?php endif;?>
      <?php endif;?>

    </div>

    <div class="row">
                <div class="col-sm-6">
            <?=$form->field($model, 'phone')->textInput(['maxlength' => 70, 'class' => 'form-control maskphone'])->widget(\yii\widgets\MaskedInput::className(), ['mask' => '999-999-9999'])?>
        </div>

        <div class="col-sm-6">
            <?=$form->field($model, 'employee_number')->textInput(['maxlength' => 70])?>
        </div>
    </div>

    <?php if (($model->sop == 1)): ?>
    <div class="row">
        <div class="col-sm-6">
            <?=$form->field($model, 'emergency_contact_name')->textInput(['maxlength' => 500])?>
        </div>
        <div class="col-sm-6">
            <?=$form->field($model, 'emergency_contact')->textInput(['maxlength' => 70])->widget(\yii\widgets\MaskedInput::className(), ['mask' => '999-999-9999'])?>
        </div>

    </div>

    <?php endif;?>

    <div class="divider"></div>

    <div class="row" id="user-jobsite-select-container">
        <div class="col-sm-12">
            <div>
                <div class="block-header p-0 m-0">
                    <h2 class="p-0 m-0">Jobsites
                        <small>( Select Jobsites for this User )</small>
                    </h2>
                     <div id="user-jobsite-error" class="has-error help-block hidden" style="color: #f44336;">Assigned Jobsite field cannot be blank.</div>
                </div>
                <div class="col-sm-6 p-l-0 m-t-15 ms-custom-header"><p>Available Jobsites</p></div>
                <div class="col-sm-6 p-r-0 m-t-15 ms-custom-header"><p>Assigned Jobsites</p></div>
                <div class="col-ms12">
                    <select id="user-jobsite-select" multiple="multiple" class="form-control duallist" name="User[jobsites][]">
<?php
$disabledjb = "";
foreach ($data_contractor_jobsite as $key => $value):
	//Sólo mostrar la opción del jobsite si el mismo es del usuario/contratista, o en caso contrario, ya estaba asignado al usuario a modificar.
	if (isset($data_contractor_user_jobsite[$key]) || isset($user_jobsites_selected[$key])):

	?>
	                            <option value="<?=$key?>" <?php if (isset($user_jobsites_selected[$key])) {
		if (isset($array)) {
			if (in_array($key, $array)) {
				if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
					echo "selected";
				} else {
					$disabledjb = $disabledjb . $key . ",";
					echo "selected disabled";
				}
			} else {
				echo "selected";
			}
		} else {
			echo "selected";
		}

		

	}?> ><?=$value?></option>

	<?php

endif;
endforeach;?>
                    </select>
<input type="hidden" id="disabledjobsites" name="disabledjobsites" value="<?=$disabledjb?>">

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1"
                           name="User[is_active]" <?=($model->is_active == 1) ? "checked" : ""?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0"
                           name="User[is_active]" <?=($model->is_active == 0) ? "checked" : ""?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?=Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right'])?>
        </div>
    </div>
    <div class="modal fade" id="create-new-user" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">


                <div class="modal-header">
                    <h4 class="p-b-0 p-l-0 modal-title">Potential existing matches</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="similarUsers"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link cancel" data-dismiss="modal" data-target="#create-new-user">Cancel</button>
                        <button type="button" class="btn btn-primary pull-right submit">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <script language="javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
     <script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.bootstrap-duallistbox.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
    <script type="text/javascript">

    $( "<label class='test'>Note: For W-T Employees use their assigned W-T Company Employee # </label>" ).insertAfter( ".field-user-employee_number > .control-label" );

      var disableroles = [7,8,10,11,12,13,14,15,16];
      var disablerolesforno = [1,2,3,4,5,6,16,19];
      

       $('.select2-dropdown').select2();
       $( document ).ready(function() {
        GetUsers(($('#Isnewrecord').val()==1)?1:0);
    
        //Check Username Error
        if($('#wtuser-no .field-user-user_name').hasClass('has-error')){
            $('#wtuser-yes .field-user-user_name').addClass('has-error');
            $('#wtuser-yes .help-block').html($('#wtuser-no .help-block').html());

        }else{
        $('#wtuser-yes .field-user-user_name').removeClass('has-error');
            $('#wtuser-yes .help-block').html('');

        }

        if(!$('#Isnewrecord').val() == 1)
        $(".select2-dropdown").attr('disabled', true);

        if($('#Isnewrecord').val() == 1){
            $(".NWT-textbox").append('<input  id="user-contractor_id"  name="User[contractor_id]" type="hidden"  />');
            $.each(disablerolesforno, function(k, v) {
                $('#user-role_id option[value=' + v + ']').prop('disabled', true);
                $('#user-role_id option[value=' + v + ']').hide();
            });
            $("#user-contractor_id").prop('disabled', true);
            $('#user-password').prop('readonly', false);
            $('#user-user_name').prop('readonly', false);
            $("#user-contractor_id option[value*='148']").prop('disabled',true);
            
        }else{
        if($('input[name=wtuser-rb]:checked').val() == 0)//NO
        {
            $.each(disableroles, function(k, v) {
            $('#user-role_id option[value=' + v + ']').prop('disabled', false);
        });
            $.each(disablerolesforno, function(k, v) {
            $('#user-role_id option[value=' + v + ']').prop('disabled', true);
            $('#user-role_id option[value=' + v + ']').hide();
        }); 
            getcontractorbyid($("#user-contractor_id").val());
            

        } else if($('input[name=wtuser-rb]:checked').val() == 1)//yes
        {
            $.each(disablerolesforno, function(k, v) {
            $('#user-role_id option[value=' + v + ']').prop('disabled', false);
        });  
            $.each(disableroles, function(k, v) {
            $('#user-role_id option[value=' + v + ']').prop('disabled', true);
        });
            $('#user-first_name').prop('readonly', true);
            $('#user-last_name').prop('readonly', true);
            $('#user-employee_number').prop('readonly', true);
            $('#user-email').prop('readonly', true);
            $('.NWT-textbox').addClass('hidden');
        }
        if($("#user-contractor_id").find(":selected").val() === '148'){

        $.each(disableroles, function(k, v) {
        $('#user-role_id option[value=' + v + ']').prop('disabled', true);
        $('#user-role_id option[value=' + v + ']').hide();
        });
        $.each(disablerolesforno, function(k, v) {
        $('#user-role_id option[value=' + v + ']').prop('disabled', false);
        $('#user-role_id option[value=' + v + ']').show();
        });
    }
}


});
   var JobsitesDuallist = $('.duallist').bootstrapDualListbox({
                                filterPlaceHolder:'Search...',
                                moveOnSelect: true,
                            });

      $(document).on('keyup', ".bootstrap-duallistbox-container .filter", function () {
      $(this).blur();
      $(this).focus();
      });

      
   $('input[type=radio][name=wtuser-rb]').change(function() {
    if (this.value == '0') { //Non -WT user
        $('#wtuser-yes').addClass('hidden');
        $("#user-contractor_id").prop('disabled', true);
        $('.WT-dropdown').addClass('hidden');
        $('.NWT-textbox').removeClass('hidden');
        $('#wtuser-no').removeClass('hidden');
        $('#user-password').prop('readonly', false);
        $('#user-user_name').prop('readonly', false);
        $('#user-email').val('');
        $('#user-user_name').val('');
        $('#user-password').val('');
        $('#user-email').val('');
        $('#user-first_name').val('');
        $('#user-last_name').val('');
        $('#user-phone').val('');
        $('#user-employee_number').val('');
        $('#user-jobsite-select').empty();
        $('#user-role_id').val('');
        $(".NWT-textbox").append('<input  id="user-contractor_id"  name="User[contractor_id]" type="hidden"  />');
        $("#user-contractor_id").val('');
        $('#user-contractor_id').attr('readonly', false);
        $("#user-contractor_id").css("pointer-events","unset");
        $("#wt-user-list").empty();
         $("#user-contractor_id option[value*='148']").prop('disabled',true);
       $.each(disableroles, function(k, v) {
           $('#user-role_id option[value=' + v + ']').prop('disabled', false);
      });
        $.each(disablerolesforno, function(k, v) {
           $('#user-role_id option[value=' + v + ']').prop('disabled', true);
      });

    }
    else if (this.value == '1') { //WT user
        GetUsers(($('#Isnewrecord').val()==1)?1:0);
        $('.NWT-textbox').children("input").remove();
        $('.NWT-textbox').addClass('hidden');
        $('.WT-dropdown').removeClass('hidden');
        $("#user-contractor_id").prop('disabled', false);
        $('#wtuser-no').addClass('hidden');
        $('#wtuser-yes').removeClass('hidden');
        $('#user-password').prop('readonly', true);
        $('#user-user_name').val('');
        $('#user-password').val('');
        $('#user-email').val('');
        $('#user-first_name').val('');
        $('#user-last_name').val('');
        $('#user-phone').val('');
        $('#user-role_id').val('');
        $('#user-employee_number').val('');
        $("#user-contractor_id").val(148);
        $('#user-contractor_id').attr('readonly', true);
        $("#user-contractor_id option[value*='148']").prop('disabled',false);
        $("#user-contractor_id").css("pointer-events","none");
        $("#wt-user-list").val('');
      $.each(disablerolesforno, function(k, v) {
           $('#user-role_id option[value=' + v + ']').prop('disabled', false);
      }); 
       $.each(disableroles, function(k, v) {
           $('#user-role_id option[value=' + v + ']').prop('disabled', true);
      });
        
             getjobsitebycontractorid(148, $("#userid").val());
        
        $('#user-jobsite-select').empty();
            }
        });


        // Contractor
        var contractorIdChange = function ( $contractor, $previousvalue, $user, $isNewRecord )
        {

          var executeajaxJ = false;
          if(!$isNewRecord){
                      jconfirm({
                title: 'Confirm!',
                content: 'You want to change the contractor, assigned jobsites will be deleted.',
                buttons: {
                     yes: {
                          btnClass: 'btn btn-primary',
                          action: function () {
                             executeajaxJ = true;
                             getjobsitebycontractorid($contractor, $user);
                          }
                      },
                      no: {
                          btnClass: 'btn-warning',
                          action: function () {
                              $contractor = $previousvalue;
                             $('#user-contractor_id').val( $previousvalue );
                          }
                      },
                }
            });
          }else{
            executeajaxJ = true;
          }

            if(executeajaxJ)
            {
              getjobsitebycontractorid($contractor, $user)
            }

            return;
        };

        function GetUsers( $Isnewrecord){
  executeAjax
            (
                "<?=Yii::$app->urlManager->createUrl('/ajax/get-wt-users')?>"+  "<?='?isnew='?>" + $Isnewrecord
            ).done(function(r){
            if (r.length != 0) {
               var exisintigemail = $('#user-user_name').val();
                
                var options = "<option value=''>-Select the whiting-turner user-</option>";
                for (var index in r) {
                    var selected = (exisintigemail == r[index]["username"]) ? 'selected': '';
                    options += "<option data-valueemail='" + r[index]["email"] + "' data-valuefirstname='" + r[index]["firstname"] + "' data-valuelastname='" + r[index]["lastname"] + "'  value='" + r[index]["username"] + "' "+selected +">" + r[index]["username"] + "</option>";
                }
                $("#wt-user-list").html(options);
                $("#wt-user-list option[value='undefined']").remove();
            } else {
                $("#wt-user-list").html("<option value=''> -Select the whiting-turner user- </option>");
            }
            });


}

        function getjobsitebycontractorid($contractor, $user){
              executeAjax
            (
                "<?=Yii::$app->urlManager->createUrl($urlSelectingContractor)?>" + $contractor +  "<?='&user_id='?>" + $user
            ).done(function(r){
                    if( r.length != 0 ){
                        var options = "";
                        for(var index in r){
                            options += "<option value='" + index + "'>" + r[index] + "</option>";

                        }
                         
                         $('#user-jobsite-select').empty();
                         JobsitesDuallist.append(options);
                         JobsitesDuallist.bootstrapDualListbox('refresh', true);
                       }

            });
        };
    function Get_wtuser_email(user)
    {
    $('#user-user_name').val(user.value);
    $('#user-email').val($("#wt-user-list option:selected").attr("data-valueemail"));
    $('#user-first_name').val($("#wt-user-list option:selected").attr("data-valuefirstname"));
    $('#user-last_name').val($("#wt-user-list option:selected").attr("data-valuelastname"));

    }

    var usernamenotrequiredroles = [11,12,13,14];
    $('#user-role_id').on('change', function() {
    if( usernamenotrequiredroles.includes(this.value)){
    $('#wtuser-no .field-user-user_name').removeClass('has-error');
    $('#wtuser-no .help-block').html('');
    }

    });

    autoSearchurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-contractors-data') ?>";

    $('#nwt-user-contractor_id').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        
        onSearchStart: function (container) {
                $('.circle-load').addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $('.circle-load').removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
            if($('#Isnewrecord').val() == 1){
                $('.NWT-textbox').children("input").val(suggestion.data);
                $('.NWT-textbox').children("input").attr('PrvSelectedValue',suggestion.data);
                getjobsitebycontractorid(suggestion.data, $("#userid").val());
            }else{
                if($('input[name=wtuser-rb]:checked').val() == 0){
                    $oldVal = $("#user-contractor_id").val();
                    $('#user-contractor_id').val(suggestion.data);
                    $newVal = $("#user-contractor_id").val();
                    contractorIdChange($newVal, $oldVal, $("#userid").val(),$('#Isnewrecord').val());
                }
                
            }
        }
    }).blur(function() {
    if($('#nwt-user-contractor_id').val().length == 0){
        $('.NWT-textbox').children("input").val('');
        }        
    })
    .focus(function() {
    if($('#nwt-user-contractor_id').val().length == 0){
        $('.NWT-textbox').children("input").val('');
        }        
    });

    /**
     * Clearable text inputs
     */
    function tog(v){return v ? "addClass" : "removeClass";} 
    $(document).on("input", ".clearable", function(){
        $(this)[tog(this.value)]("x");
    }).on("mousemove", ".x", function( e ){
        $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]("onX");
    }).on("touchstart click", ".onX", function( ev ){
        ev.preventDefault();
        $(this).removeClass("x onX").val("").change();
    });

function getcontractorbyid( $id){
  executeAjax
            (
                "<?=Yii::$app->urlManager->createUrl('/ajax/get-contractor-by-id')?>"+  "<?='?id='?>" + $id
            ).done(function(r){
            if (r.length != 0) {
                $contractor_name = r.contractor;
                $('#nwt-user-contractor_id').val($contractor_name);       
            } else {
               console.log("Contractor not found!");
            }
            });
}

    </script>
    <?php ActiveForm::end();?>
</div>

