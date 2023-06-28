<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Resources */

//$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Resources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resources-view col-sm-4">
    <div class="mini-charts-item ">
        <div class="resource_type" data-type="<?= $model->type_id   ?>">
            <?php switch($model->type_id){
                case 1:
                    echo "<i class='md md-description'></i>";
                    break;
                case 2:
                    echo "<i class='md md-folder-open'></i>";
                    break;
                case 3:
                    echo "<i class='md md-insert-link'></i>";
                    break;
            }
            ?>
        </div>
        <div class="resource_data">

            <h2 class="resources-truncate"><?= Html::encode($model->title) ?></h2>

            <a target="_blank" href="<?= $model->url ?>"><p class="resources-truncate"><?= Html::encode($model->description) ?></p></a>
        </div>
        <ul class="actions">
            <li class="dropdown action-show">
                <a href="" data-toggle="dropdown">
                    <i class="md md-more-vert"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => '']) ?>
                    </li>
                    <li>
                        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => '',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

</div>
