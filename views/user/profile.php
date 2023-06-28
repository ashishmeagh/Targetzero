<?php

use app\components\FormatterHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->asArray()->all(), 'id', 'jobsite' );
    $user_jobsites_selected = ArrayHelper::map( app\models\UserJobsite::find()->where( [ "user_id" => $model->id ] )->asArray()->all(), 'jobsite_id', 'user_id' );


    $this->title = $model->first_name . " " . $model->last_name;
    $this->params[ 'breadcrumbs' ][ ] = $model->first_name . " " . $model->last_name;
?>
<div class="user-view">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <?php if( (Yii::$app->session->get('user.id') == $model->id) && (Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN)): ?>
    <div class="block-header">
        <h2>User profile</h2>
        <ul class="actions">
            <li>
                <?= Html::a( '<i class="md md-mode-edit"></i>', [
                    'update',
                    'id' => $model->id
                ] ) ?>
            </li>
        </ul>
    </div>
    <?php endif; ?>
    <div class="card">
	
        <h2 class="p-b-0"><?= $model->first_name . " " . $model->last_name ?></h2>

        <div class="card-body table-responsive card-padding" tabindex="0" style="overflow: hidden; outline: none;">

            <table class="table">

                <tbody>
				
					<tr>
						<th><?= $model->getAttributeLabel( 'user_name' ) ?></th>
						<td><?= $model->user_name ?></td>
					</tr>
					<tr>
						<th><?= $model->getAttributeLabel( 'employee_number' ) ?></th>
						<td><?= $model->employee_number ?></td>
					</tr>
				
					<tr>
						<th><?= $model->getAttributeLabel( 'email' ) ?></th>
						<td><?= $model->email ?></td>
					</tr>
					<?php if ($model->IsAduser == 0): ?>
					<tr>
						<th><?=$model->getAttributeLabel('password')?></th>
						<td>******** <?=Html::a('Change Password', ['/user/password'], ['style' => 'cursor:pointer;'])?></td>
					</tr>
					<?php endif;?>
					<tr>
						<th><?=$model->getAttributeLabel('phone')?></th>
						<td><?=FormatterHelper::asPhone(($model->phone ?? ""))?></td>
					</tr>

					<tr>
						<th><?=$model->getAttributeLabel('contractor_id')?></th>
						<td><?=ucwords($model->contractor->contractor)?></td>
					</tr>

					<tr>
						<th><?=$model->getAttributeLabel('role_id')?></th>
						<td><?=ucwords($model->role->role)?></td>
					</tr>
					
					<tr>
						<th><?=$model->getAttributeLabel('jobsites')?></th>
						<td>
							<ul id="user-jobsite">
								<?php foreach ($data_jobsite as $key => $value): ?>
									<?php
if (isset($user_jobsites_selected[$key])) {
    echo "<li>&#8226; ";
    echo $value;
    echo "</li>";
}
?>
								<?php endforeach;?>
							</ul>
						</td>
						
					</tr>
			<?php if (($model->sop == 1)): ?>

                <tr>
                    <th><?=$model->getAttributeLabel('emergency_contact')?></th>
                    <td><?=FormatterHelper::asPhone($model->emergency_contact)?></td>
                </tr>

                <tr>
                    <th><?=$model->getAttributeLabel('emergency_contact_name')?></th>
                    <td><?=$model->emergency_contact_name?></td>
                </tr>

                <tr>
                    <th><?=$model->getAttributeLabel('digital_signature')?></th>
                    <td><img src="<?=$model->digital_signature?>"/></td>
                </tr>
<tr>
                <label class="checkbox checkbox-inline  m-r-20">
                   <th> <input type="checkbox" value="0" id="user-agree" name="User[agree]" checked disabled></th>
                    <i class="input-helper"></i>
                   <td> <span style="color: #000000 !important;">I have read (or had read to me) and received Site Safety Orientation from WHITING � TURNER CONTRACTING COMPANY, and am aware of the project Hazards, Rules and Regulations. I fully understand them and agree to follow them. <br/>
Yo, he le�do (o me han le�do) y recib� la orientaci�n de seguridad del sitio de trabajo de WHITING-TURNER CONTRACTING COMPANY,  y estoy consiente de los peligros del proyecto, reglas y regulaciones. Las entiendo completamente y estoy dispuesto a seguirlas.</span></td></tr>
                </label>
 <?php endif?>

                </tbody>

            </table>

        </div>

    </div>
</div>
