<?php
    /**
     * Created by IntelliJ IDEA.
     * User: imilano
     * Date: 03/06/2015
     * Time: 03:41 PM
     */
    namespace app\components;

    use Yii;
    use app\models\Content;
    use app\components\userData;
    use finfo;
    class attachment
    {

        static function attach( $token, $app_case_id, $photo )
        {
            $user = userData::getProfileByToken( $token );
            $user_id = $user['id'];
            $transaction = Yii::$app->db->beginTransaction();
            try
            {
                $binary = base64_decode( $photo );
                $filename = uniqid();
                header( 'Content-Type: bitmap; charset=utf-8' );
                $file = fopen( "../web/files/" . $filename . ".jpg", 'wb' );
                fwrite( $file, $binary );
                fclose( $file );

                $content = new Content();
                $content->is_active = 1;
                $content->uploader_id = $user_id;
                $content->created = date( 'Y/m/d H:i:s' );
                $content->updated = date( 'Y/m/d H:i:s' );
                $content->app_case_id = $app_case_id;
                $content->type = "jpg";
                $content->file = $filename . ".jpg";
                $content->save();

                $transaction->commit();
                $response = array(
                    'success' => TRUE,
                    'id' =>  $content->id,
                    'file_url' => "files/" . $content->file,
                    'owner_id' => $user_id
                );
            }
            catch ( \Exception $e )
            {
                $transaction->rollback();
                $response = array(
                    'success'     => FALSE,
                    'error'       => "PHOTO_UPLOAD_ERR",
                    'description' => $e,
                );
            }

            return $response;
        }

        static function getAttachments( $app_case_id )
        {
            $attachments = Yii::$app->db->createCommand( "SELECT * FROM content WHERE app_case_id = '$app_case_id'" )->queryAll();
            

            $response = array();
            foreach ( $attachments as $attachment )
            {
                if($attachment[ "type" ] == 'jpg' || $attachment[ "type" ] == 'jpeg'|| $attachment[ "type" ] == 'PNG' ){
                    $response[ ] = array(
                        'id'       => $attachment[ "id" ],
                        "created"  => $attachment[ "created" ],
                        "file_url" => "files/" . $attachment[ "file" ],
                        "uploader_id" => $attachment[ "uploader_id" ]
                    );
                }elseif($attachment[ "type" ] == 'blob'){
                    
                    $extension = pathinfo($attachment[ "file" ], PATHINFO_EXTENSION);
                    if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'heic'){
                    $response[ ] = array(
                        'id'       => $attachment[ "id" ],
                        "created"  => $attachment[ "created" ],
                        "file_url" => $attachment[ "file" ],
                        "uploader_id" => $attachment[ "uploader_id" ]
                    );
                }
            }
            }

            return $response;
        }
        
        static function SaveAttachmentsForWeb($app_case_id,$user_id,$destinationUrls)
        {
              $transaction = Yii::$app->db->beginTransaction();
            try
            {
                $count = 0;
                foreach ($destinationUrls as $destinationUrl) {
                $content = new Content();
                $content->is_active = 1;
                $content->uploader_id = $user_id;
                $content->created = date( 'Y/m/d H:i:s' );
                $content->updated = date( 'Y/m/d H:i:s' );
                $content->app_case_id = $app_case_id;
                    //$content->type = pathinfo($filename,PATHINFO_EXTENSION);//blob
                    $content->type = 'blob';
                    $content->file = $destinationUrl['destinationURL'];//destinationURl
                $content->save();

                
                
                    //$fileUrls[$count] =  "files/" . $filename;
                    $content_ids[$count] = $content->id;
                    $count++;
            }
            $transaction->commit();
                 $response = array(
                    'success' => TRUE,
                'id' =>  $content_ids,
                'destination_urls' => $destinationUrls,
                    'owner_id' => $user_id
                );
                 
            } catch (Exception $ex) {
               
                $transaction->rollback();
                $response = array(
                    'success'     => FALSE,
                    'error'       => "PHOTO_UPLOAD_ERR",
                    'description' => $e,
                );
            }
            return $response;
        }
        
         static function getAttachmentsForWeb( $app_case_id )
        {
            $attachments = Yii::$app->db->createCommand( "SELECT [file], [type] FROM content WHERE app_case_id = '$app_case_id'" )->queryAll();
           
            $count = 0;
            $response = array();
            foreach ( $attachments as $attachment )
            {
                $response[$count][ "destination_url" ] = $attachment[ "file" ];
                if(!(substr($attachment[ "file" ],0,5) == 'https') && isset($attachment[ "file" ])){
                    $file_attach = "../web/files/" . $attachment[ "file" ];
                    $isFileExist = file_exists($file_attach);
                    if($isFileExist){
                        $response[$count]['mimeType'] = self::get_image_mime_type($file_attach);
                    }
                }else{
                    $response[$count]['mimeType'] = self::get_image_mime_type($attachment[ "file" ]);
                }
                
                $response[$count][ "type" ] = $attachment[ "type" ];
                $count++;
            }
            
            return $response;
        }

        static function SaveAttachmentsForMobile($post)
        {
            try
            {
                    $postPhoto = $post['photo'];
                    $binary = base64_decode( $postPhoto );
                    $f = finfo_open();
                    $file_type = finfo_buffer($f, $binary, FILEINFO_MIME_TYPE);
                    
                    if($file_type == 'image/jpg' || $file_type == 'image/jpeg' || $file_type == 'image/png'|| $file_type == 'png' || $file_type == 'heic' || $file_type == 'image/heif'){

                        

                        if($file_type == 'image/png' || $file_type == 'png'){
                            $file_type_extension = ".png";
                        }else if( $file_type == 'heic' || $file_type == 'image/heif'){
                            $file_type_extension = ".heic";
                        }else{
                            $file_type_extension = ".jpg";
                        }


                        $filename = uniqid();
                        header( 'Content-Type: bitmap; charset=utf-8' );
                        $file = fopen( "../web/files/" . $filename . $file_type_extension, 'wb' );
                        
                        fwrite( $file, $binary );
                        fclose( $file ); 
                        $fileUrl =  Yii::$app->request->hostInfo . Yii::$app->request->baseUrl ."/"."files/" . $filename . $file_type_extension;
                        $filetoUpload = "../web/files/" . $filename . $file_type_extension;
                        $blobName = $filename . $file_type_extension;
                        $isFileExist = file_exists($filetoUpload);
                        
                        $blobStatus = self::uploadBlob($filetoUpload, $blobName);
                        
                        if($blobStatus['success'] == TRUE || $blobStatus["success"] == 1|| $blobStatus["success"] == 'true'){
                            if($isFileExist){
                                $isFileDeleted = unlink($filetoUpload);
                            }
                            $response = array(
                                'success' => TRUE,
                                'message' => 'Photo Uploaded Successfully',
                                'photo_url' => $blobStatus['destinationURL']
                            );
                        }else{
                            $response = array(
                                'success' => False,
                                'message' => 'photo Upload Error'
                            );
                        }
                        
                    }else{
                        $response = array(
                            'success' => False,
                            'message' => 'Invalid File Format'
                );
            }
            } catch (Exception $ex) {
               
                $transaction->rollback();
                $response = array(
                    'success'     => FALSE,
                    'error'       => "PHOTO_UPLOAD_ERR",
                    'description' => $e,
                );
            }
            return $response;
        }

        static function uploadBlob($filetoUpload, $blobName, $app_case_id = NULL) {
            
            $timestamp = strtotime("now");
            $filename = pathinfo($blobName, PATHINFO_FILENAME);
            $file_ext = pathinfo($blobName, PATHINFO_EXTENSION );
            $blobName = $filename."_".$timestamp.".".$file_ext;
            
            $accesskey = "pokmt0IY3pdn9PkXhL4JMYwgOrPldlcPI7TVcaNgiegU9q8QGYOl8SvB02CIkPDhWuGRBm5p8CiD+ASt4meVjw==";
            $storageAccount = 'targetzerostorage';                    
            $containerName = 'tzimages';                    
            $destinationURL = "https://$storageAccount.blob.core.windows.net/$containerName/$blobName";

                $currentDate = gmdate("D, d M Y H:i:s T", time());
                $handle = fopen($filetoUpload, "r");
                $fileLen = filesize($filetoUpload);

                $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$currentDate\nx-ms-version:2015-12-11";
                $urlResource = "/$storageAccount/$containerName/$blobName";

                $arraysign = array();
                $arraysign[] = 'PUT';               /*HTTP Verb*/  
                $arraysign[] = '';                  /*Content-Encoding*/  
                $arraysign[] = '';                  /*Content-Language*/  
                $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/  
                $arraysign[] = '';                  /*Content-MD5*/  
                $arraysign[] = 'image/png';         /*Content-Type*/   
                $arraysign[] = '';                  /*Date*/  
                $arraysign[] = '';                  /*If-Modified-Since */  
                $arraysign[] = '';                  /*If-Match*/  
                $arraysign[] = '';                  /*If-None-Match*/  
                $arraysign[] = '';                  /*If-Unmodified-Since*/  
                $arraysign[] = '';                  /*Range*/  
                $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
                $arraysign[] = $urlResource;        /*CanonicalizedResource*/

                $str2sign = implode("\n", $arraysign);

                $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));  
                $authHeader = "SharedKey $storageAccount:$sig";

                $headers = [
                    'Authorization: ' . $authHeader,
                    'x-ms-blob-cache-control: max-age=3600',
                    'x-ms-blob-type: BlockBlob',
                    'x-ms-date: ' . $currentDate,
                    'x-ms-version: 2015-12-11',
                    'Content-Type: image/png',
                    'Content-Length: ' . $fileLen
                ];

                $ch = curl_init($destinationURL);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_INFILE, $handle); 
                curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen); 
                curl_setopt($ch, CURLOPT_UPLOAD, true); 
                $result = curl_exec($ch);
                
                
            $mime_content_type = mime_content_type($filetoUpload);
            
                $response = array(
                    'success' => TRUE,
                    'destinationURL' => $destinationURL,
                    'mimeType' => $mime_content_type
                );

                curl_close($ch);
                
                
            return $response;
        }

        /**
         * @param $image_path
         * @return bool|mixed
         */
        static function get_image_mime_type($image_path)
        {
            $headers = @get_headers($image_path);
            if($headers && strpos( $headers[0], '200')) {
                $buffer = file_get_contents($image_path);
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                return $finfo->buffer($buffer);
            }
        }

    }