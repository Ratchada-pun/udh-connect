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

$this->title = 'นัดหมาย';

$this->registerCssFile("@web/js/waitMe/waitMe.min.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()]
]);
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
<style>
    /* .login-form .btn {
        padding: 0.375rem 0.75rem;
    } */
    .list-group-item.active {
        background-color: #ff518a;
        border-color: #ff518a;
    }

    .btn-outline-secondary:not(:disabled):not(.disabled):active,
    .btn-outline-secondary:not(:disabled):not(.disabled).active,
    .show>.btn-outline-secondary.dropdown-toggle {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn {
        text-align: left;
        font-size: 16pt;
    }

    .img {
        max-width: 10%;
    }

    .modal-header {
        padding: 0.50rem 0.50rem;
        padding-left: 5%;
    }

    .modal-body {
        padding: 0rem;
    }

    .datepicker-inline {
        width: 100%;
    }

    .datepicker table {
        width: 100%;
    }

    td.appoint-dot::before {
        display: table;
        top: 18px;
        left: 0;
        width: 12px;
        height: 12px;
        margin-left: 0;
        content: '';
        border-width: 1px;
        border-color: inherit;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 0 0 3px #e5ebf8 inset;
    }

    td.appoint-dot::before {
        box-shadow: 0 0 0 3px #eac459 inset !important;
    }

    .datepicker table tr td.appoint-dot:hover {
        color: #fff;
        background-color: #007bff !important;
        border-color: #007bff !important;
    }

    .hidden {
        display: none !important;
    }

    .datepicker {
        position: fixed;
        top: 20% !important;
        right: 0 !important;
        left: 0 !important;
        /* z-index: 210 !important; */
        margin: auto;
    }

    .datepicker table.table-condensed {
        position: relative;
        width: 100%;
    }

    .datepicker tbody tr>td.active:after {
        content: '';
        display: inline-block;
        border-color: #ebedf2 transparent #fff;
        border-style: solid;
        border-width: 0 0 7px 7px;
        /* position: absolute; */
        bottom: 4px;
        right: 4px;
    }

    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        background: #eaeef3 !important;
        color: red;
        cursor: default;
    }

    .overlay {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.65);
    }

    .login-form label {
        color: #53505f;
        text-transform: uppercase;
    }

    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }

    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid #fff;
        border-color: #fff transparent #fff transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }

    .loader {
        color: #ff518a;
        font-size: 10px;
        margin: 100px auto;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        position: relative;
        text-indent: -9999em;
        -webkit-animation: load4 1.3s infinite linear;
        animation: load4 1.3s infinite linear;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
    }
    .control__indicator {
    position: absolute;
    top: 2px;
    left: 0;
    width: 20px;
    height: 20px;
    border: 1px solid #f1136f;
    border-radius: 3px;
    background: #fff;
}

    @-webkit-keyframes load4 {

        0%,
        100% {
            box-shadow: 0 -3em 0 0.2em, 2em -2em 0 0em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 0;
        }

        12.5% {
            box-shadow: 0 -3em 0 0, 2em -2em 0 0.2em, 3em 0 0 0, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
        }

        25% {
            box-shadow: 0 -3em 0 -0.5em, 2em -2em 0 0, 3em 0 0 0.2em, 2em 2em 0 0, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
        }

        37.5% {
            box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 0, 2em 2em 0 0.2em, 0 3em 0 0em, -2em 2em 0 -1em, -3em 0em 0 -1em, -2em -2em 0 -1em;
        }

        50% {
            box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 0em, 0 3em 0 0.2em, -2em 2em 0 0, -3em 0em 0 -1em, -2em -2em 0 -1em;
        }

        62.5% {
            box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 0, -2em 2em 0 0.2em, -3em 0 0 0, -2em -2em 0 -1em;
        }

        75% {
            box-shadow: 0em -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0.2em, -2em -2em 0 0;
        }

        87.5% {
            box-shadow: 0em -3em 0 0, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0, -2em -2em 0 0.2em;
        }
    }

    @keyframes load4 {

        0%,
        100% {
            box-shadow: 0 -3em 0 0.2em, 2em -2em 0 0em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 0;
        }

        12.5% {
            box-shadow: 0 -3em 0 0, 2em -2em 0 0.2em, 3em 0 0 0, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
        }

        25% {
            box-shadow: 0 -3em 0 -0.5em, 2em -2em 0 0, 3em 0 0 0.2em, 2em 2em 0 0, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
        }

        37.5% {
            box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 0, 2em 2em 0 0.2em, 0 3em 0 0em, -2em 2em 0 -1em, -3em 0em 0 -1em, -2em -2em 0 -1em;
        }

        50% {
            box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 0em, 0 3em 0 0.2em, -2em 2em 0 0, -3em 0em 0 -1em, -2em -2em 0 -1em;
        }

        62.5% {
            box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 0, -2em 2em 0 0.2em, -3em 0 0 0, -2em -2em 0 -1em;
        }

        75% {
            box-shadow: 0em -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0.2em, -2em -2em 0 0;
        }

        87.5% {
            box-shadow: 0em -3em 0 0, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0, -2em -2em 0 0.2em;
        }
    }

    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }


    @media (max-width: 767px) {
        .datepicker {
            left: 10px !important;
            right: 10px !important;
            max-width: 500px;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .datepicker {
            left: 10px !important;
            right: 10px !important;
            max-width: 500px;
        }
    }

    /* md */
    @media (min-width: 992px) and (max-width: 1199px) {
        .datepicker {
            max-width: 50%;
        }
    }

    /* lg */
    @media (min-width: 1200px) {
        .datepicker {
            max-width: 50%;
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
                                <p style="font-size: 16pt;margin-top:5px;">
                                    โรงพยาบาลอุดรธานี
                                </p>
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    นัดหมาย
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
                        <div class="card-body">

                            <div class="form-group">
                                <div class="list-group">

                                    <div class="input-group">
                                        <a class="list-group-item list-group-item-action active" style="font-size: 14pt;">
                                            แผนก : <?= $deptCodeSub['deptDesc'] ?>
                                        </a>
                                    </div>
                                    <br>
                                    <p style="margin:0;"><small class="text-danger">โปรดเลือก</small></p>    
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-pill btn-outline-success btn-doc-option" style="border: 1px solid #e5e9ec;">
                                            <input type="radio" name="doc_option" id="option" checked value="0">
                                            ไม่เลือกแพทย์
                                        </label>
                                        <label class="btn btn-pill btn-outline-success btn-doc-option" style="border: 1px solid #e5e9ec;">
                                            <input type="radio" name="doc_option" id="option1" data-toggle="modal" data-target="#exampleModal3" value="selection">
                                            เลือกแพทย์
                                        </label>
                                        <label id="btn-random" class="btn btn-pill btn-outline-success btn-doc-option" style="border: 1px solid #e5e9ec;">
                                            <input type="radio" name="doc_option" id="option2"  value="random">
                                            แนะนำแพทย์ให้
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <?= Html::input('text', 'doctor', '', [
                                    'id' => 'doctor',
                                    'class' => 'form-control hidden',
                                    'placeholder' => 'ชื่อแพทย์',
                                    'readonly' => 'readonly'
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
                                echo '<label class="control-label"><b>วันที่นัดแพทย์</b></label>';
                                echo '<p style="margin:0;"><small class="text-danger">โปรดเลือกวันที่แพทย์ออกตรวจ</small></p>';

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
                                        // 'datesDisabled' => [
                                        //     "20/03/2020"
                                        // ],
                                        // "endDate" => "25/03/2020"
                                    ],

                                ]);
                                ?>
                                <div class="help-block invalid-feedback"></div>
                            </div>

                            <div class="form-group">
                                <p class="fw-600">ระบุเวลานัด</p>
                                <?=
                                    Html::activeHiddenInput($model, 'appoint_time', [
                                        'id' => 'appoint_time'
                                    ]);
                                ?>
                                <div class="appoint-time"></div>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="btn-demo mb-4 d-flex">
                                    <button type="reset" class="btn btn-danger" id="reset-form" style="text-align: center">
                                        ล้างข้อมูล
                                    </button>

                                    <button type="submit" class="btn btn-success" name="signup1" value="Sign up" style="text-align: center">
                                        ลงทะเบียน
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bd-example-row">
                <div class="card-body">
                    <?php foreach ($doctors as $key => $value) : ?>
                        <li class="list-group-item list-group-doc-name" style="padding: 5px;">
                            <label class="control control-outline control-outline-danger control--radio" style="margin-bottom: 0;">
                                <?= $value['doctor_name'] ?>
                                <input type="radio" name="docname" value="<?= $value['doctor_code'] ?>" data-docname="<?= $value['doctor_name'] ?>">
                                <span class="control__indicator"></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile(
    '@web/js/moment.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    '@web/js/waitMe/waitMe.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/sweetalert2@9',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJs(
    <<<JS
moment.locale('th');

$('input[name="doc_option"]').on('change',function(e) {
    e.preventDefault();
    if($(this).val() === '0') {
        $('#doctor').addClass('hidden')
        $('#doctor, #doctor_id').val('')
        $('input[name="docname"]').prop("checked", false);
        ClearForm()
    } else {
        $('#doctor').removeClass('hidden')
    }
})
$('input[name="docname"]').on('change',function(e) {
    e.preventDefault();
    $( ".appoint-time" ).html('')
    if($(this).is(':checked')){
        $('#doctor').val($(this).data('docname'))
        $('#doctor_id').val($(this).val())
        $(this).prop("checked", true);
        $('#exampleModal3').modal('hide');
        GetSchedules($(this).val())
    } else {
        $('#doctor').val('')
        $('#doctor_id').val('')
        $(this).prop("checked", false);
        $('#exampleModal3').modal('hide')
    }

});

function GetSchedules(docId) {
    $('#appoint-form').waitMe({
        effect : 'roundBounce',
        color: '#ff518a'
    })
    $.ajax({
        method: "GET",
        url: "/app/appoint/schedules",
        data: {
            doc_id: docId
        },
        dataType: "json",
        success: function (data) {
            if(data.length) {
                dateList = []; // YYYY-MM-DD
                for (let index = 0; index < data.length; index++) {
                    dateList.push(data[index].schedule_date);
                }
                var startDate = moment(data[0].schedule_date).format('DD/MM/YYYY');
                var endDate = moment(data[data.length-1].schedule_date).format('DD/MM/YYYY');

                var firstDate = moment(data[0].schedule_date); // YYYY-MM-DD
                var lastDate = moment(data[data.length-1].schedule_date); // YYYY-MM-DD

                var diffDates = lastDate.diff(firstDate, 'days');

                let startDateDiff = data[0].schedule_date; // YYYY-MM-DD
                var datesDisabled = [];
                for (let i = 0; i < diffDates; i++) {
                    var tomorrow = new Date(startDateDiff);
                    tomorrow.setDate(tomorrow.getDate() + 1); // add date
                    if(!dateList.includes(moment(tomorrow).format('YYYY-MM-DD'))) {
                        datesDisabled.push(moment(tomorrow).format('DD/MM/YYYY'));
                    }
                    startDateDiff = moment(tomorrow).format('YYYY-MM-DD');
                }
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setStartDate', startDate);
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setEndDate', endDate);
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setDatesDisabled', datesDisabled);
                //jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('update', startDate);
            } else {
                var startDate = moment().format('DD/MM/YYYY');
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setStartDate', startDate);
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setEndDate', null);
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setDatesDisabled', []);
                jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('update', startDate);
            }
            $('#appoint-form').waitMe("hide");
        },
        error: function(jqXHR,  textStatus,  errorThrown) {
            $('#appoint-form').waitMe("hide");
            Swal.fire({
                title: 'Error!',
                text: errorThrown,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            })
        }
    });
}

function GetScheduleTimes(date) {
    $('#appoint-form').waitMe({
        effect : 'roundBounce',
        color: '#ff518a'
    })
    var formArray = objectifyForm()
    $.ajax({
        method: "POST",
        url: "/app/appoint/schedule-times",
        data: {
            ...formArray,
            appoint_date: date
        },
        dataType: "json",
        success: function (data) {
            $('#doctor').removeClass('hidden')
            $( ".appoint-time" ).html('')
            if(data.length) {
                for (let index = 0; index < data.length; index++) {
                    $( ".appoint-time" ).append( `<label class="control control-solid control-solid-success control--radio">
                        \${data[index].text}
                        <input type="radio" name="appoint_times" value="\${data[index].value}" />
                            <span class="control__indicator"></span>
                    </label>` );
                }
            }
            $('#appoint-form').waitMe("hide");
        },
        error: function( jqXHR,  textStatus,  errorThrown) {
            $('#appoint-form').waitMe("hide");
            Swal.fire({
                title: 'Error!',
                text: errorThrown,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            })
        }
    });
}
var form = $('#appoint-form');
function objectifyForm() {//serialize data function
    var formArray = form.serializeArray()
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
    returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}

function ClearForm(){
    $( ".appoint-time" ).html('')
    $( ".btn-doc-option" ).removeClass('active')
    $(`input[name="doc_option"]`).prop("checked", false);
    $('#doctor').addClass('hidden')
    $('#doctor, #doctor_id').val('')
    $('input[name="docname"]').prop("checked", false);
    dateList = [];
    var startDate = moment().format('DD/MM/YYYY');
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setStartDate', startDate);
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setEndDate', null);
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setDatesDisabled', []);
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('update', startDate);
    $('#appoint-form').trigger('reset');
}

$('#reset-form').on('click',function(){
    ClearForm()
})

jQuery('#appointmodel-appoint_date-kvdate').on('show', function(){
    $('#overlay').removeClass('hidden');
});
jQuery('#appointmodel-appoint_date-kvdate').on('hide', function(){
    $('#overlay').addClass('hidden');
});

$('#appointmodel-appoint_date-kvdate').kvDatepicker()
    .on("changeDate", function(e) {
        GetScheduleTimes(e.format('yyyy-mm-dd'));
});


$('#btn-random').on('click',function(e){
    e.preventDefault();
    
    randomDoctor()
})

function randomDoctor(){
    var docIds = []
    var docNames = []
    $.each($('#exampleModal3').find('input[name="docname"]'), function( index, value ) {
        docIds.push($(this).val())
        docNames.push({
            id: $(this).val(),
            name: $(this).data('docname'),
        })
    });
    var doctorId = docIds[Math.floor(Math.random()*docIds.length)];
    var doctor = docNames.find(d => d.id === doctorId)
    $('#doctor').val(doctor.name)
    $('#doctor_id').val(doctorId)
    $(`input[value="\${doctorId}"]`).prop("checked", true);
    var startDate = moment().format('DD/MM/YYYY');
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setStartDate', startDate);
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setEndDate', null);
    jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('setDatesDisabled', []);
    //jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('update', startDate);
    GetSchedules(doctorId)
}

// jQuery("#appoint-form").yiiActiveForm(
//   [
//     {
//       id: "appoint_date",
//       name: "appoint_date",
//       container: ".field-appoint_date",
//       input: "#appoint_date",
//       error: ".help-block.invalid-feedback",
//       validate: function(attribute, value, messages, deferred, \$form) {
//         yii.validation.required(value, messages, {
//           message: "Appoint Date cannot be blank."
//         });
//       }
//     }
//   ],
//   []
// );

var \$form = $('#appoint-form');
\$form.on('beforeSubmit', function() {
    $('#appoint-form').yiiActiveForm('updateAttribute', 'appoint_date', ["Appoint Date cannot be blank."]);
    var data = \$form.serialize();
    var formArray = objectifyForm()
    $.ajax({
        url: "/app/appoint/save-appoint",
        type: 'POST',
        data: {
            ...formArray,
            appoint_time_from: formArray.appoint_times ? formArray.appoint_times.substring(0, 5) : '',
            appoint_time_to: formArray.appoint_times ? formArray.appoint_times.substring(6, 11) : '',
        },
        success: function (data) {
            // Implement successful
            ClearForm()
            Swal.fire({
                title: 'นัดแพทย์สำเร็จ',
                text: "",
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                    if (result.value) {
                        window.location.href = `/app/appoint/follow-up?hn=\${data.hn}&appoint_date=\${data.appoint_date}&doctor=\${data.doctor}`
                    }
                })
        },
        error: function(jqXHR,  textStatus,  errorThrown) {
            Swal.fire({
                title: 'Error!',
                text: errorThrown,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            })
        }
    });
    return false; // prevent default submit
});
JS
);
?>