<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use yii\widgets\Breadcrumbs;
    use yii\grid\GridView;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;

    /* @var $this yii\web\View */
    /* @var $model app\models\User */

    $this->title = $model->first_name . " " . $model->last_name;
	$this->params[ 'breadcrumbs' ][ ] = [
        'label' => $model->first_name . " " . $model->last_name,
        'url'   => [ 'profile' ]
    ];
    $this->params[ 'breadcrumbs' ][ ] = 'Change Password';
	
?>
<div class="user-view">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <h2>Change Password <small>Changing your password will log you out of all of your other sessions.</small></h2>
    </div>
	
    <div class="card">
	
        <h2 class="p-b-0"><?= $model->first_name . " " . $model->last_name ?></h2>

        <div class="card-body table-responsive card-padding" tabindex="0" style="overflow: hidden; outline: none;">

            <?php $form = ActiveForm::begin(['method' => 'post']); ?>
			
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= $form->field($model_form, "old_password")->input("password") ?>   
					</div>
				</div>	
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= $form->field($model_form, "password")->input("password") ?>   
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= $form->field($model_form, "password_repeat")->input("password") ?>   
					</div>
				</div>
			</div>

			<?= Html::submitButton('Change', ["class" => "btn btn-primary"]) ?>	
			
			<?php $form->end() ?>

        </div>

    </div>
</div>
