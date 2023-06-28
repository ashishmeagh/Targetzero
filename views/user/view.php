<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use yii\widgets\Breadcrumbs;
    use yii\grid\GridView;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use app\components\FormatterHelper;

    /* @var $this yii\web\View */
    /* @var $model app\models\User */


    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->orderBy("jobsite")->asArray()->all(), 'id', 'jobsite' );
    $user_jobsites_selected = ArrayHelper::map( app\models\UserJobsite::find()->where( [ "user_id" => $model->id ] )->asArray()->all(), 'jobsite_id', 'user_id' );
   $loggedInUserRole = Yii::$app->session->get('user.role_id'); 

    $this->title = $model->first_name . " " . $model->last_name . " profile";
    $this->params[ 'breadcrumbs' ][ ] = [
        'label' => 'Users',
        'url'   => [ 'index' ]
    ];
    $this->params[ 'breadcrumbs' ][ ] = $model->first_name . " " . $model->last_name;
 ?>
<div class="user-view">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>


<?php if (($model->role_id == ROLE_SYSTEM_ADMIN) && ($loggedInUserRole == ROLE_ADMIN)): ?>
    <div class="block-header">
        <h2>User data</h2>
        <ul class="actions">
            <li>
              
            </li>
        </ul>
    </div>

    <?php else: ?>

       <div class="block-header">
        <h2>User data</h2>
        <ul class="actions">
            <li>
                <?php if ($model->role_id == 19): ?>
                        <?= Html::a( '<i class="md md-mode-edit"></i>', [
                    'updatecraftmen',
                    'id' => $model->id
                ] ) ?>
                <?php else: ?>

                <?= Html::a( '<i class="md md-mode-edit"></i>', [
                    'update',
                    'id' => $model->id
                ] ) ?>
                <?php endif; ?>
            </li>
        </ul>

         <?php if (Yii::$app->session->hasFlash('success')): ?>
       <div class="alert alert-success alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
             <strong style="font-size: 15px;"> <?= Yii::$app->session->getFlash('success') ?></strong>
            
        </div>
    <?php endif; ?>
    </div>

    <?php endif ?>
    <!--        --><?php //echo Html::a('Delete', ['delete', 'id' => $model->id], [
        //            'class' => 'btn btn-danger',
        //            'data' => [
        //                'confirm' => 'Are you sure you want to delete this item?',
        //                'method' => 'post',
        //            ],
        //        ]) ?>


    <div class="card">
        <h2 class="p-b-0"><?= $model->first_name . " " . $model->last_name ?></h2>

        <div class="card-body table-responsive card-padding" tabindex="0" style="overflow: hidden; outline: none;">

            <table class="table">

                <tbody>

                <tr>
                    <th><?= $model->getAttributeLabel( 'is_active' ) ?></th>
                    <td><?= ( $model->is_active ) ? Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] ) : Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] ) ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'employee_number' ) ?></th>
                    <td><?= $model->employee_number ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'created' ) ?></th>
                    <td><?= date( "M d, Y - h:i:s A", strtotime( $model->created ) ) . " (CST)"  ?></td>

                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'updated' ) ?></th>
                    <td><?= date( "M d, Y - h:i:s A", strtotime( $model->updated ) ) . " (CST)"  ?></td>

                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'contractor_id' ) ?></th>
                    <td><?= $model->contractor->contractor ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'role_id' ) ?></th>
                    <td><?= $model->role->role ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'user_name' ) ?></th>
                    <td><?= $model->user_name ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'email' ) ?></th>
                    <td><?= $model->email ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'phone' ) ?></th>
                    <td><?= FormatterHelper::asPhone(($model->phone ?? "")) ?></td>
                </tr>

                <tr>
                    <th>Assigned jobsites</th>
                    <td><?php
                        if(sizeof($user_jobsites_selected)>0){
                            $jobsitesArray = array();
                            foreach ($data_jobsite as $key => $value){
                                if (isset($user_jobsites_selected[$key])){
                                    $jobsitesArray[] = Html::a($value, ['/jobsite/update?id=' . $key]);
                                }
                            }
                            echo implode(", ", $jobsitesArray);
                        }else{
                            echo "No jobsites assigned";
                        }
                        ?></td>
                </tr>
<?php if (($model->sop == 1) ||($model->role_id == 19) ): ?>

                <tr>
                    <th><?= $model->getAttributeLabel( 'emergency_contact' ) ?></th>
                    <td><?= FormatterHelper::asPhone($model->emergency_contact) ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'emergency_contact_name' ) ?></th>
                    <td><?= $model->emergency_contact_name ?></td>
                </tr>
<?php if (($model->sop == 1) ||($model->role_id != 19) ): ?>
                <tr>
                    <th><?= $model->getAttributeLabel( 'digital_signature' ) ?></th>
                    <td><img src="<?= $model->digital_signature ?>"/></td>
                </tr>

                <label class="checkbox checkbox-inline  m-r-20">
                   <th> <input type="checkbox" value="0" id="user-agree" name="User[agree]" checked disabled></th>
                    <i class="input-helper"></i>
                   <td> <span style="color: #000000 !important;">I have read (or had read to me) and received Site Safety Orientation from WHITING-TURNER CONTRACTING COMPANY, and am aware of the project Hazards, Rules and Regulations. I fully understand them and agree to follow them. <br/>
Yo, he le&iacute;do (o me han le&iacute;do) y recib&iacute; la orientaci&oacute;n de seguridad del sitio de trabajo de WHITING-TURNER CONTRACTING COMPANY,  y estoy consiente de los peligros del proyecto, reglas y regulaciones. Las entiendo completamente y estoy dispuesto a seguirlas.</span></td>
                </label>
                <?php endif ?>
 <?php endif ?>
                </tbody>

            </table>

        </div>

    </div>

    <?php if( Yii::$app->session->get('user.role_id') != ROLE_ADMIN || Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN ): ?>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <h2 class="p-b-0">Last changes</h2>
                <div id="user-jobsite" class="extra-data">
                    <div class="block-header p-0 m-0">
                        <h2 class="p-0 m-0"></h2>
                        <?php if($changes){
                            echo "<ul class='lastChanges'>";
                            foreach ( $changes as $change ){
                                echo "<li class='multiline'>";
                                echo "<div class='row'>";
                                echo "<div class='col-xs-6'>" . Html::a($change['userData']['first_name'] . " " . $change['userData']['last_name'], ['/user/view?id=' . $change['userData']['id']]) . "</div><div class='col-xs-6'>" . date( "M d, Y - h:i:s A", strtotime( $change['timestamp'] ) ) . " (CST)</div>";
                                echo "<div class='col-xs-6'>" . ucfirst($change['field_name']) . ": <span class='tachar'>" . $change['before_state'] . "</span> <i class='md md-redo'></i> " . $change['after_state'] . "</div>";
                                echo "</div>";
                                echo "</li>";
                                echo "<hr/>";
                            }
                            echo "</ul>";
                        }else{
                            echo "<ul>";
                            echo "<li class='m-t-15'>-No changes until the date.</li>";
                            echo "</ul>";
                        } ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <h2 class="p-b-0">Login</h2>
                <div id="user-jobsite" class="extra-data">
                    <div class="block-header p-0 m-0">
                        <h2 class="p-0 m-0"></h2>
                            <?php if($activities){

                                echo "<ul class='lastChanges'>";
                                foreach ( $activities as $activity ){
                                    switch(strtoupper($activity['device'])){
                                        case "IOS":
                                            $activity['device'] = "iOS";
                                            break;
                                        case "ANDROID":
                                            $activity['device'] = "Android";
                                            break;
                                        case "DESKTOP":
                                            $activity['device'] = "Desktop";
                                            break;

                                    }

                                    echo "<li class='multiline'>";
                                    echo "<div class='row'>";
                                    echo "<div class='col-xs-6'>From " . $activity['device'] . "</div><div class='col-xs-6'>" . date( "M d, Y - h:i:s A", strtotime( $activity['timestamp'] ) ) . " (CST)</div>";
                                    echo "</div>";
                                    echo "</li>";
                                    echo "<hr/>";
                                }
                                echo "</ul>";
                            }else{
                                echo "<ul>";
                                echo "<li class='m-t-15'>-No login until the date.</li>";
                                echo "</ul>";
                            } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php endif; ?>
</div>
