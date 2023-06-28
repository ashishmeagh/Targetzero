<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use yii\widgets\Breadcrumbs;
    use yii\grid\GridView;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;

    /* @var $this yii\web\View */
    /* @var $model app\models\User */


    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->orderBy("jobsite")->asArray()->all(), 'id', 'jobsite' );
    $user_jobsites_selected = ArrayHelper::map( app\models\UserJobsite::find()->where( [ "user_id" => $model->id ] )->asArray()->all(), 'jobsite_id', 'user_id' );


    $this->title = $model->first_name . " " . $model->last_name . " profile";
    $this->params[ 'breadcrumbs' ][ ] = [
        'label' => 'Users',
        'url'   => [ 'index' ]
    ]; 
    $this->params[ 'breadcrumbs' ][ ] = "Delete user: ".$model->first_name . " " . $model->last_name;
?>
<!-- ADAPTAR ESTE MODAL PARA MOSTRAR MENSAJES-->
<div class="modal" id="message-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete user</h4>
            </div>
            <div id='modal-body' class="modal-body">
      				<div class="row">
      					<div id='modal-text' class="col-md-12"><p>User deleted successfully</p></div>
      				</div>
			      </div>
            <div id="btn-reassign-user-creator-container" class="modal-footer">
                <button type="button" class="btn btn-link" onclick="location.href='index'">Ok</button>
            </div>
        </div>
    </div>
</div>

<div class="user-view">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <ul class="actions">
            <li>
                <?= Html::a( '<i class="md md-close"></i>', ['index'] ) ?>
            </li>
        </ul>
      <?php
        if(!count($errors) && $authorized){
      ?>
        <div id="upperMessage" class="alert alert-success" role="alert">
          <h4 class="alert-heading">IT'S POSSIBLE TO PHYSICALLY DELETE USER</h4>
          Confirm user delete? &nbsp;&nbsp;&nbsp;&nbsp;
          <?php echo Html::a('Yes', null, ['onclick' => 'cascadeDelete('.$model->id.');', 'class'=>'btn btn-danger']). "&nbsp;&nbsp;&nbsp;&nbsp; ".
          Html::a('No', ['index'], ['class'=>'btn btn-success']) ?>
        </div>
      <?php
        } else {
      ?>
        <div class="alert btn-link" style="margin:0" role="alert">
          <div class="alert btn-danger" style="margin:0" role="alert">
            <h4><b>IT'S NOT POSSIBLE TO PHYSICALLY DELETE USER</b></h4>
          </div>
        </div>
        <div class="alert btn-link" style="margin:0; padding-top:0; padding-bottom:0" role="alert">
          <ul class='list-group'>
      <?php
          foreach($errors as $error){
      ?>
          <li class='list-group-item'><?php echo $error['message']." ".Html::a($error['id'], [$error['path'].$error['id']]);?></li>
      <?php
          }
      ?>
          </ul>
        </div>
      <?php
        };
      ?>
    </div>
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
                    <td><?= $model->phone ?></td>
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
<script>
  var cascadeDelete = function(user_id){
    $("#message-modal").on('hidden.bs.modal', function () {
      location.href='index';
    });
    executeAjax
    (
      "<?= Yii::$app->urlManager->createUrl('ajax/cascade-delete?id=') ?>"+user_id
    ).done(function(r){
      if(r){
        //notify( r.class, r.message);
        $("#modal-text").html(r.message);
        $("#message-modal").modal();
      }else{
        swal("Error!", "Something is wrong, please try again later.", "error");
      }
    }).fail(function(x){
      swal("Error!", "Something is wrong, please try again later.", "error");
    });
    return false;
  }
</script>
