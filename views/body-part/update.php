<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Update Body Part / ' . ' ' . $model->body_part;
    $this->params['breadcrumbs'][] = ['label' => 'Body Parts', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Update: '.$model->body_part;
?>
<div class="body-part-update">

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
    </div>

    <div class="card">
        <div class="card-body card-padding">
            <?= $this->render('_form', [ 'model' => $model, ]) ?>
        </div>
    </div>
</div>
