<?php

namespace app\models;

use Yii;
use yii\base\Model;

class AppointModel extends Model
{

    public $doctor_id;
    public $doc_code;
    public $dept_code;
    public $appoint_date;
    public $appoint_time;

    public function attributeLabels()
    {
        return [
            'doc_code' => 'ชื่อแพทย์ที่นัด',
            'dept_code' => 'ชื่อแผนก',
            'appoint_date' => 'วันที่นัด',
            'appoint_time' => 'เวลานัด'
        ];
    }

    public function rules()
    {
        return [
            [['dept_code', 'appoint_date', 'appoint_time'], 'required'],
        ];
    }
}
