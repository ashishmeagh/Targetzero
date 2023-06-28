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


$systemAdminLogged = Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN;

$user_wt_username_value = $model->user_name;
$user_wt_username[] = $model->user_name;

$checkedyes = ($model->IsAduser == 1) ? 'checked' : '';
$checkedno = ($model->IsAduser == 0) ? 'checked' : '';


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
    <?php $form = ActiveForm::begin(['options' => ["id" => "user-form_spl_update"]]);?>
    <input style="display:none">
    <input type="password" style="display:none">
    <input type="hidden" id="Isnewrecord" value="<?php echo $model->isNewRecord ?>" style="display:none">
    <input type="hidden" id="userid" value="<?php echo $user->id ?>" style="display:none">
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group field-user-email-rb fg-line">
        <label class="control-label" for="user-email">Is this Whiting Turner Employee?</label>
         <input type="radio" class="user-rb-mt-yes" id="wtuser-yes-rb" name="wtuser-rb" value="1"   <?=$checkedyes?> >
        <label for="wtuser-yes-rb">Yes</label>
        <input type="radio" class="user-rb-mt-no" id="wtuser-no-rb" name="wtuser-rb" value="0"  <?=$checkedno?> >
        <label for="wtuser-no-rb">No</label>
        </div>
      </div>

            <div class="col-sm-3 <?=($model->IsAduser == 1) ? 'hidden' : ' '?>" id='wtuser-no'>
            <?=$form->field($model, 'user_name')->textInput(['maxlength' => 20, 'autocomplete' => 'off'])?>
            </div>
            <div class="col-sm-3 <?=($model->IsAduser == 0) ? 'hidden' : ' '?>" id='wtuser-yes' style="width: 25%;">
            <label class="control-label" for="email">Username</label>

            <?=Html::dropDownList('email', $user_wt_username_value, $user_wt_username, ['prompt' => '-Select the whiting-turner user-', 'class' => 'form-control select2-dropdown', 'id' => 'wt-user-list', 'onchange' => 'Get_wtuser_email(this)'])?>
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
            <?=Html::submitButton('Update', ['class' => 'btn btn-primary pull-right'])?>
        </div>
    </div>
    
 <script language="javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>

      var disableroles = [7,8,10,11,12,13,14,15,16];
      var disablerolesforno = [1,2,3,4,5,6,16];

       $('.select2-dropdown').select2();
       $( document ).ready(function() {
    GetUsers(0);
});


$('input[type=radio][name=wtuser-rb]').change(function() {
    if (this.value == '0') { //Non -WT user
       $('#wtuser-yes').addClass('hidden');
        $('#wtuser-no').removeClass('hidden');
        $('#user-user_name').prop('readonly', false);
        $('#user-user_name').val('');
        $("#wt-user-list").empty();
                   
               

    }
    else if (this.value == '1') { //WT user
      GetUsers(0);
        $('#wtuser-no').addClass('hidden');
        $('#wtuser-yes').removeClass('hidden');
          $('#user-user_name').val('');
                   
    }
});

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

function Get_wtuser_email(user)
{
   $('#user-user_name').val(user.value);
   $('#user-email').val($("#wt-user-list option:selected").attr("data-valueemail"));
   $('#user-first_name').val($("#wt-user-list option:selected").attr("data-valuefirstname"));
   $('#user-last_name').val($("#wt-user-list option:selected").attr("data-valuelastname"));

   //$('#user-user_name').val(user);

}



    </script>
    <?php ActiveForm::end();?>
</div>

