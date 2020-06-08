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


    public $day;

    public $month;

    public $year;


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
            [['first_name', 'last_name', 'id_card', 'brith_day', 'phone_number', 'day', 'month', 'year'], 'required'],
            [['id'], 'integer'],
            [['brith_day', 'created_at'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['line_id', 'hn'], 'string', 'max' => 100],
            [['id_card'], 'string', 'max' => 13],
            [['user_type'], 'string', 'max' => 10],
            [['phone_number'], 'string', 'max' => 10, 'min' => 10],
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
            'id_card' => 'เลขบัตรประจำตัวประชาชน',
            'hn' => 'เลขประจำตัวผู้ป่วย',
            'brith_day' => 'วัน/เดือน/ปี เกิด',
            'phone_number' => 'หมายเลขโทรศัพท์',
            'created_at' => 'วันที่บันทึก',
            'line_id' => 'id ไลน์',
            'day' => 'วันเกิด',
            'month' => 'เดือน',
            'year' => 'ปี',
            'user_type' => 'ปรเภทผู้ใช้งาน'
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

    public function getDayOptions()
    {
        $items = [];
        for ($i = 1; $i <= 31; $i++) {
            $items[$i < 10 ? '0' . $i : $i] = $i;
        }
        return $items;
    }

    public function getMonthOptions()
    {
        $items = [];
        for ($i = 1; $i <= 12; $i++) {
            $items[$i < 10 ? '0' . $i : $i] = Yii::$app->formatter->asDate(mktime(0, 0, 0, $i, 10), 'php:F');
        }
        return $items;
    }

    public function getYearOptions()
    {
        $items = [];
        $currentYear = Yii::$app->formatter->asDate('now', 'php:Y');
        $startYear = $currentYear - 100;
        for ($i = $startYear; $i <= $currentYear; $i++) {
            $items[$i] = $i + 543;
        }
        return $items;
    }
}
