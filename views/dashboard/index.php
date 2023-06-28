<?php
    /* @var $this yii\web\View */
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\ArrayHelper;
    use kartik\select2\Select2;

    // Type
    $type = ArrayHelper::map( app\models\AppCaseType::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'type' );
    // Jobsite
    $jobsite = ArrayHelper::map( app\models\Jobsite::find()->joinWith('userJobsites')->where([ "jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get( "user.id" ) ])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite' );
    // Status
    $status = ArrayHelper::map( app\models\AppCaseStatus::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'status' );
    // Contractor

    $contractor = \app\helpers\security::getContractors();
    
//    $contractor = ArrayHelper::map( app\models\Contractor::find()->where([ "is_active" => 1 ])->orderBy('contractor')->asArray()->all(), 'id', 'contractor' );
    // Trade
    $trade = ArrayHelper::map( app\models\Trade::find()->where([ "is_active" => 1 ])->orderBy('trade')->asArray()->all(), 'id', 'trade' );

    $createdBy = [];
    //\app\models\User::getUsersByJobisites(Yii::$app->session->get( "user.id" ));
    $affectedusers = [];
    //\app\models\User::getAffectedUsersByJobisites(Yii::$app->session->get( "user.id" ));

    //Incident related filters
    // Report type
    $report_type = ArrayHelper::map( app\models\ReportType::find()->where([ "is_active" => 1 ])->orderBy('report_type')->asArray()->all(), 'id', 'report_type' );
    // Report topic
    $report_topic = ArrayHelper::map( app\models\ReportTopic::find()->where([ "is_active" => 1 ])->orderBy('report_topic')->asArray()->all(), 'id', 'report_topic' );
    // Recordable
    $recordable = array(
        "0" => "No",
        "1" => "Yes"
    );
    // Injury type
    $injury_type = ArrayHelper::map( app\models\InjuryType::find()->where([ "is_active" => 1 ])->orderBy('injury_type')->asArray()->all(), 'id', 'injury_type' );
    // Body part
    $body_part = ArrayHelper::map( app\models\BodyPart::find()->where([ "is_active" => 1 ])->orderBy('body_part')->asArray()->all(), 'id', 'body_part' );
    // Lost time
    $lost_time = array(
        "0" => "No",
        "1" => "Yes"
    );
    $day_of_week = array(
        "2" => "Monday",
        "3" => "Tuesday",
        "4" => "Wednesday",
        "5" => "Thursday",
        "6" => "Friday",
        "7" => "Saturday",
        "1" => "Sunday",
    );

     $dart_days = array(
        "0" => "No",
        "1" => "Yes"
    );

    $lastMonth = date( 'F j, Y', strtotime( '-30 days' ) );
    $today = date( 'F j, Y' );

    $this->title = 'Dashboard';
    $this->params[ 'breadcrumbs' ][ ] = $this->title;
?>

<!-- Preload -->
<style type="text/css">
    .select2-container--krajee .select2-selection{
        border: none !important;
        border-bottom: 1px solid #ccc !important;
        webkit-box-shadow: none !important;
        box-shadow: none !important;
        border-radius: 0px  !important;
    }

    .loader {
      border: 10px solid #f3f3f3; /* Light grey */
      border-top: 10px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 2s linear infinite;
      text-align: center;
      margin: auto;
      padding-bottom: 15px;
    }

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
.circleloader {
    background: url(./img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;
}

</style>

<div class="preloader-backdrop"></div>
<div class="preloader">
    <div class="windows8">
        <div class="wBall" id="wBall_1">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_2">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_3">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_4">
            <div class="wInnerBall"></div>
        </div>
        <div class="wBall" id="wBall_5">
            <div class="wInnerBall"></div>
        </div>
    </div>
</div>
<!-- Preload -->

<!-- Graph Modal -->
<div class="modal" id="graph-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Issues over time</h4>
            </div>
            <div class="modal-body" id="curve_chart_modal_container">
                <div id="curve_chart_modal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Graph Modal-->
<!-- Issues Modal 
<div class="modal" id="issues-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Issues over time</h4>
            </div>
            <div class="modal-body" id="issues_modal_container">
                <div id="issues_modal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>-->
<!-- Issues Modal-->

<div class="dashboard-index">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode( $this->title ) ?>
        </h2>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="p-0">Filters</h2>
                </div>
                <div class="card-body p-l-10 p-r-10">
                    <div class="row">
                        <div class="col-sm-12">


                            <!--issue type-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Type:
                                </p>
                                <div class="select">
                                    <select id="type-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true'>
                                        <option value="all">-All-</option>
                                        <?php
                                            foreach ( $type as $key => $value )
                                            {
                                                echo "<option value='" . $key . "'>" . strtoupper($value) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!--from date-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    From:
                                </p>
                                <div class="input-group form-group m-b-10">
                                    <span class="input-group-addon"><i class="md md-event"></i></span>

                                    <div class="dtp-container fg-line open">
                                        <input id="from-date" type="text" class="form-control dashboard-date-picker"
                                               data-toggle="dropdown" placeholder="<?= $lastMonth ?>" aria-expanded="true"
                                               value="<?= $lastMonth ?>">
                                    </div>
                                </div>
                            </div>
                            <!--to date-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    To:
                                </p>

                                <div class="input-group form-group m-b-10">
                                    <span class="input-group-addon"><i class="md md-event"></i></span>

                                    <div class="dtp-container fg-line open">
                                        <input id="to-date" type="text" class="form-control dashboard-date-picker"
                                               data-toggle="dropdown" placeholder="<?= $today ?>" aria-expanded="true"
                                               value="<?= $today ?>">
                                    </div>
                                </div>
                            </div>
                            <!--scale-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-0">
                                    Scale:
                                </p>
                                <small class="m-b-10 block">Affects "issues over time" chart.</small>
                                <div class="radio m-b-15">
                                    <label><input type="radio" name="scale" value="day" checked/><i class="input-helper"></i>Days</label>
                                </div>
                                <div class="radio m-b-15">
                                    <label><input type="radio" name="scale" value="week"/><i class="input-helper"></i>Weeks</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <!--jobsite-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Jobsite:
                                </p>
                                <div class="select">
                                    <select id="jobsite-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true' onchange="jobsiteIdChange(this.value)">
                                        <option value="all">-All-</option>
                                        <?php
                                            foreach ( $jobsite as $key => $value )
                                            {
                                                echo "<option value='" . $key . "'>" . $value . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!--subjobsite-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Sub jobsite:
                                </p>
                                <div class="select">
                                    <select id="subjobsite-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true'>
                                        <option value="all">-All-</option>
                                    </select>
                                </div>
                            </div>
                            <!--building-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Building:
                                </p>
                                <div class="select">
                                    <select id="building-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true' onchange="buildingIdChange(this.value)">
                                        <option value="all">-All-</option>
                                    </select>
                                </div>
                            </div>
                            <!--status-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Status:
                                </p>
                                <div class="select">
                                    <select id="status-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true'>
                                        <option value="all">-All-</option>
                                        <?php
                                            foreach ( $status as $key => $value )
                                            {
                                                echo "<option value='" . $key . "'>" . strtoupper($value) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <!--contractor-->
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Contractor:
                                </p>
                                <div class="select">
                                    <select id="contractor-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true'>
                                        <option value="all">-All-</option>
                                        <?php
                                            if(isset($contractor)){
                                                foreach ( $contractor as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Trade:
                                </p>
                                <div class="select m-b-15">
                                    <select id="trade-filter" class="form-control" class='selectpicker' data-live-search='true'
                                            data-show-subtext='true'>
                                        <option value="all">-All-</option>
                                        <?php
                                            foreach ( $trade as $key => $value )
                                            {
                                                echo "<option value='" . $key . "'>" . $value . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Affected Employee:
                                </p>
                              <div class="select m-b-15">
                                   <input type="text" class="form-control selectpicker" placeholder="Start typing the person name" name="autoaffectedby-filter" id="autoaffectedby-filter"/>
                                  <input type="hidden" name="affectedby-filter" id="affectedby-filter"/>
                                   <input type="hidden" name="selectedjobsite" id="selectedjobsite"/>                               
                                </div>

                            </div>
                             <div class="col-sm-3">
                                <p class="c-black f-500 m-b-10">
                                    Created by:
                                </p>
                                <div class="select m-b-15">
                                        <input type="text" class="form-control selectpicker" placeholder="Start typing the person name" name="autocreatedby-filter" id="autocreatedby-filter"/>
                                          <input type="hidden" name="createdby-filter" id="createdby-filter"/>
                                  
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="incident-related-filters">
                    <div class="card-header">
                        <h2 class="p-0">Incident related filters</h2>
                    </div>
                    <div class="card-body p-l-10 p-r-10">
                        <!--------------------------->
                        <!--incident related fields-->
                        <!--------------------------->
                        <div class="row">
                            <div class="col-sm-12">
                                <!--floor-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Floor:
                                    </p>
                                    <div class="select">
                                        <select id="floor-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true' onchange="floorIdChange(this.value)">
                                            <option value="all">-All-</option>
                                        </select>
                                    </div>
                                </div>
                                <!--area-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Area:
                                    </p>
                                    <div class="select">
                                        <select id="area-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                        </select>
                                    </div>
                                </div>
                                <!--report type-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Report type:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="report-type-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $report_type as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--report topic-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Report topic:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="report-topic-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $report_topic as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <!--recordable incidents-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Recordable incidents:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="recordable-incidents-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $recordable as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--injury type-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Injury type:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="injury-type-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $injury_type as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--body part-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Body part:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="body-part-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $body_part as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--lost time-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Lost time:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="lost-time-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $lost_time as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <!--day of week-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        Day of week:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="day-week-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $day_of_week as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--day of week-->
                                <!--DART-->
                                <div class="col-sm-3">
                                    <p class="c-black f-500 m-b-10">
                                        DART:
                                    </p>
                                    <div class="select m-b-15">
                                        <select id="dart-filter" class="form-control" class='selectpicker' data-live-search='true'
                                                data-show-subtext='true'>
                                            <option value="all">-All-</option>
                                            <?php
                                                foreach ( $dart_days as $key => $value )
                                                {
                                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--DART-->
                                <div class="col-sm-6">
                                    <p class="c-black f-500 m-b-20">
                                        Time of day (hours):
                                    </p>
                                    <div class="input-slider-values"></div>
                                    <strong class="pull-left text-muted m-t-10" id="value-lower"></strong>
                                    <strong class="pull-right text-muted m-t-10" id="value-upper"></strong>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
               <div class="row" style="margin-top: 3%;">
               <?php if (in_array(Yii::$app->session->get('user.role_id'), $GLOBALS['wt_roles_access'])): ?>
                <?= Html::button( 'Download Issues', [ 'class' => 'btn btn-primary pull-right btn-filter', 'onclick'=>'showModalIssues();', 'style' => 'right: 100px' ]  ) ?>
                <?php endif; ?> 
                <?= Html::submitButton( 'Apply', [ 'class' => 'btn btn-primary pull-right btn-filter' ]  ) ?>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-sm-12 card-fluid">
            <div class="card">
                <div class="card-header">
                    <h2 class="p-0">Issues over time</h2>
                    <h3 id="issues-over-time-total"></h3>
                        <ul class="actions-basic">
                            <li>
                                <?php echo Html::a('<i class="md md-open-with"></i>', null, [
                                    'onclick'=>'showModalGraph();', 'title'=>'Expandir'
                                ] ) ?>
                                <?php echo Html::a('<i class="md md-description"></i>', null, [
                                    'onclick'=>'showModalIssues();', 'title'=>'Ver issues'
                                ] ) ?>
                            </li>
                        </ul>
                </div>
                <div class="card-body">
                     <div id="curve-loader" class="loader hidden"></div>
                    <div id="curve_chart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="p-0">Top 5 by contractor</h2>
                </div>
                <div class="card-body">
                    <div id="chart-loader" class="loader hidden"></div>
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="p-0">Top 5 by OSHA subpart</h2>
                </div>
                <div class="card-body">
                    <div id="pie-loader" class="loader hidden"></div>
                    <div id="piechart"></div>
                    <div id="legend-tooltip" class="ggl-tooltip hidden">
                          <div id="description" class="ggl-tooltiptext"></div>
                        </div> 
               </div>
        </div>
    </div>
</div>
 <!-- <div class="dashboard-discription"> 
           <div id ="piechart-description"></div>
    </div> -->
    
    
<script language="javascript" src="https://code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
<script type="text/javascript">

    var selectedjobsites = $("#jobsite-filter").val();   


    $(document).ready( function()
    {

        $( '.input-slider-values' ).noUiSlider ( {
            step: 1,
            connect:  true,
            direction:'ltr',
            behaviour:'tap-drag',
            start:    [0,24],
            range:    {
                'min':0,
                'max':24
            }
        });

        $('.input-slider-values').Link('lower').to($('#value-lower'));
        $('.input-slider-values').Link('upper').to($('#value-upper'), 'html');
    });


    google.load ( 'visualization', '1.1', { packages:[ 'corechart' ] } );

    google.setOnLoadCallback ( lineChart );
    google.setOnLoadCallback ( columnChart );
    google.setOnLoadCallback ( pieChart );
    $(document).resize(
        function(){
            google.setOnLoadCallback ( lineChart );
            google.setOnLoadCallback ( columnChart );
            google.setOnLoadCallback ( pieChart );
            return false;
        });

    function lineChart ( urlParams )
    {
        url = "<?= Yii::$app->urlManager->createUrl('ajax/line-chart') ?>";
        if( typeof urlParams === "string"){
            url += urlParams;
        }

        var json = $.ajax ( {
            url:     url,
            type:    "POST",
            dataType:"json",
            async:   false
        } ).responseText;

        var data = new google.visualization.DataTable ( json );
        $('#curve-loader').addClass('hidden');
        var data_array = JSON.parse(json ).rows;
        var total = 0;
        for(var i = 0; i<data_array.length ; i++){
            for( var j = 1; j<5; j++){
                if("c" in data_array[i]){
                    if(j in data_array[i]['c']){
                        total += data_array[i]['c'][j]['v'];
                    }
                }
            }
        }

        if (total == 0){
           var html = '<p style="text-align:  center; padding-bottom: 5%;">No data</p>';
           $("#curve_chart").empty().append(html);
           $("#issues-over-time-total").empty();
           $('.actions-basic').addClass('hidden');
          return; 
        }

        $('.actions-basic').removeClass('hidden');
        var issuesOverTimeTotal = document.getElementById("issues-over-time-total");
        issuesOverTimeTotal.innerHTML = "Total: " + total ;

        ////////////////////////////////
        // Grafico de index dashboard //
        ////////////////////////////////

        var options = {
            curveType:    'default',
            height:       350,
//            height:       '100%',
            pointSize:    5,
            animation:    {
                duration:600,
                easing:  'inAndOut',
                startup: true
            },
            crosshair:{
                trigger : 'focus',
                orientation: 'vertical',
                opacity: 0.3
            },
            legend:       { position:'bottom' },
            focusTarget: 'category',
            tooltip:      { trigger:'both' },
            selectionMode:'multiple',
            vAxis:        {
                format : '#',
                maxValue : 4,
                viewWindowMode : 'maximized',
                viewWindow:    { min:0 }
            },
            chartArea: {
                top: 10,
                height: '70%',
                width: '80%'
            },
            interpolateNulls: true,
            colors : ['#dc3912', '#109618', '#ff9900', '#3366cc']
        };
        var chart = new google.visualization.LineChart ( document.getElementById ( 'curve_chart' ) );

        chart.draw ( data, options );

        ////////////////////////////////////
        // Fin de grafico index dashboard //
        ////////////////////////////////////
        // Grafico de modal dashboard //
        ////////////////////////////////

        // Clono data para la ventana modal
        var data_modal = data;

        var chart_modal = new google.visualization.LineChart ( document.getElementById ( 'curve_chart_modal' ) );

        // create columns array
        var columns = [];

        // display these data series by default
        var defaultSeries = [1,2,3,4];
        var series = {};
        for (var i = 0; i < data_modal.getNumberOfColumns(); i++) {
            if (i == 0 || defaultSeries.indexOf(i) > -1) {
                // if the column is the domain column or in the default list, display the series
                columns.push(i);
            }
            else {
                // otherwise, hide it
                columns.push({
                    label: data_modal.getColumnLabel(i),
                    type: data_modal.getColumnType(i),
                    sourceColumn: i,
                    calc: function () {
                        return null;
                    }
                });
            }
            if (i > 0) {
//                columns.push({
//                    calc: 'stringify',
//                    sourceColumn: i,
//                    type: 'string',
//                    role: 'annotation'
//                });
                // set the default series option
                series[i - 1] = {};
                if (defaultSeries.indexOf(i) == -1) {
                    // backup the default color (if set)
                    if (typeof(series[i - 1].color) !== 'undefined') {
                        series[i - 1].backupColor = series[i - 1].color;
                    }
                    series[i - 1].color = '#CCCCCC';
                }
            }
        }

        var calculo_altura = $(window).height() - 250;

        var options_modal = {
            curveType:    'default',
            height:       calculo_altura,
            width:       '100%',
            pointSize:    5,
            crosshair:{
                trigger : 'focus',
                orientation: 'vertical',
                opacity: 0.3
            },
            legend:       { position:'right' },
            tooltip:      { trigger:'both' },
            selectionMode:'multiple',
            vAxis:        {
                format : '#',
                maxValue : 4,
                viewWindowMode : 'maximized',
                viewWindow:    { min:0 }
            },
            chartArea: {
                top: 10,
                height: '75%',
                width: '65%'
            },
            explorer: {
                axis: 'both',
                maxZoomOut:2,
                maxZoomIn:0.25,
                keepInBounds: true
            },
//            hAxis: {
//                slantedText: true,
//                slantedTextAngle: 30
//            },
            series: series,
            interpolateNulls: true,
            colors : ['#dc3912', '#109618', '#ff9900', '#3366cc']
        };

        function showHideSeries () {
            var sel = chart_modal.getSelection();
            // if selection length is 0, we deselected an element
            if (sel.length > 0) {
                // if row is undefined, we clicked on the legend
                if (sel[0].row == null) {
                    var col = sel[0].column;
                    if (typeof(columns[col]) == 'number') {
                        var src = columns[col];

                        // hide the data series
                        columns[col] = {
                            label: data_modal.getColumnLabel(src),
                            type: data_modal.getColumnType(src),
                            sourceColumn: src,
                            calc: function () {
                                return null;
                            }
                        };

                        // grey out the legend entry
                        series[src - 1].color = '#CCCCCC';
                    }
                    else {
                        var src = columns[col].sourceColumn;

                        // show the data series
                        columns[col] = src;
                        series[src - 1].color = null;
                    }

                    var view = new google.visualization.DataView(data_modal);
                    view.setColumns(columns);
                    chart_modal.draw(view, options_modal);
                }
            }
        }

        google.visualization.events.addListener(chart_modal, 'select', showHideSeries);

        // create a view with the default columns
        var view = new google.visualization.DataView(data_modal);
        view.setColumns(columns);
        chart_modal.draw(view, options_modal);

//        chart_modal.draw ( data, options_modal );

        return false;
    }
    function columnChart ( urlParams )
    {

        url = "<?= Yii::$app->urlManager->createUrl('ajax/top-contractors') ?>";
        if( typeof urlParams === "string"){
            url += urlParams;
        }
        var json = $.ajax ( {
            url:     url,
            dataType:"json",
            async:   false
        } ).responseText;
        var data = new google.visualization.DataTable ( json );
 
        $('#chart-loader').addClass('hidden');
        var data_array = JSON.parse(json ).rows;
        var total = 0;
        for(var i = 0; i<data_array.length ; i++){
            for( var j = 1; j<5; j++){
                if("c" in data_array[i]){
                    if(j in data_array[i]['c']){
                        total += data_array[i]['c'][j]['v'];
                    }
                }
            }
        }

        if (total == 0){
           var html = '<p style="text-align:  center; padding-bottom: 5%;">No data</p>';
           $("#chart_div").empty().append(html);
           return; 
        }

        var options = {
            height:       300,
            animation:    {
                duration:600,
                easing:  'inAndOut',
                startup: true
            },
            vAxis:        {
                format:'#',
                maxValue : 4,
                title:         "Issues",
                viewWindowMode:"explicit",
                viewWindow:    { min:0 },
            },
            hAxis:        { title:"Contractor" },
            seriesType:   "bars",
            series:       { 4:{ type:"line" } },
            isStacked:    true,
            tooltip:      { trigger:'both' },
            selectionMode:'multiple',
            legend:       {
                position:'top',
                maxLines:2
            },
            colors : ['#dc3912', '#109618', '#ff9900', '#3366cc']
        };
        var chart = new google.visualization.ColumnChart ( document.getElementById ( 'chart_div' ) );
        chart.draw ( data, options );

        return false;
    }
    
    function pieChart ( urlParams )
    {
        url = "<?= Yii::$app->urlManager->createUrl('ajax/pie-chart') ?>";
        if( typeof urlParams === "string"){
            url += urlParams;
        }

        var json = $.ajax ( {
            url:     url,
            dataType:"json",
            async:   false
        } ).responseText;
       
        $('#pie-loader').addClass('hidden');
        var data = new google.visualization.DataTable ( json );
        
        var data_array = JSON.parse(json ).rows;
        var total = 0;
        for(var i = 0; i<data_array.length ; i++){
             var val = data_array[i]['c'][1]['v'];
                        total += val;
        }

        if (total == 0){
           var html = '<p style="text-align:  center; padding-bottom: 5%;">No data</p>';
            $("#piechart").empty().append(html);
            $('.preloader-backdrop').fadeOut(500);
            $('.preloader').fadeOut(500);
           return; 
        }

        var options = {
            height:       300,
            animation:    {
                duration:600,
                startup: true
            },
         tooltip: { trigger: 'none' },
           selectionMode:'multiple',    
            chartArea: {
                top: 40,
                height: '60%',
                width: '75%'
            }
           // legend: 'none'
        };
        
        var chart = new google.visualization.PieChart ( document.getElementById ( 'piechart' ) ); 
        var legendTooltip = document.getElementById('legend-tooltip');  // show legend tooltip  start      
                     // show legend tooltip  start 
               // show legend tooltip  start   
         

 // /set legend tooltip position
  google.visualization.events.addListener(chart, 'ready', function (gglEvent) {
   // var chartLayout = chart.getChartLayoutInterface();
   // var legendBounds = chartLayout.getBoundingBox('legend');
    legendTooltip.style.top = (50) + 'px';
    legendTooltip.style.left = (10) + 'px';
    //legendTooltip.style.top = (legendBounds.top + (legendBounds.height * 2)) + 'px';
    //legendTooltip.style.left = legendBounds.left + 'px';
  });

  // show legend tooltip
  google.visualization.events.addListener(chart, 'onmouseover', function (gglEvent) {
    if (gglEvent.row !== null) {
        var colorForText;
        switch(gglEvent.row) {
    case 0:
        colorForText = "blue";
        break;
    case 1:
         colorForText = "red";
        break;
    case 2:
         colorForText = "orange";
        break;
    case 3:
         colorForText = "green";
        break;
    case 4:
         colorForText = "purple";
        break;
}
   var html ="<div>"
   var res = data.getValue(gglEvent.row,0).split("-");
    var percentage = Math.round(((data.getValue(gglEvent.row,1)/total)*100)* 10) / 10;
      html+= "<font color ="+colorForText+"> " +"<b>"+res[0]+"</b>"+" - "+res[1]+" - "+percentage+"% </font> <br />"+data.getValue(gglEvent.row,2)+"";      
     html+="</div>" 
    $('#description').html(html);
      // $('#description').html(data.getValue(gglEvent.row,2));
            $(legendTooltip).removeClass('hidden');
    }
  });

  // hide legend tooltip
  google.visualization.events.addListener(chart, 'onmouseout', function (gglEvent) {
      gglEvent.row = null;
    if (gglEvent.row === null) {
      $(legendTooltip).addClass('hidden');
    }
  });

   // show legend tooltip  ends     
         chart.draw ( data, options );
        $('.preloader-backdrop').fadeOut(500);
        $('.preloader').fadeOut(500);
//        $(".dashboard-index" ).fadeIn(500);
//showing tooltip for legends
   var html = "<ul>";
   if(data.Nf != undefined){
    for (var i = 0; i < data.Nf.length; i++) {
             var percentage = data.Nf[i].c[1].v/total;
             html+= "<li>"+"<b>"+data.Nf[i].c[0].v+"</b>"+" - "+data.Nf[i].c[3].v+"<br />"+data.Nf[i].c[2].v+" - "+percentage +"% </li>";      
    }
   }else  if(data.hg != undefined){
    for (var i = 0; i < data.hg.length; i++) {
             var percentage = data.hg[i].c[1].v/total;
             html+= "<li>"+"<b>"+data.hg[i].c[0].v+"</b>"+" - "+data.hg[i].c[3].v+"<br />"+data.hg[i].c[2].v+" - "+percentage +"% </li>";      
    }
   }else{

    Object.keys(data).forEach(function(key){
            var value = data[key];
            if(data[key] != null){
                   var length = data[key].length;
            if(length == 5 && key != 'cache')
              for (var i = 0; i < data[key].length; i++) {
             var percentage = data[key][i].c[1].v/total;
             html+= "<li>"+"<b>"+data[key][i].c[0].v+"</b>"+" - "+data[key][i].c[3].v+"<br />"+data[key][i].c[2].v+" - "+percentage +"% </li>";      
              }  
            }
         
        });
    
   }
   
     html+= "</ul>";
     
         $("#piechart-description").html(html);
        return false;
    }

    function issuesDetail ( urlParams ) {

        url = "<?= Yii::$app->urlManager->createUrl('ajax/issues-detail') ?>";
        if( typeof urlParams === "string"){
            url += urlParams;
        }

        var json = $.ajax ( {
            url:     url,
            dataType:"json",
            async:   false
        } ).responseText;

        var response = JSON.parse(json);

        //Armar encabezado de grilla de datos.
        var html = "<div id='w1' data-pjax-container='' data-pjax-push-state='' data-pjax-timeout='1000'>";
            html+= "  <div id='w2' class='grid-view'>";
            html+= "    <table id='table-app-case' class='table table-hover'>";
            html+= "      <thead>";
            html+= "        <tr>";
            html+= "          <th>Additional information</th>";
            html+= "          <th>Active</th>";
            html+= "          <th>Status</th>";
            html+= "          <th>Creator</th>";
            html+= "          <th>Contractor</th>";
            html+= "          <th>Jobsite</th>";
            html+= "          <th>Type</th>";
            html+= "          <th>Created</th>";
            html+= "        </tr>";
            html+= "      </thead>";

        //Armar filas de la grilla de datos
        for (var i = 0; i < response.length; i++) {
            //alert(response[i].additional_information);
            html+= "      <tbody>";
            html+= "        <tr>";
            html+= "          <td class='active-column'>"+response[i].additional_information+"</td>";
            html+= "          <td class='active-column'><i class='md md-check is-active'></i></td>";
            html+= "          <td class='active-column'>"+response[i].status+"</td>";
            html+= "          <td class='active-column'>"+response[i].creator+"</td>";
            html+= "          <td class='active-column'>"+response[i].contractor+"</td>";
            html+= "          <td class='active-column'>"+response[i].jobsite+"</td>";
            html+= "          <td class='active-column'>"+response[i].type+"</td>";
            html+= "          <td class='active-column'>"+response[i].created+"</td>";
            html+= "        </tr>";
            html+= "      </tbody>";
        }

        //Cierre de la grilla de datos
        html+= "    </table>";
        html+= "  </div>";
        html+= "</div>";

    $("#issues_modal_container").html(html);

            /*<div id="w1" data-pjax-container="" data-pjax-push-state="" data-pjax-timeout="1000">
            <div id="w2" class="grid-view">
<table id="table-app-case" class="table table-hover"><thead>
<tr><th>Active</th>*/


        $('.preloader-backdrop').fadeOut(500);
        $('.preloader').fadeOut(500);

        return false;
    }

    function makeUrl(params){
        var url = "?";
        var urlArray = new Array();
        for(var key in params){
            if (params.hasOwnProperty(key)) {
                urlArray.push(key + "=" +params[key]);
            }
        }
        url += urlArray.join("&");
        return url;
    }

    function filterGraphs ( options )
    {
        enableloading();
        var url = makeUrl(options);
        lineChart ( url );
        pieChart ( url );
        columnChart ( url );
        return false;
    }

    function filterIssues ( options ){
      var url = makeUrl(options);
      //issuesDetail ( url );
      downloadCsvIssues( url );
    }

    function downloadCsvIssues(urlParams){

        $('#curve-loader').addClass('hidden');
        $('#chart-loader').addClass('hidden');
        $('#pie-loader').addClass('hidden');
         url = "<?= Yii::$app->urlManager->createUrl('ajax/issues-detail') ?>";
        if( typeof urlParams === "string"){
            url += urlParams;
        }

        var json = $.ajax ( {
            url:     url,
            dataType:"json",
            async:   false
        } ).responseText;
               
        var data_array = JSON.parse(json);
        if(data_array.length > 0){
          exportToCsv('Issues.csv', data_array);
        } else{
            alert("No data found");
        }
    }

    function exportToCsv(filename, rows) {
        var csvFile = '';   

         var csvFile = convertArrayOfObjectsToCSV({
            data: rows
        });

        var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, filename);
        } else {
            var link = document.createElement("a");
            if (link.download !== undefined) { // feature detection
                // Browsers that support HTML5 download attribute
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }



    function convertArrayOfObjectsToCSV(args) {
        var result, ctr, keys, columnDelimiter, lineDelimiter, data;

        data = args.data || null;
        if (data == null || !data.length) {
            return null;
        }

        columnDelimiter = args.columnDelimiter || ',';
        lineDelimiter = args.lineDelimiter || '\n';

        keys = Object.keys(data[0]);

        result = '';
        result += keys.join(columnDelimiter);
        result += lineDelimiter;

        data.forEach(function(item) {
            ctr = 0;
            keys.forEach(function(key) {
                if (ctr > 0) result += columnDelimiter;
                var pardata = item[key].replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, ''); 
                result += "\"" + pardata.replace(/"/g, '\\"') + "\"";
                //result += item[key];
                ctr++;
            });
            result += lineDelimiter;
        });

        return result;
    }

    var showModalGraph = function(){

        $("#graph-modal").modal();

        var options = [];
        options["type"] = $("#type-filter").val();
        options["from"] = $("#from-date").val();
        options["to"] = $("#to-date").val();
        options["scale"] = $("input[type='radio'][name='scale']:checked").val();
        options["jobsite"] = $("#jobsite-filter").val();
        options["subjobsite"] = $("#subjobsite-filter").val();
        options["building"] = $("#building-filter").val();
        options["status"] = $("#status-filter").val();
        options["contractor"] = $("#contractor-filter").val();
        options["trade"] = $("#trade-filter").val();
        options["area"] = $("#area-filter").val();
        options["floor"] = $("#floor-filter").val();
        options["reportType"] = $("#report-type-filter").val();
        options["reportTopic"] = $("#report-topic-filter").val();
        options["recordable"] = $("#recordable-incidents-filter").val();
        options["injuryType"] = $("#injury-type-filter").val();
        options["bodyPart"] = $("#body-part-filter").val();
        options["lostTime"] = $("#lost-time-filter").val();
        options["is_dart"] = $("#dart-filter").val();
        options["dayWeek"] = $("#day-week-filter").val();
        options["timeDayFrom"] = $("#value-lower").text();
        options["timeDayTo"] = $("#value-upper").text();
        options["createdby"] = $("#createdby-filter").val();
        options["affectedby"] = $("#affectedby-filter").val();
        filterGraphs(options);

        return false;
    }

    var showModalIssues = function(){

        $("#issues-modal").modal();

        var options = [];
        options["type"] = $("#type-filter").val();
        options["from"] = $("#from-date").val();
        options["to"] = $("#to-date").val();
        options["scale"] = $("input[type='radio'][name='scale']:checked").val();
        options["jobsite"] = $("#jobsite-filter").val();
        options["subjobsite"] = $("#subjobsite-filter").val();
        options["building"] = $("#building-filter").val();
        options["status"] = $("#status-filter").val();
        options["contractor"] = $("#contractor-filter").val();
        options["trade"] = $("#trade-filter").val();
        options["area"] = $("#area-filter").val();
        options["floor"] = $("#floor-filter").val();
        options["reportType"] = $("#report-type-filter").val();
        options["reportTopic"] = $("#report-topic-filter").val();
        options["recordable"] = $("#recordable-incidents-filter").val();
        options["injuryType"] = $("#injury-type-filter").val();
        options["bodyPart"] = $("#body-part-filter").val();
        options["lostTime"] = $("#lost-time-filter").val();
        options["is_dart"] = $("#dart-filter").val();
        options["dayWeek"] = $("#day-week-filter").val();
        options["timeDayFrom"] = $("#value-lower").text();
        options["timeDayTo"] = $("#value-upper").text();
        options["createdby"] = $("#createdby-filter").val();
        options["affectedby"] = $("#affectedby-filter").val();
        filterIssues(options);

        return false;
    }

    var jobsiteIdChange = function ( $val )
    {
        $('#autoaffectedby-filter').val('');
        $('#autocreatedby-filter').val('');
        $('#createdby-filter').val('');
        $('#affectedby-filter').val('');
        if($val !== 'all')  {
        executeAjax
        (
            "<?= Yii::$app->urlManager->createUrl('ajax/get-building?id=') ?>" + $val
        ).done(function(r){
                $("#building-filter").empty().append("<option value='all'>-All-</option>");
                $("#floor-filter").empty().append("<option value='all'>-All-</option>");
                $("#area-filter").empty().append("<option value='all'>-All-</option>");
                for( var i=0, l=r.length; i<l; i++)
                {
                    $("#building-filter").append("<option value='"+r[i]['id']+"'>"+ r[i]['building'] +"</option>");
                }
            });

        executeAjax
        (
            "<?= Yii::$app->urlManager->createUrl('ajax/get-subjobsite?id=') ?>" + $val
        ).done(function(r){
                $("#subjobsite-filter").empty().append("<option value='all'>-All-</option>");
                for( var i=0, l=r.length; i<l; i++)
                {
                    $("#subjobsite-filter").append("<option value='"+r[i]['id']+"'>"+ r[i]['subjobsite'] +"</option>");
                }
            });
}
        return false;
    };

    // Building
    var buildingIdChange = function ( $val )
    {
        executeAjax
        (
            "<?= Yii::$app->urlManager->createUrl('ajax/get-floor?id=') ?>" + $val
        ).done(function(r){
                $("#floor-filter").empty().append("<option value='all'>-All-</option>");
                $("#area-filter").empty().append("<option value='all'>-All-</option>");
                for( var i=0, l=r.length; i<l; i++)
                {
                    $("#floor-filter").append("<option value='"+r[i]['id']+"'>"+ r[i]['floor'] +"</option>");
                }
            });
        return false;
    };
    // Floor
    var floorIdChange = function ( $val )
    {
        executeAjax
        (
            "<?= Yii::$app->urlManager->createUrl('ajax/get-area?id=') ?>" + $val
        ).done(function(r){
                $("#area-filter").empty().append("<option value='all'>-All-</option>");
                for( var i=0, l=r.length; i<l; i++)
                {
                    $("#area-filter").append("<option value='"+r[i]['id']+"'>"+ r[i]['area'] +"</option>");
                }
            });
        return false;
    };

    function enableloading(){
        $('#curve-loader').removeClass('hidden');
        $('#chart-loader').removeClass('hidden');
        $('#pie-loader').removeClass('hidden');
        $("#curve_chart").empty();
        $("#issues-over-time-total").empty();
        $('.actions-basic').addClass('hidden');
        $("#chart_div").empty();
        $("#piechart").empty();
    }

        //Create a Auto Search URL
    autoSearchurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-auto-search-user') ?>";

    $('#autoaffectedby-filter').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        params: {
        'Jobsites': function() {
            return $("#jobsite-filter").val();
         },
        'Affectedby': true
        },
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#affectedby-filter').val(suggestion.data);
        }
    }).blur(function() {
    if($('#autoaffectedby-filter').val().length == 0){
         $('#affectedby-filter').val('');
        }        
    })
    .focus(function() {
    if($('#autoaffectedby-filter').val().length == 0){
         $('#affectedby-filter').val('');
        }        
    });

    $('#autocreatedby-filter').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchurl,
        params: {
        'Jobsites': function() {
            return $("#jobsite-filter").val();
         },
        'Affectedby': false
        },
        onSearchStart: function (container) {
            $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
            $(this).removeClass('circleloader');
        },
        minChars:1,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
            $('#createdby-filter').val(suggestion.data);
        }
    }).blur(function() {
    if($('#autocreatedby-filter').val().length == 0){
         $('#createdby-filter').val('');
        }        
    })
    .focus(function() {
    if($('#autocreatedby-filter').val().length == 0){
         $('#createdby-filter').val('');
        }        
    });

</script>
