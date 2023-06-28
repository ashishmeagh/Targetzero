<?php
    /**
     * Created by IntelliJ IDEA.
     * User: imilano
     * Date: 22/06/2015
     * Time: 03:40 PM
     */
    use yii\helpers\Html;
    use yii\helpers\Url;


    /* @var $this \yii\web\View view component instance */
    /* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- So that mobile will display zoomed in -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- enable media queries for windows phone 8 -->
    <meta name="format-detection" content="telephone=no">
    <!-- disable auto telephone linking in iOS -->
    <?php switch ( $from )
    {
        case "new":
            ?>
            <title>Whiting Turner - New account</title>
            <?php
            break;
        case "recovery":
            ?>
            <title>Whiting Turner - Password recovery</title>
            <?php
            break;
        case "change":
            ?>
            <title>Whiting Turner - Account updated</title>
            <?php
            break;
    } ?>

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
            <table border="0" width="600" cellpadding="0" cellspacing="0" class="container"
                   style="width:600px;max-width:600px">
                <tr>
                    <td class="container-padding header" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:24px;font-weight:bold;padding-bottom:12px;color:#ffffff;padding-left:24px;padding-right:24px; background-color: #ff6319; padding-top: 20px;">
                        <img src="<?= $message->embed( $logo_wt ); ?>" alt="Whiting Turner">
                    </td>
                </tr>
                <tr>
                    <td class="container-padding content" align="left"
                        style="padding-left:24px;padding-right:24px;padding-top:12px;padding-bottom:12px;background-color:#ffffff">
                        <br>
                        <?php
                            switch ($from)
                            {
                                case "new":
                                    ?>
                                    <div class="title"
                                         style="font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:600;color:#374550">
                                        TargetZero new account
                                    </div>
                                    <br>
                                    <?php
                                    if ( isset( $data[ 'first_name' ] ) && isset( $data[ 'first_name' ] ) )
                                    {
                                        ?>
                                        <div class="body-text"
                                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                           A New User has been registered. Please find the below details.

                                            <br>

                                        </div>
                                    <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="body-text"
                                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                            You have a new account for TargetZero. Please, log in as soon as possible
                                            and
                                            change your temporary
                                            password.
                                            <br>
                                            You can login at: http://www.targetzerowt.com
                                            <br>
                                            <br>
                                        </div>
                                    <?php

                                    }
                                    break;
                                case "recovery":
                                    ?>
                                    <div class="title"
                                         style="font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:600;color:#374550">
                                        Password recovery
                                    </div>
                                    <br>
                                    <?php
                                    if ( isset( $data[ 'first_name' ] ) && isset( $data[ 'first_name' ] ) )
                                    {
                                        ?>
                                        <div class="body-text"
                                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                            <?= $data[ 'first_name' ] . ' ' . $data[ 'last_name' ]; ?>, a new temporary
                                            password
                                            has been generated for you. Please, log in as soon as possible and change
                                            it.
                                            <br><br>
                                        </div>
                                    <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="body-text"
                                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                            A new temporary password has been generated for you. Please, log in as soon
                                            as
                                            possible and change it.
                                            <br><br>
                                        </div>
                                    <?php
                                    }
                                    break;
                                case "change":
                                    ?>
                                    <div class="title"
                                         style="font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:600;color:#374550">
                                        Account updated
                                    </div>
                                    <br>
                                    <?php
                                    if ( isset( $data[ 'first_name' ] ) && isset( $data[ 'first_name' ] ) )
                                    {
                                        ?>
                                        <div class="body-text"
                                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                            <?= $data[ 'first_name' ] . ' ' . $data[ 'last_name' ]; ?>, an administrator has updated your account information. These are your new login credentials.
                                            <br><br>
                                        </div>
                                    <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="body-text"
                                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                                            An administrator has updated your account information. These are your new login credentials.
                                            <br><br>
                                        </div>
                                    <?php
                                    }
                                    break;
                            }
                        ?>
                        <br>

                        <!--[if mso]>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="50%" valign="middle"><![endif]-->
                        <table width="264" border="0" cellpadding="0" cellspacing="0" align="left"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>FirstName:</span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td>
                        <td width="50%" valign="middle"><![endif]-->
                        <table width="264" border="0" cellpadding="0" cellspacing="0" align="right"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'first_name' ]; ?></span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div>
                         <!-- //other column  -->

                        <!--[if mso]>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="50%" valign="middle"><![endif]-->
                             <table width="264" border="0" cellpadding="0" cellspacing="0" align="left"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>LastName:</span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td>
                        <td width="50%" valign="middle"><![endif]-->
                        <table width="264" border="0" cellpadding="0" cellspacing="0" align="right"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'last_name' ]; ?></span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div>
                         <!-- //other column  -->

                        <!--[if mso]>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="50%" valign="middle"><![endif]-->
                             <table width="264" border="0" cellpadding="0" cellspacing="0" align="left"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Sticker Number/Badge Number:</span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td>
                        <td width="50%" valign="middle"><![endif]-->
                        <table width="264" border="0" cellpadding="0" cellspacing="0" align="right"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'employee_number' ]; ?></span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div>
                        <!-- //other column  -->

                        <!--[if mso]>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="50%" valign="middle"><![endif]-->
                                <table width="264" border="0" cellpadding="0" cellspacing="0" align="left"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Contractor:</span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td>
                        <td width="50%" valign="middle"><![endif]-->
                        <table width="264" border="0" cellpadding="0" cellspacing="0" align="right"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $data[ 'contractor' ]; ?></span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div>    
                        <!-- //other column  -->

                         <!--[if mso]>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="50%" valign="middle"><![endif]-->
                             <table width="264" border="0" cellpadding="0" cellspacing="0" align="left"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#999999;width:100%">
                                    <span>Jobsite:</span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td>
                        <td width="50%" valign="middle"><![endif]-->
                        <table width="264" border="0" cellpadding="0" cellspacing="0" align="right"
                               class="force-row">
                            <tr>
                                <td class="col" valign="middle"
                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333;width:100%">
                                    <span><?= $jobsite[ 'jobsite' ]; ?>(<?= $jobsite[ 'job_number' ]; ?>)</span>
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div>

                      

                        <div class="body-text"
                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                            Do not reply to this e-mail. Please contact CMIC helpdesk if you have any
                            questions.
                            <br><br>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="container-padding footer-text" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:12px;line-height:16px;color:#aaaaaa;padding-left:24px;padding-right:24px">
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