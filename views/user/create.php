<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Create User';
    $this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Create';
?>
<style>
.message {
    margin-top: 10px;
}
</style>
<div class="user-create">

    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
        <p class="message">WT employee users are created automatically. If still there is a problem, please contact CMIC help desk</p>
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
