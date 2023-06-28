<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;

   // $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'jobsite' );
if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN)
{
	 $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'jobsite' );
    $contractor_jobsites_selected = ArrayHelper::map( app\models\ContractorJobsite::find()->where( [ "contractor_id" => $model->id ] )->asArray()->all(), 'jobsite_id', 'contractor_id' );
}
else{
	$contractor_jobsites_selected = ArrayHelper::map( app\models\ContractorJobsite::find()->where( [ "contractor_id" => $model->id ] )->asArray()->all(), 'jobsite_id', 'contractor_id' );

    //--
  //$data_jobsite = ArrayHelper::map(app\models\Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
    $data_jobsite = app\models\Jobsite::getJobsite(Yii::$app->session->get("user.id"),$model->id );
    $diffJobsites = app\models\Jobsite::getDifferentJobsite(Yii::$app->session->get("user.id"),$model->id );
    if(!empty($diffJobsites))
    {
        foreach ($diffJobsites as $value) {
       $array[] = $value['Id'];
    } 
}
    
 }


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
    .box1 .form-control option:hover{    
      color: #FF6319;  
    }

    .box2 .form-control option[readonly]{
     pointer-events: none; 
     color: rgba(0, 0, 0, 0.8); 
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
/*    .box2 .form-control option:before{
      content: "\f297";
      color: gray;
      font-family: 'Material Design Iconic Font';
      font-size: 13px;
      line-height: 22px;
      margin-right: 6px;
     top: 1px;
     position: relative;
    }*/

    option:disabled {
      color: #b9b6b6 !important;
    }
    .fg-line .form-control:readonly {
    background-color: #f7f7f7;
    pointer-events: none !important;
   }

</style>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/bootstrap-duallistbox.css"/>
<div class="contractor-form">
    <?php $form = ActiveForm::begin(["id" => "contractor-form"]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'contractor')->textInput(['maxlength' => 255, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'address')->textInput(['maxlength' => 255,'readonly'=>true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'vendor_number')->textInput(['maxlength' => 255,'readonly'=>true]) ?>
        </div>
    </div>
    <div class="row" id="contractor-jobsite-select-container">
        <div class="col-sm-12">
            <div>
                <div class="block-header p-0 m-0">
                    <h2 class="p-0 m-0">Jobsites
                        <small>( Select Jobsites for this contractor )</small>
                    </h2>
                </div>
                <div class="col-sm-6 p-l-0 m-t-15 ms-custom-header"><p>All Jobsites</p></div>
                <div class="col-sm-6 p-r-0 m-t-15 ms-custom-header"><p>Assigned Jobsites 
                  <?php  if (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN ): ?>
                  <br/> 
                  <span class="text-danger"> Note: "Removal of Jobsites for a contractor's profile is restricted , Please contact System Admin (CMIC Helpdesk/Sammy Torres)".</span>
                  <?php endif; ?>  
                </p></div>
                <div class="col-ms12">
				<?php
          $disabledjb = "";
         if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN ): ?>
                         <select id="user-jobsite-select" multiple="multiple" class="form-control duallist" name="Contractor[jobsites][]">
                        <?php foreach ( $data_jobsite as $key => $value ):
                            $selected_jobsites = isset( Yii::$app->request->post("Contractor")["jobsites"] )? array_flip(Yii::$app->request->post("Contractor")["jobsites"]) : null;
                            ?>
                            <!--                        --><?php //foreach ( $data_jobsite as $key => $value ): ?>
                            <option value="<?= $key ?>" <?php if ( isset( $contractor_jobsites_selected[ $key ] ) || isset( $selected_jobsites[ $key ] ) )
                            {
                                echo "selected";
                            } ?> ><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
				<?php else: ?>
				 <select id="user-jobsite-select" multiple="multiple" class="form-control duallist" name="Contractor[jobsites][]">
                        <?php foreach ( $data_jobsite as $key => $value ):
                            $selected_jobsites = isset( Yii::$app->request->post("Contractor")["jobsites"] )? array_flip(Yii::$app->request->post("Contractor")["jobsites"]) : null;
                            ?>
                                   
                            <option value="<?= $value["Id"] ?>" <?php if ( isset( $contractor_jobsites_selected[ $value["Id"] ] ) || isset( $selected_jobsites[ $value["Id"] ] ) )
                            {
                                if(isset($array))
                                {
                                  if(in_array($value["Id"],$array))
                                {
                                     $disabledjb = $disabledjb . $key.","; 
                                     echo "selected disabled"; 
                                }
                                 else {
                                     echo "selected disabled"; //Remove this Users needs to add to exisiting job sites
                                 }  
                                }
                                else
                                {
                                    echo "selected disabled";
                                }
                                 
                            } ?> ><?= $value["jobsite"] ?></option>
                            
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" id="disabledjobsites" name="disabledjobsites" value="<?= $disabledjb ?>">
				<?php endif; ?>  
                   
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 22px;">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="Contractor[is_active]" <?= ($model->is_active == 1) ? "checked" : "" ?>  disabled>
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="Contractor[is_active]" <?= ($model->is_active == 0) ? "checked" : "" ?> disabled>
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right contractor-jobsite-button']) ?>
        </div>
    </div>

    <div class="modal fade" id="create-new-contractor" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">


                <div class="modal-header">
                    <h4 class="p-b-0 p-l-0 modal-title">Potential existing matches</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="similarContractors"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link cancel" data-dismiss="modal" data-target="#create-new-contractor">Cancel</button>
                        <button type="button" class="btn btn-primary pull-right submit">Create</button>
                    </div>
                </div>


            </div>
        </div>

    </div>
 <script language="javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
     <script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.bootstrap-duallistbox.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dual-listbox/dist/dual-listbox.min.js"></script>
    <script>
   var JobsitesDuallist = $('.duallist').bootstrapDualListbox({
                                filterPlaceHolder:'Search...'
                            });
      $(document).on('keyup', ".bootstrap-duallistbox-container .filter", function () {
      $(this).blur();
      $(this).focus();
      });
                        </script>

    <?php ActiveForm::end(); ?>
</div>
