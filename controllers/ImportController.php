<?php

namespace app\controllers;

use app\helpers\excelHelper;
use app\models\Import;
use app\models\Jobsite;
use app\models\searches\Contractor;
use app\models\UserJobsite;
use Yii;
use app\helpers\functions;
use yii\helpers\ArrayHelper;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * UserController implements the CRUD actions for User model.
 */
class ImportController extends AllController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Carga y procesa el template cargado para creacion de Jobsites.
     * Recibe el Jobsite Template con datos
     *
     * @return mixed Excel cargado con resultados
     */
    public function actionJobsite()
    {
        $model = new Import();

        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {
            $model->file = UploadedFile::getInstance($model, 'file');

            // read excel
            $excelHelper = new excelHelper();

            // $cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            // $cacheSettings = array( ' memoryCacheSize ' => '8MB');
            // \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            //$objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            //todo revisar si se puede escribir encima del excel
//            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($model->file->tempName);

            $errors = array();

            if($objPHPExcel->sheetNameExists("Jobsite")){
                //busco la hoja de productos
                $objData = $objPHPExcel->getSheetByName('Jobsite');
                //convierto la hoja a array
                $arrayData = $excelHelper->excelToArray($objData);
            }else{
                $errors[]= "Missing 'Jobsite' tab";
                return $this->render('jobsite', [
                    'model' => $model
                ]);
            }
            //comprueba la integridad de los datos y los graba - devuelve resultados
            $result = $excelHelper->processData($arrayData, $excelHelper::EXCEL_JOBSITE);
            $someErrors = false;
            $allErrors = true;
            foreach($result as $row){
                if($row["Status"] == "ERROR"){
                    $someErrors = true;
                }elseif($row["Status"] == "OK"){
                    $allErrors = false;
                }
            }

            $objPHPExcel->getSheetByName('Jobsite')->setCellValue("F1", "Status");
            $objPHPExcel->getSheetByName('Jobsite')->setCellValue("G1", "Comments");
            $objPHPExcel->getSheetByName('Jobsite')->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Jobsite')->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Jobsite')->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Jobsite')->getStyle("F1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffc000');
            $objPHPExcel->getSheetByName('Jobsite')->getStyle("G1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffc000');
            for($i = 0; $i < sizeof($result); $i++){
                $row = $i+2;
                $objPHPExcel->getSheetByName('Jobsite')->setCellValue("F$row", $result[$i]["Status"]);
                $objPHPExcel->getSheetByName('Jobsite')->setCellValue("G$row", $result[$i]["Result"]);
            }

            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save('../temp/jobsite_import_result.xlsx');
//            header('Set-Cookie: fileLoading=true');
            setcookie("fileLoading", true);
            if ($allErrors == true){
//                header('Set-Cookie: fileErrors="all"');
                setcookie("fileErrors", "all");
            }elseif($someErrors == true){
//                header('Set-Cookie: fileErrors="some"');
                setcookie("fileErrors", "some");
            }else{
//                header('Set-Cookie: fileErrors="none"');
                setcookie("fileErrors", "none");
            }
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="jobsite_import_result.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit();
        }

        return $this->render('jobsite', [
            'model' => $model,
        ]);
    }

    /**
     * Crea el template para carga de jobsites
     *
     * @return mixed Jobsite template
     */
    public function actionJobsiteTemplate()
    {
        $excel = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $excel = $excel->load("../excel_templates/JobsiteTemplate.xlsx");
        $excel->setActiveSheetIndex(0);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        // We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="JobsiteTemplate.xlsx"');
        $objWriter->save('../temp/JobsiteTemplate.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        header('Content-Disposition: attachment; filename="JobsiteTemplate.xlsx"');
        header('Content-Length: ' . filesize("../temp/JobsiteTemplate.xlsx"));
        //ob_clean();
        //ob_end_clean();        
        //flush();
        $objWriter->save('php://output');
        //readfile("../temp/UserTemplate.xlsx");
        exit();
    }

    /**
     * Carga y procesa el template cargado para creacion de Users.
     * Recibe el User Template con datos
     *
     * @return mixed Excel cargado con resultados
     */
    public function actionUser()
    {
        $model = new Import();

        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {
            $model->file = UploadedFile::getInstance($model, 'file');

            // read excel
            $excelHelper = new excelHelper();

            // $cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            // $cacheSettings = array( ' memoryCacheSize ' => '8MB');
            // \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $objPHPExcel = $objReader->load($model->file->tempName);

            $errors = array();
            if($objPHPExcel->sheetNameExists("Users")){
                //busco la hoja de productos
                $objData = $objPHPExcel->getSheetByName('Users');

                //convierto la hoja a array
                $arrayData = $excelHelper->excelToArray($objData);

            }else{
                $errors[]= "Missing 'Users' tab";
                return $this->render('user', [
                    'model' => $model
                ]);
            }
            //comprueba la integridad de los datos y los graba - devuelve resultados
            $result = $excelHelper->processData($arrayData, $excelHelper::EXCEL_USERS);

            $objPHPExcel->getSheetByName('Users')->setCellValue("L1", "Status");
            $objPHPExcel->getSheetByName('Users')->setCellValue("M1", "Action");
            $objPHPExcel->getSheetByName('Users')->setCellValue("N1", "Comment");
            $objPHPExcel->getSheetByName('Users')->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Users')->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Users')->getColumnDimension('N')->setAutoSize(true);
            // $objPHPExcel->getSheetByName('Users')->getStyle("L1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            // $objPHPExcel->getSheetByName('Users')->getStyle("M1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            // $objPHPExcel->getSheetByName('Users')->getStyle("N1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            $objPHPExcel->getSheetByName('Users')->getStyle("L1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffc000');
            $objPHPExcel->getSheetByName('Users')->getStyle("M1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffc000');
            $objPHPExcel->getSheetByName('Users')->getStyle("N1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffc000');
            for($i = 0; $i < sizeof($result); $i++){
                $row = $i+2;
                $objPHPExcel->getSheetByName('Users')->setCellValue("L$row", $result[$i]["Status"]);
                $objPHPExcel->getSheetByName('Users')->setCellValue("M$row", $result[$i]["Action"]);
                $objPHPExcel->getSheetByName('Users')->setCellValue("N$row", $result[$i]["Result"]);
            }

            $someErrors = false;
            $allErrors = true;
            foreach($result as $row){
                if($row["Status"] == "ERROR"){
                    $someErrors = true;
                }elseif($row["Status"] == "OK"){
                    $allErrors = false;
                }
            }

            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save('../temp/users_import_result.xlsx');
            setcookie("fileLoading", true);
            if ($allErrors == true){
                setcookie("fileErrors", "all");
            }elseif($someErrors == true){
                setcookie("fileErrors", "some");
            }else{
                setcookie("fileErrors", "none");
            }
//            header('Set-Cookie: fileLoading=true');
            // header('Content-type: application/vnd.ms-excel');
            // header('Cache-Control: max-age=0');
            // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            // header ('Cache-Control: cache, must-revalidate');
            // header ('Pragma: public');
            // header('Content-Disposition: attachment; filename="users_import_result.xlsx"');
            // header('Content-Length: ' . filesize("../temp/users_import_result.xlsx"));
            // ob_clean();
            // flush();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="users_import_result.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            //readfile("../temp/users_import_result.xlsx");
            exit();
        }

        return $this->render('user', [
            'model' => $model,
        ]);
    }

    /**
     * Crea el template para carga de users
     *
     * @return mixed User template
     */
    public function actionUserTemplate()
    {
        
        $queryparams = Yii::$app->request->queryParams;
        $contractor = Contractor::find()->where(['!=','contractor.id', 148])->joinWith('contractorJobsites')->andWhere(["is_active" => 1, "jobsite_id" => $queryparams['jobid']])->orderBy("contractor")->all();
         
        if( Yii::$app->session->get('user.role_id') == ROLE_ADMIN){
            $rol = \app\models\Role::find()->select(['role'])->where(['not in','id',[1,2,3,4,5,6,16,18,19]])->orderBy("role")->asArray()->all();
        }else{
            $rol = \app\models\Role::find()->select(['role'])->where(['not in','id',[1,2,3,4,5,6,16,18,19]])->orderBy("role")->asArray()->all();
        }
        
        $excel = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $excel = $excel->load("../excel_templates/UserTemplate.xlsx");
        $excel->setActiveSheetIndexByName("Setup");

        for($i = 0; $i < sizeof($contractor);$i++){
            $row = $i+2;
                $excel->getActiveSheet()
                    ->setCellValue("A$row", $contractor[$i]->contractor);
        }

        $rol_arr = ArrayHelper::getColumn($rol, 'role');
        $rol_columnArray = array_chunk($rol_arr, 1);
        $excel->getActiveSheet()
            ->fromArray(
                $rol_columnArray,  // The data to set
                NULL,        // Array values with this value will not be set
                "B2"         // Top left coordinate of the worksheet range where
                             //    we want to set these values (default is A1)
            );

        // for($i = 0; $i < sizeof($rol);$i++){
        //     $row = $i+2;
        //         $excel->getActiveSheet()
        //             ->setCellValue("B$row", $rol[$i]->role);
        // }

        $user_jobsite = UserJobsite::find()->select(['jobsite_id'])->where( [ "user_id" => Yii::$app->session->get('user.id'),"jobsite_id" => $queryparams['jobid'] ] )->asArray()->all();
        // get columns jobsite ID
        $array_index_jobsite_id = ArrayHelper::getColumn($user_jobsite, 'jobsite_id');
        // get jobsite
        $jobsite = Jobsite::find()->select('id, jobsite')->where(['id'=>$array_index_jobsite_id, 'is_active' => 1])->orderBy("jobsite")->all();
        for($i = 0; $i < sizeof($jobsite);$i++){
            $row = $i+2;
            $excel->getActiveSheet()->setCellValue("D$row", $jobsite[$i]->jobsite);
        }

        $excel->setActiveSheetIndex(0);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        
        // We'll be outputting an excel file
		// header('Content-type: application/vnd.ms-excel');
		// header('Content-Disposition: attachment; filename="UserTemplate.xlsx"');
		// for ($i = 0; $i < ob_get_level(); $i++) {
		//    ob_end_flush();
		// }
		// ob_implicit_flush(1);
		// ob_clean();
        $objWriter->save('../temp/UserTemplate.xlsx');
        // header('Content-type: application/vnd.ms-excel');
        // header('Cache-Control: max-age=0');
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        // header ('Cache-Control: cache, must-revalidate');
        // header ('Pragma: public');
        // header('Content-Disposition: attachment; filename="UserTemplate.xlsx"');
        // header('Content-Length: ' . filesize("../temp/UserTemplate.xlsx"));
        //ob_clean();
        //ob_end_clean();        
        //flush();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="UserTemplate.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        //readfile("../temp/UserTemplate.xlsx");
        exit();
    }

    /**
     * Carga y procesa el template cargado para creacion de Contractors.
     * Recibe el Contractor Template con datos
     *
     * @return mixed Excel cargado con resultados
     */
    public function actionContractor()
    {
        $model = new Import();

        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {
            $model->file = UploadedFile::getInstance($model, 'file');

            // read excel
            $excelHelper = new excelHelper();

            $cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array( ' memoryCacheSize ' => '8MB');
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($model->file->tempName);

            //////////
            //CREATE//
            //////////
            $errors = array();
            if($objPHPExcel->sheetNameExists("New Contractors")){
                //busco la hoja de productos
                $objData = $objPHPExcel->getSheetByName('New Contractors');
                //convierto la hoja a array
                $arrayData = $excelHelper->excelToArray($objData);
            }else{
                $errors[]= "Missing 'New Contractors' tab";
                return $this->render('contractor', [
                    'model' => $model
                ]);
            }
            //comprueba la integridad de los datos y los graba - devuelve resultados
            $resultCreate = $excelHelper->processData($arrayData, $excelHelper::EXCEL_CONTRACTOR_CREATE);
            
            $objPHPExcel->getSheetByName('New Contractors')->setCellValue("L1", "Status");
            $objPHPExcel->getSheetByName('New Contractors')->setCellValue("M1", "Action");
            $objPHPExcel->getSheetByName('New Contractors')->setCellValue("N1", "Comments");
            $objPHPExcel->getSheetByName('New Contractors')->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getSheetByName('New Contractors')->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getSheetByName('New Contractors')->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getSheetByName('New Contractors')->getStyle("L1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            $objPHPExcel->getSheetByName('New Contractors')->getStyle("M1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            $objPHPExcel->getSheetByName('New Contractors')->getStyle("N1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            for($i = 0; $i < sizeof($resultCreate); $i++){
                $row = $i+2;
                $objPHPExcel->getSheetByName('New Contractors')->setCellValue("L$row", $resultCreate[$i]["Status"]);
                $objPHPExcel->getSheetByName('New Contractors')->setCellValue("M$row", $resultCreate[$i]["Action"]);
                $objPHPExcel->getSheetByName('New Contractors')->setCellValue("N$row", $resultCreate[$i]["Result"]);
            }

            //////////
            //UPDATE//
            //////////
            $errors = array();
            if($objPHPExcel->sheetNameExists("Update Contractors")){
                //busco la hoja de productos
                $objData = $objPHPExcel->getSheetByName('Update Contractors');
                //convierto la hoja a array
                $arrayData = $excelHelper->excelToArray($objData);
            }else{
                $errors[]= "Missing 'Update Contractors' tab";
                return $this->render('contractor', [
                    'model' => $model
                ]);
            }
            //comprueba la integridad de los datos y los graba - devuelve resultados
            $resultUpdate = $excelHelper->processData($arrayData, $excelHelper::EXCEL_CONTRACTOR_UPDATE);

            $objPHPExcel->getSheetByName('Update Contractors')->setCellValue("L1", "Status");
            $objPHPExcel->getSheetByName('Update Contractors')->setCellValue("M1", "Action");
            $objPHPExcel->getSheetByName('Update Contractors')->setCellValue("N1", "Comments");
            $objPHPExcel->getSheetByName('Update Contractors')->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Update Contractors')->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Update Contractors')->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getSheetByName('Update Contractors')->getStyle("L1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            $objPHPExcel->getSheetByName('Update Contractors')->getStyle("M1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            $objPHPExcel->getSheetByName('Update Contractors')->getStyle("N1")->applyFromArray(array('fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));;
            for($i = 0; $i < sizeof($resultUpdate); $i++){
                $row = $i+2;
                $objPHPExcel->getSheetByName('Update Contractors')->setCellValue("L$row", $resultUpdate[$i]["Status"]);
                $objPHPExcel->getSheetByName('Update Contractors')->setCellValue("M$row", $resultUpdate[$i]["Action"]);
                $objPHPExcel->getSheetByName('Update Contractors')->setCellValue("N$row", $resultUpdate[$i]["Result"]);
            }

            $result = array_merge($resultCreate, $resultUpdate);
            $someErrors = false;
            $allErrors = true;
            foreach($result as $row){
                if($row["Status"] == "ERROR"){
                    $someErrors = true;
                }elseif($row["Status"] == "OK"){
                    $allErrors = false;
                }
            }

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('../temp/contractors_import_result.xlsx');
            setcookie("fileLoading", true);
            if ($allErrors == true){
                setcookie("fileErrors", "all");
            }elseif($someErrors == true){
                setcookie("fileErrors", "some");
            }else{
                setcookie("fileErrors", "none");
            }
            header('Content-type: application/vnd.ms-excel');
            header('Cache-Control: max-age=0');
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');
            header('Content-Disposition: attachment; filename="contractors_import_result.xlsx"');
            header('Content-Length: ' . filesize("../temp/contractors_import_result.xlsx"));
            ob_clean();
            flush();
            readfile("../temp/contractors_import_result.xlsx");
        }

        return $this->render('contractor', [
            'model' => $model,
        ]);
    }

    /**
     * Crea el template para carga de Contractor
     *
     * @return mixed Contractor template
     */
    public function actionContractorTemplate()
    {
        $contractor = Contractor::find()->orderBy("contractor")->all();

        $excel = \PHPExcel_IOFactory::createReader("Excel2007");
        $excel = $excel->load("../excel_templates/ContractorTemplate.xlsx");
        $excel->setActiveSheetIndexByName("Setup");

        for($i = 0; $i < sizeof($contractor);$i++){
            $row = $i+2;
            $excel->getActiveSheet()->setCellValue("A$row", $contractor[$i]->contractor);
            $excel->getActiveSheet()->setCellValue("B$row", $contractor[$i]->is_active == 1? "YES":"NO");
            $excel->getActiveSheet()->setCellValue("C$row", $contractor[$i]->vendor_number);
            $excel->getActiveSheet()->setCellValue("D$row", $contractor[$i]->address);
        }

//        if( Yii::$app->session->get('user.role_id') == ROLE_ADMIN){}
        $user_jobsite = UserJobsite::find()->select(['jobsite_id'])->where( [ "user_id" => Yii::$app->session->get('user.id') ] )->asArray()->all();
        // get columns jobsite ID
        $array_index_jobsite_id = ArrayHelper::getColumn($user_jobsite, 'jobsite_id');
        // get jobsite
        $jobsite = Jobsite::find()->select('id, jobsite')->where(['id'=>$array_index_jobsite_id, 'is_active' => 1])->orderBy("jobsite")->all();
        for($i = 0; $i < sizeof($jobsite);$i++){
            $row = $i+2;
            $excel->getActiveSheet()->setCellValue("F$row", $jobsite[$i]->jobsite);
        }

        $excel->setActiveSheetIndex(0);

        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('../temp/ContractorTemplate.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        header('Content-Disposition: attachment; filename="ContractorTemplate.xlsx"');
        header('Content-Length: ' . filesize("../temp/ContractorTemplate.xlsx"));
        ob_clean();
        flush();
        readfile("../temp/ContractorTemplate.xlsx");
        exit();

//        header('Content-type: application/vnd.ms-excel');
//        header('Cache-Control: max-age=0');
//        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
//        header ('Cache-Control: cache, must-revalidate');
//        header ('Pragma: public');
//        header('Content-Disposition: attachment; filename="ContractorTemplate.xlsx"');
//        header('Content-Length: ' . filesize("../excel_templates/ContractorTemplate.xlsx"));
//        ob_clean();
//        flush();
//        readfile("../excel_templates/ContractorTemplate.xlsx");
//        exit();
    }
}
