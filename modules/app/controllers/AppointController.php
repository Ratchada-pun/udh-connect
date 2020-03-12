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
use yii\web\HttpException;

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
     * Creates a new TblPatient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateDepartment()
    {
        // $response = Yii::$app->response;
        // $response->format = \yii\web\Response::FORMAT_JSON;
        $DeptGroups = Yii::$app->mssql->createCommand(
            'SELECT
                REPLACE(dbo.DEPTGr.DeptGroup, \' \', \'\') as DeptGroup,
                REPLACE(dbo.DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc
            FROM
            dbo.DEPTGr
            '
        )->queryAll();

        return $this->render('_form_department.php', [
            'DeptGroups' => $DeptGroups,
        ]);
    }

    public function actionCreateSubDepartment($id)
    {
        $session = Yii::$app->session;
        if (!$session->get('user')) {
            return $this->redirect(['/']);
        }

        $params = [':DeptGroup' => $id];
        $search = Yii::$app->mssql->createCommand(
            'SELECT
            REPLACE(dbo.DEPT.deptCode, \' \', \'\') as deptCode,
            dbo.DEPT.deptDesc
            FROM
            dbo.DEPTGROUP
            INNER JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.DEPTGROUP.deptCode

            WHERE
            dbo.DEPTGROUP.DeptGroup = :DeptGroup'
        )
            ->bindValues($params)
            ->queryOne();

        $DeptGrDesc = Yii::$app->mssql->createCommand(
            'SELECT
            REPLACE(dbo.DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc
            FROM
            dbo.DEPTGr
            WHERE
            dbo.DEPTGr.DeptGroup = :DeptGroup'
        )
            ->bindValues($params)
            ->queryOne();

        $deptCodeSub = Yii::$app->mssql->createCommand(
            'SELECT
            REPLACE(dbo.DEPT.deptCode, \' \', \'\') as deptCode,
            dbo.DEPT.deptDesc
            FROM
            dbo.DEPTGROUP
            INNER JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.DEPTGROUP.deptCode

            WHERE
            dbo.DEPTGROUP.DeptGroup = :DeptGroup'
        )
            ->bindValues($params)
            ->queryAll();
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
        //     if ($query->load(Yii::$app->request->post()) && $query->save()) {

        //         return $this->redirect(['view', 'id' => $query->id]);

        // $query = Yii::$app->mssql->createCommand(
        //     'SELECT
        //         dbo.DEPT.deptCode,
        //         dbo.DEPT.deptDesc
        //     FROM
        //         dbo.DEPTGROUP
        //     INNER JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.DEPTGROUP.deptCode'
        // )->queryAll(); 
        //     }
        return $this->render('_form_sub_department.php', [
            'DeptGrDesc' => $DeptGrDesc,
            'deptCodeSub' => $deptCodeSub,
            'images' => $images,
            'search' => $search
        ]);
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

    public function actionCreateAppointments($id)
    {
        $db_mssql = Yii::$app->mssql;
        $db_queue = Yii::$app->db_queue;
        $model = new AppointModel();
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
        $doctors_list = [];
        $schedules = [];
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
        $query_doc_codes = $db_mssql->createCommand('SELECT
            Appoint_dep_doc.docCode
        FROM
            Appoint_dep_doc
        WHERE
            Appoint_dep_doc.deptCode = :deptCode')
            ->bindValues([':deptCode' => $id])
            ->queryAll();
        $doc_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_doc_codes, 'docCode'));


        // ข้อมูลแพทย์ในระบบคิว
        $doctors = $db_queue->createCommand('SELECT
        tbl_doctor.*,
        CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name
        FROM
            tbl_doctor
        WHERE
            tbl_doctor.doctor_code IN (' . implode(",", $doc_codes) . ')')
            ->queryAll();

        foreach ($doctors as $doctor) {
            $doctors_list = ArrayHelper::merge($doctors_list, [ArrayHelper::merge($doctor, [
                'service_id' => $service['service_id'],
                'service_name' => $service['service_name'],
                'service_code' => $service['service_code'],
            ])]);
        }

        return $this->render('_form_appointments', [
            'deptCodeSub' => $deptCodeSub,
            'docCode' => $docCode,
            'doctors' => $doctors_list,
            'service' => $service,
            'dept_code' => $id,
            'model' => $model,

        ]);
    }


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
        $query_doc_codes = $db_mssql->createCommand('SELECT
                Appoint_dep_doc.docCode
            FROM
                Appoint_dep_doc
            WHERE
                Appoint_dep_doc.deptCode = :deptCode')
            ->bindValues([':deptCode' => $id])
            ->queryAll();
        $doc_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_doc_codes, 'docCode'));

        // ข้อมูลแพทย์ในระบบคิว
        $doctors = $db_queue->createCommand('SELECT
                tbl_doctor.*,
                CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name
            FROM
                tbl_doctor
            WHERE
                tbl_doctor.doctor_code IN (' . implode(",", $doc_codes) . ')')
            ->queryAll();

        foreach ($doctors as $doctor) {
            $doctors_list = ArrayHelper::merge($doctors_list, [ArrayHelper::merge($doctor, [
                'service_id' => $service['service_id'],
                'service_name' => $service['service_name'],
                'service_code' => $service['service_code'],
            ])]);
        }

        return $this->render('_form_appointments', [
            'deptCodeSub' => $deptCodeSub,
            'docCode' => $docCode,
            'doctors' => $doctors_list,
            'service' => $service,
        ]);
    }

    public function actionSchedules($doc_id)
    {
        $db_queue = Yii::$app->db_queue;

        // ข้อมูลตารางแพทย์
        $med_schedules = $db_queue->createCommand('SELECT
                    tbl_med_schedule.*,
                    CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name,
                    tbl_service.service_name
                FROM
                    tbl_med_schedule
                    INNER JOIN tbl_doctor ON tbl_med_schedule.doctor_id = tbl_doctor.doctor_id
                    INNER JOIN tbl_service ON tbl_service.service_id = tbl_med_schedule.service_id
                WHERE
                    UNIX_TIMESTAMP( tbl_med_schedule.schedule_date ) >= UNIX_TIMESTAMP(NOW())
                    AND tbl_doctor.doctor_code = ' . $doc_id . '
                ORDER BY
                    tbl_med_schedule.schedule_date ASC')
            ->queryAll();
        return Json::encode($med_schedules);
    }

    public function actionScheduleTimes()
    {
        $attributes = \Yii::$app->request->post('AppointModel', []);
        $appoint_date = \Yii::$app->request->post('appoint_date', '');
        $db_queue = Yii::$app->db_queue;
        $schedule_times = $db_queue->createCommand('SELECT
            tbl_med_schedule.schedule_date,
            tbl_med_schedule_time.start_time,
            tbl_med_schedule_time.end_time,
            tbl_doctor.doctor_title,
            tbl_doctor.doctor_name,
            tbl_med_schedule.service_id,
            tbl_service.service_code,
            tbl_service.service_name,
            tbl_med_schedule_time.med_schedule_time_qty
            FROM
            tbl_med_schedule_time
            INNER JOIN tbl_med_schedule ON tbl_med_schedule.med_schedule_id = tbl_med_schedule_time.med_schedule_id
            INNER JOIN tbl_doctor ON tbl_doctor.doctor_id = tbl_med_schedule.doctor_id
            INNER JOIN tbl_service ON tbl_service.service_id = tbl_med_schedule.service_id
            WHERE
            tbl_doctor.doctor_code = :doctor_id AND
            tbl_med_schedule.schedule_date = :schedule_date
            ORDER BY
            tbl_med_schedule_time.start_time ASC')
            ->bindValues([
                ':doctor_id' => $attributes['doc_code'],
                ':schedule_date' => $appoint_date,
            ])
            ->queryAll();
        $result = [];
        foreach ($schedule_times as $key => $schedule_time) {
            $result[] = [
                'text' => Yii::$app->formatter->asDate($schedule_time['start_time'], 'php:H:i') . '-' . Yii::$app->formatter->asDate($schedule_time['end_time'], 'php:H:i') . ' น.',
                'value' => Yii::$app->formatter->asDate($schedule_time['start_time'], 'php:H:i') . '-' . Yii::$app->formatter->asDate($schedule_time['end_time'], 'php:H:i'),
            ];
        }
        if (empty($schedule_times) && empty($attributes['doc_code'])) {
            $result = [
                [
                    'text' => '08:00-09:00 น.',
                    'value' => '08:00-09:00'
                ],
                [
                    'text' => '09:00-10:00 น.',
                    'value' => '09:00-10:00'
                ],
                [
                    'text' => '10:00-11:00 น.',
                    'value' => '10:00-11:00'
                ],
                [
                    'text' => '11:00-12:00 น.',
                    'value' => '11:00-12:00'
                ],
                [
                    'text' => '13:00-14:00 น.',
                    'value' => '13:00-14:00'
                ],
                [
                    'text' => '14:00-15:00 น.',
                    'value' => '14:00-15:00'
                ],
                [
                    'text' => '15:00-16:00 น.',
                    'value' => '15:00-16:00'
                ],
            ];
        }
        return Json::encode($result);
    }

    public function actionSaveAppoint()
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        $attributes = \Yii::$app->request->post('AppointModel', []);
        $appoint_time_from = \Yii::$app->request->post('appoint_time_from', '');
        $appoint_time_to = \Yii::$app->request->post('appoint_time_to', '');
        $appoint_times = \Yii::$app->request->post('appoint_times', '');
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
            $doctor = $db_mssql->createCommand('SELECT
                    REPLACE(dbo.DOCC.docCode, \' \', \'\') as docCode,
                    REPLACE(dbo.DOCC.doctitle, \' \', \'\') as doctitle,
                    REPLACE(dbo.DOCC.docName, \' \', \'\') as docName,
                    REPLACE(dbo.DOCC.docLName, \' \', \'\') as docLName
                    
                    FROM
                    dbo.DOCC
                    WHERE
                    REPLACE(dbo.DOCC.docCode, \' \', \'\') = :docCode')
                ->bindValues([
                    ':docCode' => $attributes['doc_code'],
                ])
                ->queryOne();
            $dept = $db_mssql->createCommand('SELECT
                    REPLACE(dbo.DEPT.deptCode, \' \', \'\') as deptCode,
                    REPLACE(dbo.DEPT.deptDesc, \' \', \'\') as deptDesc
                    
                    FROM
                    dbo.DEPT
                    WHERE
                    REPLACE(dbo.DEPT.deptCode, \' \', \'\') = :deptCode')
                ->bindValues([
                    ':deptCode' => $attributes['dept_code'],
                ])
                ->queryOne();
            if ($doc_option == 'selection' && !empty($attributes['doc_code'])) {
                $history_appoints = $db_mssql->createCommand('SELECT
                    Appoint.*
                FROM
                    Appoint
                WHERE
                    Appoint.maker = :maker AND
                    Appoint.doctor = :doctor AND
                    Appoint.appoint_date = :appoint_date AND
                    Appoint.pre_dept_code = :pre_dept_code AND
                    Appoint.appoint_time_from = :appoint_time_from AND
                    Appoint.appoint_time_to = :appoint_time_to AND
                    Appoint.hn = :hn')
                    ->bindValues([
                        ':maker' => 'queue online',
                        ':doctor' => $attributes['doc_code'],
                        ':appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
                        ':pre_dept_code' => $attributes['dept_code'],
                        ':appoint_time_from' => $appoint_time_from,
                        ':appoint_time_to' => $appoint_time_to,
                        ':hn' => $profile['hn'],
                    ])
                    ->queryAll();
            } else {
                $history_appoints = $db_mssql->createCommand('SELECT
                Appoint.*
            FROM
                Appoint
            WHERE
                Appoint.maker = :maker AND
                Appoint.appoint_date = :appoint_date AND
                Appoint.pre_dept_code = :pre_dept_code AND
                Appoint.appoint_time_from = :appoint_time_from AND
                Appoint.appoint_time_to = :appoint_time_to AND
                Appoint.hn = :hn')
                    ->bindValues([
                        ':maker' => 'queue online',
                        ':appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
                        ':pre_dept_code' => $attributes['dept_code'],
                        ':appoint_time_from' => $appoint_time_from,
                        ':appoint_time_to' => $appoint_time_to,
                        ':hn' => $profile['hn'],
                    ])
                    ->queryAll();
            }

            if (!empty($history_appoints)) {
                throw new HttpException(422, 'ไม่สามารถทำรายการได้ เนื่องจากคุณมีรายการนัดตามวัน,เวลา แผนก แพทย์ ที่เลือกอยู่แล้ว.');
            }

            $db_mssql->createCommand()->insert('Appoint', [
                'app_type' => 'A',
                'doctor' => empty($attributes['doc_code']) ? '0' : sprintf("% 6s", $attributes['doc_code']),
                'hn' => $profile['hn'],
                'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
                'appoint_time_from' => $appoint_time_from,
                'appoint_time_to' => $appoint_time_to,
                'appoint_note' => 'ทดสอบข้อมูล',
                'pre_dept_code' => $attributes['dept_code'],
                'maker' => 'queue online',
                'keyin_time' => $formatter->asDate('now', 'php:Y-m-d H:i:s'),
            ])->execute();
            $appoint = [
                'doctor_name' => empty($attributes['doc_code']) ? 'ไม่ระบุแพทย์' : $doctor['doctitle'] . $doctor['docName'] . ' ' . $doctor['docLName'],
                'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:d M ') . ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543),
                'appoint_time' => $appoint_times,
                'department_name' => $dept['deptDesc'],
                'hn' => $profile['hn'],
            ];
            $transaction->commit();
            return [
                'message' => 'ทำรายการสำเร็จ',
                'appoint' => $appoint,
                'appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
                'hn' => $profile['hn'],
                'doctor' => empty($attributes['doc_code']) ? '0' : sprintf("% 6s", $attributes['doc_code'])
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


    public function actionFollowUp($hn = '', $appoint_date = '', $doctor = '')
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        $db_mssql = Yii::$app->mssql;
        $appoint = $db_mssql->createCommand('SELECT
                dbo.Appoint.*,
                dbo.DEPT.deptDesc,
                REPLACE( dbo.DOCC.docName, \' \', \'\') as docName,
                REPLACE( dbo.DOCC.docLName, \' \', \'\') as docLName,
                REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                REPLACE( dbo.PATIENT.lastName, \' \', \'\') as lastName
                FROM
                dbo.Appoint
                LEFT JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.Appoint.pre_dept_code
                LEFT JOIN dbo.DOCC ON dbo.DOCC.docCode = dbo.Appoint.doctor
                LEFT JOIN dbo.Appoint_dep_doc ON dbo.Appoint_dep_doc.docCode = dbo.DOCC.docCode
                INNER JOIN dbo.PATIENT ON dbo.PATIENT.hn = dbo.Appoint.hn
                WHERE
                dbo.Appoint.maker = \'queue online\' AND
                dbo.Appoint.hn = :hn AND
                dbo.Appoint.appoint_date = :appoint_date AND
                dbo.Appoint.doctor = :doctor
            ')
            ->bindValues([
                ':hn' => sprintf("% 7s", $profile['hn']),
                ':appoint_date' => $appoint_date,
                ':doctor' => $doctor
            ])
            ->queryOne();
        return  $this->render('_form_folle_up', [
            'appoint' => $appoint
        ]);
    }
}
