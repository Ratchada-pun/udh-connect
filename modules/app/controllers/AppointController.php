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
    public function actionCreateDepartment() //เลือกแผนก/คลีนิคหลัก
    {
        $session = Yii::$app->session;
        // if (!$session->get('user')) {
        //     return $this->redirect(['/']);
        // }
        
        // $session = Yii::$app->session;
        // $session->remove('user');
        // $response = Yii::$app->response;
        // $response->format = \yii\web\Response::FORMAT_JSON;
        // $DeptGroups = Yii::$app->mssql->createCommand(
        //     'SELECT
        //         REPLACE(dbo.DEPTGr.DeptGroup, \' \', \'\') as DeptGroup,
        //         REPLACE(dbo.DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc
        //     FROM
        //     dbo.DEPTGr
        //     '
        // )->queryAll();

        $DeptGroups = Yii::$app->mssql->createCommand( //รายชื่อแผนกหลัก
            'SELECT
                REPLACE(dbo.DEPTGr.DeptGroup, \' \', \'\') as DeptGroup,
                REPLACE(dbo.DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc
            FROM
                dbo.DEPTGROUP
                INNER JOIN dbo.DEPTGr ON dbo.DEPTGr.DeptGroup = dbo.DEPTGROUP.DeptGroup 
                '
        )->queryAll();
        $DeptGroups = ArrayHelper::map($DeptGroups, 'DeptGroup', 'DeptGrDesc');

        return $this->render('_form_department.php', [
            'DeptGroups' => $DeptGroups,
        ]);
    }

    public function actionCreateSubDepartment($id) //เลือกแผนกย่อย
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

        $deptCodeSub = Yii::$app->mssql->createCommand(  //รายชื่อแผนกย่อย
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


    /**
     * @param {string} id > รหัสแผนก
     * @param {number} doc_id รหัสประจำตัวแพทย์
     */
    public function actionCreateAppointments($id, $doc_id = '')
    {
        $db_mssql = Yii::$app->mssql;
        $db_queue = Yii::$app->db_queue;
        $model = new AppointModel();

        //ชื่อแผนกย่อย (ใช้ตรง header)
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


        //รายชื่อแพทย์ทั้งหมดตามแผนกที่เลือก
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
        //$dept_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_dept_codes, 'deptCode'));

        $doctors_list = [];
        //$schedules = [];



        //รายชื่อแผนกในระบบคิว
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



        // ค้นหา รหัสแพทย์ตามแผนกที่เลือก
        $query_doc_codes = $db_mssql->createCommand('SELECT
            Appoint_dep_doc.docCode
        FROM
            Appoint_dep_doc
        WHERE
            Appoint_dep_doc.deptCode = :deptCode')
            ->bindValues([':deptCode' => $id])
            ->queryAll();

        //ลบช่องว่างรหัส
        $doc_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_doc_codes, 'docCode'));



        // นำรหัสแพทย์ทั้งหมดตามแผนกที่เลือกมาค้นหาข้อมูลแพทย์ในระบบคิวที่ลงบันทึกตารางแพทย์ออกตรวจ
        $doctors = [];
        if ($doc_codes) { //มีแพทย์ในระบบ
            $doctors = $db_queue->createCommand(
                'SELECT
                    tbl_doctor.*,
                CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name
                FROM
                    tbl_doctor
                INNER JOIN tbl_med_schedule ON tbl_med_schedule.doctor_id = tbl_doctor.doctor_id
                WHERE
                    tbl_doctor.doctor_code IN (' . implode(",", $doc_codes) . ') AND 
                    tbl_med_schedule.schedule_date >= CURRENT_DATE
                GROUP BY  
                    tbl_doctor.doctor_code
                '
            )
                ->queryAll();
        }

        //นำรายการชื่อแพทย์ทั้งหมดมารวมกับรายชื่อแผนกในระบบคิว
        foreach ($doctors as $doctor) {
            $doctors_list = ArrayHelper::merge($doctors_list, [
                ArrayHelper::merge($doctor, [
                    'service_id' => $service['service_id'],
                    'service_name' => $service['service_name'],
                    'service_code' => $service['service_code'],
                ])
            ]);
        }

        return $this->render('_form_appointments', [
            'deptCodeSub' => $deptCodeSub,
            'docCode' => $docCode, //รายชื่อแพทย์ทั้งหมดตามแผนกที่เลือก
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
                    tbl_med_schedule.schedule_date >= CURRENT_DATE
                    AND tbl_doctor.doctor_code = ' . $doc_id . '
                ORDER BY
                    tbl_med_schedule.schedule_date ASC')
            ->queryAll();
        return Json::encode($med_schedules);
    }

    public function actionScheduleTimes()  //ตารางแพทย์
    {
        $attributes = \Yii::$app->request->post('AppointModel', []);
        $appoint_date = \Yii::$app->request->post('appoint_date', '');

        $db_mssql = Yii::$app->mssql;
        $db_queue = Yii::$app->db_queue;
        // $schedule_times = $db_queue->createCommand('SELECT
        //     tbl_med_schedule.schedule_date,
        //     tbl_med_schedule_time.start_time,
        //     tbl_med_schedule_time.end_time,
        //     tbl_doctor.doctor_id,
        //     tbl_doctor.doctor_title,
        //     tbl_doctor.doctor_name,
        //     tbl_med_schedule.service_id,
        //     tbl_service.service_code,
        //     tbl_service.service_name,
        //     tbl_med_schedule_time.med_schedule_time_qty
        //     FROM
        //     tbl_med_schedule_time
        //     INNER JOIN tbl_med_schedule ON tbl_med_schedule.med_schedule_id = tbl_med_schedule_time.med_schedule_id
        //     INNER JOIN tbl_doctor ON tbl_doctor.doctor_id = tbl_med_schedule.doctor_id
        //     INNER JOIN tbl_service ON tbl_service.service_id = tbl_med_schedule.service_id
        //     WHERE
        //     tbl_doctor.doctor_code = :doctor_id AND
        //     tbl_med_schedule.schedule_date = :schedule_date  AND
        //     LEFT(tbl_service.service_name,8) = \'ห้องตรวจ\'


        //     ORDER BY
        //     tbl_med_schedule_time.start_time ASC')
        //     ->bindValues([
        //         ':doctor_id' => $attributes['doc_code'],
        //         ':schedule_date' => $appoint_date,
        //     ])
        //     ->queryAll();
        $query_doc_codes = $db_mssql->createCommand('SELECT
                REPLACE(Appoint_dep_doc.docCode, \' \', \'\') as docCode
            FROM
                Appoint_dep_doc
            WHERE
                Appoint_dep_doc.deptCode = :deptCode')
            ->bindValues([':deptCode' => $attributes['dept_code']])
            ->queryAll();
        $doc_codes = $this->replaceEmptyString(ArrayHelper::getColumn($query_doc_codes, 'docCode'));

        $query = (new \yii\db\Query())
            ->select([
                'tbl_med_schedule.schedule_date',
                'tbl_med_schedule_time.start_time',
                'tbl_med_schedule_time.end_time',
                'tbl_doctor.doctor_id',
                'tbl_doctor.doctor_title',
                'tbl_doctor.doctor_name',
                'tbl_doctor.doctor_code',
                'tbl_med_schedule.service_id',
                'tbl_service.service_code',
                'tbl_service.service_name',
                'tbl_med_schedule_time.med_schedule_time_qty'
            ])
            ->from('tbl_med_schedule_time')
            ->innerJoin('tbl_med_schedule', 'tbl_med_schedule.med_schedule_id = tbl_med_schedule_time.med_schedule_id')
            ->innerJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_med_schedule.doctor_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_med_schedule.service_id')
            ->groupBy('tbl_med_schedule_time.med_schedule_time_id')
            ->where([
                //'tbl_doctor.doctor_code = :doctor_id' ,
                //'tbl_service.service_code' => $attributes['dept_code'],
                'tbl_med_schedule.schedule_date' => $appoint_date,
                'LEFT(tbl_service.service_name,8)' => 'ห้องตรวจ'
            ])
            ->orderBy('tbl_med_schedule_time.start_time ASC');
        if (!empty($attributes['doc_code'])) {
            $query->andWhere([
                'tbl_doctor.doctor_code' =>  $attributes['doc_code']
            ]);
        }
        $schedule_times = $query->all($db_queue);

        $result = [];
        $doctor = [];
        $doctorids = [];
        $list = '';

        $rows = (new \yii\db\Query())
            ->select([
                'tbl_med_schedule.schedule_date',
                'tbl_med_schedule_time.start_time',
                'tbl_med_schedule_time.end_time',
                'tbl_doctor.doctor_id',
                'tbl_doctor.doctor_title',
                'tbl_doctor.doctor_name',
                'tbl_doctor.doctor_code',
                'tbl_med_schedule.service_id',
                'tbl_service.service_code',
                'tbl_service.service_name',
                'tbl_med_schedule_time.med_schedule_time_qty'
            ])
            ->from('tbl_med_schedule_time')
            ->innerJoin('tbl_med_schedule', 'tbl_med_schedule.med_schedule_id = tbl_med_schedule_time.med_schedule_id')
            ->innerJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_med_schedule.doctor_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_med_schedule.service_id')
            ->groupBy('tbl_med_schedule_time.med_schedule_time_id')
            ->where([
                'tbl_med_schedule.schedule_date' => $appoint_date,
                'LEFT(tbl_service.service_name,8)' => 'ห้องตรวจ'
            ])
            ->orderBy('tbl_med_schedule_time.start_time ASC')
            ->all($db_queue);

        foreach ($rows as $schedule_time) {
            if (ArrayHelper::isIn($schedule_time['doctor_code'], $doc_codes)) {
                if (!ArrayHelper::isIn($schedule_time['doctor_id'], $doctorids)) {
                    $doctor[] = [
                        'doctor_id' => $schedule_time['doctor_id'],
                        'doctor_name' => $schedule_time['doctor_title'] . $schedule_time['doctor_name'],
                    ];
                    if ($attributes['doc_code'] == $schedule_time['doctor_code']) {
                        $list .= '<li class="list-group-item list-group-doc-name" style="padding: 5px;">
                    <label class="control control-outline control-outline-danger control--radio" style="margin-bottom: 0;">
                        ' . $schedule_time['doctor_title'] . $schedule_time['doctor_name'] . '
                        <input type="radio" id="' . $schedule_time['doctor_code'] . '" name="docname" value="' . $schedule_time['doctor_code'] . '" data-docname="' . $schedule_time['doctor_title'] . $schedule_time['doctor_name'] . '" checked="checked">
                        <span class="control__indicator"></span>
                    </label>
                </li>';
                    } else {
                        $list .= '<li class="list-group-item list-group-doc-name" style="padding: 5px;">
                        <label class="control control-outline control-outline-danger control--radio" style="margin-bottom: 0;">
                            ' . $schedule_time['doctor_title'] . $schedule_time['doctor_name'] . '
                            <input type="radio" id="' . $schedule_time['doctor_code'] . '" name="docname" value="' . $schedule_time['doctor_code'] . '" data-docname="' . $schedule_time['doctor_title'] . $schedule_time['doctor_name'] . '">
                            <span class="control__indicator"></span>
                        </label>
                    </li>';
                    }

                    $doctorids[] = $schedule_time['doctor_id'];
                }
            }
        }

        foreach ($schedule_times as $key => $schedule_time) {
            if (ArrayHelper::isIn($schedule_time['doctor_code'], $doc_codes)) {
                $result[] = [
                    'text' => Yii::$app->formatter->asDate($schedule_time['start_time'], 'php:H:i') . '-' . Yii::$app->formatter->asDate($schedule_time['end_time'], 'php:H:i') . ' น.',
                    'value' => Yii::$app->formatter->asDate($schedule_time['start_time'], 'php:H:i') . '-' . Yii::$app->formatter->asDate($schedule_time['end_time'], 'php:H:i'),
                ];
            }
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
        return Json::encode([
            'schedule_times' => $result,
            'doctor' => $doctor,
            'doc_codes' => $doc_codes,
            'schedule' => $schedule_times,
            'list' => $list,
        ]);
    }

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
                    (Appoint.hn = :hn OR
                    Appoint.CID = :CID)
                    ')
                    ->bindValues([
                        ':maker' => 'queue online',
                        ':doctor' => sprintf("% 6s", $attributes['doc_code']),
                        ':appoint_date' => ($formatter->asDate($attributes['appoint_date'], 'php:Y') + 543) . $formatter->asDate($attributes['appoint_date'], 'php:md'),
                        ':pre_dept_code' => $attributes['dept_code'],
                        ':appoint_time_from' => $appoint_time_from,
                        ':appoint_time_to' => $appoint_time_to,
                        ':hn' => sprintf("% 7s", $profile['hn']),
                        ':CID' => $profile['id_card'],
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
                (Appoint.hn = :hn OR
                Appoint.CID = :CID)
                ')
                    ->bindValues([
                        ':maker' => 'queue online',
                        ':appoint_date' => $formatter->asDate($attributes['appoint_date'], 'php:Ymd'),
                        ':pre_dept_code' => $attributes['dept_code'],
                        ':appoint_time_from' => $appoint_time_from,
                        ':appoint_time_to' => $appoint_time_to,
                        ':hn' => sprintf("% 7s", $profile['hn']),
                        ':CID' => $profile['id_card'],
                    ])
                    ->queryAll();
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


    public function actionFollowUp($hn = '', $appoint_date = '', $doctor = '', $cid = '')
    {
        $session = Yii::$app->session;
        $profile = $session->get('user');
        $db_mssql = Yii::$app->mssql;
        $query = (new \yii\db\Query())
            ->select([
                'dbo.Appoint.*',
                'dbo.DEPT.deptDesc',
                'REPLACE( dbo.DOCC.docName, \' \', \'\') as docName',
                'REPLACE( dbo.DOCC.docLName, \' \', \'\') as docLName',
                'REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE( dbo.PATIENT.lastName, \' \', \'\') as lastName'
            ])
            ->from('dbo.Appoint')
            ->leftJoin('dbo.DEPT', 'dbo.DEPT.deptCode = dbo.Appoint.pre_dept_code')
            ->leftJoin('dbo.DOCC', 'dbo.DOCC.docCode = dbo.Appoint.doctor')
            ->leftJoin('dbo.Appoint_dep_doc', 'dbo.Appoint_dep_doc.docCode = dbo.DOCC.docCode')
            ->leftJoin('dbo.PATIENT', 'dbo.PATIENT.hn = dbo.Appoint.hn')
            ->where([
                'dbo.Appoint.appoint_date' => $appoint_date,
                'dbo.Appoint.maker' => 'queue online',
                'dbo.Appoint.CID' => $cid,
                'dbo.Appoint.doctor' => sprintf("% 6s", $doctor),
                'dbo.Appoint.hn' => sprintf("% 7s", $hn)
            ]);
        // if (!empty($hn)) {
        //     $query->andWhere([
        //         'dbo.Appoint.hn' => sprintf("% 7s", $hn)
        //     ]);
        // }
        $appoint = $query->one($db_mssql);

        if ($appoint && empty($hn)) {
            $appoint = ArrayHelper::merge($appoint, [
                'firstName' => $profile['first_name'],
                'lastName' => $profile['last_name'],
            ]);
        }

        return  $this->render('_form_follow_up', [
            'appoint' => $appoint,
            'message' => empty($hn) ? 'กรุณาติดต่อห้องบัตร ตามวันและเวลาที่ท่านนัดหมาย!' : 'กรุณากดบัตรคิว ณ จุดบริการ ตามวันและเวลาที่ท่านนัดหมาย!'
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
        $db_mssql = Yii::$app->mssql;
        $query = (new \yii\db\Query())
            ->select([
                'dbo.Appoint.doctor',
                'dbo.Appoint.hn',
                'dbo.Appoint.appoint_date',
                'dbo.Appoint.appoint_time_from',
                'dbo.Appoint.appoint_time_to',
                'dbo.Appoint.maker',
                'dbo.Appoint.phone',
                'dbo.Appoint.CID',
                'dbo.Appoint.pre_dept_code',
                'dbo.DEPT.deptDesc',
                'dbo.PATIENT.phone',
                'REPLACE(dbo.PATIENT.titleCode, \' \', \'\') as titleCode',
                'REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE( dbo.PATIENT.lastName, \' \', \'\') as lastName',
                'REPLACE(dbo.DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(dbo.DOCC.docName, \' \', \'\') as docName',
                'REPLACE(dbo.DOCC.docLName, \' \', \'\') as docLName'
            ])
            ->from('dbo.Appoint')
            ->innerJoin('dbo.DEPT', 'dbo.DEPT.deptCode = dbo.Appoint.pre_dept_code')
            ->leftJoin('dbo.PATIENT', 'dbo.PATIENT.hn = dbo.Appoint.hn')
            ->leftJoin('dbo.DOCC', 'dbo.DOCC.docCode = dbo.Appoint.doctor')
            ->where(['dbo.Appoint.maker' => 'queue online'])
            ->orderBy('dbo.Appoint.appoint_date DESC');
        if ($profile['hn']) {
            $query->andWhere([
                'dbo.Appoint.hn' => sprintf("% 7s", $profile['hn'])
            ]);
        }
        if ($profile['id_card']) {
            $query->andWhere([
                'dbo.Appoint.CID' => $profile['id_card']
            ]);
        }
        $history = $query->all($db_mssql);
        // $history = $db_mssql->createCommand(
        //     'SELECT
        //     	dbo.Appoint.doctor,
        //         dbo.Appoint.hn,
        //         dbo.Appoint.appoint_date,
        //         dbo.Appoint.appoint_time_from,
        //         dbo.Appoint.appoint_time_to,
        //         dbo.Appoint.maker,
        //         dbo.Appoint.phone,
        //         dbo.Appoint.CID,
        //         dbo.Appoint.pre_dept_code,
        //         dbo.DEPT.deptDesc,
        //         dbo.PATIENT.phone,
        //     REPLACE(dbo.PATIENT.titleCode, \' \', \'\') as titleCode,
        //     REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
        //     REPLACE( dbo.PATIENT.lastName, \' \', \'\') as lastName,
        //     REPLACE(dbo.DOCC.doctitle, \' \', \'\') as doctitle,
        //     REPLACE(dbo.DOCC.docName, \' \', \'\') as docName,
        //     REPLACE(dbo.DOCC.docLName, \' \', \'\') as docLName
        //     FROM
        //     dbo.Appoint
        //     INNER JOIN dbo.DEPT ON dbo.DEPT.deptCode = dbo.Appoint.pre_dept_code
        //     LEFT JOIN dbo.PATIENT ON dbo.PATIENT.hn = dbo.Appoint.hn
        //     LEFT JOIN dbo.DOCC ON dbo.DOCC.docCode = dbo.Appoint.doctor 
        //     WHERE
        //     dbo.Appoint.maker = \'queue online\' AND
        //     dbo.Appoint.CID = :CID OR
        //     dbo.Appoint.hn = :hn 
        //     ORDER BY
        //     dbo.Appoint.appoint_date DESC
        //     '
        // )
        //     ->bindValues([
        //         ':hn' => sprintf("% 7s", $profile['hn']),
        //         ':CID' => $profile['id_card'],
        //     ])
        //     ->queryAll();
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
        $db_mssql = Yii::$app->mssql;
        $query = (new \yii\db\Query())
            ->select([
                'dbo.Appoint.hn',
                'dbo.Appoint.appoint_date',
                'dbo.Appoint.appoint_time_from',
                'dbo.Appoint.appoint_time_to',
                'dbo.Appoint.maker',
                'dbo.Appoint.phone',
                'dbo.Appoint.CID',
                'dbo.DEPT.deptDesc',
                'dbo.PATIENT.phone',
                'REPLACE(dbo.PATIENT.titleCode, \' \', \'\') as titleCode',
                'REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE( dbo.PATIENT.lastName, \' \', \'\') as lastName',
                'REPLACE(dbo.DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(dbo.DOCC.docName, \' \', \'\') as docName',
                'REPLACE(dbo.DOCC.docLName, \' \', \'\') as docLName',
                'REPLACE(dbo.Appoint.pre_dept_code, \' \', \'\') as pre_dept_code',
                'REPLACE(dbo.Appoint.doctor, \' \', \'\') as doctor'
            ])
            ->from('dbo.Appoint')
            ->innerJoin('dbo.DEPT', 'dbo.DEPT.deptCode = dbo.Appoint.pre_dept_code')
            ->leftJoin('dbo.PATIENT', 'dbo.PATIENT.hn = dbo.Appoint.hn')
            ->leftJoin('dbo.DOCC', 'dbo.DOCC.docCode = dbo.Appoint.doctor')
            ->where(['dbo.Appoint.maker' => 'queue online'])
            ->orderBy('dbo.Appoint.appoint_date DESC');
        if ($profile['hn']) {
            $query->andWhere([
                'dbo.Appoint.hn' => sprintf("% 7s", $profile['hn'])
            ]);
        }
        if ($profile['id_card']) {
            $query->andWhere([
                'dbo.Appoint.CID' => $profile['id_card']
            ]);
        }
        $history = $query->one($db_mssql);

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
        $db_mssql = Yii::$app->mssql;
        $profile = $db_mssql->createCommand('SELECT TOP
                1 dbo.PATIENT.hn,
                REPLACE( dbo.PATIENT.firstName, \' \', \'\') as firstName,
                REPLACE(dbo.PATIENT.lastName, \' \', \'\') as lastName,
                dbo.PATIENT.phone,
                dbo.PATIENT.birthDay,
                dbo.PATIENT.titleCode,
                REPLACE(dbo.PatSS.CardID, \' \', \'\') as CardID  
            FROM
                dbo.PATIENT
                INNER JOIN dbo.PatSS ON dbo.PatSS.hn = dbo.PATIENT.hn 
            WHERE
                dbo.PATIENT.hn = :hn')
            ->bindValues([
                ':hn' => sprintf("% 7s", $hn)
            ])
            ->queryOne();
        return $this->render('form_detail_status', [
            // 'rows' => $rows
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
        $startDate = Yii::$app->formatter->asDate('now', 'php:Y-m-d 00:00:00');
        $endDate = Yii::$app->formatter->asDate('now', 'php:Y-m-d 23:59:59');
        $couters = (new \yii\db\Query())
            ->select(['tbl_counter_service.*'])
            ->from('tbl_counter_service')
            ->all(Yii::$app->db_queue);
        $map_couters = ArrayHelper::map($couters, 'counter_service_id', 'counter_service_name');
        $rows = (new \yii\db\Query())
            ->select([
                'tbl_queue_detail.*',
                'tbl_queue.queue_no',
                'tbl_queue.patient_id',
                'tbl_queue.queue_type_id',
                'tbl_queue.coming_type_id',
                'tbl_queue.appoint_id',
                'tbl_service.service_id',
                'tbl_service.service_code',
                'tbl_service.service_name',
                'tbl_service.service_group_id',
                'tbl_service.prefix_id',
                'tbl_service.service_num_digit',
                'tbl_service.ticket_id',
                'tbl_service.print_copy_qty',
                'tbl_service.floor_id',
                'tbl_service.service_order',
                'tbl_service.service_status',
                'tbl_service.icon_path',
                'tbl_service.icon_base_url',
                'tbl_service_group.service_group_name',
                'tbl_patient.hn',
                'tbl_patient.fullname',
                'tbl_queue.created_at',
                'DATE_FORMAT(tbl_queue.created_at,\'%d %M %Y\') as queue_date',
                'TIME_FORMAT(tbl_queue_detail.created_at,\'%H:%i\') as queue_time',
                '`profile`.`name`',
                'tbl_queue_type.queue_type_name',
                'tbl_coming_type.coming_type_name',
                'tbl_queue_status.queue_status_name',
                'tbl_doctor.doctor_code',
                'tbl_doctor.doctor_title',
                'tbl_doctor.doctor_name',
                'tbl_counter_service.counter_service_id',
                'tbl_queue_detail.counter_service_id as counter_service_id1',
                'tbl_counter_service.counter_service_name',
                'tbl_counter_service.counter_service_no',
                'tbl_appoint.appoint_date',
                'tbl_appoint.app_time_from',
                'tbl_appoint.app_time_to',
                'tbl_appoint.doc_code',
                'tbl_appoint.doc_name',
                'file_storage_item.base_url',
                'file_storage_item.path',
                'tbl_caller.caller_id',
                'tbl_service_type.service_type_name'
            ])
            ->from('tbl_queue_detail')
            ->innerJoin('tbl_queue', 'tbl_queue.queue_id = tbl_queue_detail.queue_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_queue_detail.service_id')
            ->innerJoin('tbl_service_group', 'tbl_service_group.service_group_id = tbl_service.service_group_id')
            ->innerJoin('tbl_queue_type', 'tbl_queue_type.queue_type_id = tbl_queue.queue_type_id')
            ->innerJoin('tbl_service_type', 'tbl_service_type.service_type_id = tbl_queue_detail.service_type_id')
            ->innerJoin('tbl_coming_type', 'tbl_coming_type.coming_type_id = tbl_queue.coming_type_id')
            ->innerJoin('tbl_queue_status', 'tbl_queue_status.queue_status_id = tbl_queue_detail.queue_status_id')
            ->leftJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_queue_detail.doctor_id')
            ->leftJoin('tbl_caller', 'tbl_queue_detail.queue_detail_id = tbl_caller.queue_detail_id')
            ->leftJoin('tbl_counter_service', 'tbl_caller.counter_service_id = tbl_counter_service.counter_service_id')
            ->leftJoin('tbl_appoint', 'tbl_appoint.appoint_id = tbl_queue.appoint_id')
            ->innerJoin('`profile`', '`profile`.user_id = tbl_queue.created_by')
            ->innerJoin('tbl_patient', 'tbl_patient.patient_id = tbl_queue.patient_id')
            ->leftJoin('file_storage_item', 'file_storage_item.ref_id = tbl_patient.patient_id')
            ->where(['tbl_patient.hn' => $hn])
            ->andWhere(['between', 'tbl_queue_detail.created_at', $startDate, $endDate])
            ->andWhere('tbl_queue_detail.queue_status_id <> :queue_status_id', [':queue_status_id' => 5])
            ->groupBy('tbl_queue_detail.queue_detail_id')
            ->orderBy('tbl_queue.created_at ASC')
            ->all(Yii::$app->db_queue);

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
