<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;

    $data_sf_codes = ArrayHelper::map( app\models\AppCaseSfCode::find()->where(["parent_id"=>null])->asArray()->all(), 'id', 'code');
?>
<!-- Safety Code Modal Selection -->
<div class="modal" id="safety-code-parent-id-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select Safety Code</h4>
            </div>
            <div id="safety-code-tree-view-container" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Safety Code Modal Selection -->

<div class="app-case-sf-code-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'parent_id')->textInput() ?>
            <div class="form-group">
                <label for="input-safety-code-display" class="control-label">Safety Code Parent</label>
                <input type="text" maxlength="255" value="<?= $safetyCodeParentName ?>" name="" class="form-control" id="input-safety-code-display" onfocus="parentIdBtnClick()">
                <div class="help-block"></div>
            </div>
            <script>
                parentIdBtnClick = function()
                {
                    getSafetyCodeTreeView
                    (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-safety-code-tree-view') ?>",
                        "#safety-code-parent-id-modal",
                        "#safety-code-tree-view-container",
                        "#input-safety-code-display",
                        "#appcasesfcode-parent_id"
                    );
                    return false;
                };
            </script>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'description')->textarea() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="AppCaseSfCode[is_active]" <?= ($model->is_active == 1) ? "checked" : "" ?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="AppCaseSfCode[is_active]" <?= ($model->is_active == 0) ? "checked" : "" ?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>