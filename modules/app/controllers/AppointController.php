<?php

namespace app\modules\app\controllers;

use Yii;
use app\models\TblPatient;
use app\models\TblPatientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\AppointModel;
use app\models\TblAppoint;
use yii\db\conditions\AndCondition;
use yii\web\HttpException;
use common\components\AppQuery;
use common\components\Util;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/**
 * AppointController implements the CRUD actions for TblPatient model.
 */
class AppointController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TblPatient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TblPatientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblPatient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * รายชื่อแผนก /app/appoint/create-department
     * Creates a new TblPatient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateDepartment() //เลือกแผนก/คลีนิคหลัก
    {
        $session = Yii::$app->session;
        // if (!$session->get('user')) {
        //     return $this->redirect(['/']);
        // }
        $departments = AppQuery::getDepartmentGroupHomc(); //ชื่อกลุ่มแผนก
        $DeptGroups = ArrayHelper::map($departments, 'DeptGroup', 'DeptGrDesc');

        return $this->render('_form_department.php', [
            'DeptGroups' => $DeptGroups,
        ]);
    }

    /**
     * เลือกแผนก
     * /app/appoint/create-sub-department?id={deptGroup}
     */
    public function actionCreateSubDepartment($deptgroup) //เลือกแผนกย่อย เงื่อนไข คือรหัสแผนก จะต้องมี Appoint_hide ให้ค่า null และ จะต้อง มี รหัสแผนกนั้น ใน ตาราง DOCC_LimitApp
    {
        // $session = Yii::$app->session;
        // if (!$session->get('user')) {
        //     return $this->redirect(['/']);
        // }

        $DeptGrDesc = AppQuery::getDeptGrById($deptgroup);
        $departments = AppQuery::getDepartmentByDeptGroupHomc($deptgroup); //ชื่อแผนกจากฐานข้อมูลโรงพยาบาล

        // $services = AppQuery::getServices(); //ชื่อแผนกจากระบบคิว
        // $map_services = ArrayHelper::map($services, 'service_code', 'service_id');

        // $map_departments = [];
        // foreach ($departments as $key => $department) {
        //     if (ArrayHelper::getValue($map_services, $department['deptCode'], null) != null) {
        //         $map_departments[] = ArrayHelper::merge($department, [
        //             'service_id' => ArrayHelper::getValue($map_services, $department['deptCode'], null),
        //         ]);
        //     }
        // }

        $basePath = Yii::getAlias('@web/images');
        $images = [
            '020' => $basePath . '/scalpel3.png',
            '021' => $basePath . '/scalpel5 copy.png',
            '022' => $basePath . '/scalpel1.png',
            '023' => $basePath . '/brain.png',
            '026' => $basePath . '/newborn.png',
            '0294' => $basePath . '/cardiology.png',
            '0297' => $basePath . '/blood-test.png',
            '025' => $basePath . '/scalpel9.png',
        ];

        return $this->render('_form_sub_department.php', [
            'DeptGrDesc' => $DeptGrDesc,
            // 'deptCodeSub' => $map_departments,
            'images' => $images,
            'departments' => $departments,
            'deptgroup' => $deptgroup
        ]);
    }

    /**
     * ตรวจสอบแพทย์ไม่ระบุ
     * /app/appoint/check-schedule-doctor?dept_code={dept_code}
     */
    public function actionCheckScheduleDoctor($dept_code)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $doctors = AppQuery::getDoctorByDeptcode($dept_code);
        $doctor_ids =  ArrayHelper::getValue($doctors, 'doctor_ids', []);
        $doctor_ids = ArrayHelper::merge($doctor_ids, [1]);

        $schedules = AppQuery::getScheduleByDoctorIds($doctor_ids);
        $ids = array_unique(ArrayHelper::getColumn($schedules, 'doctor_id'));
        $isInArray = ArrayHelper::isIn('1', $ids);

        return [
            'value' => $isInArray
        ];
    }


    /**
     * Updates an existing TblPatient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblPatient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblPatient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblPatient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblPatient::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * นัดหมายระบุแพทย์ /app/appoint/create-appointments?id={id}
     * @param {string} id > รหัสแผนก
     * @param {number} doc_id รหัสประจำตัวแพทย์
     */
    public function actionCreateAppointments($deptgroup, $deptcode, $doccode = null)  //ระบุแพทย์
    {
        $model = new AppointModel();

        //ชื่อแผนกย่อย (ใช้ตรง header)
        $deptCodeSub = AppQuery::getDepartmentById($deptcode);

        // $doctors = AppQuery::getDoctorByDeptcode($id);
        // $doctor_ids =  ArrayHelper::getValue($doctors, 'doctor_ids', []);
        // $doctors_list = AppQuery::getDoctorListByDoctorIds($doctor_ids);
        $doctors = AppQuery::getDoctorByDeptcode($deptcode);

        return $this->render('_form_appointments', [
            'deptCodeSub' => $deptCodeSub,
            // // 'docCode' => $docCode, //รายชื่อแพทย์ทั้งหมดตามแผนกที่เลือก
            // 'doctors' => $doctors_list,
            'deptgroup' => $deptgroup,
            'dept_code' => $deptcode,
            'doccode' => $doccode,
            'model' => $model,
            // 'doctors_dbo' => $doctors_dbo
            'doctors' => $doctors
        ]);
    }

    /**
     * นัดหมายไม่ระบุแพทย์ /app/appoint/appointments-undocter?id={id}
     * @param {String} id รหัสแผนก
     */
    public function actionAppointmentsUndocter($id, $doc_id = '') //ไม่ระบุแพทย์
    {
        $model = new AppointModel();

        //ชื่อแผนกย่อย (ใช้ตรง header)
        $deptCodeSub = AppQuery::getDepartmentById($id);

        $DeptGroup = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPTGROUP.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPTGROUP.DeptGroup, \' \', \'\') as DeptGroup'
            ])
            ->from('DEPTGROUP')
            ->where(['REPLACE(DEPTGROUP.deptCode, \' \', \'\')' => $id])
            ->one(Yii::$app->mssql);

        $DeptGroupQueue = (new \yii\db\Query())
            ->select(['tbl_dept_group.*'])
            ->from('tbl_dept_group')
            ->where(['dept_group' => $DeptGroup['DeptGroup']])
            ->all(Yii::$app->db_queue);

        $service_ids = ArrayHelper::getColumn($DeptGroupQueue, 'service_id');
        $schedules = AppQuery::getScheduleByServiceIdAndDoctorId($service_ids, 1);
        $dates =  ArrayHelper::getColumn($schedules, 'schedule_date');

        $dateList = [];

        foreach ($dates as $key => $date) {
            $dateList[] = Yii::$app->formatter->asDate($date, 'php:d/m/Y');
        }

        $startDate = '';
        if ($dateList) {
            $startDate = $dateList[0];
        }
        $endDate = '';
        if ($dateList) {
            $endDate = array_slice($dateList, -1)[0];
        }

        return $this->render('_form_undocter_appointments', [
            'deptCodeSub' => $deptCodeSub,
            'dept_code' => $id,
            'model' => $model,
            'dateList' => $dateList,
            'endDate' => $endDate,
            'startDate' => $startDate,
        ]);
    }

    /*
    public function actionCreateAppointments1($id)
    {
        $db_mssql = Yii::$app->mssql;
        $db_queue = Yii::$app->db_queue;

        $deptCodeSub = $db_mssql->createCommand(
            'SELECT
            REPLACE(dbo.DEPT.deptCode, \' \', \'\') as deptCode,
            dbo.DEPT.deptDesc
            FROM
            dbo.DEPTGROUP
            INNER JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.DEPTGROUP.deptCode
            WHERE
            dbo.DEPT.deptCode = :deptCode
            '
        )
            ->bindValues([
                ':deptCode' => $id
            ])
            ->queryOne();

        $docCode = $db_mssql->createCommand(
            'SELECT
            REPLACE(dbo.DOCC.docCode, \' \', \'\') as docCode,
            REPLACE(dbo.DOCC.doctitle, \' \', \'\') as doctitle,
            REPLACE(dbo.DOCC.docName, \' \', \'\') as docName,
            REPLACE(dbo.DOCC.docLName, \' \', \'\') as docLName,
            dbo.DEPT.deptDesc,
            dbo.DEPT.deptCode
            FROM
            dbo.DOCC
            INNER JOIN dbo.Appoint_dep_doc ON dbo.Appoint_dep_doc.docCode = dbo.DOCC.docCode
            LEFT JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.Appoint_dep_doc.deptCode
            WHERE
            dbo.DEPT.deptCode = :deptCode
            '
        )
            ->bindValues([
                ':deptCode' => $id
            ])
            ->queryAll();

        // รหัสแผนกที่เปิดให้บริการจองนัดหมาย
        $query_dept_codes = $db_mssql->createCommand('SELECT
                DEPTGROUP.*
            FROM
                DEPTGROUP')
            ->queryAll();
        $dept_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_dept_codes, 'deptCode'));

        // // รายชื่อแผนกในระบบคิว
        // $services = $db_queue->createCommand('SELECT
        //         tbl_service.service_id,
        //         tbl_service.service_name,
        //         tbl_service.service_code,
        //         tbl_service_group.service_group_name,
        //         tbl_service.icon_path,
        //         tbl_service.icon_base_url
        //     FROM
        //         tbl_service
        //         INNER JOIN tbl_service_group ON tbl_service.service_group_id = tbl_service_group.service_group_id
        //     WHERE
        //         tbl_service.service_code IN (' . implode(",", $dept_codes) . ')')
        //     ->queryAll();
        // $group_services = ArrayHelper::index($services, null, 'service_group_name');


        $doctors_list = [];
        $schedules = [];
        // รายชื่อแผนก
        $service = $db_queue->createCommand('SELECT 
                tbl_service.service_id,
                tbl_service.service_name,
                tbl_service.service_code,
                tbl_service_group.service_group_name,
                tbl_service.icon_path,
                tbl_service.icon_base_url
            FROM
                tbl_service
                INNER JOIN tbl_service_group ON tbl_service.service_group_id = tbl_service_group.service_group_id
            WHERE
                tbl_service.service_code = ' . $id . '')
            ->queryOne();


        // รหัสแพทย์
        // $query_doc_codes = $db_mssql->createCommand('SELECT
        //         Appoint_dep_doc.docCode
        //     FROM
        //         Appoint_dep_doc
        //     WHERE
        //         Appoint_dep_doc.deptCode = :deptCode')
        //     ->bindValues([':deptCode' => $id])
        //     ->queryAll();
        // $doc_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_doc_codes, 'docCode'));

        // // ข้อมูลแพทย์ในระบบคิว
        // $doctors = $db_queue->createCommand('SELECT
        //         tbl_doctor.*,
        //         CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name
        //     FROM
        //         tbl_doctor
        //     WHERE
        //         tbl_doctor.doctor_code IN (' . implode(",", $doc_codes) . ')')
        //     ->queryAll();

        // foreach ($doctors as $doctor) {
        //     $doctors_list = ArrayHelper::merge($doctors_list, [ArrayHelper::merge($doctor, [
        //         'service_id' => $service['service_id'],
        //         'service_name' => $service['service_name'],
        //         'service_code' => $service['service_code'],
        //     ])]);
        // }

        return $this->render('_form_appointments', [
            'deptCodeSub' => $deptCodeSub,
            'docCode' => $docCode,
            'doctors' => $doctors_list,
            'service' => $service,
        ]);
    }*/

    /**
     * ตารางแพทย์ /app/appoint/schedules?doc_id={doc_id}
     * @param {String} doc_id รหัสแแพทย์
     */
    public function actionSchedules()
    {
        $response = Yii::$app->response;
        $request = \Yii::$app->request;
        $doc_id = $request->post('doc_id');
        $deptgroup = $request->post('deptgroup');
        $deptcode = $request->post('deptcode');

        $response->format = \yii\web\Response::FORMAT_JSON;

        // ข้อมูลตารางแพทย์ระบบคิว
        // $med_schedules = AppQuery::getScheduleByDoctorCode($doc_id);
        // return $med_schedules;

        //ข้อมูลตารางแพทย์จาก ระบบโรงพยาบาล
        $dayofweek = [0, 1, 2, 3, 4, 5, 6];
        $schedules = AppQuery::getScheduleDoctorByDbo($doc_id, $deptgroup, $deptcode);
        $doccHoliday = AppQuery::getDoccHoliday($doc_id);
        $holiday = AppQuery::getHoliday();

        $holidays = [];
        foreach ($holiday as $data) { //วันหยุดนักขัตฤกษ
            $arr = explode("-", $data['h_date']);
            $y = $arr[0] - 543;
            $m = $arr[1];
            $d = $arr[2];

            $holidays[] = [
                'title' => $data['h_comm'],
                'date' => "$d/$m/$y",
            ];
        }

        foreach ($doccHoliday as $data) { //วันหยุดแพทย์
            $y = substr($data['Holdate'], 0, 4) - 543;
            $m = substr($data['Holdate'], 4, -2);
            $d = substr($data['Holdate'], -2);

            $holidays[] = [
                'title' => 'วันหยุดแพทย์',
                'date' => "$d/$m/$y",
            ];
        }

        $datesDisabled1 = array_map(function ($v) { //วันหยุดนักขัตฤกษ
            $arr = explode("-", $v);
            $y = $arr[0] - 543;
            $m = $arr[1];
            $d = $arr[2];
            return "$d/$m/$y";
        }, ArrayHelper::getColumn($holiday, 'h_date'));

        $datesDisabled2 = array_map(function ($v) { //วันหยุดแพทย์
            $y = substr($v, 0, 4) - 543;
            $m = substr($v, 4, -2);
            $d = substr($v, -2);
            return "$d/$m/$y";
        }, ArrayHelper::getColumn($doccHoliday, 'Holdate'));

        $datesDisabled = ArrayHelper::merge($datesDisabled1, $datesDisabled2);

        $mapDayOfWeek =  array_map(function ($v) {
            if ($v == 7) {
                return 0;
            }
            return $v;
        }, ArrayHelper::getColumn($schedules, 'ad_orb'));

        $daysOfWeekDisabled = [];
        foreach ($dayofweek as $v) {
            if (!in_array($v, $mapDayOfWeek)) {
                $daysOfWeekDisabled[] = $v;
            }
        }


        return [
            'mapDayOfWeek' => $mapDayOfWeek,
            'daysOfWeekDisabled' => $daysOfWeekDisabled,
            'datesDisabled' => $datesDisabled,
            'holidays' => $holidays,
            'schedules' => $schedules
        ];
    }

    public function actionScheduleTimes()  //ตารางแพทย์
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $request = \Yii::$app->request;
        $attributes = $request->post('AppointModel', []);
        $doc_code = ArrayHelper::getValue($attributes, 'doc_code', '');
        $dept_code = ArrayHelper::getValue($attributes, 'dept_code', '');
        $deptgroup = $request->post('deptgroup');
        $day = \Yii::$app->request->post('day');

        if ($day == 0) {
            $day = 7;
        }

        $schedules = AppQuery::getScheduleDoctorByDbo($doc_code, $deptgroup, $dept_code, $day);


        $schedule_times =  array_map(function ($item) {
            return [
                'text' => $item['stTime'] . '-' . $item['edTime'] . ' น.',
                'value' => $item['stTime'] . '-' . $item['edTime'],
            ];
        }, $schedules);
        return [
            'schedule_times' => $schedule_times,
        ];
        /* $attributes = \Yii::$app->request->post('AppointModel', []);
        $appoint_date = \Yii::$app->request->post('appoint_date', '');  //;วันที่แพทย์ออกตรวจ
        $doc_code = ArrayHelper::getValue($attributes, 'doc_code', '');
        $dept_code = ArrayHelper::getValue($attributes, 'dept_code', '');

        $doctors = AppQuery::getDoctorByDeptcode($attributes['dept_code']);
        $doc_codes = ArrayHelper::getValue($doctors, 'doc_codes', []);
        $schedule_times = AppQuery::getSubScheduleTimes($appoint_date, $doc_code, $attributes['dept_code']);

        $result = [];
        $doctor = [];
        $list = '';

        foreach ($schedule_times as $key => $schedule_time) {
            if (ArrayHelper::isIn($schedule_time['doctor_code'], $doc_codes) ||  empty($attributes['doc_code'])) {
                $appoints = AppQuery::getAppointsHomc($appoint_date, $schedule_time, $attributes['doc_code'], $attributes['dept_code']);

                $text = count($appoints) >= $schedule_time['med_schedule_time_online_qty'] ? '(<span class="text-danger">เต็ม</span>)' : '';
                $result[] = [
                    'text' => Yii::$app->formatter->asDate($schedule_time['start_time'], 'php:H:i') . '-' . Yii::$app->formatter->asDate($schedule_time['end_time'], 'php:H:i') . ' น. ' . $text,
                    'value' => Yii::$app->formatter->asDate($schedule_time['start_time'], 'php:H:i') . '-' . Yii::$app->formatter->asDate($schedule_time['end_time'], 'php:H:i'),
                    'disabled' => count($appoints) >= $schedule_time['med_schedule_time_online_qty']
                ];
            }
        }

        return [
            'schedule_times' => $result,
            'doctor' => $doctor,
            'doc_codes' => $doc_codes,
            'schedule' => $schedule_times,
            'list' => $list,
        ]; */
    }

    /**
     * บันทึกรายการนัดหมาย
     * /app/appoint/save-appoint
     */
    public function actionSaveAppoint()
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        if (empty($profile)) {
            throw new HttpException(404, 'ไม่พบข้อมูลผู้ใช้งาน.');
        }
        $profile = $this->findModelProfile(['id_card' => $profile['id_card']]);
        $attributes = \Yii::$app->request->post('AppointModel', []);

        $model = new AppointModel();
        $model->load($attributes, '');
        if (!$model->validate()) {
            throw new HttpException(400, Json::encode($model->errors));
        }
        $appoint_time_from = \Yii::$app->request->post('appoint_time_from', '');
        $appoint_time_to = \Yii::$app->request->post('appoint_time_to', '');
        $deptgroup = \Yii::$app->request->post('deptgroup', '');
        $appoint_time = isset($attributes['appoint_time']) ? $attributes['appoint_time'] : '';
        $doc_option = \Yii::$app->request->post('doc_option', '');
        $db_mssql = Yii::$app->mssql;
        $formatter = Yii::$app->formatter;
        $appoint_date = explode("/", $attributes['appoint_date']);
        $attributes['appoint_date'] = $appoint_date[2] . '-' . $appoint_date[1] . '-' . $appoint_date[0]; // yyyy-mm-dd
        $dayofweek = date('w', strtotime($attributes['appoint_date'])); //1-7 sun-mon
        $appoint_date = ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543) . $formatter->asDate($attributes['appoint_date'], 'php:md'); //(yyyy+543)mmdd



        // $dept_group = (new \yii\db\Query()) //แผนกหลัก
        //     ->select([
        //         'REPLACE(DEPTGROUP.DeptGroup, \' \', \'\') as DeptGroup',
        //     ])
        //     ->from('DEPTGROUP')
        //     ->where(['deptCode' => $attributes['dept_code']])
        //     ->one($db_mssql);


        $DoccLimit = (new \yii\db\Query()) //จำนวนคิวนัดหมายตามที่แพทย์ลงบันทึก
            ->select(['DOCC_LimitApp.*', 'Appoint_day.*'])
            ->from('DOCC_LimitApp')
            ->innerJoin('Appoint_day', 'DOCC_LimitApp.appoint_date = Appoint_day.ad_id')
            ->where([
                'REPLACE(DOCC_LimitApp.docCode, \' \', \'\')' => $attributes['doc_code'],
                'DOCC_LimitApp.stTime' => $appoint_time_from,
                'DOCC_LimitApp.edTime' => $appoint_time_to,
                'Appoint_day.ad_orb' => $dayofweek,
                'REPLACE(DOCC_LimitApp.DeptGroup, \' \', \'\')' => $deptgroup,
            ])
            ->one($db_mssql);


        $count_appoint = (new \yii\db\Query()) //วันที่นัดหมายแพทย์
            ->select(['*'])
            ->from('Appoint')
            ->where([
                'REPLACE(Appoint.doctor, \' \', \'\')' => $attributes['doc_code'],
                'Appoint.appoint_date' => $appoint_date,
                'Appoint.appoint_time_from' => $appoint_time_from,
                'Appoint.appoint_time_to' => $appoint_time_to,
                'Appoint.pre_dept_code' =>  $attributes['dept_code']
            ])
            ->count('*', $db_mssql);



        if ($count_appoint >= ArrayHelper::getValue($DoccLimit, 'applimit', 0)) { //ตรวจสอบจำนวนที่รับนัด
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            throw new HttpException(422, 'ไม่สามารถทำรายการได้ เนื่องจากคิวเต็ม.');
        }


        $transaction = $db_mssql->beginTransaction();
        try {
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            // ตรวจสอบว่าเคยลงรายการนัดหรือยัง
            $history_appoints = [];

            $doctor = AppQuery::findOneDoctorByDocCode($attributes['doc_code']);

            $dept = AppQuery::findOneDeptByDeptCode($attributes['dept_code']);

            $params = [];


            // if ($doc_option == 'selection' && !empty($attributes['doc_code'])) {
            //     $params = [
            //         'maker' => 'queue online',
            //         'doc_code' => $attributes['doc_code'],
            //         'appoint_date' => $appoint_date,
            //         'pre_dept_code' => $attributes['dept_code'],
            //         'appoint_time_from' => $appoint_time_from,
            //         'appoint_time_to' => $appoint_time_to,
            //         'hn' => $profile['hn'],
            //         'CID' => $profile['id_card'],
            //         'status_in' => 'm', //สถานะระบบ status_in (m = mobile,c = ศูนย์บริการ,n = ระบบนัดใหม่,null= ระบบเดิม)
            //     ];
            //     $history_appoints = AppQuery::getHistoryAppoints($params);
            // } else {
            //     $params = [
            //         'maker' => 'queue online',
            //         'doc_code' => '',
            //         'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
            //         'pre_dept_code' => $attributes['dept_code'],
            //         'appoint_time_from' => $appoint_time_from,
            //         'appoint_time_to' => $appoint_time_to,
            //         'hn' => $profile['hn'],
            //         'CID' => $profile['id_card'],
            //         'status_in' => 'm', //สถานะระบบ นัดจาก mobile
            //     ];
            //     $history_appoints = AppQuery::getHistoryAppoints($params);
            // }

            $params = [ //ตรวจสอบ  ถ้าเลือกวันและเวลาเดียวกัน ไม่สามารถนัดได้
                // 'maker' => 'queue online mobile',
                // 'doc_code' => $attributes['doc_code'],
                'appoint_date' => $appoint_date,
                // 'pre_dept_code' => $attributes['dept_code'],
                'appoint_time_from' => $appoint_time_from,
                'appoint_time_to' => $appoint_time_to,
                'hn' => $profile['hn'],
                'CID' => $profile['id_card'],
                // 'status_in' => 'm', //สถานะระบบ status_in (m = mobile,c = ศูนย์บริการ,n = ระบบนัดใหม่,null= ระบบเดิม)
            ];
            $history_appoints = AppQuery::getHistoryAppoints($params);
            $params2 = [ // ตรวจสอบ เลือกแผนกเดิม และ วันเดิมไม่ได้
                // 'maker' => 'queue online mobile',
                // 'doc_code' => $attributes['doc_code'],
                'appoint_date' => $appoint_date, //วันนัด
                'pre_dept_code' => $attributes['dept_code'], //รหัสแผนก
                // 'appoint_time_from' => $appoint_time_from, //เวลาเริ่มนัด
                // 'appoint_time_to' => $appoint_time_to, //เวลาสิ้นสุดนัด
                'hn' => $profile['hn'], //รหัส hn
                'CID' => $profile['id_card'], //รหัสบัตรประจำตัวประชาชน 
                // 'status_in' => 'm', //สถานะระบบ status_in (m = mobile,c = ศูนย์บริการ,n = ระบบนัดใหม่,null= ระบบเดิม)
            ];
            $history_appoints2 = AppQuery::getHistoryAppoints($params2);

            $history_appoints3 = AppQuery::getHistoryAppoints([ //ตรวจสอบ เวลาคาบเกี่ยว จะไม่สามารถทำนัดได้
                'appoint_date' => $appoint_date,
                'appoint_time_from' => $appoint_time_from,
                'appoint_time_to' => $appoint_time_to,
                'hn' => $profile['hn'],
                'CID' => $profile['id_card']
            ]);

            if (!empty($history_appoints) || !empty($history_appoints2) || !empty($history_appoints3)) {
                throw new HttpException(422, 'มีนัดหมายแล้ว ไม่สามารถทำนัดซ้ำได้');
            }

            $db_mssql->createCommand()->insert('Appoint', [
                'app_type' => 'A',
                'doctor' => Util::sprintf($attributes['doc_code'], 6),
                'hn' =>  Util::sprintf($profile['hn'], 7),
                'appoint_date' => $appoint_date,
                'appoint_time_from' => $appoint_time_from,
                'appoint_time_to' => $appoint_time_to,
                'appoint_note' => '',
                'pre_dept_code' => $attributes['dept_code'],
                'CID' => $profile['id_card'],
                'phone' => $profile['phone_number'],
                'maker' => 'queue mobile',
                'keyin_time' => $formatter->asDate('now', 'php:Y-m-d H:i:s'),
                'status_in' => 'm',
            ])->execute();

            $qrcode = Yii::$app->security->generateRandomString();
            $appoint = [
                'doctor_name' => $doctor['doctitle'] . $doctor['docName'] . ' ' . $doctor['docLName'],
                'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:d/m/') . ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543),
                'appoint_time' => $appoint_time,
                'department_name' => $dept['deptDesc'],
                'hn' => $profile['hn'],
                'fullname' => $profile['first_name'] . ' ' . $profile['last_name'],
                'description' => $profile['hn'] ? 'กรุณาลงทะเบียนที่ Kiosk ก่อนเวลานัดหมาย 30 นาที ' : 'กรุณาลงทะเบียนผู้ป่วยใหม่ ที่งานเวชระเบียน ชั้น1 ก่อนเวลานัด 30 นาที!',
                'urlQRCode' => Url::base(true) . Url::to(['/site/qrcode', 'qrcode' => $qrcode])
            ];
            $AppointModel = \Yii::$app->request->post('AppointModel');
            $ap_date = explode("/", $AppointModel['appoint_date']);
            $modelAppoint = new TblAppoint();
            $modelAppoint->setAttributes([
                'hn' => $profile['hn'],
                'appoint_date' => $ap_date[2] . '-' . $ap_date[1] . '-' . $ap_date[0],
                'app_time_from' => $appoint_time_from,
                'app_time_to' => $appoint_time_to,
                'dept_code' => $attributes['dept_code'],
                'doc_code' => $attributes['doc_code'],
                'cid' => $profile['id_card'],
                'qrcode' => $qrcode
            ]);
            $modelAppoint->save();
            $transaction->commit();
            return [
                'message' => 'ทำรายการสำเร็จ',
                'appoint' => $appoint,
                'appoint_date' => $appoint_date,
                'hn' => $profile['hn'],
                'id_card' => $profile['id_card'],
                'doctor' => preg_replace('/\s+/', '', $attributes['doc_code']),
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function replaceEmptyString($arr = [])
    {
        $items = [];
        foreach ($arr as $value) {
            $items[] = preg_replace('/\s+/', '', $value);
        }
        return $items;
    }


    /**
     * /app/appoint/follow-up?hn={hn}&appoint_date={appoint_date}&doctor={doctor}&cid={cid}
     * @param {string} hn
     * @param {string} appoint_date วันที่นัดหมาย
     * @param {string} doctor แพทย์
     * @param {string} cid หมายเลขบัตร
     */
    public function actionFollowUp($hn = '', $appoint_date = '', $doctor = '', $cid = '')
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        $profile = $this->findModelProfile(['id_card' => $profile['id_card']]);

        $params = [
            'appoint_date' => $appoint_date,
            'cid' => $cid,
            'doctor' => str_replace(' ', '', $doctor),
            'hn' => $hn
        ];
        $appoint = AppQuery::getAppointFollowUp($params);

        if ($appoint && empty($hn)) {
            $appoint = ArrayHelper::merge($appoint, [
                'firstName' => $profile['first_name'],
                'lastName' => $profile['last_name'],
            ]);
        }


        return  $this->render('_form_follow_up', [
            'appoint' => $appoint,
            'message' => empty($hn) ? 'กรุณาติดต่องานเวชระเบียน ตามวันและเวลาที่ท่านนัดหมาย!' : ' กดบัตรคิว ณ จุดบริการ ตามวันและเวลาที่ท่านนัดหมาย!'
        ]);
    }

    public function actionProfile($userId)
    {
        $db_mssql = Yii::$app->mssql;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $profile = TblPatient::findOne(['line_id' => $userId]);

        if ($profile) {
            if (empty($profile->hn)) {
                $query = $db_mssql->createCommand('SELECT TOP
                1 REPLACE( dbo.PATIENT.hn, \' \', \'\') as hn,
                REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                REPLACE(dbo.PATIENT.lastName, \' \', \'\') as lastName,
                dbo.PATIENT.phone,
                dbo.PATIENT.birthDay,
                dbo.PATIENT.titleCode,
                REPLACE(dbo.PatSS.CardID, \' \', \'\') as CardID,
                CONVERT (
                datetime,
                SUBSTRING ( CONVERT ( CHAR, dbo.PATIENT.birthDay - 5430000 ), 1, 4 ) + (
                CASE
                WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) = \'00\' THEN
                \'07\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 5, 2 ) 
                    END 
                ) + (
                    CASE
                WHEN SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) = \'00\' THEN
                \'01\' ELSE SUBSTRING ( CONVERT ( CHAR, PATIENT.birthDay - 5430000 ), 7, 2 ) 
            END 
                ) 
            ) AS bday                   
            FROM
                dbo.PATIENT
                INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn 
            WHERE
            dbo.PatSS.CardID = :CardID')
                    ->bindValues([
                        ':CardID' => $profile['id_card']
                    ])
                    ->queryOne();
                if ($query) {
                    Yii::$app->db->createCommand()->update('tbl_patient', ['hn' => $query['hn']], ['id' => $profile['id']])->execute();
                    $profile['hn'] = $query['hn'];
                    $session = Yii::$app->session;
                    $session->set('user', $profile);
                }
            }

            $session = Yii::$app->session;
            $session->set('user', $profile);
        }

        return $profile;
    }

    public function actionUserHistory() //ประวัตใบนัดหมายแพทย์
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        $profile = $this->findModelProfile(['id_card' => $profile['id_card']]);

        $history = AppQuery::getUserHistory($profile);

        $rows = [];
        foreach ($history['result'] as $key => $value) {
            if (empty($value['firstName'])) {
                $value['firstName'] = $profile['first_name'];
                $value['lastName'] = $profile['last_name'];
            }
            $rows[] = $value;
        }
        $rows2 = [];
        $formatter = Yii::$app->formatter;
        $current_date_unix = $formatter->asTimestamp($formatter->asDate('now', 'php:Y-m-d')); // 2021-04-07 xxxxxxxxxx
        $start_date_unix = $formatter->asTimestamp(($formatter->asDate('now', 'php:Y') - 1) . $formatter->asDate('now', 'php:-m-d')); //2020-04-07 xxxxxxxxxx
        // 3333333333 วันที่นัดของปีที่แล้ว
        // 4444444444 1 ปีที่แล้ว
        // 5555555555 วันปัจจุบัน
        // 6666666666 วันที่นัด >= 1 ปีที่แล้ว

        foreach ($history['result2'] as $key => $value) {
            if ($formatter->asTimestamp($value['appoint_date2']) >= $start_date_unix) {
                if (empty($value['firstName'])) {
                    $value['firstName'] = $profile['first_name'];
                    $value['lastName'] = $profile['last_name'];
                }
                $rows2[] = $value;
            }
        }
        return $this->render('_form_user_history', [
            'history' => $rows,
            'history2' => $rows2
        ]);
    }

    public function actionAppointmentsHistory() //นัดหมายแพทย์จากประวัตินัดหมาย
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        $profile = $this->findModelProfile(['id_card' => $profile['id_card']]);
        $history = AppQuery::getAppointmentsHistory($profile);

        if ($history) {
            if (empty($history['firstName'])) {
                $history['firstName'] = $profile['first_name'];
                $history['lastName'] = $profile['last_name'];
            }
        }


        return $this->render('_appointments_history.php', [
            'history' => $history
        ]);
    }

    public function actionQueueStatus($hn = null)
    {
        $profile = null;
        if ($hn) {
            $profile = AppQuery::getQueueStatus($hn);
        }
        $session = Yii::$app->session;

        if (!$profile && $session->get('user')) {
            $user = $session->get('user');
            $profile = $this->findModelProfile(['id_card' => $user['id_card']]);
            $profile = [
                'firstName' => ArrayHelper::getValue($user, 'first_name', '-'),
                'lastName' => ArrayHelper::getValue($user, 'last_name', '-'),
                'hn' => $hn ? $hn : '-'
            ];
        }

        return $this->render('form_detail_status', [
            'profile' => $profile
        ]);
    }

    public function actionQueueList($hn)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $rows = $this->getDataQueue($hn); //queue ข้อมูล
        return $rows;
    }

    private function getDataQueue($hn)
    {
        $couters = (new \yii\db\Query())
            ->select(['tbl_counter_service.*'])
            ->from('tbl_counter_service')
            ->all(Yii::$app->db_queue);
        $map_couters = ArrayHelper::map($couters, 'counter_service_id', 'counter_service_name');
        $rows = AppQuery::getDataQueue($hn);
        $items = [];
        foreach ($rows as $key => $item) {
            $items[] = ArrayHelper::merge($item, [
                'queue_date' => Yii::$app->formatter->asDate($item['created_at'], 'php:d M Y'),
                'counter_service_name' => empty($item['counter_service_id1']) ? $item['counter_service_name'] : ArrayHelper::getValue($map_couters, $item['counter_service_id1'], '')
            ]);
        }
        return $items;
    }

    protected function findModelProfile($params)
    {
        if (($model = TblPatient::findOne($params)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Profile not found.');
    }
}
