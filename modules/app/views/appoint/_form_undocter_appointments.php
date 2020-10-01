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
use yii\helpers\Json;

$this->title = 'นัดหมายแพทย์';
$this->registerCssFile("@web/css/style.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
$this->registerCss(<<<CSS

CSS
);
$this->registerJs(
    "var dateList= ".Json::encode($dateList).";",
    View::POS_HEAD
);
$beforeShowDay = new JsExpression(
    <<<JS
function(date){
    if (dateList.includes(moment(date).format('DD/MM/YYYY'))) {
         return {
            tooltip: 'วันที่แพทย์ออกตรวจ',
            classes: 'appoint-dot'
        };
    }
}
JS
);

?>
<style>
.hidden{
    display: none !important;
}
@media (max-width: 767px) {
    .quick-links-grid .ql-grid-item {
        width: 100% !important;
    }
    .login-content {
        margin: 0;
    }
    .container-fluid {
        padding-right: 0;
        padding-left: 0;
    }
    .card-body {
            padding: 0;
    }
}

</style>

<div class="sufee-login d-flex align-content-center flex-wrap">
    <div class="container">
        <div class="login-content">
            <div class="card-header text-white bg-danger border-0  text-center">
                <div class="media p-6">
                    <div class="media-body">
                        <p class="btn-flat m-b-30 m-t-30">
                            <strong class="">
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    นัดหมายไม่ระบุแพทย์
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
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Html::input('text', 'doctor', '0', [
                                    'id' => 'doctor',
                                    'class' => 'form-control hidden',
                                    'placeholder' => 'ไม่ระบุแพทย์',
                                    'readonly' => 'readonly',
                                    'style' =>'font-size:12pt;display: none;'
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
                                    <p class="fw-600" style="font-size:14pt;">
                                        วันที่นัด
                                    </p>
                                <?php
                             //  echo '<p style="margin:0;"><small class="text-danger" style="font-size: 10pt;"> <i class="fa fa-bullhorn"></i>โปรดเลือกวันที่นัดหมายแพทย์</small></p>';

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
                                        'todayBtn' => true,
                                        'startDate' => Yii::$app->formatter->asDate('now', 'php:d-m-Y'),
                                        'daysOfWeekDisabled' => "0,6",
                                        'beforeShowDay' => $beforeShowDay,
                                        'zIndexOffset' => 1050,
                                        'style' => 'font-size:14pt;',
                                        'todayHighlight' => false,
                                        'startDate' => $startDate,
                                        'endDate' => $endDate,
                                    ],
                                    'options' => ['placeholder' => 'โปรดเลือกวันที่นัดหมายแพทย์...'],

                                ]);
                                ?>
                                <div class="help-block invalid-feedback"></div>
                            </div>
                        
                            <div class="form-group">
                                <p class="fw-600" style="font-size:14pt;">
                                    ระบุเวลานัด
                                </p>
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


<?php
echo $this->render('menu');
?>

<?php
$this->registerJs("moment.locale('th')");
// $this->registerJsFile(
//     '@web/js/appointments.js',
//     ['depends' => [\yii\web\JqueryAsset::className()]]
// );

$this->registerJs($this->render('@webroot/js/appointments.js'));
?>