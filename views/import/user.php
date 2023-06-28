<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$wt_jobsiteid_value = '';
$this->title = 'Import users';
$this->params[ 'breadcrumbs' ][ ] = $this->title;

$data_jobsite = ArrayHelper::map(app\models\Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
?>
<style type="text/css">
    .disabledonclick{
        pointer-events: none;
        cursor: default;
        color: gray;
    }
</style>
<span class="error-swal"></span>
<div class="preloader-backdrop preloader-transparent"></div>
<div class="preloader preloader-transparent">
    <div class="windows8">
        <div class="wBall" id="wBall_1">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_2">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_3">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_4">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_5">
            <div class="wInnerBall"></div>
        </div>
    </div>
</div>

<div class="import-user">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 ">
            <div class="card import">
                <h2><?= Html::encode($this->title) ?></h2>
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Select Jobsite</th>
                            <td>
                    <?= Html::dropDownList('jobsite_id',$wt_jobsiteid_value, $data_jobsite,['prompt' => '-Select a jobsite-','class' => 'form-control select2-dropdown', 'id'=> 'imp-user-jobsiteid','onchange' => 'jobsiteIdChange(this)']) ?>

                            </td>
                        </tr>
                        <tr>
                            <th>Download template<br/> <b>Note:</b> Please select a jobsite to download the template.</th>
                            <td>
                                <?= Html::a( 'Download', [ '#' ], ["class" => "btn btn-link btn-raised disabledonclick", "id" => "user-import"] );  ?> 
                            </td>
                        </tr>
                        <tr>
                            <th>Upload file</th>
                            <td>
                                <label for="file-upload" class="btn btn-link btn-raised">
                                    Select file
                                </label>
                                <?= $form->field($model, 'file')->fileInput(["id"=>"file-upload"])->label(false) ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group">
                    <?= Html::submitButton( 'Upload', [ 'class' => 'btn btn-primary align-right' ] ) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

 <script type="text/javascript">
        // Contractor
        var jobsiteIdChange = function (data)
        {
          var href = $('#user-import').attr('href');
          if(data.value != ""){
            href = "/import/user-template" + "?jobid="+data.value;
          $('#user-import').attr("href", href);
          $('#user-import').removeClass('disabledonclick');
      }else{
         $('#user-import').addClass('disabledonclick');
          
        }
      }
</script>

</div>
