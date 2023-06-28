<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;

    $data_timezone = ArrayHelper::map( app\models\Timezone::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'timezone' );
    
?>
<style>
.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
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
.circleloader {
    background: url(../img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;
}
.circle-load{
    position: absolute;
    right: 45px;
    top:4px;
    width:5%;
    padding: 23px 18px 3px 4px;
}
 .jobsite-circle-load{
    position: absolute;
    right: 45px;
    top:4px;
    width:5%;
    padding: 23px 18px 3px 4px;
}
.jobsite-admins{
    padding-left:14px;
}
.ja1 {
  display: inline-block;
  color: #FC7300;
}
.ja2 {
  display: inline-block;
  color: #FC7300;
}
.help-block{
    margin-top: 0px;
}
</style>

<div class="jobsite-form">
    <?php $form = ActiveForm::begin(["id" => "new-jobsite-form","class"=>"jobsite-form"]); ?>
    <div class="row">
        <div class="col-sm-6 jobsite-search">
                <label class="control-label" >Jobsite</label>
                <div class="m-b-15">
                    <input type="text" class="form-control selectpicker clearable"  placeholder="Search for the jobsite"  name="jobsite-name" id="jobsite-name"/>
                    <input type="hidden" id="jobsite-id" value = <?= $model->id ?>>
                    <input type="hidden" id="is-cmic" value = <?= $model->is_cmic ?>>
                    <span class="jobsite-circle-load"></span></div>
                    <div class="help-block jobsite-req-err"></div>
                </div>
                <?= $form->field($model, 'jobsite')->textInput(['maxlength' => 255]); ?>
        <div class="col-sm-6">
            <?= $form->field( $model, 'timezone_id' )->dropDownList( $data_timezone, [
                'prompt' => '-Choose a timezone-',
            ] ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'job_number')->textInput(['maxlength' => 255 , 'readonly'=> true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'address')->textInput(['maxlength' => 255, 'readonly'=> true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'city')->textInput(['maxlength' => 255, 'readonly'=> true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'state')->textInput(['maxlength' => 255, 'readonly'=> true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'zip_code')->textInput(['maxlength' => 255, 'readonly'=> true]) ?>
        </div>
        <div class="col-sm-6 wt-user-search">
        <label class="control-label" >Add Administrators to the Jobsite</label>
        <div class="m-b-15">
            <input type="text" class="form-control selectpicker clearable"  placeholder="Search for the user"  name="wt-user" id="wt-user"/>
            <input type="hidden" name="jobAdm1" id="jobAdm1">
            <input type="hidden" name="jobAdm2" id="jobAdm2">
            <span class="circle-load"></span></div>
            <div class="help-block jobsite-admin-req-err"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 hidden">
            <?= $form->field($model, 'sr_vp')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-sm-6 hidden">
            <?= $form->field($model, 'wt_group')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>
    <div class="row">
    <div class="col-sm-6 hidden">
            <?= $form->field($model, 'exec_vp')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="jobsite-jobsite" class="control-label">Photo Allowed</label>
                <div class="m-t-15">
                    <label class="radio radio-inline m-r-20">
                        <input type="radio" value="1" name="Jobsite[photo_allowed]" <?= ($model->photo_allowed == 1) ? "checked" : "" ?> >
                        <i class="input-helper"></i>
                        Yes
                    </label>
                    <label class="radio radio-inline m-r-20">
                        <input type="radio" value="0" name="Jobsite[photo_allowed]" <?= ($model->photo_allowed == 0) ? "checked" : "" ?> >
                        <i class="input-helper"></i>
                        No
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="jobsite-jobsite" class="control-label">Safety Alert Allowed</label>
                <div class="m-t-15">
                    <label class="radio radio-inline m-r-20">
                        <input type="radio" value="1" name="Jobsite[newsflash_allowed]" <?= ($model->newsflash_allowed == 1) ? "checked" : "" ?> >
                        <i class="input-helper"></i>
                        Yes
                    </label>
                    <label class="radio radio-inline m-r-20">
                        <input type="radio" value="0" name="Jobsite[newsflash_allowed]" <?= ($model->newsflash_allowed == 0) ? "checked" : "" ?> >
                        <i class="input-helper"></i>
                        No
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div>
                <ul class = "jobsite-admins">
                    <li id = "jobsite-admin-item1" class="ja1"> </li><button type="button" class="close ja1" id="jobsite-admin-item1-close" aria-label="Close">
                    <span aria-hidden="true" >&times;</span></button>
                </ul>
            </div>
            <div>
                <ul class = "jobsite-admins">
                    <li id = "jobsite-admin-item2" class="ja2">  </li><button type="button" class="close ja2" id="jobsite-admin-item2-close" aria-label="Close">
                    <span aria-hidden="true" >&times;</span></button>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="Jobsite[is_active]" <?= ($model->is_active == 1) ? "checked" : "" ?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="Jobsite[is_active]" <?= ($model->is_active == 0) ? "checked" : "" ?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right jobsite-submit']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script language="javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
<script type="text/javascript">

    $('#jobsite-admin-item1').text("");
    $('#jobsite-admin-item2').text("");
    $('.field-jobsite-jobsite').hide();
    $('#jobsite-admin-item1-close').hide();
    $('#jobsite-admin-item2-close').hide();
    
    if($("#jobsite-jobsite").val() !== ""){
        $('#jobsite-name').val($("#jobsite-jobsite").val());
        var jobsite_id = $("#jobsite-id").val();
        var count = 1;
        $.ajax({
                    url: "<?= Yii::$app->urlManager->createUrl('ajax/get-jobsite-admins?jobsite_id=') ?>" + jobsite_id,
                    type: "POST",
                    dataType: "JSON",
                    success: function($response) {
                        $response.forEach(addJobAdminIds);

                            function addJobAdminIds(item) {
                                if(count < 3 ){
                                    $('#jobAdm'+count).val(item['user_id']);
                                    $('#jobsite-admin-item'+count).text(item['first_name']+" "+item['last_name']);
                                    $('#jobsite-admin-item'+count+'-close').show();
                                    count++;
                                }
                            }
                    }
                });
                if($('#is-cmic').val() !== 0){
                    $('#jobsite-name').attr('readonly', true);
                }
    }

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


autoSearchurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-jobsites-data') ?>";

    $('#jobsite-name').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        
        onSearchStart: function (container) {
                $('.jobsite-circle-load').addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $('.jobsite-circle-load').removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
                $('#jobsite-jobsite').val(suggestion.job_name);
                $('.jobsite-search').children("input").attr('PrvSelectedValue',suggestion.data);
                $('.jobsite-search').removeClass('has-error');
                $('#jobsite-job_number').val(suggestion.data);
                $('#jobsite-address').val(suggestion.address);
                $('#jobsite-city').val(suggestion.city);
                $('#jobsite-state').val(suggestion.state);
                $('#jobsite-zip_code').val(suggestion.zipcode);
        }
    })

  $(".ja1").click(function(){
    $('#jobsite-admin-item1').text("");
    $('#jobAdm1').val('');
    $('li#jobsite-admin-item1').hide();
    $('#jobsite-admin-item1-close').hide();
    });
    $(".ja2").click(function(){
    $('#jobsite-admin-item2').text("");
        $('#jobAdm2').val('');
        $('li#jobsite-admin-item2').hide();
        $('#jobsite-admin-item2-close').hide();
    });

    autoSearchurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-wt-users-data') ?>";

    $('#wt-user').autocomplete({
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

                 $('#wt-user').val(suggestion.value);
                 $('.wt-user-search').children("input").attr('PrvSelectedValue',suggestion.data);
                 $('#wt-user').val('');

                if($('#jobsite-admin-item1').text() === ""){

                    $('li#jobsite-admin-item1').show();
                    $('#jobsite-admin-item1').text(suggestion.value);
                    $('#jobAdm1').val(suggestion.data);
                    $('.wt-user-search').removeClass('has-error');
                    $('#jobsite-admin-item1-close').show();
                    removeDivContent();
                }else if($('#jobsite-admin-item2').text() === ""){

                    $('li#jobsite-admin-item2').show();
                    $('#jobsite-admin-item2').text(suggestion.value);
                    $('#jobAdm2').val(suggestion.data);
                    $('#jobsite-admin-item2-close').show();
                    if($('#jobAdm2').val() == $('#jobAdm1').val()){
                        $('.jobsite-admin-req-err').empty().append('Same user cannot be selected again!');
                        $('.jobsite-admin-req-err').css("color", "red");
                        $('#jobAdm2').val("");
                        $('#jobsite-admin-item2').text("");
                        $('#jobsite-admin-item2-close').hide();
                    setTimeout(function(){
                        $('.jobsite-admin-req-err').empty();
                    }, 5000);
                    }
                }
                else{
                    
                    $('.jobsite-admin-req-err').empty().append('Only two jobsite administrators are allowed per jobsite!');
                    $('.jobsite-admin-req-err').css("color", "red");
                    setTimeout(function(){
                        $('.jobsite-admin-req-err').empty();
                    }, 5000);
                }
        }
    })

    
    function removeDivContent(){
            $('.jobsite-admin-req-err').empty();
        }

    $(document).ready(function() {

            $("#new-jobsite-form").submit(function(e) {
            if($('#jobsite-name').val() === ""){
                    
                    $('.jobsite-search').addClass('has-error');
                    $('.jobsite-req-err').empty().append('Jobsite cannot be blank');
                }

            if($('#jobAdm1').val() === "" && $('#jobAdm2').val() === ""){
                e.preventDefault();
                $('.wt-user-search').addClass('has-error');
                $('.jobsite-admin-req-err').empty().append('Choose atleast one jobsite admin');
            }

            function removeDivContent(){
                
                $('.jobsite-admin-req-err').empty();
            }
    
  });
})
    
</script>