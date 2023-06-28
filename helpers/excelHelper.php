<?php
    /**
     * Created by IntelliJ IDEA.
     * User: imilano
     * Date: 06/10/2015
     * Time: 12:08
     */

    namespace app\helpers;

    use app\components\notification;
    use app\models\Area;
    use app\models\Contractor;
    use app\models\ContractorJobsite;
    use app\models\Role;
    use app\models\searches\Building;
    use app\models\searches\Floor;
    use app\models\searches\Jobsite;
    use app\models\searches\SubJobsite;
    use app\models\User;
    use app\models\UserJobsite;
    use yii\helpers\ArrayHelper;
    use Yii;


    class excelHelper {

        public $errors = array();
        const EXCEL_JOBSITE = 1;
        const EXCEL_CONTRACTOR_UPDATE = 2;
        const EXCEL_CONTRACTOR_CREATE = 4;
        const EXCEL_USERS = 3;

        public function excelToArray($worksheet,$initialRow = 2){
            $sheetData = array();
            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            for ($row = $initialRow; $row <= $highestRow; ++ $row) {
                $rowData = array();
                //si la primera columna esta vacia, se toma como una fila vacia
                if(trim($worksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue() ?? "") != ""){
                    for ($col = 1; $col < $highestColumnIndex; $col++) {
                        if(trim($worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue() ?? "") != ""){
                            $columnTitle = trim($worksheet->getCellByColumnAndRow($col, $initialRow-1)->getValue());
                            $cell = $worksheet->getCellByColumnAndRow($col, $row);
                            $val = trim($cell->getCalculatedValue());
                            $rowData[$columnTitle] = $val;
                        }
                    }
                }
                !empty($rowData)? $sheetData[] = $rowData : null;
            }
            return $sheetData;
        }

        public function processData($data, $tipo){

            $errors = array();
            $result = array();
            foreach($data as $index => $row){
                switch($tipo){
                    case self::EXCEL_JOBSITE :
                        $result[] = $this->saveJobsiteRow($row);
                        break;
                    case self::EXCEL_CONTRACTOR_UPDATE :
                        $result[] = $this->updateContractorRow($row);
                        break;
                    case self::EXCEL_CONTRACTOR_CREATE :
                        $result[] = $this->createContractorRow($row);
                        break;
                    case self::EXCEL_USERS :
                        $result[] = $this->saveUserRow($row);
                        break;
                }
            }
            return $result;
        }

        public function updateContractorRow($row){

            $transaction = Yii::$app->db->beginTransaction();
            $_SESSION["result"] = array();
            $_SESSION["action"] = "";
            $error = "";

            if(isset($row["Contractor"])){
                $_SESSION["action"] = "NONE";
                $contractor = Contractor::find()->where(["UPPER(RTRIM(LTRIM(contractor)))" => strtoupper($row["Contractor"])])->one();
                //si ya existe
                if($contractor){
                    //active
                    if(isset($row["Active"])){
                        $active = (strtoupper($row["Active"]) == "YES" || $row["Active"] == 1 )? 1: 0;
                        if($contractor->is_active != $active){
                            $contractor->is_active = $active;
                            $_SESSION["action"] = "UPDATE";
                            $_SESSION["result"][] = "'Active' modified";
                        }
                    }else{
                        $error = true;
                        $_SESSION["result"][] = "'Active' missing";
                    }
                    //vendor_number
                    if(isset($row["Vendor_number"])){
                        $exists = Contractor::find()->where(["vendor_number" => $row["Vendor_number"]])->one();
                        if($exists && $exists->contractor != $row["Contractor"]){
                            $error = true;
                            $_SESSION["result"][] = "Vendor number already exists";
                        }else if($contractor->vendor_number != $row["Vendor_number"]){
                            $contractor->vendor_number = $row["Vendor_number"];
                            $_SESSION["action"] = "UPDATE";
                            $_SESSION["result"][] = "'Vendor_number' modified";
                        }
                    }
                    //address
                    if(isset($row["Address"]) && $contractor->address != $row["Address"]){
                        $contractor->address = $row["Address"];
                        $_SESSION["action"] = "UPDATE";
                        $_SESSION["result"][] = "'Address' modified";
                    }
                    $contractor->updated = date("Y-m-d H:i:s");
                    $contractor->save();
                    if(isset($row["Jobsite"])){
                        $jobsite = Jobsite::find()->where(["UPPER(RTRIM(LTRIM(jobsite)))" => strtoupper($row["Jobsite"])])->one();
                        if($jobsite){
                            $contractor_jobsite = new ContractorJobsite();
                            $contractor_jobsite->jobsite_id = $jobsite->id;
                            $contractor_jobsite->contractor_id = $contractor->id;
                            $contractor_jobsite->save();
                            $_SESSION["action"] = "UPDATE";
                            $_SESSION["result"][] = "'Jobsite' added";
                        }else{
                            $error = true;
                            $_SESSION["result"][] = "Jobsite doesn't exists";
                        }
                    }
                    if($_SESSION["action"] == "NONE"){
                        $_SESSION["result"][] = "DUPLICATED";
                    }
                //si no existe
                }else{
                    $_SESSION["result"][] = "Contractor doesn't exists";
                }
            }elseif(!isset($row["Contractor"])){
                $error = true;
                $_SESSION["result"][] = "'Contractor' missing";
            }


            $row["Result"] = implode(" - ", $_SESSION["result"]);
            $row["Action"] = $_SESSION["action"];
            if($error != true){
                $row["Status"] = "OK";
                $transaction->commit();
            }else{
                $row["Status"] = "ERROR";
                $transaction->rollBack();
            }
            return $row;
        }
        public function createContractorRow($row){

            $transaction = Yii::$app->db->beginTransaction();
            $_SESSION["result"] = array();
            $_SESSION["action"] = "";
            $error = "";
            $active = "";

            if(isset($row["Active"])){
                $active = (strtoupper($row["Active"]) == "YES" || $row["Active"] == 1 )? 1: 0;
            }else{
                $error = true;
                $_SESSION["result"][] = "'Active' missing";
            }
            if(isset($row["Vendor_number"])){
                $exists = Contractor::find()->where(["vendor_number" => $row["Vendor_number"]])->one();
                if($exists && $exists->contractor != $row["Contractor"]){
                    $error = true;
                    $_SESSION["result"][] = "Vendor number already exists";
                }
            }

            if(isset($row["Contractor"]) && $error != true){
                $_SESSION["action"] = "NEW";
                $contractor = Contractor::find()->where(["UPPER(RTRIM(LTRIM(contractor)))" => strtoupper($row["Contractor"])])->one();
                //si ya existe
                if(!$contractor){
                    $contractor = new Contractor();
                    $contractor->contractor = $row["Contractor"];
                    $contractor->is_active = $active;
                    $contractor->vendor_number = isset($row["Vendor_number"]) ? $row["Vendor_number"] : null;
                    $contractor->address = isset($row["Address"]) ? $row["Address"] : null;
                    $contractor->created = date("Y-m-d H:i:s");
                    $contractor->updated = date("Y-m-d H:i:s");
                    $contractor->save();

                    if(isset($row["Jobsite"])){
                        $jobsite = Jobsite::find()->where(["UPPER(RTRIM(LTRIM(jobsite)))" => strtoupper($row["Jobsite"])])->one();
                        if($jobsite){
                            $contractor_jobsite = new ContractorJobsite();
                            $contractor_jobsite->jobsite_id = $jobsite->id;
                            $contractor_jobsite->contractor_id = $contractor->id;
                            $contractor_jobsite->save();
                            $_SESSION["result"][] = "'Jobsite' added";
                        }else{
                            $error = true;
                            $_SESSION["result"][] = "Jobsite doesn't exists";
                        }
                    }

                    //si no existe
                }else{
                    $error = true;
                    $_SESSION["action"] = "NONE";
                    $_SESSION["result"][] = "Contractor already exists";
                }
            }elseif(!isset($row["Contractor"])){
                $error = true;
                $_SESSION["result"][] = "'Contractor' missing";
            }

            $row["Result"] = implode(" - ", $_SESSION["result"]);
            $row["Action"] = $_SESSION["action"];
            if($error != true){
                $row["Status"] = "OK";
                $transaction->commit();
            }else{
                $row["Status"] = "ERROR";
                $transaction->rollBack();
            }
            return $row;
        }

        public function saveUserRow($row){

            $transaction = Yii::$app->db->beginTransaction();
            $_SESSION["result"] = array();
            $error = "";
            $active = "";

            if(isset($row["Active"])){
                if(strtoupper($row["Active"]) == "YES" || $row["Active"] == 1 ){
                    $active = 1;
                }else{
                    $active = 0;
                }
            }else{
                $error = true;
                $_SESSION["result"][] = "'Active' missing";
            }

            if(!isset($row["First_name"])){
                $error = TRUE;
                $_SESSION[ "result" ][ ] = "'First_name' missing";
            }

            if(!isset($row["Last_name"])){
                $error = TRUE;
                $_SESSION[ "result" ][ ] = "'Last_name' missing";
            }

            if(!isset($row["Employee_number"]) && is_numeric($row["Employee_number"])){
                $error = TRUE;
                $_SESSION[ "result" ][ ] = "'Employee_number' missing";
            }

            if(!isset($row["Jobsite"])){
                $error = TRUE;
                $_SESSION[ "result" ][ ] = "'Jobsite' missing";
            }

            if(isset($row["Contractor"])){
                $exists = Contractor::find()->where(["contractor" => $row["Contractor"]])->one();
                if($exists){
                    $row["Contractor"] = $exists->id;
                }else{
                    $error = true;
                    $_SESSION["result"][] = "'Contractor' doesn´t exists";
                }
            }else{
                $error = TRUE;
                $_SESSION[ "result" ][ ] = "'Contractor' missing";
            }

            if(isset($row["Role"])){
                if($row["Role"] == "System Administrator" && Yii::$app->session->get('user.role_id') == ROLE_ADMIN){
                    $error = true;
                    $_SESSION["result"][] = "Insufficient permission to assign System Administrator role.";
                }else{
                    $exists = Role::find()->where(["role" => $row["Role"]])->one();
                    if($exists){
                        $row["Role"] = $exists->id;
                    }else{
                        $error = true;
                        $_SESSION["result"][] = "'Role' doesn´t exists";
                    }
                }
            }else{
                $error = TRUE;
                $_SESSION[ "result" ][ ] = "'Role' missing";
            }

            if(isset($row["User_name"])){
                $user = User::find()->where([ "user_name" => $row["User_name"] ])->one();
                if($user){
                    $error = TRUE;
                    $_SESSION[ "result" ][ ] = "'User_name' already exists";
                }elseif(!isset($row["Password"])){
                    // generate random password
                    $row["Password"] = uniqid();
                }
            }

            if($error == false){
                $_SESSION["action"] = "NEW";
                $jobsite = Jobsite::find()->where(["UPPER(RTRIM(LTRIM(jobsite)))" => strtoupper($row["Jobsite"])])->one();
                if($jobsite){
                    $user = User::find()->joinWith("userJobsites")->where([ "employee_number" => $row["Employee_number"] , "jobsite_id" => $jobsite->id ])->one();
                    if($user){
                        $error = true;
                        $_SESSION["result"][] = "DUPLICATED - Badge ID already exists for this jobsite";
                    }
                }else{
                    $error = true;
                    $_SESSION["result"][] = "Jobsite doesn't exists";
                }

                if($error == false){
                    $user = new User();
                    $user->created = date("Y-m-d H:i:s");
                    $user->first_name = $row["First_name"];
                    $user->last_name = $row["Last_name"];
                    $user->employee_number = $row["Employee_number"];
                    $user->is_active = $active;
                    $user->contractor_id = $row["Contractor"];
                    $user->role_id = $row["Role"];
                    isset($row["Email"]) ? $user->email = $row["Email"] : null;
                    isset($row["User_name"]) ? $user->user_name = $row["User_name"] : null;
                    isset($row["Password"]) ? $user->password = md5($row["Password"]) : null;
                    isset($row["Phone"]) ? $user->phone = $row["Phone"] : null;
                    $user->updated = date("Y-m-d H:i:s");
                    $user->save();

                    if(isset($row["Jobsite"])){
                        $jobsite = Jobsite::find()->where( [ "UPPER(RTRIM(LTRIM(jobsite)))" => strtoupper( $row[ "Jobsite" ] ) ] )->one();
                        if ( $jobsite ){
                            $user_jobsite = new UserJobsite();
                            $user_jobsite->jobsite_id = $jobsite->id;
                            $user_jobsite->user_id = $user->id;
                            $user_jobsite->save();
                        }
                    }
                }

                //si el usuario es activo y se ingreso email, usuario, y contraseña, se envia una notificacion de nueva cuenta
                if($error == false && $user->is_active == true && isset($row["Email"]) && isset($row["User_name"]) && isset($row["Password"])){
                    notification::notifyNewUser($user->id , $row["Password"]);
                }
            }else{
                $error = true;
            }

            $row["Result"] = implode(" - ", $_SESSION["result"]);
            if($error != true){
                $row["Status"] = "OK";
                $row["Action"] = $_SESSION["action"];
                $transaction->commit();
            }else{
                $row["Status"] = "ERROR";
                $row["Action"] = "NONE";
                $transaction->rollBack();
            }
            return $row;
        }

        public function saveJobsiteRow($row){
            if(Yii::$app->session->get('user.role_id') == ROLE_ADMIN && isset($row["Jobsite"])){
                $user_jobsites = UserJobsite::find()->joinWith("jobsite")->where( [ "user_id" => Yii::$app->session->get('user.id'), "jobsite" => $row["Jobsite"] ] )->asArray()->all();
                if(!$user_jobsites){
                    $row["Result"] = "Jobsite not assigned";
                    $row["Status"] = "ERROR";
                    return $row;
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            $_SESSION["result"] = array();
            $jobsite_id = "";
            $subjobsite_id = "";
            $building_id = "";
            $floor_id = "";
            $area_id = "";
            $error = "";

            if(isset($row["Jobsite"])){
                $jobsite = Jobsite::find()->where(["jobsite" => $row["Jobsite"]])->one();
                $jobsite_id = !$jobsite ? self::createJobsite($row["Jobsite"]) : $jobsite->id;

                if(isset($row["Subjobsite"])){
                    $subjobsite = SubJobsite::find()->where(["jobsite_id" => $jobsite_id, "subjobsite" => $row["Subjobsite"]])->one();
                    $subjobsite_id = !$subjobsite ? self::createSubjobsite($row["Subjobsite"], $jobsite_id) : $subjobsite->id;
                }

                if(isset($row["Building"])){
                    $building = Building::find()->where(["jobsite_id" => $jobsite_id, "building" => $row["Building"]])->one();
                    $building_id = !$building ? self::createBuilding($row["Building"], $jobsite_id) : $building->id;

                    if(isset($row["Floor"])){
                        $floor = Floor::find()->where(["building_id" => $building_id, "floor" => $row["Floor"]])->one();
                        $floor_id = !$floor ? self::createFloor($row["Floor"], $building_id) : $floor->id;

                        if(isset($row["Area"])){
                            $area = Area::find()->where(["floor_id" => $floor_id, "area" => $row["Area"]])->one();
                            $area_id = !$area ? self::createArea($row["Area"], $floor_id) : $area->id;
                        }
                    }else{
                        if(isset($row["Area"])){
                            $error = true;
                            $_SESSION["result"][] = "Can't add an area without a floor";
                        }
                    }
                }else{
                    if(isset($row["Floor"])){
                        $error = true;
                        $_SESSION["result"][] = "Can't add a floor without a building";
                    }
                    if(isset($row["Area"])){
                        $error = true;
                        $_SESSION["result"][] = "Can't add an area without a building";
                    }
                }
            }else{
                $error = true;
                $_SESSION["result"][] = "'Jobsite' missing";
            }

            if( sizeof($_SESSION["result"]) < 1 ){
                $error = true;
                $_SESSION["result"][] = "Duplicated records";
            }
            $row["Jobsite"] = $jobsite_id;
            $row["Subjobsite"] = $subjobsite_id;
            $row["Building"] = $building_id;
            $row["Floor"] = $floor_id;
            $row["Area"] = $area_id;
            $row["Result"] = implode(" - ", $_SESSION["result"]);
            if($error != true){
                $row["Status"] = "OK";
                $transaction->commit();
            }else{
                $row["Status"] = "ERROR";
                $transaction->rollBack();
            }
            $_SESSION["result"] = array();
            return $row;
        }

        public function createJobsite($jobsite){
            date_default_timezone_set("America/Chicago");
            $newJobsite = new Jobsite();
            $newJobsite->jobsite = $jobsite;
            $newJobsite->is_active = 1;
            $newJobsite->created = date("Y-m-d H:i:s");
            $newJobsite->updated = date("Y-m-d H:i:s");
            $newJobsite->photo_allowed = 0;
            $newJobsite->newsflash_allowed = 0;
            $newJobsite->save();
            $_SESSION["result"][] = "Jobsite '" . $jobsite . "' created";
            return $newJobsite->id;

        }
        public function createSubjobsite($subjobsite, $jobsite_id){
            date_default_timezone_set("America/Chicago");
            $newSubjobsite = new SubJobsite();
            $newSubjobsite->subjobsite = $subjobsite;
            $newSubjobsite->is_active = 1;
            $newSubjobsite->created = date("Y-m-d H:i:s");
            $newSubjobsite->updated = date("Y-m-d H:i:s");
            $newSubjobsite->jobsite_id = $jobsite_id;
            $newSubjobsite->save();
            $_SESSION["result"][] = "Subjobsite '" . $subjobsite . "' created";
            return $newSubjobsite->id;
        }
        public function createBuilding($building, $jobsite_id){
            date_default_timezone_set("America/Chicago");
            $newBuilding = new Building();
            $newBuilding->building = $building;
            $newBuilding->is_active = 1;
            $newBuilding->created = date("Y-m-d H:i:s");
            $newBuilding->updated = date("Y-m-d H:i:s");
            $newBuilding->jobsite_id = $jobsite_id;
            $newBuilding->save();
            $_SESSION["result"][] = "Building '" . $building . "' created";
            return $newBuilding->id;

        }
        public function createFloor($floor, $building_id){
            date_default_timezone_set("America/Chicago");
            $newFloor = new Floor();
            $newFloor->floor = $floor;
            $newFloor->is_active = 1;
            $newFloor->created = date("Y-m-d H:i:s");
            $newFloor->updated = date("Y-m-d H:i:s");
            $newFloor->building_id = $building_id;
            $newFloor->save();
            $_SESSION["result"][] = "Floor '" . $floor . "' created";
            return $newFloor->id;
        }
        public function createArea($area, $floor_id){
            date_default_timezone_set("America/Chicago");
            $newArea = new Area();
            $newArea->area = $area;
            $newArea->is_active = 1;
            $newArea->created = date("Y-m-d H:i:s");
            $newArea->updated = date("Y-m-d H:i:s");
            $newArea->floor_id = $floor_id;
            $newArea->save();
            $_SESSION["result"][] = "Area '" . $area . "' created";
            return $newArea->id;
        }
    }
