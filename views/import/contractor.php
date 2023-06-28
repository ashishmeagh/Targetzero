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

$this->title = 'Import contractors';
$this->params[ 'breadcrumbs' ][ ] = $this->title;
?>

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

<div class="import-contractor">

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
                            <th>Download template</th>
                            <td>
                                <?= Html::a( 'Download', [ 'contractor-template' ], ["class" => "btn btn-link btn-raised"] );  ?>
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

</div>
