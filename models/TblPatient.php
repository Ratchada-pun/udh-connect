<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "tbl_patient".
 *
 * @property int $id
 * @property string $first_name ชื่อ
 * @property string $last_name นามสกุล
 * @property int $id_card เลขบัตรประขำตัวประชาชน
 * @property string $brith_day วัน/เดือน/ปี เกิด
 * @property int $phone_number หมายเลขโทรศัพท์
 * @property string|null $created_at วันที่บันทึก
 * @property string $line_id id ไลน์
 */
class TblPatient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    

    


    public static function tableName()
    {
        return 'tbl_patient';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'id_card', 'brith_day', 'phone_number'], 'required'],
            [['id', 'phone_number'], 'integer'],
            [['brith_day', 'created_at'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['line_id','hn'], 'string', 'max' => 100],
            [['id_card'], 'string', 'max' => 13],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'ชื่อ',
            'last_name' => 'นามสกุล',
            'id_card' => 'เลขบัตรประขำตัวประชาชน',
            'hn' => 'เลขประจำตัวผู้ป่วย',
            'brith_day' => 'วัน/เดือน/ปี เกิด',
            'phone_number' => 'หมายเลขโทรศัพท์',
            'created_at' => 'วันที่บันทึก',
            'line_id' => 'id ไลน์',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TblPatientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblPatientQuery(get_called_class());
    }
}
