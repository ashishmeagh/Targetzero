<?php

namespace app\controllers;

use app\components\issueData;
use app\components\notification;
use app\models\AppCase;
use app\models\AppCaseHistory;
use app\models\AppCaseObservation;
use app\models\AppCaseRecognition;
use app\models\AppCaseSfCode;
use app\models\AppCaseViolation;
use app\models\Area;
use app\models\Building;
use app\models\ChangesTracker;
use app\models\Content;
use app\models\Contractor;
use app\models\Device;
use app\models\Floor;
use app\models\Follower;
use app\models\Jobsite;
use app\models\LoginTracker;
use app\models\Notification as ModelNotification;
use app\models\Session;
use app\models\SubJobsite;
use app\models\User;
use app\models\UserJobsite;
use app\models\ContractorJobsite;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\components\attachment;
use app\models\searches\AppCase as AppCaseSearch;

class AjaxController extends \yii\web\Controller {
    /**
     * Before Action
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action) {
        $this->enableCsrfValidation = FALSE;
        return parent::beforeAction($action);
    }

    /**
     * used at contractor creation. checks for similar contractor names
     * @param mixed $contractor Contractor name
     * @return mixed similar contractors
     */
    public function actionCheckDuplicatedContractors($contractor) {
        $contractorArray = explode(" ", $contractor);
        $contractorQuery = "";
        if (sizeof($contractorArray) > 1) {
            foreach ($contractorArray as $contractorWord) {
//                    $contractorWord = strlen($contractorWord) > 3 ? substr($contractorWord,1,-1) : $contractorWord;
                $contractorQuery .= " or contractor LIKE '%$contractorWord%' ";
            }
        } else {
//                $contractorArray[0] = strlen($contractorArray[0]) > 3 ? substr($contractorArray[0],1,-1) : $contractorArray[0];
            $contractorQuery = " or contractor LIKE '%$contractorArray[0]%' ";
        }
        //select *, levenshtein_ratio(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) as ratio  from contractor WHERE (levenshtein(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) and levenshtein_ratio(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) > 45) or contractor LIKE "%IDWES%" or contractor LIKE "%OUNDATION%" order by ratio desc
        $similarContractors = Yii::$app->db->createCommand("SELECT *, dbo.levenshtein_ratio(UPPER(contractor), UPPER('$contractor')) as ratio from contractor WHERE (dbo.levenshtein(UPPER(contractor), UPPER('$contractor')) > 50 and dbo.levenshtein_ratio(UPPER(contractor), UPPER('$contractor')) > 50) $contractorQuery order by ratio desc")->queryAll();
        $this->renderJSON($similarContractors);
    }

    /**
     * used at user creation. checks for similar user names
     * @param string $firstname
     * @param string $lastname
     * @param int $empid
     * @return mixed similar users
     */
    public function actionCheckDuplicatedUsers($firstname, $lastname, $empid) {
        $firstnameArray = explode(" ", $firstname);
        $lastnameArray = explode(" ", $lastname);
        $firstnameQuery = "";
        $lastnameQuery = "";
        if (sizeof($firstnameArray) > 1) {
            foreach ($firstnameArray as $firstnameWord) {
                $firstnameWord = strlen($firstnameWord) > 3 ? trim(substr($firstnameWord, 1, -1)) : trim($firstnameWord);
                $fs = str_replace("'", "''", $firstnameWord);
                $firstnameQuery .= " first_name LIKE '%$fs%' OR";
            }
        } else {
            $firstnameArray[0] = strlen($firstnameArray[0]) > 3 ? trim(substr($firstnameArray[0], 1, -1)) : trim($firstnameArray[0]);
            $fs = str_replace("'", "''", $firstnameArray[0]);
            $firstnameQuery = " first_name LIKE '%$fs%' ";
        }
        if (sizeof($lastnameArray) > 1) {
            foreach ($lastnameArray as $lastnameWord) {
                $lastnameWord = strlen($lastnameWord) > 3 ? trim(substr($lastnameWord, 1, -1)) : trim($lastnameWord);
                $ls = str_replace("'", "''", $lastnameWord);
                $lastnameQuery .= " last_name LIKE '%$ls%' OR";
            }
        } else {
            $lastnameArray[0] = strlen($lastnameArray[0]) > 3 ? trim(substr($lastnameArray[0], 1, -1)) : trim($lastnameArray[0]);
            $ls = str_replace("'", "''", $lastnameArray[0]);
            $lastnameQuery = " last_name LIKE '%$ls%' ";
        }
        $userQuery = "(" . rtrim($firstnameQuery, "OR") . ") AND (" . rtrim($lastnameQuery, "OR") . ")";
        $similarUsers = Yii::$app->db->createCommand("SELECT TOP 10 [user].*, contractor.contractor from [user] join contractor on [user].contractor_id = contractor.id WHERE $userQuery OR employee_number = '" . $empid . "' order by first_name desc, last_name desc")->queryAll();
        $similarUsersData = array();
        if (sizeof($similarUsers) > 0) {
            foreach ($similarUsers as $user) {
                $user_id = $user["id"];
                $last_login = LoginTracker::find()->where("user_id = $user_id")->orderBy("timestamp DESC")->one();
                $assigned_jobsites = UserJobsite::find()->where("user_id = $user_id")->all();
                if (sizeof($assigned_jobsites) == 0) {
                    $assigned_jobsites = "(not set)";
                } else if (sizeof($assigned_jobsites) == 1) {
                    $assigned_jobsites = $assigned_jobsites[0]->jobsite->jobsite;
                } else {
                    $jobsitesArray = array();
                    foreach ($assigned_jobsites as $jobsite) {
                        $jobsitesArray[] = $jobsite->jobsite->jobsite;
                    }
                    $assigned_jobsites = implode(", ", $jobsitesArray);
                }
                $user["last_login"] = isset($last_login->timestamp) ? $last_login->timestamp : "Never";
                $user["jobsites"] = $assigned_jobsites;
                $similarUsersData[] = $user;
            }
        }
        $similarUsers = $similarUsersData;
        $this->renderJSON($similarUsers);
    }

    /**
     * Get newsflash status
     * @param int $id
     * @return mixed
     */
    public function actionCheckNewsflash($id = NULL) {
        $jobsite = Jobsite::find()->where(["id" => $id])->andWhere(["is_active" => 1])->asArray()->one();
        $this->renderJSON($jobsite);
    }

    /**
     * Cascade Delete of a user
     * It deletes rows from tables: user,
     * @return mixed jobsites
     */
    public function actionCascadeDelete($id = NULL) {
        $response = array();
        if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
            $creatorIssues = AppCase::findAll(['creator_id' => $id]);
            $affectedIssues = AppCase::findAll(['affected_user_id' => $id]);
            $foremanViolationIssues = AppCaseViolation::findAll(['foreman_id' => $id]);
            $foremanObservationIssues = AppCaseObservation::findAll(['foreman_id' => $id]);
            $foremanRecognitionIssues = AppCaseRecognition::findAll(['foreman_id' => $id]);
            //Can delete user and child rows?
            if (count($creatorIssues) + count($affectedIssues) + count($foremanViolationIssues)
                 + count($foremanObservationIssues) + count($foremanRecognitionIssues) == 0) {
                //Delete child rows.
                try {
                    $sessions = Session::deleteAll(['user_id' => $id]);
                    $changesTracker = ChangesTracker::deleteAll(['user_id' => $id]);
                    $device = Device::deleteAll(['user_id' => $id]);
                    $follower = Follower::deleteAll(['user_id' => $id]);
                    $content = Content::deleteAll(['uploader_id' => $id]);
                    $notification = ModelNotification::deleteAll(['user_id' => $id]);
                    $userJobsite = UserJobsite::deleteAll(['user_id' => $id]);
                    $loginTracker = LoginTracker::deleteAll(['user_id' => $id]);
                    $appCaseHistory = AppCaseHistory::deleteAll(['creator_id' => $id]);
                    //Delete user.
                    $user = User::deleteAll(['id' => $id]);
                    $response['class'] = 'success';
                    $response['message'] = "User has been deleted succesfully.";
                } catch (Exception $e) {
                    $response['class'] = 'danger';
                    $response['message'] = "Error when trying to delete user.<br>The user was not deleted.";
                }
            } else {
                $response['class'] = 'danger';
                $response['message'] = "Unable to delete user due to issues associated.";
            }
        } else {
            $response['class'] = 'danger';
            $response['message'] = "Unable to delete user due to lack of permissions.";
        }
        return $this->renderJSON($response);
    }

    /**
     * Get issues for dashboard summary to dropdown list
     * @return mixed issues
     */
    public function actionDashboardIssues($type = null, $from = null, $to = null, $jobsite = null, $subjobsite = null, $building = null, $status = null, $contractor = null, $trade = null, $posStart = 0, $count = 1000) {
        $filtro = array("app_case.is_active" => "1");
        $filtroFechas = array();

        //echo "from: $from\n";
        //echo "to: $to\n";
        if (!$from || $from === null) {$from = 'January 01, 1900';}
        if (!$to || $to === null) {$to = date('F d, Y');}
        $from_format = \DateTime::createFromFormat('F d, Y', $from)->format('Y-m-d');
        $to_format = \DateTime::createFromFormat('F d, Y', $to)->format('Y-m-d');
        //echo "from_format: $from_format"; die();
        array_push($filtroFechas, 'between', 'app_case.created', $from_format, $to_format);
        //var_dump($filtroFechas); echo "<br>";
        if ($type !== null && $type !== 'all') {$filtro['app_case_type_id'] = $type;}
        if ($jobsite !== null && $jobsite !== 'all') {$filtro['jobsite_id'] = $jobsite;}
        if ($subjobsite !== null && $subjobsite !== 'all') {$filtro['sub_jobsite_id'] = $subjobsite;}
        if ($building !== null && $building !== 'all') {$filtro['building_id'] = $building;}
        if ($status !== null && $status !== 'all') {$filtro['app_case_status_id'] = $status;}
        if ($contractor !== null && $contractor !== 'all') {$filtro['contractor_id'] = $contractor;}
        if ($trade !== null && $trade !== 'all') {$filtro['trade_id'] = $trade;}
        $withArray = array('jobsite', 'contractor', 'creator', 'appCaseIncidents');
        //$filtroFechas
        // var_dump($filtro); die();
        $query = new Query();
        $query->select(['app_case.created', 'app_case_type_id', 'additional_information', 'app_case_type_id', 'app_case_status_id', '[user].last_name', '[user].first_name',
            'contractor.contractor', 'jobsite.jobsite', 'report_topic.report_topic', 'app_case_incident.recordable'])
            ->from('app_case')
            ->join('inner join', '[user]', 'app_case.creator_id = [user].id')
            ->join('inner join', 'contractor', 'app_case.contractor_id = contractor.id')
            ->join('inner join', 'jobsite', 'app_case.jobsite_id = jobsite.id')
            ->join('left join', 'app_case_incident', 'app_case.id = app_case_incident.app_case_id')
            ->join('left join', 'app_case_violation', 'app_case.id = app_case_violation.app_case_id')
            ->join('left join', 'app_case_recognition', 'app_case.id = app_case_recognition.app_case_id')
            ->join('left join', 'app_case_observation', 'app_case.id = app_case_observation.app_case_id')
            ->join('left join', 'report_topic', 'app_case_incident.report_topic_id = report_topic.id')
            ->where($filtro)->andWhere($filtroFechas);
        $command = $query->createCommand();
        $data = $command->queryAll();
        //echo($command->pdoStatement->queryString."<br>");
        //echo($command->_sql); die();
        //var_dump($command->params); die();
        $model = AppCase::find( /*["id","jobsite","contractor_id","additional_information"
    ]*/)->with($withArray)->where($filtro)->andWhere($filtroFechas)->orderBy('id')->limit($count)->offset($posStart)->all();
        //var_dump($model); die();
        //var_dump($app_case[0]); die();
        $totalCount = count($data);
        //header("Content-type: text/xml");
        echo ("<?xml version='1.0' encoding='utf-8'?>\n");
        //start output of data
        echo "<rows total_count='$totalCount' pos='$posStart'>\n";
        $i = 1;
        foreach ($data as $app_case) {
            //var_dump($row['appCaseIncidents']); die();
            switch ($app_case['app_case_type_id']) {
            case 1:$type = "Violation";
                break;
            case 2:$type = "Recognition";
                break;
            case 3:$type = "Incident";
                break;
            case 4:$type = "Observation";
            }
            echo ("\t<row id='$i'>\n");
            print("\t\t<cell><![CDATA[" . $app_case['created'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $type . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['additional_information'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['app_case_status_id'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['last_name'] . ", " . $app_case['first_name'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['contractor'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['jobsite'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['report_topic'] . "]]></cell>\n");
            print("\t\t<cell><![CDATA[" . $app_case['recordable'] . "]]></cell>\n");
            print("\t</row>\n");
            $i++;
        }
        echo "</rows>\n";
        //$result = array("rows" => array())
        //$this->renderJSON( $app_case );
    }

    /**
     * Get jobsites to dropdown list
     * @return mixed jobsites
     */
    public function actionGetAllJobsite() {
        $jobsite = Jobsite::find([
            "id",
            "jobsite",
        ])->where(["is_active" => 1])->orderBy('jobsite')->asArray()->all();
        $this->renderJSON($jobsite);
    }


    public function getIndexedArray($array) {
        $arrayTemp = array();
        for ($i=0; $i < count($array); $i++) { 
            $keys = array_keys($array[$i]);
            $innerArrayTemp = array();
            for ($j=0; $j < count($keys); $j++) { 

                $innerArrayTemp[$j] = $array[$i][$keys[$j]];                
            }
            array_push($arrayTemp, $innerArrayTemp);
        }
        return $arrayTemp;
    }

    /**
     * Get jobsites for a contractor to dropdown list
     * @param int $id contrcator id
     * @return mixed jobsites
     */
    public function actionGetJobsitesByContractor($id) {
        $jobsites = ArrayHelper::map(Jobsite::find()
                ->joinWith('contractorJobsites')
                ->where(["is_active" => 1, "contractor_id" => $id])
                ->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
//            $jobsite = Jobsite::find( [
        //                "id",
        //                "jobsite"
        //            ] )->where( [ "is_active" => 1 ] )->orderBy('jobsite')->asArray()->all();
        $this->renderJSON($jobsites);
    }

    public function actionGetContractorsData($searchkey) {
        if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
            $sqlQuery = "select c.id,c.contractor from [contractor] as c where c.is_active = 1 AND c.id != 148 AND c.contractor like '%" . $searchkey . "%'";
            $ContractorList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
            $contractorListdataArray = array();
            foreach ($ContractorList as $key => $value) {
                $temp['value'] = $value['contractor'];
                $temp['data'] = $value['id'];
                array_push($contractorListdataArray, $temp);
            }
            $resultArray = array();
            $resultArray["suggestions"] = $contractorListdataArray;
            $searchusers = json_encode($resultArray, true);
            exit($searchusers);
        } else {
            // $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'jobsite' );
            $data_jobsite = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
            $data_contractor = $this->getContractorsForJobsites($data_jobsite,$searchkey);
            $jobsitContractorListdataArray = array();
            foreach ($data_contractor as $key => $value) {
                $temp['value'] = $value['contractor'];
                $temp['data'] = $value['id'];
                array_push($jobsitContractorListdataArray, $temp);
            }
            $resultArray = array();
            $resultArray["suggestions"] = $jobsitContractorListdataArray;
            $searchusers = json_encode($resultArray, true);
            exit($searchusers);
        }
    }

    public function actionGetContractorById($id) {
        $contractor = Contractor::find([
            "contractor"])
            ->where(["contractor.id" => $id,"is_active" => 1])
            ->orderBy('contractor')
            ->asArray()->one();
        $this->renderJSON($contractor);
    }

    /**
     * Get issues based on selected affected user
     * @param int affected user Id -> User table employee id
     * @return mixed issue type and appcase id
     */
    public function actionGetIssuesByAffectedUser($affected_user_id, $jobsite_id) {
        $offenderuserandissues = Yii::$app->db->createCommand("SELECT a.id,Upper(ac.type) as issue_type "
            . "FROM [app_case] as a "
            . "JOIN [user] u on u.id =a.affected_user_id "
            . "JOIN app_case_type ac on ac.id =a.app_case_type_id "
            . "WHERE a.affected_user_id = $affected_user_id "
            . "AND a.app_case_type_id <> 2 AND a.jobsite_id =$jobsite_id"
            . "order by a.created")->queryAll();
        $this->renderJSON($offenderuserandissues);
    }
    /**
     * Get jobsites for a contractor and user to dropdown list
     * @param int $contractor_id
     * @param int $user_id
     * @return mixed jobsites
     */
    public function actionGetJobsitesByContractorAndUser($contractor_id, $user_id) {
        $jobsites = ArrayHelper::map(Jobsite::find()
                ->joinWith('userJobsites')
                ->joinWith('contractorJobsites')
                ->where(["contractor_id" => $contractor_id, "user_jobsite.user_id" => $user_id])
                ->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');

        $this->renderJSON($jobsites);
    }
    /**
     * Get contractors for a contractor to dropdown list from a selected jobsite
     *
     * @param null $id
     */
    public function actionGetContractorsByJobsite($id) {
        if ($id != "all") {
            $contractors = ArrayHelper::map(Contractor::find()->joinWith('contractorJobsites')->where(["contractor.is_active" => 1, "contractor_jobsite.jobsite_id" => $id])->orderBy("contractor.contractor ASC")->asArray()->all(), 'id', 'contractor');
        } else {
            $contractors = ArrayHelper::map(Contractor::find()->where(["contractor.is_active" => 1])->orderBy("contractor.contractor ASC")->asArray()->all(), 'id', 'contractor');
        }
        $contractorsOrdered = array();
        foreach ($contractors as $id => $name) {
            $contractorsOrdered[" $id"] = $name;
        }
        $this->renderJSON($contractorsOrdered);
    }
    /**
     * Get users for a dropdown list from a selected jobsite
     *
     * @param null $id
     */
    public function actionGetUsersByJobsite($id) {
        //$users = User::find()->joinWith('userJobsites')->where( [ "user.is_active" => 1, "user_jobsite.jobsite_id" => $id] )->orderBy('employee_number')->asArray()->all();
        $users = Yii::$app->db->createCommand("SELECT u.id, user_id,user_name, first_name,last_name, employee_number from [user] u JOIN user_Jobsite uj ON uj.user_id = u.id WHERE u.is_active = 1 AND uj.jobsite_id ='$id' ORDER BY employee_number")->queryAll();
        $this->renderJSON($users);
    }
    /**
     * Get sub jobsites for a dropdown list from a selected jobsite
     *
     * @param null $id
     */
    public function actionGetSubJobsites($id) {
        $subjobsites = SubJobsite::find()->joinWith('jobsite')->where(["sub_jobsite.is_active" => 1, "sub_jobsite.jobsite_id" => $id])->orderBy('subjobsite')->asArray()->all();
        $this->renderJSON($subjobsites);
    }
    /**
     * Get users for a dropdown list from a selected jobsite and contractor
     *
     * @param null $id
     */
    public function actionGetUsersByContractorAndJobsite($contractor, $jobsite) {
        $users = User::find()->joinWith('userJobsites')->where(["user.is_active" => 1, "user.contractor_id" => $contractor, "user_jobsite.jobsite_id" => $jobsite])->orderBy('employee_number')->asArray()->all();
        $this->renderJSON($users);
    }
    /**
     * Get foremans for a dropdown list from a selected jobsite and contractor
     *
     * @param null $id
     */
    public function actionGetForemanByContractorAndJobsite($contractor, $jobsite) {
        $users = User::find()->joinWith('userJobsites')->where(["user.is_active" => 1, "user.role_id" => ROLE_CONTRACTOR_FOREMAN, "user.contractor_id" => $contractor, "user_jobsite.jobsite_id" => $jobsite])->orderBy('employee_number')->asArray()->all();
        $this->renderJSON($users);
    }

    /**
     * Get contractor from a selected user
     *
     * @param null $id
     */
    public function actionGetContractorByUser($id) {
        $contractor = User::find()->joinWith('contractor')->where(["user.is_active" => 1, "contractor.is_active" => 1, "user.id" => $id])->asArray()->one();
        $this->renderJSON($contractor);
    }

    /**
     * Get Building to dropdown list
     *
     * @param null $id
     */
    public function actionGetBuilding($id = NULL) {
        $building = Building::find([
            "id",
            "building",
        ])->where(["jobsite_id" => $id])->andWhere(["is_active" => 1])->orderBy('building')->asArray()->all();
        $this->renderJSON($building);
    }
    /**
     * Get subjobsite to dropdown list
     *
     * @param null $id
     */
    public function actionGetSubjobsite($id = NULL) {
        $subjobsite = SubJobsite::find([
            "id",
            "subjobsite",
        ])->where(["jobsite_id" => $id])->andWhere(["is_active" => 1])->orderBy('subjobsite')->asArray()->all();
        $this->renderJSON($subjobsite);
    }

    /**
     * Get Floor to dropdown list
     *
     * @param null $id
     */
    public function actionGetFloor($id = NULL) {
        $floor = Floor::find([
            "id",
            "floor",
        ])->where(["building_id" => $id])->andWhere(["is_active" => 1])->orderBy('floor')->asArray()->all();
        $this->renderJSON($floor);
    }

    /**
     * Get Area to dropdown list
     *
     * @param null $id
     */
    public function actionGetArea($id = NULL) {
        $area = Area::find([
            "id",
            "area",
        ])->where(["floor_id" => $id])->andWhere(["is_active" => 1])->orderBy('area')->asArray()->all();
        $this->renderJSON($area);
    }

    /**
     * Get Employee from employee_number to show
     *
     * @param null $id
     */
    public function actionGetEmployee($id = NULL) {
        $employee = User::find([
            "id",
            "first_name",
        ])->where('employee_number != ""')->where('employee_number LIKE :query')->addParams([':query' => $id . '%'])->asArray()->all();
        $this->renderJSON($employee);
    }

    /**
     * Get Employee to dropdown list
     *
     * @param null $id
     */
    public function actionGetAffectedUser($id = NULL) {
        if ($id != "") {
            $affected_user = User::find()->where(["contractor_id" => $id])->andWhere(["is_active" => 1])->asArray()->all();
        } else {
            $affected_user = User::find()->where(["is_active" => 1])->asArray()->all();
        }
        $this->renderJSON($affected_user);
    }

    /**
     * Get Foreman to dropdown list
     *
     * @param null $id
     */
    public function actionGetForeman($id = NULL) {
        $foreman = User::find()->where(["contractor_id" => $id, "role_id" => ROLE_CONTRACTOR_FOREMAN])->andWhere(["is_active" => 1])->asArray()->all();
        $this->renderJSON($foreman);
    }

    /**
     * Get Jobsite to dropdown list
     *
     * @param null $id
     */
    public function actionGetJobsite($id = NULL) {
        // get IDs jobsite by user
        $user_jobsite = UserJobsite::find()->select(['jobsite_id'])->where(["user_id" => $id])->asArray()->all();
        // get columns jobsite ID
        $array_index_jobsite_id = ArrayHelper::getColumn($user_jobsite, 'jobsite_id');
        // get jobsite
        $jobsite = Jobsite::find()->select('id, jobsite')->where(['id' => $array_index_jobsite_id])->asArray()->all();
        $this->renderJSON($jobsite);
    }
    /**
     * Get contractor to dropdown list
     *
     * @param int $id
     */
    public function actionGetContractor($id = NULL) {
        if ($id != "") {
            // get contractor ID by user
            $contractor_id = User::find()->select(['contractor_id'])->where(["id" => $id])->asArray()->all();
            // get contractor
            $contractor = Contractor::find()->select('id, contractor')->where(['id' => $contractor_id[0]["contractor_id"]])->orderBy('contractor')->asArray()->all();
        } else {
            $contractor = 0;
        }
        $this->renderJSON($contractor);
    }

    /**
     * Get Safty Code Tree View
     */
    public function actionGetSafetyCodeTreeView() {
        $response = array();

        $data = AppCaseSfCode::find()->select([
            "id",
            "code",
            "description",
            "parent_id",
        ])->where(["parent_id" => NULL, "is_active" => 1])->orderBy(["code" => "SORT_ASC"])->asArray()->all();

        for ($i = 0; $i < count($data); $i++) {
            $response[$i] = array();
            $response[$i]["id"] = $data[$i]["id"];
            $response[$i]["label"] = $data[$i]["code"] . " - " . $data[$i]["description"];

            $data_i = AppCaseSfCode::find()->select([
                "id",
                "code",
                "description",
                "parent_id",
            ])->where(["parent_id" => $data[$i]["id"], "is_active" => 1])->orderBy(["code" => "SORT_ASC"])->asArray()->all();

            if (count($data_i)) {
                $response[$i]["children"] = array();

                for ($j = 0; $j < count($data_i); $j++) {
                    $response[$i]["children"][$j] = array();
                    $response[$i]["children"][$j]["id"] = $data_i[$j]["id"];
                    $response[$i]["children"][$j]["label"] = $data_i[$j]["code"] . " - " . $data_i[$j]["description"];

                    $data_j = AppCaseSfCode::find()->select([
                        "id",
                        "code",
                        "description",
                        "parent_id",
                    ])->where(["parent_id" => $data_i[$j]["id"], "is_active" => 1])->orderBy(["code" => "SORT_ASC"])->asArray()->all();

                    if (count($data_j)) {
                        $response[$i]["children"][$j]["children"] = array();

                        for ($k = 0; $k < count($data_j); $k++) {
                            $response[$i]["children"][$j]["children"][$k] = array();
                            $response[$i]["children"][$j]["children"][$k]["id"] = $data_j[$k]["id"];
                            $response[$i]["children"][$j]["children"][$k]["label"] = $data_j[$k]["code"] . " - " . $data_j[$k]["description"];
                        }
                    }
                }
            }
        }

//            $first = [
        //                "id" => "",
        //                "label" => "-Choose a Safety Code-"
        //            ];
        //            $response = array_merge([ $first ], $response);
        $this->renderJSON($response);
    }

    /**
     * Get Users to dropdown by contractor_id
     *
     * @param null $contractor_id
     */
//        public function actionGetUsersByContractor( $contractor_id = NULL ){
    //            $data_query = User::find()
    //                ->select('id, first_name, last_name, employee_number')
    //                //filtro solo usuarios de WT
    //                ->where(['is_active' => 1, 'contractor_id' => 148])
    //                ->asArray()
    //                ->all();
    //
    //            $this->renderJSON( $data_query );
    //        }

    /**
     * Set Reassign creator issue
     */
    public function actionSetUserCreatorIssue($app_case_id = NULL, $creator_id = NULL) {

        $app_case = issueData::getCase($app_case_id);
        $affected_user_id = $app_case["affected_user_id"];
        $jobsite_id = $app_case["jobsite_id"];
        $former_owner_id = $app_case["creator_id"];
        $app_case = AppCase::find()
            ->where('id=:id', [':id' => $app_case_id])
            ->one();
        $app_case->creator_id = $creator_id;
        $success = $app_case->save();
        $session = Yii::$app->getSession();
        $reptoffeder = false;
        $searchModel = new AppCaseSearch();
        $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
        $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;
        //$user_id = Yii::$app->session->get( '[user].id' );
        notification::notifyAssign($session["id"], $app_case_id, $creator_id, $former_owner_id,$reptoffeder);
        $this->renderJSON($success);
    }

    /**
     * Set Status Issue
     */
    public function actionSetStatusIssue($app_case_id = NULL, $status_id = NULL) {
        $app_case = AppCase::find()
            ->where('id=:id', [':id' => $app_case_id])
            ->one();
        $app_case->app_case_status_id = $status_id;
        if ($app_case->save()) {

            if ($status_id == APP_CASE_STATUS_CLOSE) {
                $user_id = Yii::$app->session->get('[user].id');
                notification::notifyClose($app_case_id, $user_id);
            }
            $this->renderJSON(true);
        } else {
            $this->renderJSON(false);
        }

    }

    /**
     * Set Status to user from index
     */
    public function actionSetStatusUser($user_id = NULL, $status_id = NULL) {
        $users = User::find()
            ->where('id=:id', [':id' => $user_id])
            ->one();
        $users->is_active = $status_id;
        if ($users->save()) {

            if ($status_id == APP_CASE_STATUS_CLOSE) {
                $user_id = Yii::$app->session->get('[user].id');
                notification::notifyClose($app_case_id, $user_id);
            }
            $this->renderJSON(true);
        } else {
            $this->renderJSON(false);
        }

    }

    /**
     * data for top 5 contractors
     */
    public function actionLineChart($type = NULL, $from = NULL, $to = NULL, $scale = NULL, $jobsite = NULL, $subjobsite = NULL, $building = NULL, $status = NULL, $contractor = NULL, $trade = NULL, $area = NULL, $floor = NULL, $reportType = NULL, $reportTopic = NULL, $recordable = NULL, $injuryType = NULL, $bodyPart = NULL, $lostTime = NULL, $dayWeek = NULL, $timeDayFrom = NULL, $timeDayTo = NULL, $createdby = NULL, $affectedby = NULL, $is_dart = NULL) {
        //For Comma Seperated numbers
        $regex = '/^\d+(?:,\d+)*$/';

        //si es incidente
        if ($type == 3) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " CONVERT(DATE,acx.incident_datetime) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                $join = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
//                else{
            //                    $jobsiteData = ArrayHelper::map( Jobsite::find()->joinWith('userJobsites')->where([ "jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get( "user.id" ) ])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite' );
            //                    $jobsitesData = array();
            //                    if($jobsiteData){
            //                        foreach($jobsiteData as $key => $name){
            //                            $jobsitesData[] = $key;
            //                        }
            //                        $contractorData = ArrayHelper::map( ContractorJobsite::find()->joinWith('contractor')->where( [ "contractor_jobsite.jobsite_id IN (" . implode(",",$jobsitesData) . ")" ] )->asArray()->all(), 'contractor.id', 'contractor.contractor' );
            //                        $contractorsData = array();
            //                        foreach($contractorData as $key => $name){
            //                            $contractorsData[] = $key;
            //                        }
            //                        $queryFilter .= " ac.contractor_id IN (" . implode(",",$contractorsData) . ") AND"  ;
            //
            //                    }
            //                }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (is_numeric($area)) {
                $queryFilter .= " ac.area_id = " . (int) $area . " AND ";
            }
            if (is_numeric($floor)) {
                $queryFilter .= " ac.floor_id = " . (int) $floor . " AND ";
            }
            if (is_numeric($reportType)) {
                $queryFilter .= " acx.report_type_id = " . (int) $reportType . " AND ";
            }
            if (is_numeric($reportTopic)) {
                $queryFilter .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
            }
            if (is_numeric($recordable)) {
                $queryFilter .= " acx.recordable = " . (int) $recordable . " AND ";
            }
            if (is_numeric($injuryType)) {
                $queryFilter .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
            }
            if (is_numeric($bodyPart)) {
                $queryFilter .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
            }
            if (is_numeric($lostTime)) {
                if ($lostTime == 1) {
                    $queryFilter .= " acx.is_lost_time != 0 AND ";
                } else {
                    $queryFilter .= " acx.is_lost_time = 0  AND ";
                }
            }

            if (is_numeric($is_dart)) {
                if ($is_dart == 1) {
                    $queryFilter .= " acx.is_dart = 1 AND ";
                } else {
                    $queryFilter .= " acx.is_dart = 0  AND ";
                }
            }

            if (is_numeric($dayWeek)) {
                $queryFilter .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
            }
            if ($timeDayFrom != 0 || $timeDayTo != 24) {
                $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                $queryFilter .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
            }

            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " CONVERT(DATE,acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }
            // check de escala (por dia o mes) y armado de datos
            $issueTypes = array();
            if ($scale == "day" || is_null($scale)) {
                $betweenDays = Yii::$app->db->createCommand("select CONVERT(DATE,acx.incident_datetime) as created, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by CONVERT(DATE,acx.incident_datetime) order by CONVERT(DATE,acx.incident_datetime) asc")->queryAll();
                foreach ($betweenDays as $days) {
                    $created = $days["created"];
                    $day = date("F j, Y", strtotime($days["created"]));
                    $issueTypes[$day] = Yii::$app->db->createCommand("select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter CONVERT(DATE,acx.incident_datetime) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0")->queryAll();
                }
            } else {
                $betweenWeeks = Yii::$app->db->createCommand("select year(acx.incident_datetime) as anio, month(acx.incident_datetime) as mes, DATEPART(wk,acx.incident_datetime) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.incident_datetime)) -1), acx.incident_datetime))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) hasta, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by acx.incident_datetime order by CONVERT(DATE, acx.incident_datetime) asc")->queryAll();

                foreach ($betweenWeeks as $weeks) {
                    //                    $week = date ("F j, Y", strtotime($weeks[ 'desde' ])) . " to " . date ("F j, Y", strtotime($weeks[ 'hasta' ]));
                    $week = date("F j, Y", strtotime($weeks['desde']));
                    $year = $weeks["anio"];
                    $month = $weeks["mes"];
                    $beginDate = $weeks["desde"];
                    $endDate = $weeks["hasta"];
                    $issueTypes[$week] = Yii::$app->db->createCommand("select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter year(acx.incident_datetime) = '$year' AND month(acx.incident_datetime) = '$month' AND convert(DATE, acx.incident_datetime) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0")->queryAll();
                }
            }

            //si es otro tipo de issue
        } else if (is_numeric($type)) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " CONVERT(DATE,acx.correction_date) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                switch ($type) {
                case 1:
                    $join = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " CONVERT(DATE,acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }
            // check de escala (por dia o mes) y armado de datos
            $issueTypes = array();
            if ($scale == "day" || is_null($scale)) {
                $betweenDays = Yii::$app->db->createCommand("select CONVERT(DATE,acx.correction_date) as created, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by CONVERT(DATE,acx.correction_date) order by CONVERT(DATE,acx.correction_date) asc")->queryAll();
                foreach ($betweenDays as $days) {
                    $created = $days["created"];
                    $day = date("F j, Y", strtotime($days["created"]));
                    $issueTypes[$day] = Yii::$app->db->createCommand("select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter CONVERT(DATE,acx.correction_date) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0")->queryAll();
                }
            } else {
                $betweenWeeks = Yii::$app->db->createCommand("select year(acx.correction_date) as anio, month(acx.correction_date) as mes,DATEPART(wk,acx.correction_date) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.correction_date)) -1), acx.correction_date))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) hasta, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by acx.correction_date order by CONVERT(DATE,acx.correction_date) asc")->queryAll();

                foreach ($betweenWeeks as $weeks) {
                    //                    $week = date ("F j, Y", strtotime($weeks[ 'desde' ])) . " to " . date ("F j, Y", strtotime($weeks[ 'hasta' ]));
                    $week = date("F j, Y", strtotime($weeks['desde']));
                    $year = $weeks["anio"];
                    $month = $weeks["mes"];
                    $beginDate = $weeks["desde"];
                    $endDate = $weeks["hasta"];
                    $issueTypes[$week] = Yii::$app->db->createCommand("select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter year(acx.correction_date) = '$year' AND month(acx.correction_date) = '$month' AND CONVERT(DATE,acx.correction_date) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0")->queryAll();
                }
            }
            //si son todos (all)
        } else {
            $query = array();
            $queryFilter = array();
            $typeFilter = array();
            $join = array();
            $dateFilter = array();
            for ($i = 1; $i <= 4; $i++) {
                $queryFilter[$i] = "";
                $typeFilter[$i] = "";
                $join[$i] = "";
                if ($i == 3) {
                    $dateFilter[$i] = " CONVERT(DATE,acx.incident_datetime) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
                } else {
                    $dateFilter[$i] = " CONVERT(DATE,acx.correction_date) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
                }
                // check de filtros para armar condiciones
                switch ($i) {
                case 1:
                    $join[$i] = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join[$i] = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 3:
                    $join[$i] = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join[$i] = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
                if (is_numeric($status)) {
                    $queryFilter[$i] .= " ac.app_case_status_id = " . (int) $status . " AND ";
                }
                if (is_numeric($contractor)) {
                    $queryFilter[$i] .= " ac.contractor_id = " . (int) $contractor . " AND ";
                }
                if (is_numeric($jobsite)) {
                    $queryFilter[$i] .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
                } else {
                    $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                    $jobsitesData = array();
                    if ($jobsiteData) {
                        foreach ($jobsiteData as $key => $name) {
                            $jobsitesData[] = $key;
                        }
                        if (count($jobsitesData) == 1) {
                            $queryFilter[$i] .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                        } else {
                            $queryFilter[$i] .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                        }
                    } else {
                        $queryFilter[$i] .= " ac.jobsite_id = 99999999999 AND ";
                    }
                }
                if (is_numeric($subjobsite)) {
                    $queryFilter[$i] .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
                }
                if (is_numeric($building)) {
                    $queryFilter[$i] .= " ac.building_id = " . (int) $building . " AND ";
                }
                if (is_numeric($trade)) {
                    $queryFilter[$i] .= " ac.trade_id = " . (int) $trade . " AND ";
                }
                if (preg_match($regex, $createdby ?? "")) {
                    $queryFilter[$i] .= " ac.creator_id IN (" . $createdby . ") AND ";
                }
                if (preg_match($regex, $affectedby ?? "")) {
                    $queryFilter[$i] .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
                }

                if ($i == 3) {
                    if (is_numeric($area)) {
                        $queryFilter[$i] .= " ac.area_id = " . (int) $area . " AND ";
                    }
                    if (is_numeric($floor)) {
                        $queryFilter[$i] .= " ac.floor_id = " . (int) $floor . " AND ";
                    }
                    if (is_numeric($reportType)) {
                        $queryFilter[$i] .= " acx.report_type_id = " . (int) $reportType . " AND ";
                    }
                    if (is_numeric($reportTopic)) {
                        $queryFilter[$i] .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
                    }
                    if (is_numeric($recordable)) {
                        $queryFilter[$i] .= " acx.recordable = " . (int) $recordable . " AND ";
                    }
                    if (is_numeric($injuryType)) {
                        $queryFilter[$i] .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
                    }
                    if (is_numeric($bodyPart)) {
                        $queryFilter[$i] .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
                    }
                    if (is_numeric($lostTime)) {
                        if ($lostTime == 1) {
                            $queryFilter[$i] .= " acx.is_lost_time != 0 AND ";
                        } else {
                            $queryFilter[$i] .= " acx.is_lost_time = 0  AND ";
                        }
                    }

                    if (is_numeric($is_dart)) {
                        if ($is_dart == 1) {
                             $queryFilter .= " acx.is_dart = 1 AND ";
                        } else {
                            $queryFilter .= " acx.is_dart = 0  AND ";
                        }
                    }

                    if (is_numeric($dayWeek)) {
                        $queryFilter[$i] .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
                    }
                    if ($timeDayFrom != 0 || $timeDayTo != 24) {
                        $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                        $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                        $queryFilter[$i] .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
                    }
                }
                if (!is_null($from) && !is_null($to)) {
                    $from_array = date_parse($from);
                    $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                    $to_array = date_parse($to);
                    $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                    if ($i == 3) {
                        $dateFilter[$i] = "  CONVERT(DATE, acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    } else {
                        $dateFilter[$i] = "  CONVERT(DATE, acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    }
                }
                if ($i == 3) {
                    $query[$i] = "select  CONVERT(DATE, acx.incident_datetime) as created, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by  CONVERT(DATE, acx.incident_datetime)";
                } else {
                    $query[$i] = "select  CONVERT(DATE, acx.correction_date) as created, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by  CONVERT(DATE, acx.correction_date)";
                }
            }

            // check de escala (por dia o mes) y armado de datos
            $issueTypes = array();
            if ($scale == "day" || is_null($scale)) {
                $betweenDaysQuery = "SELECT issues.created, SUM(issues.cantidad) AS cantidad FROM ( " . implode(" UNION ALL ", $query) . ") AS issues GROUP BY created ORDER BY created;";
                $betweenDays = Yii::$app->db->createCommand($betweenDaysQuery)->queryAll();
                foreach ($betweenDays as $days) {
                    $created = $days["created"];
                    $day = date("F j, Y", strtotime($days["created"]));
                    $query = array();
                    for ($i = 1; $i <= 4; $i++) {
                        if ($i == 3) {
                            $query[$i] = "select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i]  CONVERT(DATE, acx.incident_datetime) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        } else {
                            $query[$i] = "select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] CONVERT(DATE,acx.correction_date) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        }
                    }
                    $issueTypesQuery = "( " . implode(" )UNION ALL( ", $query) . ");";
                    $issueTypes[$day] = Yii::$app->db->createCommand($issueTypesQuery)->queryAll();
                }
            } else {
                $query = array();
                for ($i = 1; $i <= 4; $i++) {
                    if ($i == 3) {
                        $query[$i] = "select year(acx.incident_datetime) as anio, month(acx.incident_datetime) as mes,DATEPART(wk,acx.incident_datetime) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.incident_datetime)) -1), acx.incident_datetime))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) hasta, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by acx.incident_datetime ";

                    } else {
                        $query[$i] = "select year(acx.correction_date) as anio, month(acx.correction_date) as mes, DATEPART(wk,acx.correction_date) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.correction_date)) -1), acx.correction_date))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) hasta, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by acx.correction_date ";

                    }
                }
                $betweenWeeksQuery = "SELECT issues.anio, issues.mes, issues.semana, issues.desde, issues.hasta, SUM(issues.cantidad) AS cantidad FROM ( " . implode(" UNION ALL ", $query) . ") AS issues group by anio, mes, semana,desde,hasta,cantidad";
                $betweenWeeks = Yii::$app->db->createCommand($betweenWeeksQuery)->queryAll();
                foreach ($betweenWeeks as $weeks) {
                    //                    $week = date ("F j, Y", strtotime($weeks[ 'desde' ])) . " to " . date ("F j, Y", strtotime($weeks[ 'hasta' ]));
                    $week = date("F j, Y", strtotime($weeks['desde']));
                    $year = $weeks["anio"];
                    $month = $weeks["mes"];
                    $beginDate = $weeks["desde"];
                    $endDate = $weeks["hasta"];
                    $query = array();
                    for ($i = 1; $i <= 4; $i++) {
                        if ($i == 3) {
                            $query[$i] = "select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] year(acx.incident_datetime) = '$year' AND month(acx.incident_datetime) = '$month' AND  CONVERT(DATE, acx.incident_datetime) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        } else {
                            $query[$i] = "select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] year(acx.correction_date) = '$year' AND month(acx.correction_date) = '$month' AND  CONVERT(DATE, acx.correction_date) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        }
                    }
                    $issueTypesQuery = "( " . implode(" )UNION ALL( ", $query) . ");";
                    $issueTypes[$week] = Yii::$app->db->createCommand($issueTypesQuery)->queryAll();
                }
            }

        }

        $rows = array();
        $table = array();
        $table["cols"] = array(
            array(
                'id' => '',
                'label' => 'Date',
                'type' => 'string',
            ),
            array(
                'id' => '',
                'label' => 'Violations',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'Recognitions',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'Incidents',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'Observations',
                'type' => 'number',
            ),
        );

        foreach ($issueTypes as $key => $value) {
            $temp = array();
//                $key = date ("F j, Y", strtotime($key));

//                $date = date ("Y-m-d", strtotime($key));
            //                $orderdate = explode('-', $date);
            //                $m = $orderdate[0];
            //                $d   = $orderdate[1];
            //                $y = $orderdate[2];
            //                $key = "Date($m, $d, $y)";
            $temp[] = array('v' => $key);
            foreach ($value as $llave => $valor) {
                $temp[$valor["app_case_type_id"]] = array('v' => (int) $valor["cantidad"]);
            }
            $rows[] = array('c' => $temp);
        }

        $table["rows"] = $rows;

//            var_dump(json_encode($table, true));
        //            exit;

        $lineChart = json_encode($table, true);
        exit($lineChart);
    }

    /**
     * Dashboard Issues CSV (Excel) Download
     */
    public function actionIssuesDetail($type = NULL, $from = NULL, $to = NULL, $scale = NULL, $jobsite = NULL, $subjobsite = NULL, $building = NULL, $status = NULL, $contractor = NULL, $trade = NULL, $area = NULL, $floor = NULL, $reportType = NULL, $reportTopic = NULL, $recordable = NULL, $injuryType = NULL, $bodyPart = NULL, $lostTime = NULL, $dayWeek = NULL, $timeDayFrom = NULL, $timeDayTo = NULL, $createdby = NULL, $affectedby = NULL, $is_dart = NULL) {
        //For Comma Seperated numbers
        $regex = '/^\d+(?:,\d+)*$/';


        /*--------------For Incident------------------------*/
        if ($type == 3) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " CONVERT(DATE,acx.incident_datetime) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                $join = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (is_numeric($area)) {
                $queryFilter .= " ac.area_id = " . (int) $area . " AND ";
            }
            if (is_numeric($floor)) {
                $queryFilter .= " ac.floor_id = " . (int) $floor . " AND ";
            }
            if (is_numeric($reportType)) {
                $queryFilter .= " acx.report_type_id = " . (int) $reportType . " AND ";
            }
            if (is_numeric($reportTopic)) {
                $queryFilter .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
            }
            if (is_numeric($recordable)) {
                $queryFilter .= " acx.recordable = " . (int) $recordable . " AND ";
            }
            if (is_numeric($injuryType)) {
                $queryFilter .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
            }
            if (is_numeric($bodyPart)) {
                $queryFilter .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
            }
            if (is_numeric($lostTime)) {
                if ($lostTime == 1) {
                    $queryFilter .= " acx.is_lost_time != 0 AND ";
                } else {
                    $queryFilter .= " acx.is_lost_time = 0  AND ";
                }
            }
            if (is_numeric($is_dart)) {
                if ($is_dart == 1) {
                     $queryFilter .= " acx.is_dart = 1 AND ";
                } else {
                    $queryFilter .= " acx.is_dart = 0  AND ";
                }
            }
            if (is_numeric($dayWeek)) {
                $queryFilter .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
            }
            if ($timeDayFrom != 0 || $timeDayTo != 24) {
                $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                $queryFilter .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
            }

            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " CONVERT(DATE,acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }
            // scale check (per day or month) and data assembly
            $issueTypes = array();
            if ($scale == "day" || is_null($scale)) {
                $betweenDays = Yii::$app->db->createCommand("select CONVERT(DATE,acx.incident_datetime) as created, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by CONVERT(DATE,acx.incident_datetime),ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id order by CONVERT(DATE,acx.incident_datetime) asc ")->queryAll();
                //echo "query1 </br>"."select CONVERT(DATE,acx.incident_datetime) as created, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by CONVERT(DATE,acx.incident_datetime) order by CONVERT(DATE,acx.incident_datetime) asc" ;
                foreach ($betweenDays as $days) {
                    $created = $days["created"];
                    $day = date("F j, Y", strtotime($days["created"]));
                    $issueTypes[$day] = Yii::$app->db->createCommand("select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter CONVERT(DATE,acx.incident_datetime) = '$created' and ac.is_active = 1 group by ac.app_case_type_id,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0")->queryAll();
                    //echo "query2 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter CONVERT(DATE,acx.incident_datetime) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                }
            } else {
                $betweenWeeks = Yii::$app->db->createCommand("select year(acx.incident_datetime) as anio, month(acx.incident_datetime) as mes, DATEPART(wk,acx.incident_datetime) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.incident_datetime)) -1), acx.incident_datetime))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) hasta, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by acx.incident_datetime,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id order by acx.incident_datetime asc ")->queryAll();
                //echo "query3 </br>"."select year(acx.incident_datetime) as anio, month(acx.incident_datetime) as mes, WEEK(acx.incident_datetime) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.incident_datetime)) -1), acx.incident_datetime))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) hasta, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by anio, mes, semana order by date(acx.incident_datetime) asc";
                foreach ($betweenWeeks as $weeks) {
                    //                    $week = date ("F j, Y", strtotime($weeks[ 'desde' ])) . " to " . date ("F j, Y", strtotime($weeks[ 'hasta' ]));
                    $week = date("F j, Y", strtotime($weeks['desde']));
                    $year = $weeks["anio"];
                    $month = $weeks["mes"];
                    $beginDate = $weeks["desde"];
                    $endDate = $weeks["hasta"];
                    $issueTypes[$week] = Yii::$app->db->createCommand("select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter year(acx.incident_datetime) = '$year' AND month(acx.incident_datetime) = '$month' AND acx.incident_datetime between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0")->queryAll();
                    //echo "query4 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter year(acx.incident_datetime) = '$year' AND month(acx.incident_datetime) = '$month' AND date(acx.incident_datetime) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                }
            }

            //si es otro tipo de issue
        } else if (is_numeric($type)) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " CONVERT(DATE,acx.correction_date) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                switch ($type) {
                case 1:
                    $join = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " CONVERT(DATE,acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }
            // check de escala (por dia o mes) y armado de datos
            $issueTypes = array();
            if ($scale == "day" || is_null($scale)) {
                $betweenDays = Yii::$app->db->createCommand("select CONVERT(DATE,acx.correction_date) as created, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by CONVERT(DATE,acx.correction_date),ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id order by CONVERT(DATE,acx.correction_date) asc ")->queryAll();
                // echo "query5 </br>"."select CONVERT(DATE,acx.correction_date) as created, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by CONVERT(DATE,acx.correction_date) order by CONVERT(DATE,acx.correction_date) asc";
                foreach ($betweenDays as $days) {
                    $created = $days["created"];
                    $day = date("F j, Y", strtotime($days["created"]));
                    $issueTypes[$day] = Yii::$app->db->createCommand("select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter CONVERT(DATE,acx.correction_date) = '$created' and ac.is_active = 1 group by ac.app_case_type_id,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0")->queryAll();
                    // echo "query6 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter CONVERT(DATE,acx.correction_date) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                }
            } else {
                $betweenWeeks = Yii::$app->db->createCommand("select year(acx.correction_date) as anio, month(acx.correction_date) as mes, DATEPART(wk,acx.correction_date) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.correction_date)) -1), acx.correction_date))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) hasta, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by anio, mes, semana,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id order by CONVERT(DATE,acx.correction_date) asc")->queryAll();
                // echo "query7 </br>"."select year(acx.correction_date) as anio, month(acx.correction_date) as mes, WEEK(acx.correction_date) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.correction_date)) -1), acx.correction_date))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) hasta, count(ac.id) as cantidad from app_case ac $join where $queryFilter $dateFilter and ac.is_active = 1 group by anio, mes, semana order by CONVERT(DATE,acx.correction_date) asc";
                foreach ($betweenWeeks as $weeks) {
                    //                    $week = date ("F j, Y", strtotime($weeks[ 'desde' ])) . " to " . date ("F j, Y", strtotime($weeks[ 'hasta' ]));
                    $week = date("F j, Y", strtotime($weeks['desde']));
                    $year = $weeks["anio"];
                    $month = $weeks["mes"];
                    $beginDate = $weeks["desde"];
                    $endDate = $weeks["hasta"];
                    $issueTypes[$week] = Yii::$app->db->createCommand("select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join where $queryFilter year(acx.correction_date) = '$year' AND month(acx.correction_date) = '$month' AND CONVERT(DATE,acx.correction_date) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0")->queryAll();
                    //echo "query8 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter year(acx.correction_date) = '$year' AND month(acx.correction_date) = '$month' AND CONVERT(DATE,acx.correction_date) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                }
            }
            //si son todos (all)
        } else {
            $query = array();
            $queryFilter = array();
            $typeFilter = array();
            $join = array();
            $dateFilter = array();
            for ($i = 1; $i <= 4; $i++) {
                $queryFilter[$i] = "";
                $typeFilter[$i] = "";
                $join[$i] = "";

                /*--------------For Incident------------------------*/
                if ($i == 3) {
                    $dateFilter[$i] = " CONVERT(DATE,acx.incident_datetime) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
                } else {
                    $dateFilter[$i] = " CONVERT(DATE,acx.correction_date) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
                }
                // check de filtros para armar condiciones
                switch ($i) {
                case 1:
                    $join[$i] = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join[$i] = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 3:
                    $join[$i] = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join[$i] = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
                if (is_numeric($status)) {
                    $queryFilter[$i] .= " ac.app_case_status_id = " . (int) $status . " AND ";
                }
                if (is_numeric($contractor)) {
                    $queryFilter[$i] .= " ac.contractor_id = " . (int) $contractor . " AND ";
                }
                if (is_numeric($jobsite)) {
                    $queryFilter[$i] .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
                } else {
                    $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                    $jobsitesData = array();
                    if ($jobsiteData) {
                        foreach ($jobsiteData as $key => $name) {
                            $jobsitesData[] = $key;
                        }
                        if (count($jobsitesData) == 1) {
                            $queryFilter[$i] .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                        } else {
                            $queryFilter[$i] .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                        }
                    } else {
                        $queryFilter[$i] .= " ac.jobsite_id = 99999999999 AND ";
                    }
                }
                if (is_numeric($subjobsite)) {
                    $queryFilter[$i] .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
                }
                if (is_numeric($building)) {
                    $queryFilter[$i] .= " ac.building_id = " . (int) $building . " AND ";
                }
                if (is_numeric($trade)) {
                    $queryFilter[$i] .= " ac.trade_id = " . (int) $trade . " AND ";
                }
                if (preg_match($regex, $createdby)) {
                    $queryFilter[$i] .= " ac.creator_id IN (" . $createdby . ") AND ";
                }
                if (preg_match($regex, $affectedby)) {
                    $queryFilter[$i] .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
                }

                /*--------------For Incident------------------------*/
                if ($i == 3) {
                    if (is_numeric($area)) {
                        $queryFilter[$i] .= " ac.area_id = " . (int) $area . " AND ";
                    }
                    if (is_numeric($floor)) {
                        $queryFilter[$i] .= " ac.floor_id = " . (int) $floor . " AND ";
                    }
                    if (is_numeric($reportType)) {
                        $queryFilter[$i] .= " acx.report_type_id = " . (int) $reportType . " AND ";
                    }
                    if (is_numeric($reportTopic)) {
                        $queryFilter[$i] .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
                    }
                    if (is_numeric($recordable)) {
                        $queryFilter[$i] .= " acx.recordable = " . (int) $recordable . " AND ";
                    }
                    if (is_numeric($injuryType)) {
                        $queryFilter[$i] .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
                    }
                    if (is_numeric($bodyPart)) {
                        $queryFilter[$i] .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
                    }
                    if (is_numeric($lostTime)) {
                        if ($lostTime == 1) {
                            $queryFilter[$i] .= " acx.is_lost_time != 0 AND ";
                        } else {
                            $queryFilter[$i] .= " acx.is_lost_time = 0  AND ";
                        }
                    }
                    if (is_numeric($is_dart)) {
                        if ($is_dart == 1) {
                             $queryFilter .= " acx.is_dart = 1 AND ";
                        } else {
                            $queryFilter .= " acx.is_dart = 0  AND ";
                        }
                    }
                    if (is_numeric($dayWeek)) {
                        $queryFilter[$i] .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
                    }
                    if ($timeDayFrom != 0 || $timeDayTo != 24) {
                        $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                        $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                        $queryFilter[$i] .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
                    }
                }
                if (!is_null($from) && !is_null($to)) {
                    $from_array = date_parse($from);
                    $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                    $to_array = date_parse($to);
                    $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                    if ($i == 3) {
                        $dateFilter[$i] = "  CONVERT(DATE, acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    } else {
                        $dateFilter[$i] = "  CONVERT(DATE, acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    }
                }
                if ($i == 3) {
                    $query[$i] = "select  CONVERT(DATE, acx.incident_datetime) as created, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by  CONVERT(DATE, acx.incident_datetime),ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id ";
                    //echo "query9 </br>"."select  CONVERT(DATE, acx.incident_datetime) as created, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by  CONVERT(DATE, acx.incident_datetime)";
                } else {
                    $query[$i] = "select  CONVERT(DATE, acx.correction_date) as created, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by  CONVERT(DATE, acx.correction_date), ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id ";
                    //echo "query10 </br>"."select  CONVERT(DATE, acx.correction_date) as created, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by  CONVERT(DATE, acx.correction_date)";
                }
            }

            // check de escala (por dia o mes) y armado de datos
            $issueTypes = array();
            if ($scale == "day" || is_null($scale)) {
                $betweenDaysQuery = "SELECT issues.created FROM ( " . implode(" UNION ALL ", $query) . ") AS issues GROUP BY created ORDER BY created;";
                $betweenDays = Yii::$app->db->createCommand($betweenDaysQuery)->queryAll();
                foreach ($betweenDays as $days) {
                    $created = $days["created"];
                    $day = date("F j, Y", strtotime($days["created"]));
                    $query = array();
                    for ($i = 1; $i <= 4; $i++) {
                        if ($i == 3) {
                            $query[$i] = "select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i]  CONVERT(DATE, acx.incident_datetime) = '$created' and ac.is_active = 1 group by ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0";
                            //echo "query11 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i]  CONVERT(DATE, acx.incident_datetime) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        } else {
                            $query[$i] = "select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i] CONVERT(DATE,acx.correction_date) = '$created' and ac.is_active = 1 group by ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0";
                            //echo "query12 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] CONVERT(DATE,acx.correction_date) = '$created' and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        }
                    }
                    $issueTypesQuery = "( " . implode(" )UNION ALL( ", $query) . ");";
                    $issueTypes[$day] = Yii::$app->db->createCommand($issueTypesQuery)->queryAll();
                }
            } else {
                $query = array();
                for ($i = 1; $i <= 4; $i++) {
                    if ($i == 3) {
                        $query[$i] = "select year(acx.incident_datetime) as anio, month(acx.incident_datetime) as mes, DATEPART(wk,acx.incident_datetime) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.incident_datetime)) -1), acx.incident_datetime))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) hasta, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by acx.incident_datetime,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  ";
                        // echo "query13 </br>"."select year(acx.incident_datetime) as anio, month(acx.incident_datetime) as mes, WEEK(acx.incident_datetime) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.incident_datetime)) -1), acx.incident_datetime))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.incident_datetime)),  CONVERT(DATE,acx.incident_datetime)) hasta, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by anio, mes, semana";
                    } else {
                        $query[$i] = "select year(acx.correction_date) as anio, month(acx.correction_date) as mes, DATEPART(wk,acx.correction_date) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.correction_date)) -1), acx.correction_date))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) hasta, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by acx.correction_date, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  ";
                        // echo "query14 </br>"."select year(acx.correction_date) as anio, month(acx.correction_date) as mes, WEEK(acx.correction_date) - DATEPART(wk,CONVERT(DATE, DATEADD(DAY, -(DATEPART(dd,CONVERT(DATE,acx.correction_date)) -1), acx.correction_date))) + 1 as semana, DATEADD(day, 1-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) desde, DATEADD(day, 7-DATEPART(dw,CONVERT(DATE,acx.correction_date)),  CONVERT(DATE,acx.correction_date)) hasta, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 group by anio, mes, semana";
                    }
                }
                $betweenWeeksQuery = "SELECT issues.anio, issues.mes, issues.semana, issues.desde, issues.hasta FROM ( " . implode(" UNION ALL ", $query) . ") AS issues group by anio, mes, semana, desde, hasta";

                $betweenWeeks = Yii::$app->db->createCommand($betweenWeeksQuery)->queryAll();
                foreach ($betweenWeeks as $weeks) {
                    //                    $week = date ("F j, Y", strtotime($weeks[ 'desde' ])) . " to " . date ("F j, Y", strtotime($weeks[ 'hasta' ]));
                    $week = date("F j, Y", strtotime($weeks['desde']));
                    $year = $weeks["anio"];
                    $month = $weeks["mes"];
                    $beginDate = $weeks["desde"];
                    $endDate = $weeks["hasta"];
                    $query = array();
                    for ($i = 1; $i <= 4; $i++) {
                        if ($i == 3) {
                            $query[$i] = "select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i] year(acx.incident_datetime) = '$year' AND month(acx.incident_datetime) = '$month' AND  CONVERT(DATE, acx.incident_datetime) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0";
                            // echo "query15 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] year(acx.incident_datetime) = '$year' AND month(acx.incident_datetime) = '$month' AND  CONVERT(DATE, acx.incident_datetime) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        } else {
                            $query[$i] = "select ac.app_case_type_id, ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id from app_case ac $join[$i] where $queryFilter[$i] year(acx.correction_date) = '$year' AND month(acx.correction_date) = '$month' AND  CONVERT(DATE, acx.correction_date) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id,ac.creator_id, ac.jobsite_id, ac.contractor_id, ac.app_case_status_id, ac.additional_information,ac.app_case_sf_code_id,ac.affected_user_id,ac.id,ac.building_id, ac.sub_jobsite_id  having count(ac.id) > 0";
                            // echo "query16 </br>"."select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] year(acx.correction_date) = '$year' AND month(acx.correction_date) = '$month' AND  CONVERT(DATE, acx.correction_date) between '$beginDate' and '$endDate'  and ac.is_active = 1 group by ac.app_case_type_id having count(ac.id) > 0";
                        }
                    }
                    $issueTypesQuery = "( " . implode(" )UNION ALL( ", $query) . ");";
                    $issueTypes[$week] = Yii::$app->db->createCommand($issueTypesQuery)->queryAll();
                }
            }

        }

        $rows = array();
        // print_r($issueTypes); exit;
        foreach ($issueTypes as $key => $value) {

            foreach ($value as $key1 => $value1) {
                $temp = array();
                $temp['Date'] = date('m-d-Y', strtotime($key));
                $type = Yii::$app->db->createCommand(" select type from app_case_type where id =" . $value1["app_case_type_id"])->queryAll();
                $creater = Yii::$app->db->createCommand(" select first_name, last_name from [dbo].[user] where id =" . $value1["creator_id"])->queryAll();
                $jobsite = Yii::$app->db->createCommand(" select jobsite from jobsite where id =" . $value1["jobsite_id"])->queryAll();
                $contractor = Yii::$app->db->createCommand(" select contractor from contractor where id =" . $value1["contractor_id"])->queryAll();
                $affecteduser = Yii::$app->db->createCommand(" select first_name, last_name from [dbo].[user] where id =" . $value1["affected_user_id"])->queryAll();
                $appcasestatus = Yii::$app->db->createCommand(" select status from app_case_status where id =" . $value1["app_case_status_id"])->queryAll();
                $appcasesfcode = Yii::$app->db->createCommand(" select description, code from app_case_sf_code where id =" . $value1["app_case_sf_code_id"])->queryAll();
                $appcasebuilding = Yii::$app->db->createCommand(" select building, description from building where id =" . $value1["building_id"])->queryAll();
                if (is_numeric($value1["sub_jobsite_id"])) {
                    $sub_jobsite_id = Yii::$app->db->createCommand(" select subjobsite from sub_jobsite where id =" . $value1["sub_jobsite_id"])->queryAll();
                } else {
                    $sub_jobsite_id[0]["subjobsite"] = "-";
                }

                //var_dump( $sub_jobsite_id); exit();
                //print_r($value1) ;exit;
                $description = $value1['additional_information'];
                $add_info = "-";
                if ($value1["app_case_type_id"] == 3) {
                    $add_info = $value1['additional_information'];
                    $description = "-";
                }

                $temp['IssueId'] = $value1["id"];
                $temp['Issue-Type'] = $type[0]['type'];
                $temp['Status'] = $appcasestatus[0]['status'];
                $temp['Jobsite'] = $jobsite[0]['jobsite'];
                $temp['Sub Jobsite'] = $sub_jobsite_id[0]["subjobsite"];
                $temp['Building'] = $appcasebuilding[0]['building'] . ' ' . $appcasebuilding[0]['description'];
                $temp['Owner'] = $creater[0]['first_name'] . ' ' . $creater[0]['last_name'];
                $temp['AffectedUser'] = $affecteduser[0]['first_name'] . ' ' . $affecteduser[0]['last_name'];
                $temp['Contractor'] = $contractor[0]['contractor'];
                $temp['Description'] = $description;
                $temp['AdditionalInformation'] = $add_info;
                $temp['Safety Code'] = $appcasesfcode[0]['code'] . '-' . $appcasesfcode[0]['description'];

                $reporttopic = '-';
                $recordable = '-';
                $losttime = '-';
                $pd = '-';
                $causationfactor = '-';
                $dart = '-';
                $bodypart = '-';
                if ($value1["app_case_type_id"] == 3) {
                    $reporttopicarr = Yii::$app->db->createCommand(" select case
                                when aci.is_lost_time = 1 then 'YES'
                                when aci.is_lost_time = 0 then 'NO'
                                else '-'
                            end AS lost_time, case
                                when aci.is_property_damage = 1 then 'YES'
                                when aci.is_property_damage = 0 then 'NO'
                                else '-'
                            end AS is_property_damage, case
                                when aci.recordable = 1 then 'YES'
                                when aci.recordable = 0 then 'NO'
                                else '-'
                            end AS recordable,case
                                when aci.is_dart = 1 then 'YES'
                                when aci.is_dart = 0 then 'NO'
                                else '-'
                            end AS dart,cf.causation_factor, rt.report_topic, bp.body_part from [dbo].[app_case_incident]  aci join report_topic rt on rt.id = aci.report_topic_id join body_part bp on bp.id = aci.body_part_id JOIN causation_factor cf ON cf.id = aci.causation_factor where aci.app_case_id   =" . $value1["id"])->queryAll();
                    if (isset($reporttopicarr[0]['report_topic'])) {
                        $reporttopic = $reporttopicarr[0]['report_topic'];
                    }

                    if (isset($reporttopicarr[0]['recordable'])) {
                        $recordable = $reporttopicarr[0]['recordable'];
                    }

                    if (isset($reporttopicarr[0]['lost_time'])) {
                        $losttime = $reporttopicarr[0]['lost_time'];
                    }

                    if (isset($reporttopicarr[0]['is_property_damage'])) {
                        $pd = $reporttopicarr[0]['is_property_damage'];
                    }

                    if (isset($reporttopicarr[0]['causation_factor'])) {
                        $causationfactor = $reporttopicarr[0]['causation_factor'];
                    }

                    if (isset($reporttopicarr[0]['dart'])) {
                        $dart = $reporttopicarr[0]['dart'];
                    }

                    if (isset($reporttopicarr[0]['body_part'])) {
                        $bodypart = $reporttopicarr[0]['body_part'];
                    }

                }
                $temp['Causation Factor'] = $causationfactor;
                $temp['ReportTopic'] = $reporttopic;
                $temp['Recordable'] = $recordable;
                $temp['DART'] = $dart;
                $temp['LostTime'] = $losttime;
                $temp['PropertyDamage'] = $pd;
                $temp['Body Part'] = $bodypart;
                $rows[] = $temp;
            }

        }
        $CSVIssues = json_encode($rows, true);
        exit($CSVIssues);

/*            $filename = "Issues.csv";
$f = fopen('php://memory', 'w');
// loop over the input array
if(isset($rows['0']))
fputcsv($f, array_keys($rows['0']));

foreach($rows AS $values){
fputcsv($f, $values);
}

// reset the file pointer to the start of the file
fseek($f, 0);
// tell the browser it's going to be a csv file
header('Content-Type: application/csv');
// tell the browser we want to save it instead of displaying it
header('Content-Disposition: attachment; filename="'.$filename.'";');
// make php send the generated csv lines to the browser
fpassthru($f);*/
        exit();
    }
    /**
     * data for top 5 contractors
     */
    public function actionTopContractors($type = NULL, $from = NULL, $to = NULL, $scale = NULL, $jobsite = NULL, $subjobsite = NULL, $building = NULL, $status = NULL, $contractor = NULL, $trade = NULL, $area = NULL, $floor = NULL, $reportType = NULL, $reportTopic = NULL, $recordable = NULL, $injuryType = NULL, $bodyPart = NULL, $lostTime = NULL, $dayWeek = NULL, $timeDayFrom = NULL, $timeDayTo = NULL, $createdby = NULL, $affectedby = NULL, $is_dart = NULL) {

        //For Comma Seperated numbers
        $regex = '/^\d+(?:,\d+)*$/';

        //si es incidente
        if ($type == 3) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " convert(date,acx.incident_datetime) BETWEEN convert(date,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                $join = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (is_numeric($area)) {
                $queryFilter .= " ac.area_id = " . (int) $area . " AND ";
            }
            if (is_numeric($floor)) {
                $queryFilter .= " ac.floor_id = " . (int) $floor . " AND ";
            }
            if (is_numeric($reportType)) {
                $queryFilter .= " acx.report_type_id = " . (int) $reportType . " AND ";
            }
            if (is_numeric($reportTopic)) {
                $queryFilter .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
            }
            if (is_numeric($recordable)) {
                $queryFilter .= " acx.recordable = " . (int) $recordable . " AND ";
            }
            if (is_numeric($injuryType)) {
                $queryFilter .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
            }
            if (is_numeric($bodyPart)) {
                $queryFilter .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
            }
            if (is_numeric($lostTime)) {
                if ($lostTime == 1) {
                    $queryFilter .= " acx.is_lost_time != 0 AND ";
                } else {
                    $queryFilter .= " acx.is_lost_time = 0  AND ";
                }
            }
            if (is_numeric($is_dart)) {
                if ($is_dart == 1) {
                     $queryFilter .= " acx.is_dart = 1 AND ";
                } else {
                    $queryFilter .= " acx.is_dart = 0  AND ";
                }
            }
            if (is_numeric($dayWeek)) {
                $queryFilter .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
            }
            if ($timeDayFrom != 0 || $timeDayTo != 24) {
                $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                $queryFilter .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
            }

            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " convert(date,acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }

            $topContractors = Yii::$app->db->createCommand("select distinct top 5 ac.contractor_id, c.contractor, count(ac.id) as cantidad from app_case ac join contractor c on ac.contractor_id = c.id $join WHERE $queryFilter $dateFilter and ac.is_active = 1  group by ac.contractor_id,c.contractor having count(ac.id) > 0 order by cantidad desc  ")->queryAll();
            $issueTypes = array();
            foreach ($topContractors as $contractor) {
                $contractor_id = $contractor["contractor_id"];
                $issueTypes[$contractor["contractor"]] = Yii::$app->db->createCommand("select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter ac.contractor_id = '$contractor_id' AND $dateFilter  and ac.is_active = 1  group by ac.app_case_type_id having  count(ac.id) > 0")->queryAll();
            }
            //si es otro tipo de issue
        } else if (is_numeric($type)) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " convert(date, acx.correction_date) BETWEEN convert(date,DATEADD(month, -1, GETDATE())) AND convert(date, getdate()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                switch ($type) {
                case 1:
                    $join = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " convert(date, acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }
            $topContractors = Yii::$app->db->createCommand("select distinct top 5 ac.contractor_id, c.contractor, count(ac.id) as cantidad from app_case ac join contractor c on ac.contractor_id = c.id $join WHERE $queryFilter $dateFilter and ac.is_active = 1  group by ac.contractor_id,c.contractor having count(ac.id) > 0 order by cantidad desc ")->queryAll();
            $issueTypes = array();
            foreach ($topContractors as $contractor) {
                $contractor_id = $contractor["contractor_id"];
                $issueTypes[$contractor["contractor"]] = Yii::$app->db->createCommand("select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join where $queryFilter ac.contractor_id = '$contractor_id' AND $dateFilter  and ac.is_active = 1  group by ac.app_case_type_id having  count(ac.id) > 0")->queryAll();
            }
            //si son todos (all)
        } else {
            $query = array();
            $queryFilter = array();
            $typeFilter = array();
            $join = array();
            $dateFilter = array();
            for ($i = 1; $i <= 4; $i++) {
                $queryFilter[$i] = "";
                $typeFilter[$i] = "";
                $join[$i] = "";
                if ($i == 3) {
                    $dateFilter[$i] = " convert(date,acx.incident_datetime) BETWEEN convert(date,DATEADD(month, -1, GETDATE())) AND convert(date, getdate()) ";
                } else {
                    $dateFilter[$i] = " convert(date,acx.correction_date) BETWEEN convert(date,DATEADD(month, -1, GETDATE())) AND convert(date, getdate()) ";
                }
                // check de filtros para armar condiciones
                switch ($i) {
                case 1:
                    $join[$i] = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join[$i] = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 3:
                    $join[$i] = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join[$i] = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
                if (is_numeric($status)) {
                    $queryFilter[$i] .= " ac.app_case_status_id = " . (int) $status . " AND ";
                }
                if (is_numeric($contractor)) {
                    $queryFilter[$i] .= " ac.contractor_id = " . (int) $contractor . " AND ";
                }
                if (is_numeric($jobsite)) {
                    $queryFilter[$i] .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
                } else {
                    $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                    $jobsitesData = array();
                    if ($jobsiteData) {
                        foreach ($jobsiteData as $key => $name) {
                            $jobsitesData[] = $key;
                        }
                        if (count($jobsitesData) == 1) {
                            $queryFilter[$i] .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                        } else {
                            $queryFilter[$i] .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                        }
                    } else {
                        $queryFilter[$i] .= " ac.jobsite_id = 99999999999 AND ";
                    }
                }
                if (is_numeric($subjobsite)) {
                    $queryFilter[$i] .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
                }
                if (is_numeric($building)) {
                    $queryFilter[$i] .= " ac.building_id = " . (int) $building . " AND ";
                }
                if (is_numeric($trade)) {
                    $queryFilter[$i] .= " ac.trade_id = " . (int) $trade . " AND ";
                }
                if (preg_match($regex, $createdby ?? "")) {
                    $queryFilter[$i] .= " ac.creator_id IN (" . $createdby . ") AND ";
                }
                if (preg_match($regex, $affectedby ?? "")) {
                    $queryFilter[$i] .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
                }

                if ($i == 3) {
                    if (is_numeric($area)) {
                        $queryFilter[$i] .= " ac.area_id = " . (int) $area . " AND ";
                    }
                    if (is_numeric($floor)) {
                        $queryFilter[$i] .= " ac.floor_id = " . (int) $floor . " AND ";
                    }
                    if (is_numeric($reportType)) {
                        $queryFilter[$i] .= " acx.report_type_id = " . (int) $reportType . " AND ";
                    }
                    if (is_numeric($reportTopic)) {
                        $queryFilter[$i] .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
                    }
                    if (is_numeric($recordable)) {
                        $queryFilter[$i] .= " acx.recordable = " . (int) $recordable . " AND ";
                    }
                    if (is_numeric($injuryType)) {
                        $queryFilter[$i] .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
                    }
                    if (is_numeric($bodyPart)) {
                        $queryFilter[$i] .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
                    }
                    if (is_numeric($lostTime)) {
                        if ($lostTime == 1) {
                            $queryFilter[$i] .= " acx.is_lost_time != 0 AND ";
                        } else {
                            $queryFilter[$i] .= " acx.is_lost_time = 0  AND ";
                        }
                    }
                    if (is_numeric($is_dart)) {
                        if ($is_dart == 1) {
                             $queryFilter .= " acx.is_dart = 1 AND ";
                        } else {
                            $queryFilter .= " acx.is_dart = 0  AND ";
                        }
                    }
                    if (is_numeric($dayWeek)) {
                        $queryFilter[$i] .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
                    }
                    if ($timeDayFrom != 0 || $timeDayTo != 24) {
                        $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                        $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                        $queryFilter[$i] .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
                    }
                }
                if (!is_null($from) && !is_null($to)) {
                    $from_array = date_parse($from);
                    $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                    $to_array = date_parse($to);
                    $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                    if ($i == 3) {
                        $dateFilter[$i] = " convert(date,acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    } else {
                        $dateFilter[$i] = " convert(date,acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    }
                }
                $query[$i] = "select distinct ac.contractor_id, c.contractor, count(ac.id) as cantidad from app_case ac join contractor c on ac.contractor_id = c.id $join[$i] WHERE $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1  group by ac.contractor_id,c.contractor having count(ac.id) > 0 ";
            }
            $topContractorsQuery = "select top 5 issues.contractor_id, issues.contractor, sum(issues.cantidad) as cantidad from (" . implode(' UNION ALL ', $query) . ") as issues group by issues.contractor_id,issues.contractor order by cantidad desc";
            $topContractors = Yii::$app->db->createCommand($topContractorsQuery)->queryAll();
            $issueTypes = array();
            foreach ($topContractors as $contractor) {
                $contractor_id = $contractor["contractor_id"];
                $query = array();
                for ($i = 1; $i <= 4; $i++) {
                    $query[$i] = "select ac.app_case_type_id, count(ac.id) as cantidad from app_case ac $join[$i] where $queryFilter[$i] ac.contractor_id = '$contractor_id' AND $dateFilter[$i] and ac.is_active = 1  group by ac.app_case_type_id having count(ac.id) > 0";
                }
                $issueTypesQuery = "( " . implode(" )UNION ALL( ", $query) . ");";
                $issueTypes[$contractor["contractor"]] = Yii::$app->db->createCommand($issueTypesQuery)->queryAll();
            }
        }

        $rows = array();
        $table = array();
        $table["cols"] = array(
            array(
                'id' => '',
                'label' => 'Contractor',
                'type' => 'string',
            ),
            array(
                'id' => '',
                'label' => 'Violations',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'Recognitions',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'Incidents',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'Observations',
                'type' => 'number',
            ),
        );

        foreach ($issueTypes as $key => $value) {
            $temp = array();
            $temp[] = array('v' => (string) $key);
            foreach ($value as $llave => $valor) {
                $temp[$valor["app_case_type_id"]] = array('v' => (int) $valor["cantidad"]);
            }
            $rows[] = array('c' => $temp);
        }

        $table["rows"] = $rows;

        $topContractors = json_encode($table, true);
        exit($topContractors);
    }
    /**
     * data for pie chart
     */
    public function actionPieChart($type = NULL, $from = NULL, $to = NULL, $scale = NULL, $jobsite = NULL, $subjobsite = NULL, $building = NULL, $status = NULL, $contractor = NULL, $trade = NULL, $area = NULL, $floor = NULL, $reportType = NULL, $reportTopic = NULL, $recordable = NULL, $injuryType = NULL, $bodyPart = NULL, $lostTime = NULL, $dayWeek = NULL, $timeDayFrom = NULL, $timeDayTo = NULL, $createdby = NULL, $affectedby = NULL, $is_dart = NULL) {
        //For Comma Seperated numbers
        $regex = '/^\d+(?:,\d+)*$/';

        $top5osha = array();
        //si es incidente
        if ($type == 3) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " CONVERT(DATE,acx.incident_datetime) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                $join = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (is_numeric($area)) {
                $queryFilter .= " ac.area_id = " . (int) $area . " AND ";
            }
            if (is_numeric($floor)) {
                $queryFilter .= " ac.floor_id = " . (int) $floor . " AND ";
            }
            if (is_numeric($reportType)) {
                $queryFilter .= " acx.report_type_id = " . (int) $reportType . " AND ";
            }
            if (is_numeric($reportTopic)) {
                $queryFilter .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
            }
            if (is_numeric($recordable)) {
                $queryFilter .= " acx.recordable = " . (int) $recordable . " AND ";
            }
            if (is_numeric($injuryType)) {
                $queryFilter .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
            }
            if (is_numeric($bodyPart)) {
                $queryFilter .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
            }
            if (is_numeric($lostTime)) {
                if ($lostTime == 1) {
                    $queryFilter .= " acx.is_lost_time != 0 AND ";
                } else {
                    $queryFilter .= " acx.is_lost_time = 0  AND ";
                }
            }
            if (is_numeric($is_dart)) {
                if ($is_dart == 1) {
                     $queryFilter .= " acx.is_dart = 1 AND ";
                } else {
                    $queryFilter .= " acx.is_dart = 0  AND ";
                }
            }
            if (is_numeric($dayWeek)) {
                $queryFilter .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
            }
            if ($timeDayFrom != 0 || $timeDayTo != 24) {
                $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                $queryFilter .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
            }

            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " CONVERT(DATE,acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }

            //$top5osha = Yii::$app->db->createCommand( "SELECT top 5 apsf.code as code, COUNT(ac.app_case_sf_code_id) AS cantidad,apsf.[description] as childdescription,childapsf.[description] as parentdescription FROM app_case ac JOIN app_case_sf_code apsf ON ac.app_case_sf_code_id = apsf.id LEFT JOIN app_case_sf_code childapsf ON childapsf.Parent_id = apsf.id $join WHERE $queryFilter $dateFilter and ac.is_active = 1 GROUP BY apsf.code,apsf.description,childapsf.[description] ORDER BY cantidad DESC" )->queryAll();
            //$top5osha = ArrayHelper::toArray( $top5osha, $recursive = TRUE );
            $oshaQuery = "SELECT apsf.code as code, COUNT(ac.app_case_sf_code_id) AS cantidad,apsf.Parent_id as ParentId, c.description As Description FROM app_case ac JOIN app_case_sf_code apsf ON ac.app_case_sf_code_id = apsf.id LEFT JOIN [app_case_sf_code] c ON apsf.code = c.code $join WHERE $queryFilter $dateFilter and ac.is_active = 1 GROUP BY apsf.code,apsf.Parent_id,c.description ORDER BY cantidad DESC";

            $top5osha_results = Yii::$app->db->createCommand($oshaQuery)->queryAll();
            $top5osha_resultArray = ArrayHelper::toArray($top5osha_results, $recursive = TRUE);

            //si es otro tipo de issue
        } else if (is_numeric($type)) {
            $queryFilter = "";
            $typeFilter = "";
            $join = "";
            $dateFilter = " CONVERT(DATE,acx.correction_date) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
            // check de filtros para armar condiciones
            if (is_numeric($type)) {
                $queryFilter .= " ac.app_case_type_id = " . (int) $type . " AND ";
                $typeFilter = " ac.app_case_type_id = " . (int) $type . " AND ";
                switch ($type) {
                case 1:
                    $join = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
            }
            if (is_numeric($status)) {
                $queryFilter .= " ac.app_case_status_id = " . (int) $status . " AND ";
            }
            if (is_numeric($contractor)) {
                $queryFilter .= " ac.contractor_id = " . (int) $contractor . " AND ";
            }
            if (is_numeric($jobsite)) {
                $queryFilter .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
            } else {
                $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsitesData = array();
                if ($jobsiteData) {
                    foreach ($jobsiteData as $key => $name) {
                        $jobsitesData[] = $key;
                    }
                    if (count($jobsitesData) == 1) {
                        $queryFilter .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                    } else {
                        $queryFilter .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                    }
                } else {
                    $queryFilter .= " ac.jobsite_id = 99999999999 AND ";
                }
            }
            if (is_numeric($subjobsite)) {
                $queryFilter .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
            }
            if (is_numeric($building)) {
                $queryFilter .= " ac.building_id = " . (int) $building . " AND ";
            }
            if (is_numeric($trade)) {
                $queryFilter .= " ac.trade_id = " . (int) $trade . " AND ";
            }
            if (preg_match($regex, $createdby)) {
                $queryFilter .= " ac.creator_id IN (" . $createdby . ") AND ";
            }
            if (preg_match($regex, $affectedby)) {
                $queryFilter .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
            }
            if (!is_null($from) && !is_null($to)) {
                $from_array = date_parse($from);
                $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                $to_array = date_parse($to);
                $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                $dateFilter = " CONVERT(DATE, acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
            }

            $oshaQuery = "SELECT sum(cantidad) as cantidad ,CTE.parentid as ParentId,CTE.code,b.code AS parentcode
        ,b.description AS parentDesc,c.description As Description from (SELECT apsf.code as code, COUNT(ac.app_case_sf_code_id) AS cantidad,apsf.[description] as childdescription,apsf.parent_id As ParentId FROM app_case ac JOIN app_case_sf_code apsf ON ac.app_case_sf_code_id = apsf.id $join WHERE $queryFilter $dateFilter and ac.is_active = 1 GROUP BY apsf.code,apsf.description,apsf.parent_id )CTE LEFT JOIN [app_case_sf_code] b ON CTE.ParentId = b.id LEFT JOIN [app_case_sf_code] c ON CTE.code = c.code group by CTE.code,parentid,b.code,b.description,c.description";

            //$top5oshaQuery = "select TOP 5 x.code,x.ParentId,x.parentcode ,coalesce(x.parentDesc,y.description) as parentdescription,x.cantidad from (".$oshaQuery.") x join [app_case_sf_code] y on x.code = y.code order by x.cantidad desc";

            $top5osha_results = Yii::$app->db->createCommand($oshaQuery)->queryAll();
            $top5osha_resultArray = ArrayHelper::toArray($top5osha_results, $recursive = TRUE);

            // $top5osha = Yii::$app->db->createCommand($top5oshaQuery)->queryAll();
            //$top5osha = ArrayHelper::toArray( $top5osha, $recursive = TRUE );

            //si son todos (all)
        } else {
            $query = array();
            $queryFilter = array();
            $typeFilter = array();
            $join = array();
            $dateFilter = array();
            for ($i = 1; $i <= 4; $i++) {
                $queryFilter[$i] = "";
                $typeFilter[$i] = "";
                $join[$i] = "";
                if ($i == 3) {
                    $dateFilter[$i] = " CONVERT(DATE, acx.incident_datetime) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
                } else {
                    $dateFilter[$i] = " CONVERT(DATE, acx.correction_date) BETWEEN CONVERT(DATE,DATEADD(month, -1, GETDATE())) AND CONVERT(DATE, GETDATE()) ";
                }
                // check de filtros para armar condiciones
                switch ($i) {
                case 1:
                    $join[$i] = " JOIN app_case_violation acx ON acx.app_case_id = ac.id ";
                    break;
                case 2:
                    $join[$i] = " JOIN app_case_recognition acx ON acx.app_case_id = ac.id ";
                    break;
                case 3:
                    $join[$i] = " JOIN app_case_incident acx ON acx.app_case_id = ac.id ";
                    break;
                case 4:
                    $join[$i] = " JOIN app_case_observation acx ON acx.app_case_id = ac.id ";
                    break;
                }
                if (is_numeric($status)) {
                    $queryFilter[$i] .= " ac.app_case_status_id = " . (int) $status . " AND ";
                }
                if (is_numeric($contractor)) {
                    $queryFilter[$i] .= " ac.contractor_id = " . (int) $contractor . " AND ";
                }
                if (is_numeric($jobsite)) {
                    $queryFilter[$i] .= " ac.jobsite_id = " . (int) $jobsite . " AND ";
                } else {
                    $jobsiteData = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                    $jobsitesData = array();
                    if ($jobsiteData) {
                        foreach ($jobsiteData as $key => $name) {
                            $jobsitesData[] = $key;
                        }
                        if (count($jobsitesData) == 1) {
                            $queryFilter[$i] .= " ac.jobsite_id = " . $jobsitesData[0] . " AND ";
                        } else {
                            $queryFilter[$i] .= " ac.jobsite_id IN (" . implode(",", $jobsitesData) . ") AND";
                        }
                    } else {
                        $queryFilter[$i] .= " ac.jobsite_id = 99999999999 AND ";
                    }
                }
                if (is_numeric($subjobsite)) {
                    $queryFilter[$i] .= " ac.sub_jobsite_id = " . (int) $subjobsite . " AND ";
                }
                if (is_numeric($building)) {
                    $queryFilter[$i] .= " ac.building_id = " . (int) $building . " AND ";
                }
                if (is_numeric($trade)) {
                    $queryFilter[$i] .= " ac.trade_id = " . (int) $trade . " AND ";
                }
                if (preg_match($regex, $createdby ?? "")) {
                    $queryFilter[$i] .= " ac.creator_id IN (" . $createdby . ") AND ";
                }
                if (preg_match($regex, $affectedby ?? "")) {
                    $queryFilter[$i] .= " ac.affected_user_id IN (" . $affectedby . ") AND ";
                }
                if ($i == 3) {
                    if (is_numeric($area)) {
                        $queryFilter[$i] .= " ac.area_id = " . (int) $area . " AND ";
                    }
                    if (is_numeric($floor)) {
                        $queryFilter[$i] .= " ac.floor_id = " . (int) $floor . " AND ";
                    }
                    if (is_numeric($reportType)) {
                        $queryFilter[$i] .= " acx.report_type_id = " . (int) $reportType . " AND ";
                    }
                    if (is_numeric($reportTopic)) {
                        $queryFilter[$i] .= " acx.report_topic_id = " . (int) $reportTopic . " AND ";
                    }
                    if (is_numeric($recordable)) {
                        $queryFilter[$i] .= " acx.recordable = " . (int) $recordable . " AND ";
                    }
                    if (is_numeric($injuryType)) {
                        $queryFilter[$i] .= " acx.injury_type_id = " . (int) $injuryType . " AND ";
                    }
                    if (is_numeric($bodyPart)) {
                        $queryFilter[$i] .= " acx.body_part_id = " . (int) $bodyPart . " AND ";
                    }
                    if (is_numeric($lostTime)) {
                        if ($lostTime == 1) {
                            $queryFilter[$i] .= " acx.is_lost_time != 0 AND ";
                        } else {
                            $queryFilter[$i] .= " acx.is_lost_time = 0  AND ";
                        }
                    }
                    if (is_numeric($is_dart)) {
                        if ($is_dart == 1) {
                             $queryFilter .= " acx.is_dart = 1 AND ";
                        } else {
                            $queryFilter .= " acx.is_dart = 0  AND ";
                        }
                    }
                    if (is_numeric($dayWeek)) {
                        $queryFilter[$i] .= " DATEPART(dw,acx.incident_datetime) = " . (int) $dayWeek . " AND ";
                    }
                    if ($timeDayFrom != 0 || $timeDayTo != 24) {
                        $timeDayFrom = date('H:i', mktime(0, $timeDayFrom * 60));
                        $timeDayTo = date('H:i', mktime(0, ($timeDayTo * 60) - 1));
                        $queryFilter[$i] .= " CONVERT(varchar(5), acx.incident_datetime, 108) BETWEEN '$timeDayFrom' AND '$timeDayTo' AND  ";
                    }
                }
                if (!is_null($from) && !is_null($to)) {
                    $from_array = date_parse($from);
                    $from = $from_array["year"] . "-" . $from_array["month"] . "-" . $from_array["day"];
                    $to_array = date_parse($to);
                    $to = $to_array["year"] . "-" . $to_array["month"] . "-" . $to_array["day"];
                    if ($i == 3) {
                        $dateFilter[$i] = " CONVERT(DATE,acx.incident_datetime) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    } else {
                        $dateFilter[$i] = " CONVERT(DATE,acx.correction_date) BETWEEN '" . $from . "' AND '" . $to . "' ";
                    }
                }
                $query[$i] = "SELECT apsf.code as code, COUNT(ac.app_case_sf_code_id) AS cantidad,apsf.parent_id As ParentId"
                    . " FROM app_case ac JOIN app_case_sf_code apsf ON ac.app_case_sf_code_id = apsf.id"
                    . " $join[$i] "
                    . " WHERE $queryFilter[$i] $dateFilter[$i] and ac.is_active = 1 "
                    . "GROUP BY apsf.code,apsf.parent_id";
            }
            $oshaQuery = "SELECT issues.code,issues.ParentId, SUM(issues.cantidad) as cantidad,b.code as parentcode ,b.description as parentDesc,c.description As Description FROM (" . implode(' UNION ALL ', $query) . ") AS issues left join [app_case_sf_code] b on issues.ParentId = b.id LEFT JOIN [app_case_sf_code] c ON issues.code = c.code GROUP BY issues.code,issues.ParentId,b.code,b.description, issues.cantidad,c.description";
            //$top5oshaQuery = "select TOP 5 x.code,x.ParentId,x.parentcode ,coalesce(x.parentDesc,y.description) as parentdescription,x.cantidad from (".$oshaQuery.") x join [app_case_sf_code] y on x.code = y.code order by x.cantidad desc";
            //echo $oshaQuery;

            $top5osha_results = Yii::$app->db->createCommand($oshaQuery)->queryAll();
            $top5osha_resultArray = ArrayHelper::toArray($top5osha_results, $recursive = TRUE);
        }

        $osharesultsArray = array();
        foreach ($top5osha_resultArray as $row) {
            if ($row['ParentId'] == null) {
                $osharesultsArray_temp['code'] = $row['code'];
                $osharesultsArray_temp['Description'] = $row['Description'];
                $osharesultsArray_temp['cantidad'] = $row['cantidad'];
                array_push($osharesultsArray, $osharesultsArray_temp);
            } else {

                $queryForGetParentlevel = ";WITH SFCCTE AS (
                      SELECT code,parent_id,description, id, 0 AS EmpLevel
                      FROM [app_case_sf_code] where code = '" . $row['code'] . "'

                      UNION ALL

                      SELECT sfc.code,sfc.parent_id,sfc.description, sfc.id, sfcpr.[EmpLevel]+1
                      FROM [app_case_sf_code] AS sfc
                        INNER JOIN SFCCTE AS sfcpr
                          ON sfc.id = sfcpr.parent_id
                    )
                    SELECT *
                      FROM SFCCTE AS sfcode where sfcode.parent_id is null";
                $results = Yii::$app->db->createCommand($queryForGetParentlevel)->queryAll();
                $resultArray = ArrayHelper::toArray($results, $recursive = TRUE);
                $osharesultsArray_temp['code'] = $resultArray[0]['code'];
                $osharesultsArray_temp['Description'] = $resultArray[0]['description'];
                $osharesultsArray_temp['cantidad'] = $row['cantidad'];
                array_push($osharesultsArray, $osharesultsArray_temp);
            }
        }
        $osharesultsgroups = array();
        $code = array();

        foreach ($osharesultsArray as $item) {
            $key = $item['code'];
            if (!isset($osharesultsgroups[$key])) {
                $osharesultsgroups[$key] = array(
                    'code' => $key,
                    'Description' => $item['Description'],
                    'cantidad' => $item['cantidad'],
                );
            } else {
                $osharesultsgroups[$key]['cantidad'] = $osharesultsgroups[$key]['cantidad'] + $item['cantidad'];
            }
        }
        array_multisort(array_column($osharesultsgroups, 'cantidad'), SORT_DESC, $osharesultsgroups);

        $top5osha = array();
        $i = 0;
        foreach ($osharesultsgroups as $item) {
            if ($i < 5) {
                $top5osha[$i]['parentcode'] = $item['code'];
                $top5osha[$i]['parentdescription'] = $item['Description'];
                $top5osha[$i]['cantidad'] = $item['cantidad'];
                $top5osha[$i]['code'] = null;
                $i++;
            }
        }
        $rows = array();
        $table = array();
        $table["cols"] = array(
            array(
                'id' => '',
                'label' => 'OSHA Subpart',
                'type' => 'string',
            ),
            array(
                'id' => '',
                'label' => 'Quantity',
                'type' => 'number',
            ),
            array(
                'id' => '',
                'label' => 'childdescription',
                'type' => 'string',
            )
            ,
            array(
                'id' => '',
                'label' => 'parentdescription',
                'type' => 'string',
            ),
        );

        foreach ($top5osha as $row) {
            $code = ($row["parentcode"] == null) ? $row["code"] : $row["parentcode"];
            $out = strlen($row["parentdescription"]) > 100 ? substr($row["parentdescription"], 0, 100) . "..." : $row["parentdescription"]; // to show ellipsis in pie chart for dashboard
            $temp = array();
            $temp[] = array('v' => (string) $code . ' - ' . (string) $row["parentdescription"]);
            $temp[] = array('v' => (int) $row["cantidad"]);
            $temp[] = array('v' => (string) $out);
            $temp[] = array('v' => (string) $code);
            $rows[] = array('c' => $temp);
        }
        $table["rows"] = $rows;
        $top5osha = json_encode($table, true);
        exit($top5osha);
    }

    /**
     * data for newflash notifications
     */
    public function actionGetNewsflash($id) {
        $quantity = Yii::$app->db->createCommand("SELECT count(*) as quantity FROM notification WHERE is_read = '0' AND user_id ='$id'")->queryAll();
        $notifications = Yii::$app->db->createCommand("SELECT ac.id, ac.app_case_type_id, j.jobsite, ac.additional_information, ac.created FROM notification n join app_case ac on n.app_case_id = ac.id join jobsite j on ac.jobsite_id = j.id WHERE is_read ='0' AND user_id = '$id' ORDER BY n.created DESC")->queryAll();
        $newsflash = array(
            'quantity' => $quantity,
            'notifications' => $notifications,
        );
        $newsflash = json_encode($newsflash, true);
        return $newsflash;
    }
    /**
     * send broadcast newflash notifications
     */
    public function actionSendNewsflash($id) {
        notification::newsflash($id);
        return;
    }
    /**
     * mark newflashes as read
     */
    public function actionMarkNewsflashRead() {
        $user_id = Yii::$app->session->get("user.id");
        //mark all newsflash as read
        Yii::$app->db->createCommand("UPDATE notification SET is_read = 1 WHERE user_id = '$user_id'")->execute();
        return true;
    }

    /**
     * Get the createb by users
     */
    public function actionGetCreatedByUsers() {

        $sqlQuery = (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) ? "SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u where u.is_active = 1 group by u.id,u.email, u.first_name, u.last_name" : "SELECT u.id, u.email, u.first_name, u.last_name FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id left join [dbo].[user_jobsite] J on UJ.jobsite_id = J.jobsite_id where j.user_id = " . Yii::$app->session->get("user.id") . " and u.is_active = 1 group by u.id,u.email, u.first_name, u.last_name";

        $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($usersList as $key => $value) {
            $userListdataArray['result'][$value['id']] = $value['first_name'] . ' ' . $value['last_name'];
        }

        $userListdataArray['pagination']['more'] = true;

        $topContractors = json_encode($userListdataArray, true);
        exit($topContractors);
    }

    /**
     * Get the Auto search Users
     */
    public function actionGetAutoSearchUser($searchkey, $Jobsites, $Affectedby = false) {

        if ($Jobsites != 'all') {
            $sqlQuery = "SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id where u.is_active = 1 and U.first_name + ' ' + U.last_name like '%" . $searchkey . "%'and UJ.jobsite_id IN (" . $Jobsites . ") and u.role_id in (1,2,3,4,5,6,16) group by u.id,u.email, u.first_name, u.last_name";
        } else {
            $jobsite = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
            $jobsites_string = implode(',', array_keys($jobsite));
            $sqlQuery = "SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id where u.is_active = 1 and U.first_name + ' ' + U.last_name like '%" . $searchkey . "%'and UJ.jobsite_id IN (" . $jobsites_string . ") and u.role_id in (1,2,3,4,5,6,16) group by u.id,u.email, u.first_name, u.last_name";
        }

        if (($Affectedby === true) || ($Affectedby == 'true')) {
            if ($Jobsites != 'all') {
                $sqlQuery = "SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u LEFT JOIN [dbo].[app_case] AC ON AC.affected_user_id = u.id where u.is_active = 1 and U.first_name + ' ' + U.last_name like '%" . $searchkey . "%'and AC.jobsite_id IN (" . $Jobsites . ") group by u.id,u.email, u.first_name, u.last_name";
            } else {
                $jobsite = ArrayHelper::map(Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');
                $jobsites_string = implode(',', array_keys($jobsite));
                $sqlQuery = "SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u LEFT JOIN [dbo].[app_case] AC ON AC.affected_user_id = u.id where u.is_active = 1 and U.first_name + ' ' + U.last_name like '%" . $searchkey . "%'and AC.jobsite_id IN (" . $jobsites_string . ") group by u.id,u.email, u.first_name, u.last_name";
            }
        }
        $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($usersList as $key => $value) {
            $temp['value'] = $value['first_name'] . ' ' . $value['last_name'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }

        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);
    }

    /**
     * Get the Auto Contractors
     */
    public function actionGetAutoSearchContractors($searchkey) {
        $sqlQuery = "select * from [dbo].[cmic_contractor] where Name like '%" . $searchkey . "%'";
        $ContractorList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $contractorListdataArray = array();
        foreach ($ContractorList as $key => $value) {
            $temp['value'] = $value['Name'];
            $temp['data'] = $value['id'];
            array_push($contractorListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $contractorListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);
    }
    /**
     * Get the CMIC Contractor by id
     */
    public function actionGetCmicContractorById($id) {
        $sqlQuery = "select * from [dbo].[cmic_contractor] where id = " . $id;
        $ContractorList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $resultArray = array();
        $resultArray["data"] = $ContractorList;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);
    }

    /**
     * Get the Owners in App case by Auto Search
     */
    public function actionGetAppCaseOwnerAds($searchkey) {
        $userid = Yii::$app->session->get("user.id");
        $sqlQuery = "SELECT u.id, u.first_name, u.last_name, u.employee_number  FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id where u.is_active = 1 and U.first_name + ' ' + U.last_name like '%" . $searchkey . "%'  and UJ.jobsite_id IN (SELECT J.id
                        FROM [dbo].[jobsite] J
                        JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
                        WHERE J.is_active = 1
                            AND UJ.user_id = $userid)order by u.first_name";

        //SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id where u.is_active = 1 and U.first_name + ' ' + U.last_name like '%".$searchkey."%'and UJ.jobsite_id IN (". $Jobsites .") and u.role_id in (1,2,3,4,5,6,16) group by u.id,u.email, u.first_name, u.last_name

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        //User::find()->where( [ "is_active" => 1, "first_name + ' ' + U.last_name like '%".$searchkey."%'" ] )->orderBy('employee_number')->all();

        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['first_name'] . ' ' . $value['last_name'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);

    }

    /**
     * Get the Contractors in App case by Auto Search
     */
    public function actionGetAppCaseContractorAs($searchkey) {
        $userid = Yii::$app->session->get("user.id");
        $sqlQuery = "SELECT c.id, c.contractor
                        FROM contractor c
                        JOIN contractor_jobsite cj ON cj.contractor_id = c.id
                        WHERE c.is_active = 1
                            AND cj.jobsite_id IN (SELECT J.id
                        FROM [dbo].[jobsite] J
                        JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
                        WHERE J.is_active = 1
                            AND UJ.user_id = $userid) AND c.contractor like '%" . $searchkey . "%'
                        Group by c.id, c.contractor ORDER BY c.contractor ASC";
        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['contractor'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);

    }

    /**
     * Get the Jobsite in App case by Auto Search
     */
    public function actionGetAppCaseJobsiteAs($searchkey) {
        $userid = Yii::$app->session->get("user.id");
        $sqlQuery = "SELECT J.id as id, J.jobsite as jobsite
                        FROM [dbo].[jobsite] J
                        JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
                        WHERE J.is_active = 1
                        AND UJ.user_id = $userid
                        AND J.jobsite like '%" . $searchkey . "%'";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['jobsite'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);

    }

    /**
     * Get the Jobsite in App case Create and edit
     */
    public function actionGetAppCaseCreateJobsite($offset) {
        $userid = Yii::$app->session->get("user.id");
        $sqlQuery = "SELECT J.id as id, J.jobsite as jobsite
                        FROM [dbo].[jobsite] J
                        JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
                        WHERE J.is_active = 1
                        AND UJ.user_id = $userid
                        order by jobsite
                        OFFSET " . $offset . " Rows
                        fetch next 10 Rows only";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $AppcaseJobsites = array();
        foreach ($data_creator as $id => $name) {
            $AppcaseJobsites["$id"] = $name;
        }
        $this->renderJSON($AppcaseJobsites);
    }

    /**
     * Get the all jobsites
     */
    public function actionGetAllJobsites($searchkey) {
               $userid = Yii::$app->session->get("user.id");

    if (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN) {

        $sqlQuery = "SELECT J.id as id, J.jobsite as jobsite
                        FROM [dbo].[jobsite] J
                        LEFT JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
                        WHERE J.is_active = 1
                        AND UJ.user_id = $userid
                        AND J.jobsite like '%" . $searchkey . "%' group by J.jobsite, J.id";
        }else{
            $sqlQuery = "SELECT J.id as id, J.jobsite as jobsite
            FROM [dbo].[jobsite] J
            LEFT JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
            WHERE J.is_active = 1
            AND J.jobsite like '%" . $searchkey . "%' group by J.jobsite, J.id";

        } 

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['jobsite'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);

    }

    /**
     * Get the all contractors
     */
    public function actionGetAllContractors($searchkey) {

        $sqlQuery = "SELECT C.id as id, C.contractor as contractor
                        FROM [dbo].[contractor]  C
                        WHERE C.is_active = 1
                        AND C.contractor like '%" . $searchkey . "%' group by C.contractor, C.id";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['contractor'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);

    }

    /**
     * Get the all contractors
     */
    public function actionGetAllContractorsByJobsite($Jobsites, $searchkey) {

        $sqlQuery = "SELECT C.id as id, C.contractor as contractor
                        FROM [dbo].[contractor]  C
                        INNER JOIN [dbo].[contractor_jobsite] CJ on CJ.contractor_id = C.id
                        WHERE C.is_active = 1 AND CJ.jobsite_id = " . $Jobsites . "
                        AND C.contractor like '%" . $searchkey . "%' group by C.contractor, C.id";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['contractor'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);

    }

    public function actionGenerateUrl($url, $jobid, $encode) {
        $result1 = 01;
        /*BLINK Token*/
        /* $token = $this->CreateBlInkToken(); */

        /*BITLY Token*/
        $token = $this->CreateBitlyToken();
        $sgortenurl_array =0; 
        if ($token == 0) {
        } else {
            /*Bitly*/
            $sgortenurl_array = $this->CreateShortenBitlyURL($url, $token);
        }

        for ($level = ob_get_level(); $level > 0; --$level) {
        if (!@ob_end_clean()) {
            ob_clean();
        }
    }

            if ($sgortenurl_array === 0) {

            } else {
                $obj2 = json_decode($sgortenurl_array ?? "");
                 
                $resultArray = array();
                if (isset($obj2->{'link'})) {
                    $resultArray["shorturl"] = $obj2->{'link'};
                    $result = json_encode($resultArray ?? "", true); 
                } else { 
                    $result = 00;
                }
            }

             $shortenurl = (isset($resultArray['shorturl'])) ? $resultArray['shorturl'] : " ";                

              $sqlQuery = Yii::$app->db->createCommand("Update [dbo].[qr_code] set [is_active] = 0 where [jobsite_id] = $jobid")->execute();
             $insert_query = "INSERT INTO [dbo].[qr_code]
           ([jobsite_id]
           ,[URL]
           ,[bitlyURL] 
           ,[secretcode]          
           ,[is_active]) values ($jobid,'$url', '".$shortenurl."', '$encode',  1) ";

            $Insert_toDB = Yii::$app->db->createCommand($insert_query)->execute();
      
         $this->renderJSON($result); 
    }

    private function CreateBlInkToken() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://app.bl.ink/api/v3/access_token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"email\": \"Shilpashri.N@winwire.com\",\n\"password\": \"Winwire01*\"\n}",
            CURLOPT_COOKIE => "PHPSESSID=hte9rvjctfe64tubfm0vkaid7d",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

            //return "cURL Error #:" . $err;
            return 0;
        } else {

            if ($httpcode == 200) {
                return $response;
            } else {
                return 0;
            }
        }
    }

    private function CreateShortenURL($url, $token) {
        $curl = curl_init();

        $dataarr = array("url" => $url);

// Function to convert array into JSON
        //echo json_encode($dataarr);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://app.bl.ink/api/v3/78293/links",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($dataarr),
            CURLOPT_HTTPHEADER => array(
                "authorization:  Bearer $token",
                "content-type: application/json",
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

    private function CreateBitlyToken() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-ssl.bitly.com/oauth/access_token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic c2VzaGVuZHJhLm1Ad2lud2lyZS5jb206V2lud2lyZTAxIQ==",
                "content-type: application/json",
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

    private function CreateShortenBitlyURL($url, $token) {
        $curl = curl_init();

        $dataarr = array("long_url" => urldecode($url));

// Function to convert array into JSON
        //echo json_encode($dataarr, JSON_UNESCAPED_SLASHES);  exit();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-ssl.bitly.com/v4/bitlinks",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($dataarr, JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => array(
                "authorization:  Bearer $token",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 01;
        } else {
            if ($httpcode == 200) {
                exit($response);
                return $response;
            }
        }
    }

    public function actionAutoSave($typeid) {
        $userid = Yii::$app->session->get("user.id");
        $formdata = Yii::$app->request->post();
        $myJSON = json_encode($formdata);
        $sqlQuery = "select count(*) as rows from [dbo].[app_case_draft] where typeid = $typeid AND userid = $userid";

        $checkdataexits = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        if ($checkdataexits[0]["rows"] > 0) {
            $sqlinsert = Yii::$app->db->createCommand("update [dbo].[app_case_draft] set [formdata] = '$myJSON' where typeid = $typeid AND userid = $userid")->execute();
        } else {
            $sqlinsert = Yii::$app->db->createCommand("INSERT INTO [dbo].[app_case_draft]
           ([typeid]
           ,[userid]
           ,[formdata]
           ,[is_active]) values ($typeid,$userid, '$myJSON', 0) ")->execute();
        }

        echo "success";
    }

    public function actionGetAutoSavedata($typeid) {
        $userid = Yii::$app->session->get("user.id");
        $formdata = Yii::$app->request->post();
        $myJSON = json_encode($formdata);
        $sqlQuery = "select formdata from [dbo].[app_case_draft] where typeid = $typeid AND userid = $userid";

        $data = Yii::$app->db->createCommand("$sqlQuery")->queryAll();

        if ($data[0]["formdata"]) {
            echo $data[0]["formdata"];
        } else {
            echo "nodata";
        }
    }

    public function actionGetSaftetyCode($parentcode) {
        if ($parentcode == 0) {
            $parentcode = null;
        }
        $data_creator = AppCaseSfCode::find()->select([
            "id",
            "code",
            "description",
            "parent_id",
        ])->where(["parent_id" => $parentcode, "is_active" => 1])->orderBy(["code" => "SORT_ASC"])->asArray()->all();

        $ListdataArray = array();
        foreach ($data_creator as $value) {
            $id = $value['id'];
            $ListdataArray["$id"] = $value['code'] . ' ' . $value['description'];
        }

        $this->renderJSON($ListdataArray);
    }

    public function actionGetWtUsers($isnew) {

       if($isnew == 1){
         $sqlQuery = "select id, email,firstname,lastname,username from [dbo].[ad_emails] where status = 0  and username not in (select user_name from [dbo].[user] where is_active = 1 and IsAduser = 1 )";
     }else{
         $sqlQuery = "select id, email,firstname,lastname,username from [dbo].[ad_emails] where status = 0 ";
     }
       

        $data = Yii::$app->db->createCommand("$sqlQuery")->queryAll();

        $ListuserArray = array();
        foreach ($data as $value) {
            $id = $value['id'];
            $ListuserArray[] = ["id"=>$value['id'],"email"=>$value['email'], "firstname"=>$value['firstname'],"lastname"=>$value['lastname'],"username"=>$value['username']] ;
        }

        $this->renderJSON($ListuserArray);
    }

     public function actionGetUsers($isactive, $conid) {

       if($conid == 0){

         $sqlQuery = "select id, email, first_name,last_name,user_name from [dbo].[user] where is_active = $isactive and id not in (select child_userid from [dbo].[merge_users] where status = 0)";
       }else{
         $sqlQuery = "select id, email, first_name,last_name,user_name from [dbo].[user] where is_active = $isactive and contractor_id = $conid and id not in (select child_userid from [dbo].[merge_users] where status = 0)";
       }
   
       

        $data = Yii::$app->db->createCommand("$sqlQuery")->queryAll();

        $ListuserArray = array();
        foreach ($data as $value) {
            $id = $value['id'];
            $ListuserArray[] = ["id"=>$value['id'],"fulltname"=>$value['first_name']." ".$value['last_name'],"username"=>$value['user_name']] ;
        }

        $this->renderJSON($ListuserArray);
    }

     /**
     * Get the all contractors
     */
    public function actionGetActiveUser($searchkey) {

        $sqlQuery = "SELECT id , first_name, last_name
                        FROM [dbo].[user] 
                        WHERE is_active = 1
                        AND first_name like '%" . $searchkey . "%' OR last_name like '%" . $searchkey . "%' OR first_name + ' ' + last_name like '%" . $searchkey . "%' OR user_name like '%" . $searchkey . "%' group by id, first_name, last_name";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['first_name'] . " ". $value['last_name'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);
    }

    /**
     * Get the Merge User Contractors in Other Account Issues by Auto Search
     */
    public function actionGetOtherAccountContractorAs($searchkey) {
        $userid = Yii::$app->session->get("user.id");

        $sqlQuery = "SELECT c.id, c.contractor
        FROM contractor c
        JOIN contractor_jobsite cj ON cj.contractor_id = c.id
        WHERE c.is_active = 1
            AND cj.jobsite_id IN (SELECT J.id
        FROM [dbo].[jobsite] J
        JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
        WHERE J.is_active = 1
            AND UJ.user_id in (select child_userid from [dbo].[merge_users] where parent_userid = $userid and status = 0 )) AND c.contractor like '%" . $searchkey . "%'
        Group by c.id, c.contractor ORDER BY c.contractor ASC";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['contractor'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);
    }

    /**
     * Get the Jobsite in App case by Auto Search
     */
    public function actionGetOtherAccountJobsiteAs($searchkey) {
        $userid = Yii::$app->session->get("user.id");
        $sqlQuery = "SELECT J.id as id, J.jobsite as jobsite
                        FROM [dbo].[jobsite] J
                        JOIN [dbo].[user_jobsite] UJ ON UJ.jobsite_id = J.id
                        WHERE J.is_active = 1
                        AND UJ.user_id in (select child_userid from [dbo].[merge_users] where parent_userid = $userid  and status = 0)
                        AND J.jobsite like '%" . $searchkey . "%'";

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
        foreach ($data_creator as $value) {
            $temp['value'] = $value['jobsite'];
            $temp['data'] = $value['id'];
            array_push($userListdataArray, $temp);
        }
        $resultArray = array();
        $resultArray["suggestions"] = $userListdataArray;

        $searchusers = json_encode($resultArray, true);
        exit($searchusers);
    }

        /**
     * Set Status Issue
     */
    public function actionSetStatusOtherIssue($app_case_id = NULL, $status_id = NULL) {
       
        
        if ($app_case_id != NULL && $status_id!= NULL ) {

            Yii::$app->db->createCommand("UPDATE [dbo].[app_case]  SET app_case_status_id = $status_id WHERE id = $app_case_id")->execute();

            $this->renderJSON(true);
        } else {
          $this->renderJSON(false);
        }
    }

    public function actionDelDraftIssue($typeid) {
        $userid = Yii::$app->session->get("user.id");
        $sqlQuery = "Delete from [dbo].[app_case_draft] where typeid = $typeid AND userid = $userid";

        $data = Yii::$app->db->createCommand("$sqlQuery")->execute();

        if ($data) {
            echo $data;
        } else {
            echo "nodata";
        }
    }

      /**
     * Set Status Issue
     */
    public function actionCheckJobsiteQrcode($jobid = NULL){
       
        
        if ($jobid != NULL ) {

        

           $data = Yii::$app->db->createCommand("SELECT jobsite_id
                        FROM [dbo].[qr_code] 
                        WHERE is_active = 1 AND jobsite_id = $jobid")->queryAll();
            
           if(isset($data[0]["jobsite_id"])){
           	$this->renderJSON(true);
           }else{
           	$this->renderJSON(false);
           }            
        } else {
          $this->renderJSON(false);
        }
    }

    /**
     * Render JSON
     *
     * @param $data
     */
    protected function renderJSON($data) {
        header('Content-type: application/json');
        exit(json_encode($data));
    }

    /**
     * Trae los contractors relacionados con n jobsites
     * @params mix $array_jobsites Array de jobsites
     * @return \yii\db\ActiveQuery
     */
    public function getContractorsForJobsites($array_jobsites, $searchkey){
        if($array_jobsites){
            $jobsites = array();
            foreach($array_jobsites as $jobsite_id => $jobsite){
                $jobsites[] = $jobsite_id;
            }
            $jobsites = "( " . implode( ", ", $jobsites) . " )";
            $query = "SELECT c.id, c.contractor FROM contractor c JOIN contractor_jobsite cj ON cj.contractor_id = c.id WHERE c.is_active = 1 AND cj.jobsite_id IN $jobsites  AND c.contractor like '%" . $searchkey . "%'" ;
            $array_contractors = Yii::$app->db->createCommand( "SELECT c.id, c.contractor FROM contractor c JOIN contractor_jobsite cj ON cj.contractor_id = c.id WHERE c.is_active = 1 AND cj.jobsite_id IN $jobsites  AND c.contractor like '%" . $searchkey . "%' group by c.id, c.contractor" )->queryAll();
        }else{
            $array_contractors = array("" => "-");
        }
        return $array_contractors;
    }

    public function actionDownloadBlob($destinationURL) {
        
        $filename=explode("/",$destinationURL);  
        $blobName = $filename[4];
        attachment::downloadBlob($blobName);
    }

    /**
     * Add jobsites to a contractor
     * * @return Status  
     */
    public function actionAddJobsite($cid,$jobsites) {
        
        $jobsites_array = explode(',', $jobsites);
        $contractor_jobsite_status = true;

        if( count($jobsites_array) > 0 )
        {
            $previous_jobsites_array = array();
            $new_jobsites_array = $jobsites_array;

            $previous_jobsites = ContractorJobsite::find()->where(["contractor_id" => $cid])->asArray()->all();
            
            foreach($previous_jobsites as $jobsite){
                $previous_jobsites_array[] = $jobsite["jobsite_id"];
            }

            $added_jobsites = array_diff($new_jobsites_array, $previous_jobsites_array);

            if(!empty($added_jobsites)){
                foreach($added_jobsites as $jobsite_id){
                    $contractor_jobsite_model = new ContractorJobsite();
                    $contractor_jobsite_model->contractor_id = $cid;
                    $contractor_jobsite_model->jobsite_id = $jobsite_id;
                    if( !$contractor_jobsite_model->save() )
                    {
                        $contractor_jobsite_status = false;
                        break;
                    }
                }
            }
        }

        if( $contractor_jobsite_status )
        {
            Yii::$app->session->setFlash('jobsite','Alert: Contractor has been added to the jobsite(s)!');
            return $contractor_jobsite_status;
        }
    }

    /**
     * Get jobsites to datatable
     * @return jobsites
     */
    public function actionGetJobsitesByUser($cid) {
        $userId = Yii::$app->session->get("user.id");
        $previous_jobsites = ContractorJobsite::find()->select(['jobsite_id'])->where(["contractor_id" => $cid])->groupBy(['jobsite_id'])->asArray()->all();
            if(count($previous_jobsites) > 0){
                foreach($previous_jobsites as $jobsite){
                    $previous_jobsites_array[] = $jobsite["jobsite_id"];
            }
            $previous_jobsites = 'and j.id NOT IN (' . implode( ',', $previous_jobsites_array ) . ')';
        }else{
            $previous_jobsites = '';
        }    
        
        if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN)
        {
            $data_jobsite = Yii::$app->db->createCommand("select j.id,j.jobsite from jobsite j where j.is_active = 1 $previous_jobsites group by j.id,j.jobsite order by jobsite asc ")->queryAll();
        }
        else{
            $data_jobsite = Yii::$app->db->createCommand("select j.id,j.jobsite from jobsite j join [user_jobsite] uj on j.id = uj.Jobsite_id where j.is_active = 1 and uj.user_id = $userId $previous_jobsites")->queryAll();
        }
        $this->renderJSON($data_jobsite);
    }

    /**
     * Delete jobsite for contractor
     * @return status
     */
    public function actionDeleteJobsite($cid,$jid) {
        $contractor_jobsite_status = false;
        if (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_ADMIN)
        {
            $contractor_jobsite_status = ContractorJobsite::deleteAll( [ "contractor_id" => $cid ,"jobsite_id" => $jid] );
            
        }
        
        return $contractor_jobsite_status;
    }

    public function actionGetJobsitesData($searchkey) {
        
            $sqlQuery = "select distinct TRIM(CP.PROJ_NAME) as jobsite, TRIM(CP.PROJ_CODE) as job_number, 
            CP.ADD1 + ISNULL(','+CP.ADD2, '') as address,  ISNULL(TRIM(CP.ADD3), '') as city, ISNULL(TRIM(CP.STATE_CODE), '') as state, 
            ISNULL(TRIM(CP.POSTAL_CODE), '') as zipcode from [dbo].[CMIC_PROJECTS] CP where (CP.PROJ_NAME like '%".$searchkey."%' 
            OR CP.PROJ_CODE like '%".$searchkey."%') AND TRIM(CP.PROJ_CODE) not in (SELECT distinct TRIM(CP.PROJ_CODE)
            FROM [dbo].[CMIC_PROJECTS] CP  WHERE TRIM(CP.PROJ_CODE) IN (SELECT distinct job_number FROM [dbo].[jobsite]))";

            
            
            $JobsiteList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
            $JobsiteListdataArray = array();
            foreach ($JobsiteList as $key => $value) {
                
                $temp['value'] = $value['job_number']." - ". $value['jobsite'];
                $temp['data'] = $value['job_number'];
                $temp['job_name'] = $value['jobsite'];
                $temp['address'] = $value['address'];
                $temp['city'] = $value['city'];
                $temp['state'] = $value['state'];
                $temp['zipcode'] = (int)$value['zipcode'];
                array_push($JobsiteListdataArray, $temp);
            }
            $resultArray = array();
            $resultArray["suggestions"] = $JobsiteListdataArray;
            $searchjobsites = json_encode($resultArray, true);
            exit($searchjobsites);
        
    }

    public function actionGetWtUsersData($searchkey) {
        
            $sqlQuery = "select distinct id, first_name+' '+last_name as fullname from [dbo].[user] u where is_active = 1 AND IsAduser = 1 AND role_id in (1,2,3,4,5,6) AND (u.first_name like '%" . $searchkey . "%' OR u.last_name like '%" . $searchkey . "%' OR u.user_name like '%" . $searchkey . "%' OR u.employee_number like '%" . $searchkey . "%')  ";
            $UserList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
            $UserListdataArray = array();
            foreach ($UserList as $key => $value) {
                $temp['value'] = $value['fullname'];
                $temp['data'] = $value['id'];
                
                array_push($UserListdataArray, $temp);
            }
            $resultArray = array();
            $resultArray["suggestions"] = $UserListdataArray;
            $searchusers = json_encode($resultArray, true);
            exit($searchusers); 
    }

    /**
     * Get jobsite admins
     * @param int $jobsite_id
     * @return mixed
     */
    public function actionGetJobsiteAdmins($jobsite_id) {
        $job_admins = UserJobsite::find()->select('first_name, last_name, user_id')->joinWith('user')->where(["jobsite_id" => $jobsite_id, "is_admin"=>1])->asArray()->all();
        $this->renderJSON($job_admins);
    }
}

?>