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
        <td align="left" valign="left" bgcolor="#ffffff" style="background-color: #ffffff;">
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
                        <div class="title"
                                         style="font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:600;color:#374550">
                                        Welcome to TargetZero
                        </div>
                        <br>
                        <div class="body-text" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                        Please click this link to verify your email: 
                        <?= Html::a(Html::encode($confirmLink), $confirmLink) ?>        
                       
                        <br>

                        </div>
                                    
                        <br>

                        <!--[if mso]></td></tr></table><![endif]-->
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div>
                        <!--[if mso]></td></tr></table><![endif]-->


                        <div class="body-text"
                             style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#333333">
                            Do not reply to this e-mail. Please contact the TZ jobsite administrator or CMIC helpdesk if you have any questions.
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