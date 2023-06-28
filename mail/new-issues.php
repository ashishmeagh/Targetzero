<?php
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- So that mobile will display zoomed in -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- enable media queries for windows phone 8 -->
    <meta name="format-detection" content="telephone=no">
    <!-- disable auto telephone linking in iOS -->
<?php
    switch ( $data[ 'app_case_type_id' ] ) {
        case APP_CASE_VIOLATION:
?>
            <title>Whiting Turner - Violation</title>
<?php
            break;
        case APP_CASE_RECOGNITION:
?>
            <title>Whiting Turner - Recognition</title>
<?php
            break;
        case APP_CASE_INCIDENT:
?>
            <title>Whiting Turner - Incident</title>
<?php
            break;
        case APP_CASE_OBSERVATION:
?>
            <title>Whiting Turner - Observation</title>
<?php
            break;
    }
?>

    <style type="text/css">
        body
        {
            margin:                   0;
            padding:                  0;
            -ms-text-size-adjust:     100%;
            -webkit-text-size-adjust: 100%;
        }


        table
        {
            border-spacing: 0;
        }


        table td
        {
            border-collapse: collapse;
        }


        .ExternalClass
        {
            width: 100%;
        }


        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div
        {
            line-height: 100%;
        }


        .ReadMsgBody
        {
            width:            100%;
            background-color: #ebebeb;
        }


        table
        {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }


        img
        {
            -ms-interpolation-mode: bicubic;
        }


        .yshortcuts a
        {
            border-bottom: none !important;
        }


        @media screen and (max-width: 599px)
        {
            table[class="force-row"],
            table[class="container"]
            {
                width:     100% !important;
                max-width: 100% !important;
            }
        }


        @media screen and (max-width: 400px)
        {
            td[class*="container-padding"]
            {
                padding-left:  12px !important;
                padding-right: 12px !important;
            }
        }


        .ios-footer a
        {
            color:           #aaaaaa !important;
            text-decoration: underline;
        }
    </style>

</head>
<body style="margin:0; padding:0;" bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- 100% background wrapper (grey background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
    <tr>
        <td align="center" valign="middle" bgcolor="#ffffff" style="background-color: #ffffff;">
            <br>
            <!-- 600px container (white background) -->
            <table border="0" width="800" cellpadding="0" cellspacing="0" class="container" style="width:800px;max-width:800px">
                <tr>
                    <td class="container-padding header" align="left" style="font-family:Helvetica, Arial, sans-serif;font-size:24px;font-weight:bold;padding-bottom:12px;color:#ffffff;padding-left:24px;padding-right:24px; background-color: #ff6319; padding-top: 20px;">
                        <img src="https://www.targetzerowt.com/web/img/logo.png" alt="Whiting Turner">
                    </td>
                </tr>
                <tr>
                    <td class="container-padding content" align="left" style="padding-left:24px;padding-right:24px;padding-top:12px;padding-bottom:12px;background-color:#ffffff">
                        <br>
<?php
    $title = "Titulo";
    $report_text = '';
    $style = "font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:600;color:#374550";
    switch ( $from ) {
        case "new":
            switch ( $data[ 'app_case_type_id' ] ) {
                case APP_CASE_VIOLATION:
                    $title = "A new safety violation has been recorded.";
                    break;
                case APP_CASE_RECOGNITION:
                    $title = "Your safe habits have been recognized!";
                    break;
                case APP_CASE_INCIDENT:
                    if(isset($data[ 'report_type_id' ])){
                        switch ( $data[ 'report_type_id' ] ) {
                            case APP_CASE_INCIDENT_PRELIMINARY:
                                $title = "PRELIMINARY REPORT: A safety incident has been recorded at " . $data[ 'jobsite' ] . " and is under investigation";
                                break;
                            case APP_CASE_INCIDENT_INTERIM:
                                $title = "INTERIM REPORT: An amendment to the original safety incident report has been recorded at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                            case APP_CASE_INCIDENT_FINAL:
                                $title = "FINAL REPORT: This safety incident is no longer under investigation at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                        }
                    }
                    $style = "font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333";
                    break;
                case APP_CASE_OBSERVATION:
                    $title = "A new safety observation has been recorded.<br><br>";
                    break;
            };
            break;
         case "edit":
            switch ( $data[ 'app_case_type_id' ] ) {
                case APP_CASE_VIOLATION:
                    $title = "Safety violation has been recorded.";
                    break;
                case APP_CASE_RECOGNITION:
                    $title = "Your safe habits have been recognized!";
                    break;
                case APP_CASE_INCIDENT:
                    if(isset($data[ 'report_type_id' ])){
                        switch ( $data[ 'report_type_id' ] ) {
                            case APP_CASE_INCIDENT_PRELIMINARY:
                                $title = "PRELIMINARY REPORT: A safety incident has been recorded at " . $data[ 'jobsite' ] . " and is under investigation";
                                break;
                            case APP_CASE_INCIDENT_INTERIM:
                                $title = "INTERIM REPORT: An amendment to the original safety incident report has been recorded at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                            case APP_CASE_INCIDENT_FINAL:
                                $title = "FINAL REPORT: This safety incident is no longer under investigation at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                        }
                    }
                    $title.= "<br><br>";
                    $style = "font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333";
                    break;
                case APP_CASE_OBSERVATION:
                    $title = "Safety observation has been recorded.<br><br>";
                    break;
            };
            break;
        case "close":
            switch ( $data[ 'app_case_type_id' ] ) {
                case APP_CASE_VIOLATION:
                    $title = "A violation has been closed.";
                    break;
                case APP_CASE_RECOGNITION:
                    $title = "A recognition has been closed.";
                    break;
                case APP_CASE_INCIDENT:
                    $title = "An incident has been closed.";
                    $report_text = '';
                    if(isset($data[ 'report_type_id' ])){
                        switch ( $data[ 'report_type_id' ] ) {
                            case APP_CASE_INCIDENT_PRELIMINARY:
                                $report_text = "PRELIMINARY REPORT: A safety incident has been recorded at " . $data[ 'jobsite' ] . " and is under investigation";
                                break;
                            case APP_CASE_INCIDENT_INTERIM:
                                $report_text = "INTERIM REPORT: An amendment to the original safety incident report has been recorded at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                            case APP_CASE_INCIDENT_FINAL:
                                $report_text = "FINAL REPORT: This safety incident is no longer under investigation at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                        }
                    }
                    break;
                case APP_CASE_OBSERVATION:
                    $title = "An observation has been closed.";
                    break;
            }; //switch($data[ 'app_case_type_id' ])
            break;
        case "reassign":
            switch ( $data[ 'app_case_type_id' ] ) {
                case APP_CASE_VIOLATION:
                    $title = "This violation has been assigned to you.";
                    break;
                case APP_CASE_RECOGNITION:
                    $title = "This recognition has been assigned to you.";
                    break;
                case APP_CASE_INCIDENT:
                    $title = "This incident has been assigned to you.";
                    $report_text = '';
                    if(isset($data[ 'report_type_id' ])){
                        switch ( $data[ 'report_type_id' ] ) {
                            case APP_CASE_INCIDENT_PRELIMINARY:
                                $report_text = "PRELIMINARY REPORT: A safety incident has been recorded at " . $data[ 'jobsite' ] . " and is under investigation";
                                break;
                            case APP_CASE_INCIDENT_INTERIM:
                                $report_text = "INTERIM REPORT: An amendment to the original safety incident report has been recorded at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                            case APP_CASE_INCIDENT_FINAL:
                                $report_text = "FINAL REPORT: This safety incident is no longer under investigation at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                        }
                    }
                    break;
                case APP_CASE_OBSERVATION:
                    $title = "This observation has been assigned to you.<br><br>";
                    break;
            } //switch($data[ 'app_case_type_id' ])
            break;
        case "comment":
            switch ( $data[ 'app_case_type_id' ] ) {
                case APP_CASE_VIOLATION:
                    $title = "New comment on this violation report.";
                    break;
                case APP_CASE_RECOGNITION:
                    $title = "New comment on this recognition report.";
                    break;
                case APP_CASE_INCIDENT:
                    $title = "New comment on this incident report.";
                    if(isset($data[ 'report_type_id' ])){
                        switch ( $data[ 'report_type_id' ] ) {
                            case APP_CASE_INCIDENT_PRELIMINARY:
                                $report_text = "PRELIMINARY REPORT: A safety incident has been recorded at " . $data[ 'jobsite' ] . " and is under investigation";
                                break;
                            case APP_CASE_INCIDENT_INTERIM:
                                $report_text = "INTERIM REPORT: An amendment to the original safety incident report has been recorded at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                            case APP_CASE_INCIDENT_FINAL:
                                $report_text = "FINAL REPORT: This safety incident is no longer under investigation at " . $data[ 'jobsite' ] . ". Please read the updated status in the incident summary below.";
                                break;
                        }
                    }
                    break;
                case APP_CASE_OBSERVATION:
                    $title = "New comment on this observation report.<br><br>";
                    break;
            } //switch($data[ 'app_case_type_id' ])
            break;

    } //switch($from)
?>
                        <div class="title" style="<?= $style;?>">
                            <?= $title;?> (<a href=" <?=$data['baseUrl'];?>"  target="_blank" style="color: inherit;display: inline-block;"> view issue </a>) <br><br>
                        </div>
                        <br>
<?php
    if($data[ 'app_case_type_id' ] == APP_CASE_INCIDENT){
?>
                        <div class="title" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                            <?= $report_text; ?>
                            <br><br>
                        </div>
<?php
    }
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
              

                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span><?= ($from=='close' ? "Closed by" : "Issued by");?>:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'creator_first_name' ] . " " . $data[ 'creator_last_name' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Jobsite:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'jobsite' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Jobsite Number:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'job_number' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>
                        
<?php
    if ( $data[ 'app_case_type_id' ] == APP_CASE_INCIDENT ){
        $dateTitle = "Incident date & time";
        $dateValue = date( "M d, Y - h:i:s A", strtotime( $data[ 'incident_datetime' ] ) ) . " (" . $data[ 'timezone' ] . ")";
    }else if( $data[ 'app_case_type_id' ] == APP_CASE_OBSERVATION ){
        $dateTitle = "Observation date";
        $dateValue = date( "M d, Y", strtotime( $data[ 'correction_date' ] ) );
    }else{
        $dateTitle = "Correction date";
        $dateValue = date( "M d, Y", strtotime( $data[ 'correction_date' ] ) );
    }
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span><?=$dateTitle;?>:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?=$dateValue;?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>
<?php
    if ( $data[ 'app_case_type_id' ] != APP_CASE_INCIDENT ):
        // si es violation || recognition || observation
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Badge ID Number:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?php echo $data[ 'badge' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>

<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Employee Name:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?php echo $data[ 'employee_name' ] . ' ' . $data[ 'employee_last_name' ]; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#F32424;width:100%">
                                    <span><?php if($data['reptoffeder'] && $data[ 'app_case_type_id' ] != APP_CASE_RECOGNITION && $data[ 'app_case_type_id' ] != APP_CASE_OBSERVATION){echo '(Repeat Offender!&nbsp;<a style="color:#F32424;" href='.$data['reptoffendUrl'].'>View Previous Issues</a>)';} ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    endif;
?>

<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Status:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= strtoupper( $data[ 'status' ] ); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    if ( $data[ 'app_case_type_id' ] == APP_CASE_INCIDENT ){
        // si es violation || recognition || observation
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Report Type:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= (isset($data[ 'report_type' ]) ? ucfirst( $data[ 'report_type' ] ) : ""); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Report Topic:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= (isset($data[ 'report_topic' ]) ? ucfirst( $data[ 'report_topic' ] ) : ""); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>

                     
<!--[if mso]>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="50%" valign="middle">
<![endif]-->
                                    <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                                        <tr>
                                            <td class="col" valign="middle"
                                                style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                                <span>Recordable Injury:</span>
                                            </td>
                                        </tr>
                                    </table>
<!--[if mso]>
                                </td>
                                <td width="50%" valign="middle">
<![endif]-->
                                    <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                                        <tr>
                                            <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                                <span><?php echo $data[ 'recordable' ]; ?></span>
                                            </td>
                                        </tr>
                                    </table>
            <!--[if mso]>
                                </td>
                            </tr>
                        </table>
            <![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>


<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Lost Time Injury:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?php echo $data[ 'is_lost_time' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
<table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Lost Time Days:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= (isset($data[ 'lost_time' ]) ?  $data[ 'lost_time' ]  : ""); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                        &nbsp;
                        </div>                        
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>DART:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= (isset($data[ 'is_dart' ]) ? ucfirst( $data[ 'is_dart' ] ) : ""); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                            &nbsp;
                        </div>
                        
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
<table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>DART Days:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= (isset($data[ 'dart_time' ]) ? $data[ 'dart_time' ]  : ""); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->                        

                        
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                        &nbsp;
                        </div>
<?php
    };
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
<?php
    if ( $data[ 'app_case_type_id' ] != APP_CASE_INCIDENT ):
?>
                                    <span>Employer:</span>
<?php
    else:
?>
                                    <span>Affected contractor:</span>
<?php
    endif;
?>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= ucfirst( $data[ 'contractor_name' ] ); ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>

<?php
    if ( $data[ 'app_case_type_id' ] != APP_CASE_INCIDENT ):
        if ( !is_null( $data[ 'foreman_name' ] ) ):
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Foreman:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'foreman_name' ] . ' ' . $data[ 'foreman_last_name' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
        endif;
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Trade:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'trade' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    endif;
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
<?php
    if ( $data[ 'app_case_type_id' ] != APP_CASE_INCIDENT ):
?>
                                    <span>Building:</span>
<?php
    else:
?>
                                    <span>Incident Location:</span>
<?php
    endif;
?>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
<?php
    if ( $data[ 'app_case_type_id' ] != APP_CASE_INCIDENT ):
?>
                                    <span><?= $data[ 'building' ]; ?></span>
<?php
    else:
?>
                                    <span>Building: <?= $data[ 'building' ];?>, Floor: <?= $data[ 'floor' ];?>, Area: <?= $data[ 'area' ];?></span>
<?php
    endif;
?>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    if ( isset($data['subjobsite']) && !is_null($data[ 'subjobsite' ])):
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Sub jobsite:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                        <span><?= $data[ 'subjobsite' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    endif;

    if ( isset( $data[ 'osha' ] ) ):
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>OSHA Subpart:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'osha' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    endif;
    if ( isset( $data[ 'osha_detail' ] ) ):
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>OSHA Subpart Detail:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle"><![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'osha_detail' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    endif;

    if ( $data[ 'app_case_type_id' ] == APP_CASE_OBSERVATION && isset( $data[ 'coaching_provider' ] ) ):
?>
<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Coaching provided:</span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'coaching_provider' ]; ?></span>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
<?php
    endif;
?>

<!--[if mso]>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
<?php
    if ( $data[ 'app_case_type_id' ] != APP_CASE_INCIDENT ):
?>
                                    <span>Description:</span>
<?php
    else:
?>
                                    <span>Incident Summary:</span>
<?php
    endif;
?>
                                </td>
                            </tr>
                        </table>
<!--[if mso]>
                    </td>
                    <td width="50%" valign="middle">
<![endif]-->
                        <table width="376" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
                            <tr>
                                <td class="col" valign="middle" style="padding-bottom:10px;font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'description' ]; ?></span>
                                </td>
                            </tr>
<?php
    if ( !empty( $comments ) ){
        foreach ( $comments as $comment ) {
?>
                            <tr>
                                <td class="col" valign="middle" style="font-family:Helvetica, Arial, sans-serif;border-top: 1px solid #cccccc;text-align:left;color:#333333;width:100%;padding-top:5px;padding-bottom:10px;">
<?php
            if ( isset( $comment[ 'report_type' ] ) ):
?>
                                    <span style="font-family:Helvetica, Arial, sans-serif;font-size:15px;line-height:18px;letter-spacing:0px;text-align:left;color:#333333;width:100%;font-weight: bold;"><?= strtoupper( $comment[ 'report_type' ] ); ?>
                                        Report
                                    </span>
                                    <br>
<?php
            endif;
?>
                                    <span style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px; letter-spacing:0px;text-align:left;color:#999999;width:100%;"><?= $comment[ 'first_name' ]; ?> <?= $comment[ 'last_name' ]; ?>
<?php
            echo date( "M d, Y - h:i:s A", strtotime( $comment[ 'created' ] ) );
            if(isset($data[ 'timezone' ]) && !is_null($data[ 'timezone' ])){
                echo " (" . $data[ 'timezone' ] . ")";
            }
?>
                                    </span>
                                    <br>
                                    <span style="padding-bottom:20px;font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%"><?= $comment[ 'comment' ]; ?></span>
                                </td>
                            </tr>
<?php
        }
    }
?>
                        </table>
<!--[if mso]>
                    </td>
                </tr>
            </table>
<![endif]-->
                        <div class="hr" style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px; ">
                            &nbsp;
                        </div>
                        <br><br>
                        <div class="body-text" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                            Do not reply to this e-mail. Please see the WT safety representative if you have any
                            questions.
                            <br><br>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="container-padding footer-text" align="left" style="font-family:Helvetica, Arial, sans-serif;font-size:12px;line-height:16px;color:#aaaaaa;padding-left:24px;padding-right:24px">
                        <br><br>
                        © Whiting Turner <?php echo date("Y"); ?>
                        <br><br>
                        <br>
                    </td>
                </tr>
            </table>
            <!--/600px container -->
        </td>
    </tr>
</table>
<!--/100% background wrapper-->
</body>
</html>
