<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use yii\widgets\Breadcrumbs;
    use yii\grid\GridView;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use app\helpers\security;

    /* @var $this yii\web\View */
    /* @var $model app\models\User */


    // $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->orderBy("jobsite")->asArray()->all(), 'id', 'jobsite' );
    // $user_jobsites_selected = ArrayHelper::map( app\models\UserJobsite::find()->where( [ "user_id" => $model->id ] )->asArray()->all(), 'jobsite_id', 'user_id' );
    $time_zone = app\models\Timezone::find()->where([ "id" => $model->timezone_id ])->asArray()->one();
    $data_role = security::getAvailableRoles();
    $this->title = $model->jobsite;
    $this->params[ 'breadcrumbs' ][ ] = [
        'label' => 'Jobsite',
        'url'   => [ 'index' ]
    ];
    $this->params[ 'breadcrumbs' ][ ] = $model->jobsite;
?>
<style>
.create-new-contractor-user{
    position: relative;
    top: 5px;
}
</style>
<div class="modal fade" id="create-new-contractor-user" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">


            <div class="modal-header">
                <h4 class="p-b-0 p-l-0 modal-title"><?= strtoupper( "Add ".$model->jobsite." user" ); ?></h4>
            </div>

            <div class="modal-body">
                <?php \yii\widgets\Pjax::begin(); ?>
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field( $userModel, 'role_id' )->dropDownList( $data_role, [ 'prompt' => '-Choose a Role-' ] ) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field( $userModel, 'employee_number' )->textInput( [ 'maxlength' => 70 ] ) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field( $userModel, 'first_name' )->textInput( [ 'maxlength' => 70 ] ) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field( $userModel, 'last_name' )->textInput( [ 'maxlength' => 70 ] ) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field( $userModel, 'contractor_id' )->dropDownList( $dataContractor, [ 'prompt' => '-Choose a Contractor-' ] ) ?>
                        <input type='hidden' id="jobsite" name="User[jobsites][]" value='<?= $model->id;?>'>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-left m-t-10">
                            <label class="radio radio-inline m-r-20">
                                <input type="radio" value="1"
                                       name="User[is_active]" <?= ( $model->is_active == 1 ) ? "checked" : "" ?> >
                                <i class="input-helper"></i>
                                Active
                            </label>
                            <label class="radio radio-inline m-r-20">
                                <input type="radio" value="0"
                                       name="User[is_active]" <?= ( $model->is_active == 0 ) ? "checked" : "" ?> >
                                <i class="input-helper"></i>
                                Inactive
                            </label>
                        </div>
                        <?= Html::submitButton( 'Create', [ 'class' => 'btn btn-primary pull-right' ] ) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>


        </div>
    </div>

</div>

<div class="user-view">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <h2>Jobsite data</h2>
        <ul class="actions">
            <li>
                <?= Html::a( '<i class="md md-mode-edit"></i>', [
                    'update',
                    'id' => $model->id
                ] ) ?>
                <?= Html::a('<i class="md md-close"></i>', ['index']) ?>
            </li>
        </ul>
    </div>

    <div class="card">
        <h2 class="p-b-0"><?= $model->jobsite?></h2>

        <div class="card-body table-responsive card-padding" tabindex="0" style="overflow: hidden; outline: none;">

            <table class="table">

                <tbody>

                <tr>
                    <th><?= $model->getAttributeLabel( 'jobsite' ) ?></th>
                    <td><?= $model->jobsite ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'timezone_id' ) ?></th>
                    <td><?= date( "M d, Y - h:i:s A", strtotime( $model->created ) ) . " (CST)"  ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'job_number' ) ?></th>
                    <td><?= $model->job_number ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'address' ) ?></th>
                    <td><?= $model->address ?></td>
                </tr>
                 <tr>
                    <th><?= $model->getAttributeLabel( 'city' ) ?></th>
                    <td><?= $model->city ?></td>
                </tr>
                 <tr>
                    <th><?= $model->getAttributeLabel( 'state' ) ?></th>
                    <td><?= $model->state ?></td>
                </tr>
                 <tr>
                    <th><?= $model->getAttributeLabel( 'zip_code' ) ?></th>
                    <td><?= $model->zip_code ?></td>
                </tr>
<!--                  <tr>
                    <th><?= $model->getAttributeLabel( 'exec_vp' ) ?></th>
                    <td><?= $model->exec_vp ?></td>
                </tr>
                 <tr>
                    <th><?= $model->getAttributeLabel( 'sr_vp' ) ?></th>
                    <td><?= $model->sr_vp ?></td>
                </tr>                
                 <tr>
                    <th><?= $model->getAttributeLabel( 'wt_group' ) ?></th>
                    <td><?= $model->wt_group ?></td>
                </tr> -->
                <tr>
                    <th><?= $model->getAttributeLabel( 'photo_allowed' ) ?></th>
                    <td><?= ( $model->photo_allowed ) ? Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] ) : Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] ) ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'newsflash_allowed' ) ?></th>
                    <td><?= ( $model->newsflash_allowed ) ? Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] ) : Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] ) ?></td>
                </tr>
                <tr>
                    <th>Jobsite Administrator(s)</th>
                    <td><?php if(count( $jobAdmins ) > 0){
                        for($i=0; count( $jobAdmins ) > $i; $i++){
                        echo $jobAdmins[$i]["first_name"]." ".$jobAdmins[$i]["last_name"]."<br/>";
                    }
                    }else{
                            echo "N/A";
                    } 
                    ?></td>
                </tr>
                </tbody>

            </table>
            <div class="card">
                <div class="card-body table-responsive card-padding " tabindex="0" style="overflow: hidden; outline: none;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="block-header p-l-0 m-b-10">
                                <h2 class="p-0"><?= strtoupper( "Jobsite users" ); ?></h2>
                            </div>

                            <?php \yii\widgets\Pjax::begin(); ?>

                            <?php $form = ActiveForm::begin( [
                                'method' => 'get'
                            ] ); ?>
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-7 text-right ">
                                    <?= $form->field( $searchModel, 'all_search' )->textInput( [ 'placeholder' => 'Find User ...'] )->label( FALSE ) ?>
                                </div>
                                <div class="col-sm-1 p-l-0 text-right pull-right">
                                    <?= Html::submitButton( '<i class="md md-search"></i>', [
                                        'class' => 'btn btn-primary waves-effect',
                                        'style' => 'margin-top: 4px;'
                                    ] ) ?>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                            <a href="/user/create" class="btn btn-primary btn-default waves-effect waves-button pull-right create-new-contractor-user">Add user</a>
                            <?= GridView::widget( [
                                'dataProvider' => $dataProvider,
                                'tableOptions' => [ 'class' => 'table' ],
                                'columns'      => [
                                    [
                                        'attribute'      => 'is_active',
                                        'format'         => 'html',
                                        'value'          => function ( $data )
                                        {
                                            if ( $data->is_active == TRUE )
                                            {
                                                return Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] );
                                            }
                                            else
                                            {
                                                return Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] );
                                            }
                                        },
                                        'contentOptions' => [ 'class' => 'active-column' ],
                                        'filter'         => array(
                                            '1' => 'Active',
                                            '0' => 'Inactive'
                                        ),
                                    ],
                                    [
                                        'attribute'      => 'employee_number',
                                        'label'          => 'Emp. ID',
                                        'contentOptions' => [ 'class' => 'contractor_employee_number' ],
                                    ],
                                    [
                                        'attribute' => 'first_name',
                                        'format'    => 'html',
                                        'value'     => function ( $data )
                                        {
                                            return Html::a( $data->first_name . ' ' . $data->last_name, [ '/user/update?id=' . $data->id ] );
                                        },
                                        'label'     => 'Name',
                                    ],
                                    [
                                        'attribute' => 'contractor_id',
                                        'value'     => 'contractor.contractor',
                                        'label'     => 'Contractor',
                                    ],
                                    [
                                        'attribute' => 'role_id',
                                        'value'     => 'role.role',
                                        'label'     => 'Role',
                                    ]
                                ],
                            ] ); ?>

                            
                            <?php \yii\widgets\Pjax::end(); ?>
                        </div>


                    </div>
                </div>
            </div>

    </div>

</div>
