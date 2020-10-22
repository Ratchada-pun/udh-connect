<?php

namespace common\components;

use Yii;
use yii\helpers\ArrayHelper;

class AppQuery
{
    //รายชื่อแพทย์ในฐานข้อมูล homc โรงพยาบาล
    public static function getDoctorHomc()
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DOCC.docCode, \' \', \'\') as docCode',
                'REPLACE(DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(DOCC.docName, \' \', \'\') as docName',
                'REPLACE(DOCC.docLName, \' \', \'\') as docLName',
                'REPLACE(Appoint_dep_doc.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPT.deptDesc, \' \', \'\') as deptDesc',
                'REPLACE(DEPTGROUP.DeptGroup, \' \', \'\') as DeptGroup',
            ])
            ->from('DOCC')
            ->innerJoin('Appoint_dep_doc', 'Appoint_dep_doc.docCode = DOCC.docCode')
            ->leftJoin('DEPT', 'DEPT.deptCode = Appoint_dep_doc.deptCode')
            ->leftJoin('DEPTGROUP', 'DEPTGROUP.deptCode = DEPT.deptCode')
            ->all(Yii::$app->mssql);
        return $rows;
    }

    //รายชื่อแพทย์ฐานข้อมูล queue
    public static function getDoctorQueue()
    {
        $rows = (new \yii\db\Query())
            ->select([
                'tbl_doctor.*'
            ])
            ->from('tbl_doctor')
            ->all(Yii::$app->db_queue);
        return $rows;
    }

    //รายชื่อแผนกจากฐานข้อมูล queue
    public static function getServices()
    {
        $rows = (new \yii\db\Query())
            ->select(['tbl_service.service_id', 'tbl_service.service_name', 'tbl_service.service_code'])
            ->from('tbl_service')
            ->where(['service_status' => 1])
            ->all(Yii::$app->db_queue);
        return $rows;
    }

    //รายชื่อแผนกในฐานข้อมูล homc โรงพยาบาล
    public static function getDepartmentHomc()
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPT.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPT.deptDesc, \' \', \'\') as deptDesc',
                'REPLACE(DEPTGROUP.DeptGroup, \' \', \'\') as DeptGroup',
                'REPLACE(DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc',
            ])
            ->from('DEPT')
            ->innerJoin('Appoint_dep_doc', 'Appoint_dep_doc.deptCode = DEPT.deptCode')
            ->leftJoin('DEPTGROUP', 'DEPTGROUP.deptCode = DEPT.deptCode')
            ->leftJoin('DEPTGr', 'DEPTGr.DeptGroup = DEPTGROUP.DeptGroup')
            ->all(Yii::$app->mssql);
        return $rows;
    }

    //รายชื่อแผนกในฐานข้อมูล homc โรงพยาบาล
    public static function getDepartmentGroupHomc()
    {
        $DEPTGROUP = (new \yii\db\Query())
            ->select([
                'DEPTGROUP.*'
            ])
            ->from('DEPTGROUP')
            ->all(Yii::$app->mssql);
        $DEPTGROUP = array_unique(ArrayHelper::getColumn($DEPTGROUP, 'DeptGroup'));

        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPTGr.DeptGroup, \' \', \'\') as DeptGroup',
                'REPLACE(DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc'
            ])
            ->from('DEPTGr')
            ->where(['DEPTGr.DeptGroup' => $DEPTGROUP])
            ->all(Yii::$app->mssql);


        return $rows;
    }

    public static function getDepartmentByDeptGroupHomc($dept_group)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPT.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPT.deptDesc, \' \', \'\') as deptDesc',
                'REPLACE(DEPTGROUP.DeptGroup, \' \', \'\') as DeptGroup',
                'REPLACE(DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc',
            ])
            ->from('DEPT')
            //->innerJoin('Appoint_dep_doc', 'Appoint_dep_doc.deptCode = DEPT.deptCode')
            ->leftJoin('DEPTGROUP', 'DEPTGROUP.deptCode = DEPT.deptCode')
            ->leftJoin('DEPTGr', 'DEPTGr.DeptGroup = DEPTGROUP.DeptGroup')
            ->where(['REPLACE(DEPTGROUP.DeptGroup, \' \', \'\')' => $dept_group])
            ->all(Yii::$app->mssql);
        return $rows;
    }

    //ค้นหารหัสแพทย์ตามรหัสแผนก
    public static function getDoctorByDeptcode($dept_code)
    {
        $doctors_homc =  (new \yii\db\Query())
            ->select([
                'REPLACE(Appoint_dep_doc.deptCode, \' \', \'\') as deptCode',
                'REPLACE(Appoint_dep_doc.docCode, \' \', \'\') as docCode',
                'REPLACE(DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(DOCC.docName, \' \', \'\') as docName',
                'REPLACE(DOCC.docLName, \' \', \'\') as docLName',
                'REPLACE(DEPT.deptDesc, \' \', \'\') as deptDesc'
            ])
            ->from('Appoint_dep_doc')
            ->innerJoin('DOCC', 'DOCC.docCode = Appoint_dep_doc.docCode')
            ->leftJoin('DEPT', 'DEPT.deptCode = Appoint_dep_doc.deptCode')
            ->where(['Appoint_dep_doc.deptCode' => $dept_code])
            ->all(Yii::$app->mssql);

        $doctors_queue = AppQuery::getDoctorQueue();
        $map_doctors_queue = ArrayHelper::map($doctors_queue, 'doctor_code', 'doctor_id');

        $map_doctors = [];
        foreach ($doctors_homc as $key => $doctor) {
            $map_doctors[] = ArrayHelper::merge($doctor, [
                'doctor_id' => ArrayHelper::getValue($map_doctors_queue, $doctor['docCode']),
                'fullname' => $doctor['doctitle'] . $doctor['docName'] . ' ' . $doctor['docLName']
            ]);
        }
        return [
            'doctors' => $map_doctors,
            'doc_codes' => array_unique(ArrayHelper::getColumn($doctors_homc, 'docCode')),
            'doctor_ids' => array_unique(ArrayHelper::getColumn($map_doctors, 'doctor_id')),
        ];
    }

    public static function getScheduleByDoctorIds($ids, $schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $med_schedules = (new \yii\db\Query())
            ->select(['tbl_med_schedule.*', 'tbl_doctor.*'])
            ->from('tbl_med_schedule')
            ->innerJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_med_schedule.doctor_id')
            ->where(['tbl_med_schedule.doctor_id' => $ids])
            ->andWhere('tbl_med_schedule.schedule_date >= :schedule_date', [':schedule_date' => $schedule_date])
            ->all(Yii::$app->db_queue);

        $schedules = [];
        foreach ($med_schedules as $key => $med_schedule) {
            $schedules[] = ArrayHelper::merge($med_schedule, [
                '_sub_schedules' => AppQuery::getSubScheduleByScheduleId($med_schedule['med_schedule_id'])
            ]);
        }

        return $schedules;
    }

    public static function getScheduleByDoctorId($doctor_id, $schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $med_schedules = (new \yii\db\Query())
            ->select([
                'tbl_med_schedule.*',
                'CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name',
                'tbl_service.service_name'
            ])
            ->from('tbl_med_schedule')
            ->innerJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_med_schedule.doctor_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_med_schedule.service_id')
            ->where(['tbl_med_schedule.doctor_id' => $doctor_id])
            ->andWhere('tbl_med_schedule.schedule_date >= :schedule_date', [':schedule_date' => $schedule_date])
            ->orderBy('tbl_med_schedule.schedule_date ASC')
            ->all(Yii::$app->db_queue);

        $schedules = [];
        foreach ($med_schedules as $key => $med_schedule) {
            $schedules[] = ArrayHelper::merge($med_schedule, [
                '_sub_schedules' => AppQuery::getSubScheduleByScheduleId($med_schedule['med_schedule_id'])
            ]);
        }

        return $schedules;
    }

    public static function getScheduleByDoctorCode($doctor_code, $schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $med_schedules = (new \yii\db\Query())
            ->select([
                'tbl_med_schedule.*',
                'CONCAT( IFNULL( tbl_doctor.doctor_title, \'\' ), tbl_doctor.doctor_name ) AS doctor_name',
                'tbl_service.service_name'
            ])
            ->from('tbl_med_schedule')
            ->innerJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_med_schedule.doctor_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_med_schedule.service_id')
            ->where(['tbl_doctor.doctor_code' => $doctor_code])
            ->andWhere('tbl_med_schedule.schedule_date >= :schedule_date', [':schedule_date' => $schedule_date])
            ->orderBy('tbl_med_schedule.schedule_date ASC')
            ->all(Yii::$app->db_queue);

        $schedules = [];
        foreach ($med_schedules as $key => $med_schedule) {
            $schedules[] = ArrayHelper::merge($med_schedule, [
                '_sub_schedules' => AppQuery::getSubScheduleByScheduleId($med_schedule['med_schedule_id'])
            ]);
        }

        return $schedules;
    }

    public static function getScheduleByServiceId($service_ids, $schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $med_schedules = (new \yii\db\Query())
            ->select(['tbl_med_schedule.*'])
            ->from('tbl_med_schedule')
            ->where(['tbl_med_schedule.service_id' => $service_ids])
            ->andWhere('tbl_med_schedule.schedule_date >= :schedule_date', [':schedule_date' => $schedule_date])
            ->all(Yii::$app->db_queue);

        $schedules = [];
        foreach ($med_schedules as $key => $med_schedule) {
            $schedules[] = ArrayHelper::merge($med_schedule, [
                '_sub_schedules' => AppQuery::getSubScheduleByScheduleId($med_schedule['med_schedule_id'])
            ]);
        }

        return $schedules;
    }

    public static function getScheduleByServiceIdAndDoctorId($service_ids, $doctor_id, $schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $med_schedules = (new \yii\db\Query())
            ->select(['tbl_med_schedule.*'])
            ->from('tbl_med_schedule')
            ->where(['tbl_med_schedule.service_id' => $service_ids, 'tbl_med_schedule.doctor_id' => $doctor_id])
            ->andWhere('tbl_med_schedule.schedule_date >= :schedule_date', [':schedule_date' => $schedule_date])
            ->all(Yii::$app->db_queue);

        $schedules = [];
        foreach ($med_schedules as $key => $med_schedule) {
            $schedules[] = ArrayHelper::merge($med_schedule, [
                '_sub_schedules' => AppQuery::getSubScheduleByScheduleId($med_schedule['med_schedule_id'])
            ]);
        }

        return $schedules;
    }

    public static function getSubScheduleByScheduleId($id)
    {
        $sub_schedules = (new \yii\db\Query())
            ->select(['tbl_med_schedule_time.*'])
            ->from('tbl_med_schedule_time')
            ->where(['tbl_med_schedule_time.med_schedule_id' => $id])
            ->all(Yii::$app->db_queue);
        return $sub_schedules;
    }

    public static function getScheduleByScheduleDate($schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $med_schedules = (new \yii\db\Query())
            ->select(['tbl_med_schedule.*'])
            ->from('tbl_med_schedule')
            ->andWhere('tbl_med_schedule.schedule_date = :schedule_date', [':schedule_date' => $schedule_date])
            ->all(Yii::$app->db_queue);

        $schedules = [];
        foreach ($med_schedules as $key => $med_schedule) {
            $schedules[] = ArrayHelper::merge($med_schedule, [
                '_sub_schedules' => AppQuery::getSubScheduleByScheduleId($med_schedule['med_schedule_id'])
            ]);
        }

        return $schedules;
    }

    public static function getDeptGrById($dept_group)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPTGr.DeptGrDesc, \' \', \'\') as DeptGrDesc',
            ])
            ->from('DEPTGr')
            ->where(['REPLACE(DEPTGr.DeptGroup, \' \', \'\')' => $dept_group])
            ->one(Yii::$app->mssql);
        return $rows;
    }

    public static function getDepartmentById($deptCode)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPT.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPT.deptDesc, \' \', \'\') as deptDesc'
            ])
            ->from('DEPT')
            ->where(['REPLACE(DEPT.deptCode, \' \', \'\')' => $deptCode])
            ->one(Yii::$app->mssql);
        return $rows;
    }

    public static function getDoctorListByDoctorIds($ids, $schedule_date = null)
    {
        if ($schedule_date ==  null) {
            $schedule_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
        }

        $doctors_list = (new \yii\db\Query())
            ->select(['tbl_doctor.*'])
            ->from('tbl_doctor')
            ->innerJoin('tbl_med_schedule', 'tbl_med_schedule.doctor_id = tbl_doctor.doctor_id')
            ->where(['tbl_doctor.doctor_id' => $ids])
            ->andWhere('tbl_med_schedule.schedule_date >= :schedule_date', [':schedule_date' => $schedule_date])
            ->groupBy('tbl_doctor.doctor_id')
            ->all(Yii::$app->db_queue);

        return $doctors_list;
    }

    public static function getSubScheduleTimes($appoint_date, $doc_code, $dept_code)
    {
        $formatter = Yii::$app->formatter;
        $date = new \DateTime($formatter->asDate('now', 'php:Y-m-d H:i:s'));
        $date->modify('+' . 2 . ' hours'); // เวลาปัจจุบัน + นาทีที่หาได้
        $unix_time = $formatter->asTimestamp($date);

        $current_date = Yii::$app->formatter->asDate('now', 'php:Y-m-d'); //;วัน/เวลา/ปัจจุบัน
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
        $query = (new \yii\db\Query())  //ตารางเวลาแพทย์
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
                'tbl_med_schedule_time.med_schedule_time_qty',
                'tbl_med_schedule_time.med_schedule_time_online_qty',
            ])
            ->from('tbl_med_schedule_time')
            ->innerJoin('tbl_med_schedule', 'tbl_med_schedule.med_schedule_id = tbl_med_schedule_time.med_schedule_id')
            ->innerJoin('tbl_doctor', 'tbl_doctor.doctor_id = tbl_med_schedule.doctor_id')
            ->innerJoin('tbl_service', 'tbl_service.service_id = tbl_med_schedule.service_id')
            ->where([
                'tbl_med_schedule.schedule_date' => $appoint_date,
                'tbl_med_schedule.service_id' => $service_ids,
               // 'tbl_doctor.doctor_id' =>  1,

                // 'tbl_med_schedule.service_id' => $service_ids,
                //'LEFT(tbl_service.service_name,8)' => 'ห้องตรวจ'
            ])
            ->groupBy('tbl_med_schedule_time.med_schedule_time_id')
            ->orderBy('tbl_med_schedule_time.start_time ASC');

        if ($current_date == $appoint_date) {  //ถ้าวันที่นัดแพทย์ เท่ากับ วันที่แพทย์ออกตรวจ
            $query->andWhere('(
                UNIX_TIMESTAMP(CONCAT( tbl_med_schedule.schedule_date, \' \', tbl_med_schedule_time.start_time )) >= '.$unix_time.' 
                OR 
                UNIX_TIMESTAMP( CONCAT( tbl_med_schedule.schedule_date, \' \', tbl_med_schedule_time.end_time )) >= '.$unix_time.'
                )');
            // $query->andWhere('UNIX_TIMESTAMP(CONCAT( tbl_med_schedule.schedule_date, \' \', tbl_med_schedule_time.start_time )) >= ' . $unix_time); //;เวลาเริ่มต้นที่แพย์ออกตรวจ มากกว่าเท่ากับ เวลาปัจุบัน
            // $query->orWhere('UNIX_TIMESTAMP(CONCAT( tbl_med_schedule.schedule_date, \' \', tbl_med_schedule_time.end_time )) >= ' . $unix_time); //;เวลาสิ้นสุดที่แพย์ออกตรวจ มากกว่าเท่ากับ เวลาปัจุบัน
        }


        if (!empty($doc_code)) {
            $query->andWhere([
                'tbl_doctor.doctor_code' =>  $doc_code
            ]);
        } else {
            $query->andWhere([
                'tbl_doctor.doctor_code' =>  '0'
            ]);
        }

        $schedule_times = $query->all(Yii::$app->db_queue);
        return $schedule_times;
    }

    public static function getAppointsHomc($appoint_date, $schedule_time, $doc_code, $dept_code)
    {
        $formatter = Yii::$app->formatter;
        $appoint_date_th = explode("-", $appoint_date);
        $appoint_date_th = ($appoint_date_th[0] + 543) . $appoint_date_th[1] . $appoint_date_th[2];

        $rows = (new \yii\db\Query())
            ->select([
                'Appoint.app_type',
                'Appoint.doctor',
                'Appoint.hn',
                'Appoint.appoint_date',
                'Appoint.appoint_time_from',
                'Appoint.appoint_time_to',
                'Appoint.appoint_note',
                'Appoint.pre_dept_code',
                'Appoint.maker',
                'Appoint.keyin_time',
                'Appoint.delete_flag',
                'DEPT.deptDesc',
                'DOCC.docName',
                'DOCC.docLName'
            ])
            ->from('Appoint')
            ->innerJoin('DEPT', 'DEPT.deptCode = Appoint.pre_dept_code')
            ->innerJoin('DOCC', 'DOCC.docCode = Appoint.doctor')
            ->leftJoin('Appoint_dep_doc', 'Appoint_dep_doc.docCode = DOCC.docCode')
            ->where([
                'Appoint.maker' => 'queue online',
                'Appoint.appoint_date' => $appoint_date_th,
                'Appoint.appoint_time_from' => $formatter->asDate($schedule_time['start_time'], 'php:H:i'),
                'Appoint.appoint_time_to' => $formatter->asDate($schedule_time['end_time'], 'php:H:i'),
                'Appoint.doctor' => sprintf("% 6s", $doc_code),
                'Appoint.pre_dept_code' => $dept_code,
            ])
            ->all(Yii::$app->mssql);

        return $rows;
    }

    public static function findOneDoctorByDocCode($doctor_code)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DOCC.docCode, \' \', \'\') as docCode',
                'REPLACE(DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(DOCC.docName, \' \', \'\') as docName',
                'REPLACE(DOCC.docLName, \' \', \'\') as docLName'
            ])
            ->from('DOCC')
            ->where([
                'REPLACE(DOCC.docCode, \' \', \'\')' => $doctor_code
            ])
            ->one(Yii::$app->mssql);
        return $rows;
    }

    public static function findOneDeptByDeptCode($dept_code)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'REPLACE(DEPT.deptCode, \' \', \'\') as deptCode',
                'REPLACE(DEPT.deptDesc, \' \', \'\') as deptDesc'
            ])
            ->from('DEPT')
            ->where([
                'REPLACE(DEPT.deptCode, \' \', \'\')' => $dept_code
            ])
            ->one(Yii::$app->mssql);
        return $rows;
    }

    public static function getHistoryAppoints($params)
    {
        $query = (new \yii\db\Query())
            ->select(['Appoint.*'])
            ->from('Appoint')
            ->where([
                'maker' => 'queue online',
                'appoint_date' => $params['appoint_date'],
                'pre_dept_code' => $params['pre_dept_code'],
                'appoint_time_from' => $params['appoint_time_from'],
                'appoint_time_to' => $params['appoint_time_to'],
                'hn' => sprintf("% 7s", $params['hn']),
                'CID' => $params['CID'],
            ]);

        if ($params['doc_code']) {
            $query->andWhere(['doctor' => sprintf("% 6s", $params['doc_code']),]);
        }

        return $query->all(Yii::$app->mssql);
    }

    public static function getAppointFollowUp($params)
    {
        $query = (new \yii\db\Query())
            ->select([
                'Appoint.*',
                'DEPT.deptDesc',
                'REPLACE( Appoint.hn, \' \', \'\') as hn',
                'REPLACE( DOCC.docName, \' \', \'\') as docName',
                'REPLACE( DOCC.docLName, \' \', \'\') as docLName',
                'REPLACE( PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE( PATIENT.lastName, \' \', \'\') as lastName'
            ])
            ->from('Appoint')
            ->leftJoin('DEPT', 'DEPT.deptCode = Appoint.pre_dept_code')
            ->leftJoin('DOCC', 'DOCC.docCode = Appoint.doctor')
            ->leftJoin('Appoint_dep_doc', 'Appoint_dep_doc.docCode = DOCC.docCode')
            ->leftJoin('PATIENT', 'PATIENT.hn = Appoint.hn')
            ->where([
                'Appoint.appoint_date' => $params['appoint_date'],
                'Appoint.maker' => 'queue online',
                'Appoint.CID' =>  $params['cid'],
                'Appoint.doctor' => sprintf("% 6s", $params['doctor']),
                'Appoint.hn' => sprintf("% 7s", $params['hn'])
            ]);
        if (!empty($hn)) {
            $query->andWhere([
                'Appoint.hn' => sprintf("% 7s", $params['hn'])
            ]);
        }
        $appoint = $query->one(Yii::$app->mssql);
        return $appoint;
    }

    public static function getUserHistory($profile)
    {
        $query = (new \yii\db\Query())
            ->select([
                'Appoint.doctor',
                'Appoint.hn',
                'Appoint.appoint_date',
                'Appoint.appoint_time_from',
                'Appoint.appoint_time_to',
                'Appoint.maker',
                'Appoint.phone',
                'Appoint.CID',
                'Appoint.pre_dept_code',
                'DEPT.deptDesc',
                'PATIENT.phone',
                'REPLACE(PATIENT.titleCode, \' \', \'\') as titleCode',
                'REPLACE( PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE( PATIENT.lastName, \' \', \'\') as lastName',
                'REPLACE(DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(DOCC.docName, \' \', \'\') as docName',
                'REPLACE(DOCC.docLName, \' \', \'\') as docLName'
            ])
            ->from('Appoint')
            ->innerJoin('DEPT', 'DEPT.deptCode = Appoint.pre_dept_code')
            ->leftJoin('PATIENT', 'PATIENT.hn = Appoint.hn')
            ->leftJoin('DOCC', 'DOCC.docCode = Appoint.doctor')
            ->where(['Appoint.maker' => 'queue online'])
            ->orderBy('Appoint.appoint_date DESC');
        if ($profile['hn']) {
            $query->andWhere([
                'Appoint.hn' => sprintf("% 7s", $profile['hn'])
            ]);
        }
        if ($profile['id_card']) {
            $query->andWhere([
                'Appoint.CID' => $profile['id_card']
            ]);
        }
        return $query->all(Yii::$app->mssql);
    }

    public static function getAppointmentsHistory($profile)
    {
        $query = (new \yii\db\Query())
            ->select([
                'Appoint.hn',
                'Appoint.appoint_date',
                'Appoint.appoint_time_from',
                'Appoint.appoint_time_to',
                'Appoint.maker',
                'Appoint.phone',
                'Appoint.CID',
                'DEPT.deptDesc',
                'PATIENT.phone',
                'REPLACE(PATIENT.titleCode, \' \', \'\') as titleCode',
                'REPLACE( PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE( PATIENT.lastName, \' \', \'\') as lastName',
                'REPLACE(DOCC.doctitle, \' \', \'\') as doctitle',
                'REPLACE(DOCC.docName, \' \', \'\') as docName',
                'REPLACE(DOCC.docLName, \' \', \'\') as docLName',
                'REPLACE(Appoint.pre_dept_code, \' \', \'\') as pre_dept_code',
                'REPLACE(Appoint.doctor, \' \', \'\') as doctor'
            ])
            ->from('Appoint')
            ->innerJoin('DEPT', 'DEPT.deptCode = Appoint.pre_dept_code')
            ->leftJoin('PATIENT', 'PATIENT.hn = Appoint.hn')
            ->leftJoin('DOCC', 'DOCC.docCode = Appoint.doctor')
            ->where(['Appoint.maker' => 'queue online'])
            ->orderBy('Appoint.appoint_date DESC');
        if ($profile['hn']) {
            $query->andWhere([
                'Appoint.hn' => sprintf("% 7s", $profile['hn'])
            ]);
        }
        if ($profile['id_card']) {
            $query->andWhere([
                'Appoint.CID' => $profile['id_card']
            ]);
        }
        return $query->one(Yii::$app->mssql);
    }

    public static function getQueueStatus($hn)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'PATIENT.hn',
                'REPLACE( PATIENT.firstName, \' \', \'\') as firstName',
                'REPLACE(PATIENT.lastName, \' \', \'\') as lastName',
                'PATIENT.phone',
                'PATIENT.birthDay',
                'PATIENT.titleCode',
                'REPLACE(PatSS.CardID, \' \', \'\') as CardID'
            ])
            ->from('PATIENT')
            ->innerJoin('PatSS', 'PatSS.hn = PATIENT.hn')
            ->where(['PATIENT.hn' => sprintf("% 7s", $hn)])
            ->one(Yii::$app->mssql);
        return $rows;
    }

    public static function getDataQueue($hn)
    {
        $startDate = Yii::$app->formatter->asDate('now', 'php:Y-m-d 00:00:00');
        $endDate = Yii::$app->formatter->asDate('now', 'php:Y-m-d 23:59:59');
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

        return $rows;
    }
}
