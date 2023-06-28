<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Update User / ' . ' ' . $model->first_name . ' ' . $model->last_name;
    $this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Update: '.$model->first_name . ' ' . $model->last_name;
?>
<div class="user-update">

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
            <?= $this->render('_formcraftmen', [ 'model' => $model, ]) ?>
        </div>
    </div>
</div>