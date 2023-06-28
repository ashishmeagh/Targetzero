<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$user_wt_email = [];
//get user
$user = app\models\User::find()->where([
	"id" => Yii::$app->session->get("user.id"),
])->one();


$user_inactive_users = [];


$data_role = \app\helpers\security::getAvailableRoles($model->role_id, $model->isNewRecord);


$systemAdminLogged = Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN;

$user_wt_username_value = $model->user_name;
$user_wt_username[] = $model->user_name;

$checkedyes = ($model->IsAduser == 1) ? 'checked' : '';
$checkedno = ($model->IsAduser == 0) ? 'checked' : '';

$data_contractor = ArrayHelper::map(app\models\Contractor::find()->where(["is_active" => 1])->orderBy('contractor')->asArray()->all(), 'id', 'contractor');

$selectedcontractor = [];

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
    background: url(./img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;
}

</style>
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/dual-listbox/dist/dual-listbox.css"/> -->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/bootstrap-duallistbox.css"/>

<div class="user-form">
    <?php $form = ActiveForm::begin(['options' => ["id" => "user-form_user_merge"]]);?>
    <input style="display:none">
    <input type="hidden" id="userid" value="<?php echo $user->id ?>" style="display:none">
    <div class="row">
          <div class="col-sm-3">
             <div class="form-group field-wt-parent-user required">

                <p class="c-black f-500 m-b-10">
                    Parent user:
                </p>
                <div class="select m-b-15">
                        <input type="text" class="form-control selectpicker" placeholder="Start typing the person name" name="active-user-search" id="active-user-search"/>
                          <input type="hidden" name="parent-user" id="parent-user"/>
                </div>

                  <div class="help-block" id="parent-user-help-block"></div>
            </div>
            </div>
        <div class="col-sm-3">
        <div class="form-group field-user-email-radio fg-line">
        <label class="control-label" for="user-email">Would you like to pull the users based on contractors?</label>
         <input type="radio" class="user-rb-mt-yes" id="conuser-yes-rb" name="conuser-rb" value="1" >
        <label for="conuser-yes-rb">Yes</label>
        <input type="radio" class="user-rb-mt-no" id="conuser-no-rb" name="conuser-rb" value="0" checked>
        <label for="conuser-no-rb" >No</label>

        <div class="help-block"></div>
        </div>
            </div>

         <div class="col-sm-3 hidden" id="contractor-div">
         	<div class="form-group field-user-contractor_id required fg-line has-success">
<label class="control-label" for="user-contractor_id">Contractor</label>

      <?=Html::dropDownList('User[contractor_id]',$selectedcontractor, $data_contractor,  ['prompt' => '-Choose a Contractor-',  'id' => 'user-contractor_id', 'class' => 'form-control', 'onchange' => 'contractorIdChange(this.value)'])?>

<div class="help-block"></div>
</div>
  
        </div>

   

             <div class="col-sm-3"  style="width: 25%;">
              <div class="form-group field-wt-inactive-user-list required">

            <label class="control-label" for="email">User to Merge</label>

            <?=Html::dropDownList('mergeuser', $user_wt_username_value, $user_inactive_users, ['prompt' => '-Select the Inactive user-', 'class' => 'form-control select2-dropdown', 'id' => 'wt-inactive-user-list'])?>
            <input type="hidden" name="merge-user-id" id="merge-user-id"/>
            <div class="help-block" id="wt-inactive-user-list-help-block"></div>
            </div>
            </div>


    </div>

    <div class="row">
        <div class="col-sm-12">
            <?=Html::submitButton('Merge', ['class' => 'btn btn-primary pull-right', 'id' => 'merge-submit-button'])?>
        </div>
    </div>
    
 <script language="javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
    <script>


      var disableroles = [10,11,12,13,14,15];


       $('.select2-dropdown').select2();
       $( document ).ready(function() {
   $('#user-form_user_merge').on('beforeSubmit', function (e) {
    var returnstatus = true;
    $('#wt-inactive-user-list-help-block').empty();
     $('#parent-user-help-block').empty();
        if($('#wt-inactive-user-list').val()  == ""){
         $('#wt-inactive-user-list-help-block').append('Please select the user to Merge ');
          $('.field-wt-inactive-user-list').addClass('has-error');
           returnstatus = false;
        }
        if($('#parent-user').val() == ""){
         $('#parent-user-help-block').append('Please select the Parent User');
          $('.field-wt-parent-user').addClass('has-error');
           returnstatus = false;
        }
  return returnstatus;
      });
       	//Inactive
    GetUsers(0,0);
});
 //Create a Auto Search URL
    autoSearchurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-active-user') ?>";

$('#active-user-search').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
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
           // $('#active-user-search').val(suggestion.data);
            $('#parent-user').val(suggestion.data);
        }
    }).blur(function() {
    if($('#active-user-search').val().length == 0){
         $('#parent-user').val('');
        }        
    })
    .focus(function() {
    if($('#active-user-search').val().length == 0){
         $('#parent-user').val('');
        }        
    });

     $('input[type=radio][name=conuser-rb]').change(function() {
    if (this.value == '0') 
    { 
    	$("#contractor-div").addClass('hidden');
    	$('#user-contractor_id').val('');
    	GetUsers(0,0);

    }
    else if (this.value == '1') 
    {
        $("#contractor-div").removeClass('hidden');
        
        $('#user-contractor_id').val('');


    }
    });

 var contractorIdChange = function ( $contractor)
        {
        	 GetUsers(0,$contractor);
        }    


function GetUsers( $Iactive, $conid){
  executeAjax
            (
                "<?=Yii::$app->urlManager->createUrl('/ajax/get-users')?>"+  "<?='?isactive='?>" + $Iactive+  "<?='&conid='?>" + $conid
            ).done(function(r){
            if (r.length != 0) {
                var options = "<option value=''>-Select the inactive user-</option>";
                for (var index in r) {
                   
                    options += "<option value='" + r[index]["id"] + "'>" + r[index]["fulltname"] + "</option>";
                }
                $("#wt-inactive-user-list").html(options);
                $("#wt-inactive-user-list option[value='undefined']").remove();
            } else {
                $("#wt-inactive-user-list").html("<option value=''> -Select the whiting-turner user- </option>");
            }

            });


}




    </script>
    <?php ActiveForm::end();?>
</div>

