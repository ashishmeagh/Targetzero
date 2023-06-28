<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Update Issue / ' . ' ' . $model_master->id;
    $this->params['breadcrumbs'][] = ['label' => 'Issues', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Update: '.$model_master->id;
?>
<div class="app-case-update">

    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
        <ul class="actions">
            <li>
                <?= Html::a('<i class="md md-close"></i>', ['index']) ?>
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
        <div class="card-body card-padding">
            <?= $this->render("_form", [
                'model_master' => $model_master,
                'model_detail' => $model_detail,
                'modeUpdate' => true
            ]) ?>
        </div>
    </div>
</div>
