<?php

use app\models\CausationFactor;
use app\models\ReportType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
?>

<style type="text/css">
	.select2-container--krajee .select2-selection--multiple .select2-search--inline .select2-search__field{
		min-width: 10em;
	}
</style>
<div class="card">

    <div class="card-header">
       <label class="checkbox checkbox-inline m-r-20"  style="line-height: 100%; font-size: 17px; font-weight: 400;">
        <input type="checkbox" value="1" class="newsflash-checkbox"
        name="newsflash_allowed"  id="newsflash_allowed" onchange="onChangeNewsFlash(this)" <?php echo ($enableSafetyalert !== null && $enableSafetyalert->jobsite->newsflash_allowed == 1) ? "" : " disabled='disabled'" ?>>
        <i class="input-helper"></i>Send Safety Alert?
    </label>
</div>
    <div id="dvnewsflash" class="card-body card-padding" tabindex="0" style="display: none; padding-top: 2%;" >
     <?php $form = ActiveForm::begin([
    'options' => [
        'onsubmit' => "return validateNewsFlashForm()",        
    ],
]);?>

     <div class="row hide" id="news-flash-body-header">
        <div class="col-sm-12">
           <div class="form-group pull-left m-t-10">
              <label class="radio radio-inline m-r-20 hide">
                <input type="radio" value="user" onclick="selectusergroup(this);" name="newsflash-user[is_active]" checked>
                <i class="input-helper"></i>
                Users
            </label>
            <label class="radio radio-inline m-r-20 hide">
                <input type="radio" value="group" onclick="selectusergroup(this);"name="newsflash-user[is_active]">
                <i class="input-helper"></i>
                Groups
            </label>
         <div id="div-news-flash-no-users hide">
            <h5 style="font-size: 13px;color: #f44336;" > <?=(Yii::$app->session->get('user.role_id') != ROLE_ADMIN) ? 'Please select "select users" / "Enter users"':'Please select "select users"' ?></h5>
        </div>
        </div>
    </div>
</div>
<div class="row" id = "userList">
    <div class="col-sm-6">
        <div class="form-group fg-line" id="select-user-div">
            <?php 
              echo '<label class="control-label">Select Users</label>';
              echo Select2::widget([
                'id' => 'newsflash-users',
                'name' => 'newsflash-users',
                'data' => $followerUserlist,
                'options' => [
                    'placeholder' => 'Select users ...',
                    'multiple' => true,
                    'onchange' => "onChangeNewsFlashSelectUsers(this)"
                ],
                'pluginOptions' => [
			        'allowClear' => true
			    ],
            ]);?>

    </div>
</div>
</div>

<div class="row hide" id = "userList-groupname" style="display:none !important;">
    <div class="col-sm-6">
        <div class="form-group fg-line">
            <div class="form-group fg-line">
                <label class="checkbox checkbox-inline m-r-20">
                    <input type="checkbox" value="1" name="group-name" id="group-name" onchange="onChangeGroupName(this)"><i
                    class="input-helper"></i> Do you like to create a group?
                </label>
                <?=Html::textInput('newsflash-group-name', '', array('class' => 'form-control hide', 'id' => 'newsflash-group-name', 'placeholder' => 'Please enter group name' , 'style' => 'margin-top: 1%;'))?>
            </div>
        </div>
    </div>
</div>

<div class="row hide" id = "groupList">
    <div class="col-sm-6">
        <div class="form-group fg-line" id="select-user-group">
            <div class="form-group fg-line">
              <?php 
              echo '<label class="control-label">Select Groups</label>';
              echo Select2::widget([
                'id' => 'newsflash-groups',
                'name' => 'newsflash-groups',
                'data' => ['1'=>'Observation Group', '2'=>'Recognation Group'],
                'maintainOrder' => true,
                'options' => [
                    'placeholder' => 'Select Groups ...',
                    'multiple' => true,
                    'allowClear' => true
                ],
            ]);?>

        </div>
    </div>
</div>
</div>

<div class="row">
 <div class="col-sm-6">
    <div class="form-group fg-line" id="select-customemails-div">
        <div class="form-group fg-line">
           <label for="newsflash-emails-field" class="control-label">Enter the Email id's</label>
           <?=Html::textInput('newsflash-emails-field', '', array('class' => 'form-control', 'id' => 'newsflash-emails-field', 'onchange' => 'onChangenewflashinput(this)', 'placeholder' => 'Please enter comma seperated emails'))?>

           <div id="div-news-flash-error" style="display: none">
            <h5 style="font-size: 13px;color: #f44336;" > Please enter the valid email id's with comma seperated.</h5>
        </div>
        <div id="div-news-flash-blank-error" style="display: none">
            <h5 style="font-size: 13px;color: #f44336;" >Enter the Email id's cannot be blank.</h5>
        </div>
    </div>
</div>
</div>
</div>

<div class="row">
    <div class="col-sm-6 pull-right">
        <?=Html::submitButton('Send Safety Alert', ['class' => 'btn btn-primary pull-right', 'id' => 'newsflash-form-submit-button'])?>
    </div>
</div>
<?php ActiveForm::end();?>

</div>

</div>

<script>


var onChangeNewsFlash = function (element) {
       if (element.checked)
        {
            $("#dvnewsflash").show();

        } else
        {
            $("#dvnewsflash").hide();
            
        }
    };
    function selectusergroup(myRadio) {
        if(myRadio.value == "user"){
           $("#groupList").addClass('hide');
           $("#userList, #userList-groupname").each(function(){
            $(this).removeClass('hide') });
       } else if(myRadio.value == "group")
       {
        $("#userList, #userList-groupname").each(function(){
            $(this).addClass('hide') });

        $("#groupList").removeClass('hide');

    }
}

var onChangeGroupName = function (element) {
    if (element.checked)
    {
        $("#newsflash-group-name").removeClass('hide');

    } else
    {
        $("#newsflash-group-name").addClass('hide');

    }
};

var onChangenewflashinput = function(evt) {
    $("#div-news-flash-blank-error").hide();
    let valid  = validateEmailList(evt.value);
     $("#newsflash-form-submit-button").removeAttr("disabled");
    if(valid)
    {
       $("#div-news-flash-error").hide();
       document.getElementById("newsflash-emails-field").parentNode.className =
       document.getElementById("newsflash-emails-field").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
       document.getElementById("newsflash-emails-field").parentNode.className += " has-success";
       
   }else{
    $("#div-news-flash-error").show();
    document.getElementById("newsflash-emails-field").parentNode.className =
    document.getElementById("newsflash-emails-field").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
    document.getElementById("newsflash-emails-field").parentNode.className += " has-error";
   
}
};
function validateEmailList(raw){
    var emails = raw.split(',');
    var valid = true;
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    for (var i = 0; i < emails.length; i++) {
        if( emails[i] === "" || !regex.test(emails[i].replace(/\s/g, ""))){
            valid = false;
        }
    }
    return valid;
}

function validateNewsFlashForm(){
    
    var customEmailFields =  document.getElementById("newsflash-emails-field").value;
    var SelectedUsers =  getMultipleSelectedUserValue();

    if((typeof SelectedUsers != "undefined" && SelectedUsers != null && SelectedUsers.length != null && SelectedUsers.length > 0) || customEmailFields != "")
    {
        if(!validateEmailList(customEmailFields) && customEmailFields != "") {
            return false;
        }
        $("#select-user-div, #select-customemails-div").each(function(){
            $(this).removeClass('has-error') });
    $("#news-flash-body-header, #div-news-flash-no-users").each(function(){
            $(this).addClass('hide') });
    
    }else{
          $("#news-flash-body-header, #div-news-flash-no-users").each(function(){
            $(this).removeClass('hide') });
     $("#select-user-div, #select-customemails-div").each(function(){
            $(this).addClass('has-error') });
     $("#newsflash-form-submit-button").attr("disabled");
    return false;
     
    }   
    
}

var onChangeNewsFlashSelectUsers = function(evt) {
   var SelectedUsers =  getMultipleSelectedUserValue();
 var customEmailFields =  document.getElementById("newsflash-emails-field").value;
     if((typeof SelectedUsers != "undefined" && SelectedUsers != null && SelectedUsers.length != null && SelectedUsers.length > 0) || customEmailFields != "")
    {
        $("#newsflash-form-submit-button").removeAttr("disabled");
           $("#select-user-div, #select-customemails-div").each(function(){
            $(this).removeClass('has-error') });
           $("#news-flash-body-header, #div-news-flash-no-users").each(function(){
            $(this).addClass('hide') });
    
    } 

}

function getMultipleSelectedUserValue()
    {
     var SelectusersArray = [];
      var selectedUser =document.getElementById("newsflash-users");
      for (var i = 0; i < selectedUser.options.length; i++) {
         if(selectedUser.options[i].selected ==true){
              SelectusersArray.push(selectedUser.options[i].value);
          }
      }
       return SelectusersArray; 
    }



</script>