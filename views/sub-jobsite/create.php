<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\SubJobsite */

$this->title = 'Create Sub jobsite';
$this->params['breadcrumbs'][] = ['label' => 'Sub jobsites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-jobsite-create">

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
