<?php

use app\models\CausationFactor;
use app\models\ReportType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\AppCase */

$this->title = strtoupper($model->appCaseType->type);
$this->params['breadcrumbs'][] = [
    'label' => 'App Cases',
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

if ($model->app_case_type_id == APP_CASE_INCIDENT) {
    $listData = ['1' => 'YES', '0' => 'NO'];
    $data_causation_factor = ArrayHelper::map(app\models\CausationFactor::find()->where(["is_active" => 1])->orderBy('causation_factor')->asArray()->all(), 'id', 'causation_factor');
    $data_report_type = ArrayHelper::map(app\models\ReportType::find()->where(["is_active" => 1])->orderBy('report_type')->asArray()->all(), 'id', 'report_type');
}
?>

<div class="app-case-view">

    <?=
    Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ])
    ?>

    <div class="block-header">
        <h2>Issue ID: <?= $model->id ?></h2>
        <ul class="actions">
            <li>
                <?php
                if (( Yii::$app->session->get('user.role_id') != ROLE_WT_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_WT_SAFETY_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_CLIENT_SAFETY_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_CONTRACTOR_SAFETY_MANAGER && Yii::$app->session->get('user.role_id') != ROLE_CLIENT_MANAGER ) || $model->creator_id == Yii::$app->session->get('user.id')) {
                    echo Html::a('<i class="md md-mode-edit"></i>', [
                        'update',
                        'id' => $model->id
                    ]);
                }
                ?>
            </li>
        </ul>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>
    </div>

    <div class="card">

        <div class="card-header" style="position:relative;">
            <h2 class="p-b-0 p-l-0"><?= strtoupper($model->appCaseType->type); ?>
                <small></small>
            </h2>

            <!--            --><?php
            //echo Html::tag( 'i', '', [
//                'class' => 'md-visibility',
//                'onclick' => '$(".table-responsive").slideToggle(); $(this).toggleClass("md-visibility-off");
//			'
//            ] ) 
            ?>
            <?= Html::img('@web/img/IssueType-' . $model->app_case_type_id . '.png', ['style' => 'position:absolute; width:60px; right:26px; top:23px;']) ?>
        </div>

        <div class="card-body table-responsive" tabindex="0" style="overflow: hidden; outline: none;">

            <table class="table">

                <tbody>

                    <tr>
                        <th><?= $model->getAttributeLabel('is_active') ?></th>
                        <td><?= ( $model->is_active ) ? Html::tag('i', '', ['class' => 'md md-check is-active']) : Html::tag('i', '', ['class' => 'md md-close is-active']) ?></td>
                    </tr>

                    <?php if ($model->app_case_type_id == APP_CASE_INCIDENT): ?>

                        <tr>
                            <th><?= $model_type->getAttributeLabel('incident_datetime') ?></th>
                            <td><?php
                                echo date("M d, Y - h:i:s A", strtotime($model_type->incident_datetime));
                                if (!is_null($model->jobsite->timezone_id)) {
                                    echo " (" . $model->jobsite->timezone->timezone . ")";
                                }
                                ?></td>
                        </tr>


                    <?php endif; ?>
                    <?php if ($model->app_case_type_id == APP_CASE_VIOLATION || $model->app_case_type_id == APP_CASE_RECOGNITION || $model->app_case_type_id == APP_CASE_OBSERVATION): ?>
                        <tr>
                            <th><?= $model_type->getAttributeLabel('correction_date') ?></th>
                            <td><?php
                                echo date("M d, Y", strtotime($model_type->correction_date));
                                if (!is_null($model->jobsite->timezone_id)) {
//                                echo " (" . $model->jobsite->timezone->timezone . ")";
                                }
                                ?></td>
                        </tr>

                    <?php endif; ?>

                    <?php if ($model->affectedUser): ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('affected_user_id') ?></th>
                            <td><?= $model->affectedUser->employee_number . ' - ' . $model->affectedUser->first_name . ' ' . $model->affectedUser->last_name ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <th><?= $model->getAttributeLabel('jobsite_id') ?></th>
                        <td><?= $model->jobsite->jobsite ?></td>
                    </tr>

                    <?php if ($model->subJobsite): ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('sub_jobsite_id') ?></th>
                            <td><?= $model->subJobsite->subjobsite ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <th><?= $model->getAttributeLabel('building_id') ?></th>
                        <td><?= $model->building->building ?></td>
                    </tr>
                    <?php
                    if (!empty($model->floor)):
                        ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('floor_id') ?></th>
                            <td><?= $model->floor->floor ?></td>
                        </tr>
                        <?php
                    endif;
                    ?>

                    <?php
                    if (!empty($model->area)):
                        ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('area_id') ?></th>
                            <td><?= ucwords($model->area->area) ?></td>
                        </tr>
                        <?php
                    endif;
                    ?>

                    <tr>
                        <th><?= $model->getAttributeLabel('contractor_id') ?></th>
                        <td><?= ucwords($model->contractor->contractor) ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('app_case_priority_id') ?></th>
                        <td><?= ucwords($model->appCasePriority->priority) ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('app_case_sf_code_id') ?></th>
                        <td><?= $model->appCaseSfCode->code ?></td>
                    </tr>

                    <tr>
                        <th>Safety Code Description</th>
                        <td><?= $model->appCaseSfCode->description ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('trade_id') ?></th>
                        <td><?= ucwords($model->trade->trade) ?></td>
                    </tr>

                    <?php if ($model->app_case_type_id == APP_CASE_VIOLATION || $model->app_case_type_id == APP_CASE_RECOGNITION): ?>
                        <?php if ($model_type->foreman): ?>

                            <tr>
                                <th><?= $model_type->getAttributeLabel('foreman_id') ?></th>
                                <td><?= $model_type->foreman->employee_number . ' - ' . $model_type->foreman->first_name . ' ' . $model_type->foreman->last_name ?></td>
                            </tr>

                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($model->app_case_type_id == APP_CASE_OBSERVATION): ?>
                        <?php if ($model_type->foreman): ?>

                            <tr>
                                <th><?= $model_type->getAttributeLabel('foreman_id') ?></th>
                                <td><?= $model_type->foreman->employee_number . ' - ' . $model_type->foreman->first_name . ' ' . $model_type->foreman->last_name ?></td>
                            </tr>

                        <?php endif; ?>

                        <tr>
                            <th><?= $model_type->getAttributeLabel('coaching_provider') ?></th>
                            <td><?= $model_type->coaching_provider ?></td>
                        </tr>

                       <tr>
                            <th>Description</th>
                            <td><?= $model->additional_information ?></td>
                        </tr>

                    <?php endif ?>

                    <?php if ($model->app_case_type_id == APP_CASE_INCIDENT): ?>

                        <tr>
                            <th><?= $model_type->getAttributeLabel('report_type_id') ?></th>
                            <td><?= $model_type->reportType->report_type ?></td>
                        </tr>



                        <tr>
                            <th><?= $model_type->getAttributeLabel('report_topic_id') ?></th>
                            <td><?= $model_type->reportTopic->report_topic ?></td>
                        </tr>

                        <?php
                        if (isset($model_type->recordable)) {
                            ?>
                            <tr>
                                <th><?= $model_type->getAttributeLabel('recordable') ?></th>
                                <td><?= $model_type->recordable == 1 ? 'YES':'NO' ?></td>
                            </tr>
                            <?php
                        }
                        if (isset($model_type->lost_time)) {
                            ?>
                            <tr>
                                
                                <th >Lost Time Injury</th>
                                <td> <span class='flag-field'><?= $model_type->is_lost_time == 1 ? 'YES':'NO' ?></span>  <span class='time-field'><?= $model_type->getAttributeLabel('lost_time') ?>  </span> <span ><?= $model_type->lost_time ?></span> </td>      
                            </tr>
                            
                            <?php
                        }
                        if (isset($model_type->dart_time)) {
                            ?>

                            <tr>
                            <th >DART</th>
                            <td > <span class='flag-field'><?= $model_type->is_dart == 1 ? 'YES':'NO' ?></span>  <span class='time-field'><?= $model_type->getAttributeLabel('dart_time') ?>  </span> <span><?= $model_type->dart_time ?></span> </td>
                            
                            <?php
                        }
                        
                        if (isset($model_type->bodyPart)) {
                            ?>
                            <tr>
                                <th><?= $model_type->getAttributeLabel('body_part_id') ?></th>
                                <td><?= $model_type->bodyPart->body_part ?></td>
                            </tr>
                            <?php
                        }
                        if (isset($model_type->injuryType)) {
                            ?>

                            <tr>
                                <th><?= $model_type->getAttributeLabel('injury_type_id') ?></th>
                                <td><?= $model_type->injuryType->injury_type ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    <?php endif; ?>



                    <?php if ($model->app_case_type_id != APP_CASE_OBSERVATION): ?>
                    <tr>
                        <th><?= $model->getAttributeLabel('additional_information') ?></th>
                        <td><?= $model->additional_information ?></td>
                    </tr>

                    <?php endif ?>

                 

                    <tr>
                        <th><?= $model->getAttributeLabel('app_case_status_id') ?></th>
                        <td>
                            <?= ucwords($model->appCaseStatus->status) ?>
                            <?= Html::img('@web/img/IssueState-' . $model->app_case_status_id . '.png', ['style' => 'width:15px; margin-left: 10px;']) ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Created by</th>
                        <td><?= $model->creator->employee_number . ' - ' . $model->creator->first_name . ' ' . $model->creator->last_name ?></td>
                    </tr>

                    <tr>
                        <th>Platform</th>
                        <td><span class='flag-field'><?php if($model->getPlatform($model->platform_id) != NULL){
                                                                echo $model->getPlatform($model->platform_id);
                                                            }else{
                                                                echo 'N/A';
                                                            } ?></span>
                        </td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('created') ?></th>
                        <td><?= date("M d, Y - h:i:s A", strtotime($created)) . " (" . $model->jobsite->timezone->timezone . ")" ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('updated') ?></th>
                        <td><?= date("M d, Y - h:i:s A", strtotime($model->updated)) . " (" . $model->jobsite->timezone->timezone . ")" ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('is_attachment') ?></th>
                        <td><?= ( $model->is_attachment ) ? Html::tag('i', '', ['class' => 'md md-check is-active']) : Html::tag('i', '', ['class' => 'md md-close is-active']) ?></td>
                    </tr>

                </tbody>

            </table>
            <?php for($count = 0;(!empty($model->attachments[$count]["destination_url"]) && count($model->attachments)>=$count);$count++){ ?>
                
                <?php if ($model->attachments[$count]["type"] == 'blob') : ?>
                
                <ul>
                    <?php $destinationURL = $model->attachments[$count]["destination_url"]; $mimeType = $model->attachments[$count]["mimeType"];  $mimeType_blob_url = $mimeType.",".$destinationURL;  ?>
                        <li> <a href=<?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION); if($extension == 'jpg' || $extension == 'jpeg'|| $extension == 'png' || $extension == 'JPG'){echo $destinationURL;}else{echo "javascript:void(0)" ;}  ?>  <?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION); if($extension == 'jpg' || $extension == 'jpeg'|| $extension == 'png' || $extension == 'JPG'){echo "target='_blank'";}else{echo "onclick = saveFile('".$mimeType_blob_url."')" ;}  ?>   > <?php $filename=explode("/",$destinationURL); echo $filename[4]; ?> </a> </li>     
                </ul>
            <?php elseif(!empty($model->attachments[$count]["destination_url"])): ?>
                <ul>
                <?php $filename = $model->attachments[$count]["destination_url"]  ?>
                        <li> <a href="<?php echo "/files/" . $filename; ?>" <?php $extension = pathinfo($filename, PATHINFO_EXTENSION); if($extension == 'jpg' || $extension == 'jpeg'|| $extension == 'png' || $extension == 'JPG'){echo "target='_blank'";}else{echo 'download';}?> > <?php echo $filename; ?> </a> </li>
                    
                </ul>
            <?php endif; ?> 
            <?php  }?>  
        </div>
    </div>


<?= $this->render("addattachment", ['model' => $model, 'Isattachmentenable'=>$Isattachmentenable]);
?>

    <?php if(count($comments) > 0): ?>
<div class="card"  style="overflow: auto; height:210px; ">
       <div class="card-header">
        
         <label class="checkbox checkbox-inline m-r-20"  style="line-height: 100%; font-size: 17px; font-weight: 400;">
        Comments
    </label>
    </div>
     <div class="card-body card-padding" tabindex="0" id = "displayComments" style="
    margin-top: -2%;">
 <?php
            foreach ($comments as $comment):
                ?>

                <div class="card comment-card">
                    <div class="card-header ch-alt">
                        <h2>
                            <?php if ($model->app_case_type_id == APP_CASE_INCIDENT): ?>
                                <?= strtoupper($comment->reportType->report_type) . ' Report' ?>
                            <?php endif; ?>
                            <small>
                                <?= '&#8226; ' . $comment->user->first_name . ' ' . $comment->user->last_name . ' &#8226; ' . date("M d, Y h:i:s A", strtotime($comment->created)) . " (" . $model->jobsite->timezone->timezone . ")" ?>
                            </small>
                        </h2>
                    </div>
                    <div class="card-body card-padding">
                        <?= $comment->comment ?>
                    </div>
                </div>

                <?php
endforeach;
?>
</div>
</div>
 <?php endif; ?> 
    <div class="card">

    <div class="card-header">
        
         <label class="checkbox checkbox-inline m-r-20"  style="line-height: 100%; font-size: 17px; font-weight: 400;">
        <input type="checkbox" value="1" class="comments-checkbox"
        name="comments_allowed" onchange="onChangeCommentSection(this)"><i
        class="input-helper"></i>Add Comment
    </label>
    </div>

    <div class="card-body card-padding" tabindex="0" style="overflow: hidden; outline: none; display: none;" id = "viewComments">

           
                <!-- Formulario para crear un comentario -->
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'id' => 'IssueViewForm'
                    ],
                ]);?>

                <div class="col-sm-12">
                    <?=$form->field($model_comment, 'comment')->textarea(['rows' => 6, 'onchange' => "onChangeComment(this)"])?>
                </div>

                <?php if ($model->app_case_type_id == APP_CASE_INCIDENT): ?>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group fg-line">
                                <div class="form-group fg-line">
                                    <?=
$form->field($model_comment, 'report_type_id')->dropDownList($data_report_type, ['prompt' => '-Choose a type-',
    'id' => 'report_type_id'])
?>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="form-group fg-line">
                                <?=$form->field($model_comment, 'causation_factor')->dropDownList($data_causation_factor, ['prompt' => '-Choose a causation factor-', 'id' => 'causation_id'])
?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group fg-line">
                                <label class="control-label" for="is_dart-select"><b>Days Away, Restricted and Transfer (DART)</b></label>
                                <?=
                                    Html::dropDownList('Comment[is_dart]', $model_type->is_dart, $listData, [
                                        'id' => 'is_dart-select',
                                        'class' => 'form-control','onchange' => 'onLostTimeClick(this.value)'
                                    ])
                                    ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group fg-line">
                                <label class="control-label" for="is_lost_time-select"><b>Lost Time Injury</b></label>
                                <?=
                                    Html::dropDownList('Comment[is_lost_time]', $model_type->is_lost_time, $listData, [
                                        'id' => 'is_lost_time-select',
                                        'class' => 'form-control','onchange' => 'onLostTimeClick(this.value)'
                                    ])
                                    ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                    <div class="col-sm-6">
                            <div class="form-group fg-line">
                                <label class="control-label" for="recordable-select"><b>Recordable Injury</b></label>
                                <?=
                                    Html::dropDownList('Comment[recordable]', $model_type->recordable, $listData, [
                                        'id' => 'recordable_select',
                                        'class' => 'form-control',
                                    ])
                                    ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group fg-line">
                                <label class="control-label" for="is_lost_time-select"><b>Property Damage</b></label>
                                <?=
Html::dropDownList('Comment[is_property_damage]', $model_type->is_property_damage, $listData, [
    'id' => 'is_property_damage-select',
    'class' => 'form-control',
])
?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?php if (Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN): ?>
                                <div class="pull-left">
                                    <div class="form-group field-appcaseincident-area_id required fg-line">
                                        <label class="checkbox checkbox-inline m-r-20">
                                            <input type="checkbox" value="1" name="skip" id="skip"><i
                                                class="input-helper"></i> Skip notifications
                                        </label>

                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            <?php endif;?>
                            
                        </div>
                        <div class="col-sm-6 ">
                                <?=Html::submitButton('Comment', ['class' => 'btn btn-primary pull-right', 'id' => 'comment-form-submit-button'])?>
                            </div>
                        
                    </div>

                <?php endif;?>

                <?php if ($model->app_case_type_id == APP_CASE_VIOLATION || $model->app_case_type_id == APP_CASE_RECOGNITION || $model->app_case_type_id == APP_CASE_OBSERVATION): ?>
                    <?php if (Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN): ?>
                        <div class="col-sm-6 m-b-20">
                            <div class="form-group field-appcaseincident-area_id required fg-line">
                                <label class="checkbox checkbox-inline m-r-20" name="skip" id="skip">
                                    <input type="checkbox" value="1" name="skip" id="skip" ><i
                                        class="input-helper"></i> Skip notifications
                                </label>

                                <div class="help-block"></div>
                            </div>
                        </div>
                    <?php endif;?>

                    <div class="col-sm-12">
                        <?=Html::submitButton('Comment', ['class' => 'btn btn-primary pull-right', 'id' => 'comment-form-submit-button'])?>
                    </div>
                <?php endif;?>
                <?php ActiveForm::end();?>
                <!-- Formulario para crear un comentario -->
        </div>

</div>
<?= !(Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) ?'': $this->render("newsflash", [
    'followerUserlist' => $followerUserlist, 'enableSafetyalert' => $model]);
?>

</div>
<style>
.time-field{
    display: inline-block;
    width: 240px;
    margin-left: 100px;
    font-weight: bold; 
}
.flag-field{
    display: inline-block;
        width: 181px;
        padding-right: 5px;
        
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
    var newsflash = function (app_case_id) {
        swal({
            title: "Newsflash",
            text: "Please confirm you want to send this Newsflash.",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancel",
            confirmButtonColor: "#FF6319",
            confirmButtonText: "Confirm",
            closeOnConfirm: true
        }, function () {
            executeAjax("<?=Yii::$app->urlManager->createUrl('ajax/send-newsflash?id=')?>" + app_case_id);
        });
        return false;
    }


    var onChangeComment = function (element) {
     if (element.value.length > 0)
     {
        document.getElementById("comment-form-submit-button").disabled = false;

    }
};
var onChangeCommentSection = function (element) {
        if (element.checked)
        {
            $("#viewComments").show();

        } else
        {
            $("#viewComments").hide();
            
        }
    };
    var onLostTimeClick = function (event)
    { 
        if(event == 1)
        {
            $('#recordable_select').val("1");
            $("#recordable_select").attr("disabled", "disabled");
        } else if($('#is_lost_time-select').val() == 0 && $('#is_dart-select').val() == 0)
        {
            $('#recordable_select').val("0");
            $("#recordable_select").removeAttr("disabled");
        }
    };

// Download a file form a url.
function saveFile(mimetype_blob_url) {

  var blob_mime_arr = mimetype_blob_url.split(",");
  var blob_url = blob_mime_arr[1];
  // Get file name from url.
  var filename = blob_url.substring(blob_url.lastIndexOf("/") + 1).split("?")[0];
  var xhr = new XMLHttpRequest();
  xhr.responseType = 'blob';
  xhr.onload = function() {
    var a = document.createElement('a');
    a.href = window.URL.createObjectURL(xhr.response); // xhr.response is a blob
    a.download = filename; // Set the file name.
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    delete a;
  };
  xhr.overrideMimeType(blob_mime_arr[0]);
  xhr.open('GET', blob_url);
  xhr.send();
}

    if( $('#recordable_select').val() == 1 ){
        $("#recordable_select").attr("disabled", "disabled");
    }

    $( "#IssueViewForm" ).submit(function( event ) {
    $("#recordable_select").prop( "disabled", false );
    });  



</script>
