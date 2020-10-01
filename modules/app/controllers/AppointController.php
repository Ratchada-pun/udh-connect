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
use yii\db\conditions\AndCondition;
use yii\web\HttpException;
use common\components\AppQuery;

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
        $departments = AppQuery::getDepartmentGroupHomc();
        $DeptGroups = ArrayHelper::map($departments, 'DeptGroup', 'DeptGrDesc');

        return $this->render('_form_department.php', [
            'DeptGroups' => $DeptGroups,
        ]);
    }

    /**
     * เลือกแผนก
     * /app/appoint/create-sub-department?id={deptGroup}
     */
    public function actionCreateSubDepartment($id) //เลือกแผนกย่อย
    {
        $session = Yii::$app->session;
        if (!$session->get('user')) {
            return $this->redirect(['/']);
        }

        $DeptGrDesc = AppQuery::getDeptGrById($id);
        $departments = AppQuery::getDepartmentByDeptGroupHomc($id);

        $services = AppQuery::getServices();
        $map_services = ArrayHelper::map($services, 'service_code', 'service_id');

        $map_departments = [];
        foreach ($departments as $key => $department) {
            if (ArrayHelper::getValue($map_services, $department['deptCode'], null) != null) {
                $map_departments[] = ArrayHelper::merge($department, [
                    'service_id' => ArrayHelper::getValue($map_services, $department['deptCode'], null),
                ]);
            }
        }

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
            'deptCodeSub' => $map_departments,
            'images' => $images,
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
    public function actionCreateAppointments($id, $doc_id = '')  //ระบุแพทย์
    {
        $model = new AppointModel();

        //ชื่อแผนกย่อย (ใช้ตรง header)
        $deptCodeSub = AppQuery::getDepartmentById($id);

        $doctors = AppQuery::getDoctorByDeptcode($id);
        $doctor_ids =  ArrayHelper::getValue($doctors, 'doctor_ids', []);
        $doctors_list = AppQuery::getDoctorListByDoctorIds($doctor_ids);

        return $this->render('_form_appointments', [
            'deptCodeSub' => $deptCodeSub,
            // 'docCode' => $docCode, //รายชื่อแพทย์ทั้งหมดตามแผนกที่เลือก
            'doctors' => $doctors_list,
            // 'service' => $service,
            'dept_code' => $id,
            'model' => $model,

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
    public function actionSchedules($doc_id)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        // ข้อมูลตารางแพทย์
        $med_schedules = AppQuery::getScheduleByDoctorCode($doc_id);
        return $med_schedules;
    }

    public function actionScheduleTimes()  //ตารางแพทย์
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $attributes = \Yii::$app->request->post('AppointModel', []);
        $appoint_date = \Yii::$app->request->post('appoint_date', '');  //;วันที่แพทย์ออกตรวจ
        $doc_code = ArrayHelper::getValue($attributes, 'doc_code', '');

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
        ];
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
        $attributes = \Yii::$app->request->post('AppointModel', []);
        $model = new AppointModel();
        $model->load($attributes, '');
        if (!$model->validate()) {
            throw new HttpException(400, Json::encode($model->errors));
        }
        $appoint_time_from = \Yii::$app->request->post('appoint_time_from', '');
        $appoint_time_to = \Yii::$app->request->post('appoint_time_to', '');
        $appoint_time = isset($attributes['appoint_time']) ? $attributes['appoint_time'] : '';
        $doc_option = \Yii::$app->request->post('doc_option', '');
        $db_mssql = Yii::$app->mssql;
        $formatter = Yii::$app->formatter;
        $appoint_date = explode("/", $attributes['appoint_date']);
        $attributes['appoint_date'] = $appoint_date[2] . '-' . $appoint_date[1] . '-' . $appoint_date[0];
        $transaction = $db_mssql->beginTransaction();
        try {
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            // ตรวจสอบว่าเคยลงรายการนัดหรือยัง
            $history_appoints = [];

            $doctor = AppQuery::findOneDoctorByDocCode($attributes['doc_code']);

            $dept = AppQuery::findOneDeptByDeptCode($attributes['dept_code']);

            $params = [];

            if ($doc_option == 'selection' && !empty($attributes['doc_code'])) {
                $params = [
                    'maker' => 'queue online',
                    'doc_code' => $attributes['doc_code'],
                    'appoint_date' => ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543) . $formatter->asDate($attributes['appoint_date'], 'php:md'),
                    'pre_dept_code' => $attributes['dept_code'],
                    'appoint_time_from' => $appoint_time_from,
                    'appoint_time_to' => $appoint_time_to,
                    'hn' => $profile['hn'],
                    'CID' => $profile['id_card'],
                ];
                $history_appoints = AppQuery::getHistoryAppoints($params);
            } else {
                $params = [
                    'maker' => 'queue online',
                    'doc_code' => '',
                    'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
                    'pre_dept_code' => $attributes['dept_code'],
                    'appoint_time_from' => $appoint_time_from,
                    'appoint_time_to' => $appoint_time_to,
                    'hn' => $profile['hn'],
                    'CID' => $profile['id_card'],
                ];
                $history_appoints = AppQuery::getHistoryAppoints($params);
            }

            if (!empty($history_appoints)) {
                throw new HttpException(422, 'ไม่สามารถทำรายการได้ เนื่องจากคุณมีรายการนัดตามวัน,เวลา แผนก แพทย์ ที่เลือกอยู่แล้ว.');
            }

            $db_mssql->createCommand()->insert('Appoint', [
                'app_type' => 'A',
                'doctor' => empty($attributes['doc_code']) ? sprintf("% 6s", '0') : sprintf("% 6s", $attributes['doc_code']),
                'hn' => sprintf("% 7s", $profile['hn']),
                'appoint_date' => ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543) . $formatter->asDate($attributes['appoint_date'], 'php:md'),
                'appoint_time_from' => $appoint_time_from,
                'appoint_time_to' => $appoint_time_to,
                'appoint_note' => 'ทดสอบข้อมูล',
                'pre_dept_code' => $attributes['dept_code'],
                'CID' => $profile['id_card'],
                'phone' => $profile['phone_number'],
                'maker' => 'queue online',
                'keyin_time' => $formatter->asDate('now', 'php:Y-m-d H:i:s'),
            ])->execute();
            $appoint = [
                'doctor_name' => empty($attributes['doc_code']) ? 'ไม่ระบุแพทย์' : $doctor['doctitle'] . $doctor['docName'] . ' ' . $doctor['docLName'],
                'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:d M ') . ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543),
                'appoint_time' => $appoint_time,
                'department_name' => $dept['deptDesc'],
                'hn' => $profile['hn'],
                'fullname' => $profile['first_name'] . ' ' . $profile['last_name']
            ];
            $transaction->commit();
            return [
                'message' => 'ทำรายการสำเร็จ',
                'appoint' => $appoint,
                'appoint_date' => ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543) . $formatter->asDate($attributes['appoint_date'], 'php:md'),
                'hn' => $profile['hn'],
                'id_card' => $profile['id_card'],
                'doctor' => empty($attributes['doc_code']) ? sprintf("% 6s", '0') : sprintf("% 6s", $attributes['doc_code'])
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

        $params = [
            'appoint_date' => $appoint_date,
            'cid' => $cid,
            'doctor' => $doctor,
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
            'message' => empty($hn) ? 'กรุณาติดต่อห้องบัตร ตามวันและเวลาที่ท่านนัดหมาย!' : ' กดบัตรคิว ณ จุดบริการ ตามวันและเวลาที่ท่านนัดหมาย!'
        ]);
    }

    public function actionProfile($userId)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $profile = TblPatient::findOne(['line_id' => $userId]);

        if ($profile) {
            $session = Yii::$app->session;
            $session->set('user', $profile);
        }

        return $profile;
    }

    public function actionUserHistory() //ประวัตใบนัดแพทย์
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');

        $history = AppQuery::getUserHistory($profile);

        $rows = [];
        foreach ($history as $key => $value) {
            if (empty($value['firstName'])) {
                $value['firstName'] = $profile['first_name'];
                $value['lastName'] = $profile['last_name'];
            }
            $rows[] = $value;
        }
        return $this->render('_form_user_history', [
            'history' => $rows
        ]);
    }

    public function actionAppointmentsHistory() //นัดหมายแพทย์จากประวัตินัดหมาย
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
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

    public function actionQueueStatus($hn)
    {
        $profile = AppQuery::getQueueStatus($hn);
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
}
