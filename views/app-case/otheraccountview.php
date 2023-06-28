<?php

use app\models\CausationFactor;
use app\models\ReportType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\AppCase */

$this->title = strtoupper($model->appCaseType->type);
$this->params['breadcrumbs'][] = [
    'label' => 'Other Account Issues Cases',
    'url' => ['other-account-issues']
];
$this->params['breadcrumbs'][] = $this->title;

if ($model->app_case_type_id == APP_CASE_INCIDENT) {
    $listData = ['1' => 'YES', '0' => 'NO'];
    $data_causation_factor = ArrayHelper::map(app\models\CausationFactor::find()->where(["is_active" => 1])->orderBy('causation_factor')->asArray()->all(), 'id', 'causation_factor');
    $data_report_type = ArrayHelper::map(app\models\ReportType::find()->where(["is_active" => 1])->orderBy('report_type')->asArray()->all(), 'id', 'report_type');
}
?>

<div class="app-case-view">

    <?=
    Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ])
    ?>

    <div class="block-header">
        <h2>Issue ID: <?= $model->id ?></h2>
        <ul class="actions">
            <li>
               
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

        <div class="card-header" style="position:relative;">
            <h2 class="p-b-0 p-l-0"><?= strtoupper($model->appCaseType->type); ?>
                <small></small>
            </h2>
            <?= Html::img('@web/img/IssueType-' . $model->app_case_type_id . '.png', ['style' => 'position:absolute; width:60px; right:26px; top:23px;']) ?>
        </div>

        <div class="card-body table-responsive" tabindex="0" style="overflow: hidden; outline: none;">

            <table class="table">

                <tbody>

                    <tr>
                        <th><?= $model->getAttributeLabel('is_active') ?></th>
                        <td><?= ( $model->is_active ) ? Html::tag('i', '', ['class' => 'md md-check is-active']) : Html::tag('i', '', ['class' => 'md md-close is-active']) ?></td>
                    </tr>

                    <?php if ($model->app_case_type_id == APP_CASE_INCIDENT): ?>

                        <tr>
                            <th><?= $model_type->getAttributeLabel('incident_datetime') ?></th>
                            <td><?php
                                echo date("M d, Y - h:i:s A", strtotime($model_type->incident_datetime));
                                if (!is_null($model->jobsite->timezone_id)) {
                                    echo " (" . $model->jobsite->timezone->timezone . ")";
                                }
                                ?></td>
                        </tr>


                    <?php endif; ?>
                    <?php if ($model->app_case_type_id == APP_CASE_VIOLATION || $model->app_case_type_id == APP_CASE_RECOGNITION): ?>
                        <tr>
                            <th><?= $model_type->getAttributeLabel('correction_date') ?></th>
                            <td><?php
                                echo date("M d, Y", strtotime($model_type->correction_date));
                                if (!is_null($model->jobsite->timezone_id)) {
//                                echo " (" . $model->jobsite->timezone->timezone . ")";
                                }
                                ?></td>
                        </tr>

                    <?php endif; ?>

                    <?php if ($model->affectedUser): ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('affected_user_id') ?></th>
                            <td><?= $model->affectedUser->employee_number . ' - ' . $model->affectedUser->first_name . ' ' . $model->affectedUser->last_name ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <th><?= $model->getAttributeLabel('jobsite_id') ?></th>
                        <td><?= $model->jobsite->jobsite ?></td>
                    </tr>

                    <?php if ($model->subJobsite): ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('sub_jobsite_id') ?></th>
                            <td><?= $model->subJobsite->subjobsite ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <th><?= $model->getAttributeLabel('building_id') ?></th>
                        <td><?= $model->building->building ?></td>
                    </tr>
                    <?php
                    if (!empty($model->floor)):
                        ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('floor_id') ?></th>
                            <td><?= $model->floor->floor ?></td>
                        </tr>
                        <?php
                    endif;
                    ?>

                    <?php
                    if (!empty($model->area)):
                        ?>
                        <tr>
                            <th><?= $model->getAttributeLabel('area_id') ?></th>
                            <td><?= ucwords($model->area->area) ?></td>
                        </tr>
                        <?php
                    endif;
                    ?>

                    <tr>
                        <th><?= $model->getAttributeLabel('contractor_id') ?></th>
                        <td><?= ucwords($model->contractor->contractor) ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('app_case_priority_id') ?></th>
                        <td><?= ucwords($model->appCasePriority->priority) ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('app_case_sf_code_id') ?></th>
                        <td><?= $model->appCaseSfCode->code ?></td>
                    </tr>

                    <tr>
                        <th>Safety Code Description</th>
                        <td><?= $model->appCaseSfCode->description ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('trade_id') ?></th>
                        <td><?= ucwords($model->trade->trade) ?></td>
                    </tr>

                    <?php if ($model->app_case_type_id == APP_CASE_VIOLATION || $model->app_case_type_id == APP_CASE_RECOGNITION): ?>
                        <?php if ($model_type->foreman): ?>

                            <tr>
                                <th><?= $model_type->getAttributeLabel('foreman_id') ?></th>
                                <td><?= $model_type->foreman->employee_number . ' - ' . $model_type->foreman->first_name . ' ' . $model_type->foreman->last_name ?></td>
                            </tr>

                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($model->app_case_type_id == APP_CASE_OBSERVATION): ?>
                        <?php if ($model_type->foreman): ?>

                            <tr>
                                <th><?= $model_type->getAttributeLabel('foreman_id') ?></th>
                                <td><?= $model_type->foreman->employee_number . ' - ' . $model_type->foreman->first_name . ' ' . $model_type->foreman->last_name ?></td>
                            </tr>

                        <?php endif; ?>

                        <tr>
                            <th><?= $model_type->getAttributeLabel('coaching_provider') ?></th>
                            <td><?= $model_type->coaching_provider ?></td>
                        </tr>

                       <tr>
                            <th>Description</th>
                            <td><?= $model->additional_information ?></td>
                        </tr>

                    <?php endif ?>

                    <?php if ($model->app_case_type_id == APP_CASE_INCIDENT): ?>

                        <tr>
                            <th><?= $model_type->getAttributeLabel('report_type_id') ?></th>
                            <td><?= $model_type->reportType->report_type ?></td>
                        </tr>



                        <tr>
                            <th><?= $model_type->getAttributeLabel('report_topic_id') ?></th>
                            <td><?= $model_type->reportTopic->report_topic ?></td>
                        </tr>

                        <?php
                        if (isset($model_type->lost_time)) {
                            ?>
                            <tr>
                                <th ><?= $model_type->getAttributeLabel('lost_time') ?></th>
                                <td><?= $model_type->lost_time ?></td>
                            </tr>
                            <?php
                        }
                        if (isset($model_type->bodyPart)) {
                            ?>
                            <tr>
                                <th><?= $model_type->getAttributeLabel('body_part_id') ?></th>
                                <td><?= $model_type->bodyPart->body_part ?></td>
                            </tr>
                            <?php
                        }
                        if (isset($model_type->injuryType)) {
                            ?>

                            <tr>
                                <th><?= $model_type->getAttributeLabel('injury_type_id') ?></th>
                                <td><?= $model_type->injuryType->injury_type ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    <?php endif; ?>



                    <?php if ($model->app_case_type_id != APP_CASE_OBSERVATION): ?>
                    <tr>
                        <th><?= $model->getAttributeLabel('additional_information') ?></th>
                        <td><?= $model->additional_information ?></td>
                    </tr>

                    <?php endif ?>

                 

                    <tr>
                        <th><?= $model->getAttributeLabel('app_case_status_id') ?></th>
                        <td>
                            <?= ucwords($model->appCaseStatus->status) ?>
                            <?= Html::img('@web/img/IssueState-' . $model->app_case_status_id . '.png', ['style' => 'width:15px; margin-left: 10px;']) ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Created by</th>
                        <td><?= $model->creator->employee_number . ' - ' . $model->creator->first_name . ' ' . $model->creator->last_name ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('created') ?></th>
                        <td><?= date("M d, Y - h:i:s A", strtotime($created)) . " (" . $model->jobsite->timezone->timezone . ")" ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('updated') ?></th>
                        <td><?= date("M d, Y - h:i:s A", strtotime($model->updated)) . " (" . $model->jobsite->timezone->timezone . ")" ?></td>
                    </tr>

                    <tr>
                        <th><?= $model->getAttributeLabel('is_attachment') ?></th>
                        <td><?= ( $model->is_attachment ) ? Html::tag('i', '', ['class' => 'md md-check is-active']) : Html::tag('i', '', ['class' => 'md md-close is-active']) ?></td>
                    </tr>

                </tbody>

            </table>

        </div>
    </div>





</div>
<script>
   

   

</script>
