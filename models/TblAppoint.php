<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_appoint".
 *
 * @property int $appoint_id ไอดีนัด
 * @property string|null $hn รหัสผู้ป่วย
 * @property string $appoint_date วันที่นัด
 * @property string|null $app_time_from เวลาเริ่มนัด
 * @property string|null $app_time_to เวลาสิ้นสุดนัด
 * @property string|null $app_note รายละเอียดนัด
 * @property string|null $dept_code รหัสแผนก
 * @property string|null $dept_desc ชื่อแผนก
 * @property string|null $doc_code รหัสแพทย์
 * @property string|null $doc_name ชื่อแพทย์
 * @property string|null $cid รหัสบัตรประจำตัว
 * @property string|null $qrcode รหัส qr code
 */
class TblAppoint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_appoint';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appoint_date'], 'required'],
            [['appoint_date'], 'safe'],
            [['hn', 'app_time_from', 'app_time_to', 'cid'], 'string', 'max' => 50],
            [['app_note', 'dept_desc', 'doc_name', 'qrcode'], 'string', 'max' => 255],
            [['dept_code', 'doc_code'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'appoint_id' => 'ไอดีนัด',
            'hn' => 'รหัสผู้ป่วย',
            'appoint_date' => 'วันที่นัด',
            'app_time_from' => 'เวลาเริ่มนัด',
            'app_time_to' => 'เวลาสิ้นสุดนัด',
            'app_note' => 'รายละเอียดนัด',
            'dept_code' => 'รหัสแผนก',
            'dept_desc' => 'ชื่อแผนก',
            'doc_code' => 'รหัสแพทย์',
            'doc_name' => 'ชื่อแพทย์',
            'cid' => 'รหัสบัตรประจำตัว',
            'qrcode' => 'รหัส qr code',
        ];
    }
}
