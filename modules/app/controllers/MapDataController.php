<?php

namespace app\modules\app\controllers;

use Yii;
use yii\web\Controller;
use common\components\AppQuery;
use yii\helpers\ArrayHelper;

class MapDataController extends Controller
{
    //แพทย์
    public function actionDoctor()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $doctors = AppQuery::getDoctorHomc();

        $doctors_queue = AppQuery::getDoctorQueue();
        $map_doctors_queue = ArrayHelper::map($doctors_queue, 'doctor_code', 'doctor_id');

        $services = AppQuery::getServices();
        $map_services = ArrayHelper::map($services, 'service_code', 'service_id');

        $map_doctors = [];
        foreach ($doctors as $key => $doctor) {
            $map_doctors[] = ArrayHelper::merge($doctor, [
                'doctor_id' => ArrayHelper::getValue($map_doctors_queue, $doctor['docCode']),
                'service_id' => ArrayHelper::getValue($map_services, $doctor['deptCode']),
                'fullname' => $doctor['doctitle'] . $doctor['docName'] . ' ' . $doctor['docLName']
            ]);
        }

        return $map_doctors;
    }

    //แผนก
    public function actionDepartment()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $departments = AppQuery::getDepartmentHomc();

        $services = AppQuery::getServices();
        $map_services = ArrayHelper::map($services, 'service_code', 'service_id');

        $map_departments = [];
        foreach ($departments as $key => $department) {
            $map_departments[] = ArrayHelper::merge($department, [
                'service_id' => ArrayHelper::getValue($map_services, $department['deptCode']),

            ]);
        }

        return $map_departments;
    }

    //แผนก
    public function actionDepartmentGroup()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        //$departments = AppQuery::getDepartmentGroupHomc();
        $departments = AppQuery::getDepartmentByDeptGroupHomc('02');

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

        return $map_departments;
    }

    //แพทย์ตามแผนก
    public function actionDoctorByDeptcode($dept_code)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $doctors = AppQuery::getDoctorByDeptcode($dept_code);

        return $doctors;
    }


    public function actionScheduleByDeptcode($dept_code)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $doctors = AppQuery::getDoctorByDeptcode($dept_code);
        $doctor_ids =  ArrayHelper::getValue($doctors, 'doctor_ids', []);
        $doctor_ids = ArrayHelper::merge($doctor_ids, [1]);

        $schedules = AppQuery::getScheduleByDoctorIds($doctor_ids);
        $ids = array_unique(ArrayHelper::getColumn($schedules, 'doctor_id'));
        $isInArray = ArrayHelper::isIn('1', $ids);

        return $schedules;
    }

    //ตารางแพทย์ตามรหัสแพทย์
    public function actionScheduleByDoctorId($dept_code)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $DeptGroup = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPTGROUP.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPTGROUP.DeptGroup, \' \', \'\') as DeptGroup'
            ])
            ->from('DEPTGROUP')
            ->where(['REPLACE(DEPTGROUP.deptCode, \' \', \'\')' => $dept_code])
            ->one(Yii::$app->mssql);

        $DeptGroupQueue = (new \yii\db\Query())
            ->select(['tbl_dept_group.*'])
            ->from('tbl_dept_group')
            ->where(['dept_group' => $DeptGroup['DeptGroup']])
            ->all(Yii::$app->db_queue);

        $service_ids = ArrayHelper::getColumn($DeptGroupQueue, 'service_id');
        $schedules = AppQuery::getScheduleByServiceIdAndDoctorId($service_ids, 1);

        return $schedules;
    }
}
