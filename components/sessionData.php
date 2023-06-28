<?php
    /**
     * Created by IntelliJ IDEA.
     * User: imilano
     * Date: 30/04/2015
     * Time: 02:42 PM
     */

    namespace app\components;

    use Yii;
    use app\models\Session;
    use app\models\User;
    use app\models\Device;
    use app\components\RandomStringGenerator;

    class sessionData
    {

        static function check( $token )
        {
            $session = Yii::$app->db->createCommand( "SELECT user_id FROM session WHERE token='$token'" )->execute();
            return $session;
        }

        static function createSession( $deviceToken, $os, $user_id )
        {
            // TOKEN//
            $tokenLength = 45;
            $randomStringGenerator = new RandomStringGenerator;
            $token = $randomStringGenerator->generate( $tokenLength );
            $existeToken = Yii::$app->db->createCommand( "SELECT token FROM session WHERE token='$token'" )->execute();
            while ( $existeToken != 0 )
            {
                $token = $randomStringGenerator->generate( $tokenLength );
                $existeToken = Yii::$app->db->createCommand( "SELECT token FROM session WHERE token='$token'" )->execute();
            }
            //DEVICE//
            $deviceID = '';
            $existeDevice = '';

            if ( $os == "ios" ):
                $existeDevice = Yii::$app->db->createCommand( "SELECT * FROM device WHERE device='$deviceToken' AND type = '$os' AND user_id = $user_id AND device IS NOT NULL")->queryOne();
            elseif ( $os == "android" ):
                $firstSix = mb_substr( $deviceToken, 0, 6 ); 
                                           
                $existeDevice = Yii::$app->db->createCommand( "SELECT * FROM device WHERE device LIKE '$firstSix%' AND type = '$os' AND user_id = $user_id AND device IS NOT NULL")->queryOne();
                if($existeDevice != false){
                        $device_id = $existeDevice[ "id" ];
                    Yii::$app->db->createCommand( "DELETE FROM session WHERE device_id='$device_id'" )->execute();
                    Yii::$app->db->createCommand( "DELETE FROM device WHERE id='$device_id'" )->execute();
                    $existeDevice = FALSE;
                }
                
            endif;



             $deviceToken = ("" ? "" : "Android_Need_to_migrate_to_FCM_from_GCM");

             
            if ( $existeDevice != FALSE ):
                $deviceID = (int) $existeDevice[ "id" ];
                Yii::$app->db->createCommand( "UPDATE device set updated = CAST('1900-01-01 00:00:00' AS datetime) WHERE id = $deviceID")->execute();
            else:
                $device = new Device();
                $device->is_active = 1;
                $device->created = date('Y/m/d H:i:s');
                $device->updated = '1900-01-01 00:00:00';
                $device->device = $deviceToken;
                $device->type = $os;
                $device->user_id = (int) $user_id;
                $device->save();

                $deviceID = $device->id;
            endif;


            //SESSION//
            
            $session = new Session();
            $session->token = $token;
            $session->created = date('Y/m/d H:i:s');
            $session->updated = date('Y/m/d H:i:s');
            $session->device_id = (int) $deviceID;
            $session->user_id = (int) $user_id;
            $session->save();

            return $token;
        }

        static function deleteSession( $token )
        {
            $command = Yii::$app->db->createCommand( "DELETE FROM session WHERE token='$token'" )->execute();

            return $command;
        }

        static function hasPermission( $token, $action )
        {
            $permission = Yii::$app->db->createCommand( "SELECT * FROM permission JOIN action ON action.id = permission.action_id WHERE role_id = (SELECT role_id FROM session JOIN [user] ON session.user_id = [user].id WHERE token = '$token') AND action.action = '$action'" )->queryOne();

            return $permission;
        }

        static function idFromToken( $token )
        {
            $user = Yii::$app->db->createCommand( "SELECT user_id FROM session WHERE token='$token'" )->queryOne();

            return $user[ "user_id" ];
        }
        static function lastUpdate($token)
        {
            date_default_timezone_set("UTC");
            $user = Yii::$app->db->createCommand( "SELECT device_id FROM session WHERE token='$token'" )->queryOne();
            $device = $user["device_id"];
            $time = date( 'Y/m/d H:i:s' );
            $command = Yii::$app->db->createCommand("UPDATE device SET updated = '$time' WHERE id='$device'")->execute();
            return ;
        }
        static function getLastUpdate($token)
        {
            $command = Yii::$app->db->createCommand("SELECT d.updated FROM device d JOIN session s ON s.device_id = d.id WHERE s.token = '$token'")->queryOne();
            return $command["updated"];
        }

    }