<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
?>
<div class="trade-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'trade')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="Trade[is_active]" <?= ($model->is_active == 1) ? "checked" : "" ?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="Trade[is_active]" <?= ($model->is_active == 0) ? "checked" : "" ?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>