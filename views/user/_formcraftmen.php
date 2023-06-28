<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$user_wt_email = [];
//get user
$user = app\models\User::find()->where([
	"id" => Yii::$app->session->get("user.id"),
])->one();
$data_role = \app\helpers\security::getAvailableRoles($model->role_id, $model->isNewRecord);
if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
	$data_contractor = ArrayHelper::map(app\models\Contractor::find()->where(["is_active" => 1])->orderBy('contractor')->asArray()->all(), 'id', 'contractor');
} else {
	// $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'jobsite' );
	$data_jobsite = ArrayHelper::map(app\models\Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
	$data_contractor = app\models\ContractorJobsite::getContractorsForJobsites($data_jobsite);
}
//$data_contractor = ArrayHelper::map( app\models\Contractor::find()->where([ "is_active" => 1 ])->orderBy('contractor')->asArray()->all(), 'id', 'contractor' );
//$data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'jobsite' );
//$data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'jobsite' );
//get available jobsites for current user
//  $data_jobsite = ArrayHelper::map(app\models\Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
// $data_contractor = ArrayHelper::map( app\models\Contractor::find()->where([ "is_active" => 1,"contractor_" ])->orderBy('contractor')->asArray()->all(), 'id', 'contractor' );
//  $data_contractor = app\models\ContractorJobsite::getContractorsForJobsites($data_jobsite);
//Logged in user is system admin?
$systemAdminLogged = Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN;
$urlSelectingContractor = 'ajax/get-jobsites-by-contractor-and-user?contractor_id=';
if ($model->contractor_id) {
	$data_contractor_jobsite = ArrayHelper::map(app\models\Jobsite::find()
			->joinWith('contractorJobsites')
			->where(["is_active" => 1, "contractor_id" => $model->contractor_id])
			->asArray()->all(), 'id', 'jobsite');
	//Jobsites para el contractor y el usuario logueado.
	/*->joinWith('userJobsites')*/
	$data_contractor_user_jobsite = ArrayHelper::map(app\models\Jobsite::find()
			->joinWith('userJobsites')
			->joinWith('contractorJobsites')
			->where(["is_active" => 1, "contractor_id" => $model->contractor_id, "user_jobsite.user_id" => $user->id])
			->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
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
$user_jobsites_selected = ArrayHelper::map(app\models\UserJobsite::find()->where(["user_id" => $model->id])->asArray()->all(), 'jobsite_id', 'user_id');
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
if($disablecontractor){
  if((Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_ADMIN) && ($model->IsAduser == 0))
   $disablecontractor = false;
}

$checkedyes = ($model->IsAduser == 1) ? 'checked' : '';
$checkedno = ($model->IsAduser == 0) ? 'checked' : '';

//Disable user name
$usernamereadonly = 'false';
if ($model->role_id > 6) {
	$usernamereadonly = 'true';
}

$disableactivestatus = 'disabled';
if((Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) )
   $disableactivestatus = '';

?>
<style>
   .ms-selected[readonly] { pointer-events: none; opacity: 0.6;}
   .box1 {width:50% !important;}
   .box2 {width:50% !important;}
   .bootstrap-duallistbox-container .buttons{display:none;}
/*   .box1 .form-control option:before{
      content: "\f298";
      font-family: 'Material Design Iconic Font';
      font-size: 13px;
      line-height: 22px;
      margin-right: 6px;
      top: 1px;
      position: relative;
    }*/
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

   .fg-line .form-control:readonly {
    background-color: #f7f7f7;
    pointer-events: none !important;
}

  .select2-container--disabled, .select2-container--default {
    width: 100% !important;
  }

/*    .box2 .form-control option:before{
      content: "\f297";
      color: gray;
      font-family: 'Material Design Iconic Font';
      font-size: 13px;
      line-height: 22px;
      margin-right: 6px;
      top: 1px;
      position: relative;
    }
*/

</style>
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/dual-listbox/dist/dual-listbox.css"/> -->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/bootstrap-duallistbox.css"/>

<div class="user-form">
    <?php $form = ActiveForm::begin(['options' => ["id" => "user-form"]]);?>
    <input style="display:none">
    <input type="password" style="display:none">
    <input type="hidden" id="Isnewrecord" value="<?php echo $model->isNewRecord ?>" style="display:none">
    <input type="hidden" id="userid" value="<?php echo $user->id ?>" style="display:none">
    <div class="row">

        <div class="col-sm-6">
            <?=$form->field($model, 'contractor_id')->dropDownList($data_contractor, ['prompt' => '-Choose a Contractor-', 'onfocus' => "this.setAttribute('PrvSelectedValue',this.value);", 'readonly' => 'true', 'style' =>'pointer-events: none;', 'onchange' => 'contractorIdChange(this.value, this.getAttribute("PrvSelectedValue"), ' . $user->id . ',' . $model->isNewRecord . ')'])?>
        </div>
       
        <div class="col-sm-6">
            <?=$form->field($model, 'role_id')->dropDownList($data_role, ['prompt' => '-Choose a Role-', 'style' =>'pointer-events: none;',  'readonly' => true])?>
            <input type="hidden" name="User[role_id]" value="1">
        </div>
        


    </div>
    <div class="row">


         <div class="col-sm-3">
            <?=$form->field($model, 'first_name')->textInput(['maxlength' => 70,'readonly' => true])?>
        </div>
        <div class="col-sm-3">
            <?=$form->field($model, 'last_name')->textInput(['maxlength' => 70,'readonly' => true])?>
        </div>

      <div class="col-sm-6">
            <?=$form->field($model, 'email')->textInput(['maxlength' => 255,'readonly' => true])?>
       </div>
    </div>

    <div class="row">
                <div class="col-sm-6">
            <?=$form->field($model, 'phone')->textInput(['maxlength' => 70, 'class' => 'form-control maskphone'])->widget(\yii\widgets\MaskedInput::className(), ['mask' => '999-999-9999'])?>
        </div>

        <div class="col-sm-6">
            <?=$form->field($model, 'employee_number')->textInput(['maxlength' => 70,'disabled' => true])?>
        </div>
    </div>

   
    <div class="row">
        <div class="col-sm-6">
            <?=$form->field($model, 'emergency_contact_name')->textInput(['maxlength' => 500])?>
        </div>
        <div class="col-sm-6">
            <?=$form->field($model, 'emergency_contact')->textInput(['maxlength' => 70])->widget(\yii\widgets\MaskedInput::className(), ['mask' => '999-999-9999'])?>
        </div>

    </div>

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

		// echo "selected";

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
                           name="User[is_active]" <?=($model->is_active == 1) ? "checked" : ""?> <?=($disableactivestatus)?>>
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0"
                           name="User[is_active]" <?=($model->is_active == 0) ? "checked" : ""?> <?=($disableactivestatus)?>>
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
    <!-- <script src="https://cdn.jsdelivr.net/npm/dual-listbox/dist/dual-listbox.min.js"></script>
    --> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>

      var disableroles = [7,8,10,11,12,13,14,15,16];
      var disablerolesforno = [1,2,3,4,5,6,16];


       $('.select2-dropdown').select2();
       $( document ).ready(function() {
    
     if($('#user-emergency_contact').val() == 0)
     	$('#user-emergency_contact').val('');

    if(!$('#Isnewrecord').val() == 1)
       $(".select2-dropdown").attr('disabled', true);

});
   var JobsitesDuallist = $('.duallist').bootstrapDualListbox({
                                filterPlaceHolder:'Search...',
                                moveOnSelect: true,
                            });

      $(document).on('keyup', ".bootstrap-duallistbox-container .filter", function () {
      $(this).blur();
      $(this).focus();
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
                         //$( ".dual-listbox" ).remove();
                         //$(".bootstrap-duallistbox-container").remove();
                         $('#user-jobsite-select').empty();
                         JobsitesDuallist.append(options);
                         JobsitesDuallist.bootstrapDualListbox('refresh', true);
                       }

            });
        };




    </script>
    <?php ActiveForm::end();?>
</div>

