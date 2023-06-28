<?php

namespace app\components;

use app\models\AdEmails;
use Yii;
use bryglen\sendgrid;

class aduserData {

    static function getAdUsersProfile($sourceid) {
        $Adusers_array = self::getAdUsers($sourceid);

        return $Adusers_array;
    }
    static function getAdUsers($sourceid) {
        
        switch ($sourceid) {
				  case 1:
				     $memberobjectids = array( 'Towson_Everyone' => '04655824-2b3a-44a5-9f47-cc6a221dab05');
				     $sourcename = 'Towson_Everyone';
				    break;
				  case 2:
				    $memberobjectids = array( 'Branches_Everyone' => '48fcba2c-d6bb-4bdc-bb3a-00b379c0c166');
				        $sourcename = 'Branches_Everyone';
				    break;
				  case 3:
				     $memberobjectids = array( 'Interns' => '8dfee437-8a2e-4533-ba50-32a890dd5a8c');
				     $sourcename = 'Interns';
				    break;
				    case 4:
				     $memberobjectids = array( 'NewGroup' => 'b69c8e69-872e-4f84-9c82-a5597dd14ee5');
				     $sourcename = 'NewGroup';
				    break;
				    case 5:
				     $memberobjectids = array( 'JointVenture'=>'b69c8e69-872e-4f84-9c82-a5597dd14ee5');
				     $sourcename = 'JointVenture';
				    break;
				  default:
				     $memberobjectids = array( 'winwire' => 'bea9c1a8-a145-41df-90ea-4f1c150f9825');
				     $sourcename = 'winwire';

				}

				//Update all Ad users to Inactive
    	$updateddate =  date('Y/m/d H:i:s');
        Yii::$app->db->createCommand("UPDATE ad_emails SET updated_at = '".$updateddate."', updated_by  = 'WebJob', status = 1 where sourcegroup = '".$sourcename."' ")->execute();

        //$memberobjectids = array( 'Towson_Everyone' => '04655824-2b3a-44a5-9f47-cc6a221dab05');
         //$memberobjectids = array('Towson_Everyone' => '04655824-2b3a-44a5-9f47-cc6a221dab05', 'Branches_Everyone' => '48fcba2c-d6bb-4bdc-bb3a-00b379c0c166', 'Interns' => '8dfee437-8a2e-4533-ba50-32a890dd5a8c', 'winwire' => 'bea9c1a8-a145-41df-90ea-4f1c150f9825'); 

        $nextpageurl = false;
        $Adusers_array = array();
        $tokenarray = self::CreateGraphApiToken();
        $tokenarray_json = json_decode($tokenarray, true);
        $token = $tokenarray_json["access_token"];
        foreach ($memberobjectids as $membername => $memberid) {
            $usersdataarray = self::GetAdData($token, $memberid);
            $usersdata_json = json_decode($usersdataarray, true);
             if(isset($usersdata_json["@odata.nextLink"]))
                  $nextpageurl = $usersdata_json["@odata.nextLink"];
             
            if (isset($usersdata_json["value"])) {
                foreach ($usersdata_json["value"] as $i => $i_value) {
                    
                    $Adusers_array[] = ["email"=> $i_value["mail"],"firstname"=>$i_value["givenName"], "lastname"=>$i_value["surname"], "username"=>$i_value["userPrincipalName"]];
                }
                //$Adusers_array = array_unique($Adusers_array);

                $response = self::InsertAdUserstoDB($Adusers_array, $membername); 
               
            } else {
                $response = array(
                    'success' => FALSE,
                );

                echo "error";
                exit();
            }

            $ni = 1;
   //echo $nextpageurl;
            while ($nextpageurl) { 

                $ni = $ni+1;
            $usersdataarray = self::GetNextpageAdData($token, $nextpageurl);
            $usersdata_json = json_decode($usersdataarray, true);


           // var_dump($usersdata_json); 
             if(isset($usersdata_json["@odata.nextLink"]))
                  $nextpageurl = $usersdata_json["@odata.nextLink"];
              else
                $nextpageurl = false;

           // echo $nextpageurl;

            if (isset($usersdata_json["value"])) {
                foreach ($usersdata_json["value"] as $i => $i_value) {
                    
                    ${"Adusers_array" . $ni}[] = ["email"=> $i_value["mail"],"firstname"=>$i_value["givenName"], "lastname"=>$i_value["surname"], "username"=>$i_value["userPrincipalName"]];
                }
                //$Adusers_array = array_unique($Adusers_array);

                $response = self::InsertAdUserstoDB(${"Adusers_array" . $ni}, $membername ); 
              
            } else {
                $response = array(
                    'success' => FALSE,
                );

                echo "error";
                exit();
            }
                }

        }
        
        return $response;
    }

    private static function CreateGraphApiToken() {  
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://login.microsoftonline.com/e05675ba-c568-489a-a43d-4cf3830c6af0/oauth2/v2.0/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=57e0eb33-9213-4c6a-b5ed-13c11b425bd3&client_secret=GL-qLbO1DJBdj2x_8~1S-tz67D5Qh4eIH7&scope=https%3A%2F%2Fgraph.microsoft.com%2F.default",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 0;
        } else {
            if ($httpcode == 200) {
                return $response;
            } else {
                return 0;
            }

        }
    }

    private static function GetAdData($token, $memberid) { 
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://graph.microsoft.com/v1.0/groups/$memberid/members?%24top=999",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 0;
        } else {
            if ($httpcode == 200) {
                return $response;
            } else {
                return 0;
            }

        }
    }

    private function GetNextpageAdData($token, $nexturl) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$nexturl",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 0;
        } else {
            if ($httpcode == 200) {
                return $response;
            } else {
                return 0;
            }

        }
    }

    private function InsertAdUserstoDB($aduserarray, $membername) {

        foreach ($aduserarray as $aduser) {

            $transaction = Yii::$app->db->beginTransaction();
            try
            {
           $model = AdEmails::find()->where(['username' => $aduser["username"]])->one();  
           if(!$model){
           	    $addata = new AdEmails();
                $addata->status = 0;
                $addata->email = $aduser["email"];
                $addata->username=$aduser["username"];
                $addata->firstname=$aduser["firstname"];
                $addata->lastname=$aduser["lastname"];
                $addata->created_at = date('Y/m/d H:i:s');
                $addata->created_by = "Web Job";
                $addata->sourcegroup = $membername;
                $addata->save();
           }else{
           	$model->status = 0;
                $model->email = $aduser["email"];
                $model->username=$aduser["username"];
                $model->firstname=$aduser["firstname"];
                $model->lastname=$aduser["lastname"];
                $model->updated_at = date('Y/m/d H:i:s');
                $model->updated_by = "Web Job";
                $model->sourcegroup = $membername;
                $model->save();
           }
                

                $transaction->commit();

                $response = array(
                    'success' => TRUE,
                );

            } catch (Exception $ex) {

                $transaction->rollback();
                $response = array(
                    'success' => FALSE,
                    'message' => $ex
                );
            }
        }
        return  $response ; 

    }

        static function UpdateUsersDetails() {

        	$membername = 'null';


        	$result = Yii::$app->db->createCommand("Exec [dbo].[UpdateUsers] :paramName1") 
                      ->bindValue(':paramName1' , $membername )
                      ->queryScalar();
        if($result != "NoDatatoupdate"){
            $date = date('M d Y');
          $message = "AD Sync User Deactivated on ".$date;
          Yii::$app->mailer->compose('email-adusersync', ['users' => $result, 'logo_wt' => '../mail/images/logo.png', 'date' => $date ])
                
                 ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                   ->setTo(['seshendra.m@winwire.com', 'shilpashri.n@winwire.com', 'samuel.torres@whiting-turner.com', 'daniel.pearlman@whiting-turner.com'])
                    ->setSubject("$message")
                    ->send();
        }
          
 
        return  $result ; 

    }

}
