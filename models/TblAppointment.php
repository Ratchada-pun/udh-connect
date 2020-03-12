<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_appointment".
 *
 * @property int $appointment_id
 * @property int $department_id แผนก
 * @property int|null $doctor_id หมอ
 * @property string $appointment_date วันที่นัด
 * @property string $appointment_time เวลานัด
 * @property string $created_at วันที่บันทึก
 * @property string $updated_at วันที่แก้ไข
 */
class TblAppointment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_appointment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appointment_id', 'department_id', 'appointment_date', 'appointment_time', 'created_at', 'updated_at'], 'required'],
            [['appointment_id', 'department_id', 'doctor_id'], 'integer'],
            [['appointment_date', 'created_at', 'updated_at'], 'safe'],
            [['appointment_time'], 'string', 'max' => 50],
            [['appointment_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'appointment_id' => 'Appointment ID',
            'department_id' => 'แผนก',
            'doctor_id' => 'หมอ',
            'appointment_date' => 'วันที่นัด',
            'appointment_time' => 'เวลานัด',
            'created_at' => 'วันที่บันทึก',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TblAppointmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblAppointmentQuery(get_called_class());
    }
}
