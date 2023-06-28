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
            <title>Whiting Turner - Target Zero Setup is Complete for job <?=$job_number?></title>
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
            /* body .column {width:100% !important; min-width:100% !important;} */
            table[class="force-row"],
            table[class="container"]
            {
                width:     100% !important;
                max-width: 100% !important;
            }
        }


        @media screen and (max-width: 400px)
        {
            /* body .column {width:100% !important; min-width:100% !important;} */
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

        .container {
            display: inline-block;
            padding-right: 17px;
        }
        .app-title{
            padding-left: 5px;
        }
        .sub-title{
            font-size:18px;
        }
        .footer-text-padding{
            margin:0px;
        }
    </style>

</head>
<body style="margin:0; padding:0;" bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- 100% background wrapper (grey background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#434242">
    <tr>
        <td align="center" valign="middle" bgcolor="#ffffff" style="background-color: #ffffff;">
            <br>
            <!-- 600px container (white background) -->
            <table border="0" width="600" cellpadding="0" cellspacing="0" class="container"
                   style="width:600px;max-width:600px">
                <tr>
                    <td class="container-padding header" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:24px;font-weight:bold;color:#ffffff; background-color: #ffffff; padding-top: 20px;">
                        <img src="<?= $message->embed( $computer_wt ); ?>" alt="Whiting Turner">
                    </td>
                </tr>
                <tr>
                    <td class="container-padding header" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:24px;font-weight:bold;padding-bottom:12px;color:#ffffff;padding-left:24px; background-color: #ff6319; padding-top: 20px;">
                        YOUR TARGETZERO SETUP IS COMPLETE!<br>
                        <p class="sub-title" >For Job Number(s) : <?=$job_number?> </p> 
                    </td>
                </tr>
                <tr>
                    <td class="container-padding header" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;color:#03001C; background-color: #ffffff; padding-top: 20px;">
                        <div class="body-text"
                                            style="font-family:Helvetica, Arial, sans-serif;font-size:12px;line-height:20px;text-align:left;color:#333333">
                                            The Jobsite administrator with the assistance of the site superintendent is responsible of uploading and populating the initial data by setting up your jobsite logistics such as buildings, floors and areas, adding contractors and creating user profiles. Please click on the link below for instructions in how to do this. <b>(note: Whiting Turner Contracting Co. has also been added to your jobsite. <u style=" color:#AC4425;">Do not add W-T as a new contractor</u> ).</b><br>
                        <br>
                        <a href="https://whitingturner.sharepoint.com/:b:/s/TargetZeroDB/EdZRmMSZuUFBmTGTzBAhp04BidLf0Qyejsoe4eYLtjQLYQ?e=qvTwaP">Target Zero Admin Training Package</a><br>
                        <br>
                        <b>Note: Without this initial set up users will not be able to use the mobile app.</b><br>
                        <br>
                            WT users can download the latest version of the mobile app from the Apple App Store for iOS devices, the Google Play Market for android devices and the Company Portal App for WT employees. Click <a href="https://whitingturner-my.sharepoint.com/:p:/g/personal/storres_whiting-turner_com/EeJDPf6e6L9IrX_u8_OlrK4BIzK2URoVPS5FSlVFVLQDiQ?e=Db5T0j">HERE</a>  to review the app description and capabilities.
                        <br>
                        <br>
                            WT Employees can download the app through this <a href="https://portal.manage.microsoft.com/WebCP/Apps/b1b12ec6-7069-4d9c-882a-48d2a5dfec4d">link</a>
                        <br>
                            Non WT employees can download the app from the respective app stores
                        <br>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px;">
                    <!-- your first table : BEGIN -->
                    <table class="column" align="left" width="32%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                        <td ><a href="https://apps.apple.com/us/app/target-zero/id1286980195"><p class="app-title">iOS</p><img src="<?= $message->embed( $ios_wt ); ?>" alt="Whiting Turner"></a></td>
                        </tr>
                    </table>
                    <!-- your first table : END -->
                    <!-- your second table : BEGIN -->
                    <table class="column" align="left" width="32%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                        <td>
                        <a href="https://play.google.com/store/apps/details?id=com.inclusionservices.whitingturner&hl=en_IN&gl=US"><p class="app-title"><p class="app-title">Android</p><img src="<?= $message->embed( $android_wt ); ?>" alt="Whiting Turner"> </a>
                            
                        </td>
                        </tr>
                    </table>
                    <!-- your second table : END -->
                    </td>
                </tr>
                <tr>
                    <td class="container-padding header" align="left"
                        style="padding-bottom:12px;">
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div></td>
                
                </tr>
                <tr>
                    <td class="container-padding header" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:bold;padding-bottom:12px;color:#ffffff;padding-left:24px;padding-right:24px; background-color: #ff6319; padding-top: 20px;text-align: center;">
                        Visit the <a style="color:#F7A440;" href="https://whitingturner.sharepoint.com/dept/CMIC/SitePages/CMIC-Group.aspx">CMiC SHAREPONT SITE FOR MORE!</a> <br>
                        
                    </td>
                    
                </tr>
                <tr>
                    <td class="container-padding header" align="left"
                        style="padding-top: 20px;">
                        <div class="hr"
                             style="height:1px;border-bottom:1px solid #cccccc;clear: both;  margin-bottom: 15px;">
                             
                        </div></td>
                
                </tr>
                
                <tr>
                    <td class="container-padding header" align="left"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:14px;color:#03001C; background-color: #ffffff;padding-top: 10px;">
                        <p class="footer-text-padding">Please contact the CMiC Helpdesk with questions for anything CMiC or Target Zero related!</p>  <br>
                        <p class="footer-text-padding">Email: <a href="mailto:CMiChelpdesk@whiting-turner.com">CMiChelpdesk@whiting-turner.com</a></p>   <br>
                        <p class="footer-text-padding">Phone: <span style="color:#ff6319;">410-337-8100</span></p>   <br>
                    </td>
                </tr>
                <tr>
                    <td class="container-padding header" align="center"
                        style="font-family:Helvetica, Arial, sans-serif;font-size:12px;font-weight:bold;color:#03001C; background-color: #ffffff; ">
                        <img src="<?= $message->embed( $WtLogo ); ?>" alt="Whiting Turner">
                        
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
