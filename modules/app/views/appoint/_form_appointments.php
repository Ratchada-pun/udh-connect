<?php

use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */
/* @var $form yii\widgets\ActiveForm */

Yii::$app->formatter->locale = 'th-TH';
$currentday = Yii::$app->formatter->asDate('now', 'php:d');
$currentmonth = Yii::$app->formatter->asDate('now', 'php:m');
$currentYear = Yii::$app->formatter->asDate('now', 'php:Y');
$startYear = $currentYear - 100;
$last_day_str = strtotime('last day of this month', Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s')));
$last_day = Yii::$app->formatter->asDate($last_day_str, 'php:d');

use yii\web\JsExpression;
use yii\web\View;

$this->title = 'นัดหมายแพทย์';
$this->registerCssFile("@web/css/style.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
$this->registerCss(<<<CSS

CSS
);
$this->registerJs(
    "var dateList= [];",
    View::POS_HEAD
);
$beforeShowDay = new JsExpression(
    <<<JS
function(date){
    if (dateList.includes(moment(date).format('YYYY-MM-DD'))) {
         return {
            tooltip: 'วันที่แพทย์ออกตรวจ',
            classes: 'appoint-dot'
        };
    }
}
JS
);

?>

<div class="sufee-login d-flex align-content-center flex-wrap">
    <div class="container">
        <div class="login-content">
            <div class="card-header text-white bg-danger border-0  text-center">
                <div class="media p-6">
                    <div class="media-body">
                        <p class="btn-flat m-b-30 m-t-30">
                            <strong class="">
                                <p style="font-size: 16pt;margin-top:5px;">
                                    โรงพยาบาลอุดรธานี
                                </p>
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    นัดหมายแพทย์
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'appoint-form', 'type' => ActiveForm::TYPE_VERTICAL, 'options' => ['data-pjax' => true]]); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card-body" style="padding: 0;">

                            <div class="form-group">
                                <div class="list-group">

                                    <div class="input-group">
                                        <a class="list-group-item list-group-item-action active" style="font-size: 14pt;">
                                            แผนก : <?= $deptCodeSub['deptDesc'] ?>
                                        </a>
                                    </div>
                                    <br>
                                    <p style="margin:0;">
                                        <small class="text-danger" style="font-size: 10pt;">
                                            <i class="fa fa-bullhorn"></i>
                                            โปรดเลือก
                                        </small>
                                    </p>    
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-pill btn-outline-success btn-doc-option" style="border: 1px solid #e5e9ec;text-align:center;font-size:14pt;">
                                            <input type="radio" name="doc_option" id="option" value="0">
                                            ไม่ระบุแพทย์
                                        </label>
                                        <label class="btn btn-pill btn-outline-success btn-doc-option" style="border: 1px solid #e5e9ec;text-align:center;font-size:14pt;">
                                            <input type="radio" name="doc_option" id="option1" data-toggle="modal" data-target="#exampleModal3" value="selection">
                                            ระบุแพทย์
                                        </label>
                                        <!-- <label id="btn-random" class="btn btn-pill btn-outline-success btn-doc-option" style="border: 1px solid #e5e9ec;">
                                            <input type="radio" name="doc_option" id="option2"  value="random">
                                            แนะนำแพทย์ให้
                                        </label> -->
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <?= Html::input('text', 'doctor', '', [
                                    'id' => 'doctor',
                                    'class' => 'form-control hidden',
                                    'placeholder' => 'ไม่ระบุแพทย์',
                                    'readonly' => 'readonly',
                                    'style' =>'font-size:12pt;'
                                ]) ?>
                                <?=
                                    Html::activeHiddenInput($model, 'doc_code', [
                                        'id' => 'doctor_id'
                                    ]);
                                ?>
                                <?=
                                    Html::activeHiddenInput($model, 'dept_code', [
                                        'id' => 'dept_code',
                                        'value' => $dept_code
                                    ]);
                                ?>
                            </div>
                             
                            <div class="form-group field-appoint_date">
                                <?php
                                echo '<label class="control-label" style="font-size:14pt;"><b>วันที่นัดแพทย์</b></label>';
                                echo '<p style="margin:0;"><small class="text-danger" style="font-size: 10pt;"> <i class="fa fa-bullhorn"></i>โปรดเลือกวันที่แพทย์ออกตรวจ</small></p>';

                                echo DatePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'appoint_date',
                                    //'type' => DatePicker::TYPE_INLINE,
                                    'readonly' => true,
                                    'pickerIcon' => '<i class="fa fa-calendar"></i>',
                                    'removeIcon' => '<i class="fa fa-trash"></i>',
                                    'language' => 'th',
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd/mm/yyyy',
                                        //'todayHighlight' => true,
                                        'todayBtn' => true,
                                        'startDate' => Yii::$app->formatter->asDate('now', 'php:d-m-Y'),
                                        'daysOfWeekDisabled' => "0,6",
                                        'beforeShowDay' => $beforeShowDay,
                                        'zIndexOffset' => 1050,
                                        'style' => 'font-size:14pt;',
                                        'todayHighlight' => false
                                        // 'datesDisabled' => [
                                        //     "20/03/2020"
                                        // ],
                                        // "endDate" => "25/03/2020"
                                    ],
                                    'options' => ['placeholder' => 'เลือกวันที่แพทย์ออกตรวจ....'],

                                ]);
                                ?>
                                <div class="help-block invalid-feedback"></div>
                            </div>
                                
                            <div class="form-group">
                                <p class="fw-600" style="font-size:14pt;">ระบุเวลานัด</p>
                                   <div class="appoint-time"></div>
                            </div>
                            <br>

                            <div class="form-group">
                                <div class="btn-demo mb-4 d-flex">
                                    <button type="reset" class="btn btn-danger" id="reset-form" style="text-align: center">
                                        ล้างข้อมูล
                                    </button>
                                    <button type="submit" class="btn btn-success" name="signup1" value="Sign up" style="text-align: center">
                                        นัดแพทย์
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>

            </div>
        </div>

    </div>
</div>

<div id="overlay" class="overlay hidden"></div>


<!-- Modal -->
<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel3" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel3">รายชื่อแพทย์</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            
            <div class="modal-body bd-example-row">
                <div class="card-body">
                    <?php 
                        if(empty($doctors)){
                            echo '<h1 class="text-center" style="color:red">ไม่พบรายชื่อแพทย์</h1>';
                        }
                    ?>
                    <?php foreach ($doctors as $key => $value) : ?>
                        <li class="list-group-item list-group-doc-name" style="padding: 5px;">
                            <label class="control control-outline control-outline-danger control--radio" style="margin-bottom: 0;">
                                <?= $value['doctor_name'] ?>
                                <input type="radio" id="<?= $value['doctor_code'] ?>" name="docname" value="<?= $value['doctor_code'] ?>" data-docname="<?= $value['doctor_name'] ?>">
                                <span class="control__indicator"></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger " data-dismiss="modal">
                    <i class="fa fa-close"></i>
                    ปิดหน้า
               </button>        
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("moment.locale('th')");
// $this->registerJsFile(
//     '@web/js/appointments.js',
//     ['depends' => [\yii\web\JqueryAsset::className()]]
// );

$this->registerJs($this->render('@webroot/js/appointments.js'));
?>