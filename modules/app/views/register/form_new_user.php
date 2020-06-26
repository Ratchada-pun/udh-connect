<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\icons\Icon;
use yii\bootstrap4\Alert;
use app\assets\SweetAlert2Asset;

SweetAlert2Asset::register($this);
Yii::$app->formatter->locale = 'th-TH';
$currentday = Yii::$app->formatter->asDate('now', 'php:d');
$currentmonth = Yii::$app->formatter->asDate('now', 'php:m');
$currentYear = Yii::$app->formatter->asDate('now', 'php:Y');
$startYear = $currentYear - 100;
$last_day_str = strtotime('last day of this month', Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s')));
$last_day = Yii::$app->formatter->asDate($last_day_str, 'php:d');

$this->title = "ลงทะเบียนผู้ป่วยใหม่";

$this->registerCssFile("@web/js/waitMe/waitMe.min.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
?>
<style>
    .form-group.has-error .form-control {
        border-color: #a94442;
    }
    .form-group.has-success .form-control {
        border-color: #3c763d;
    }
    .form-group.has-error .help-block {
        color: #a94442;
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

        @media (max-width: 767px) {
            .card-body {
                padding: 0;
            }
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
                                    นัดหมายแพทย์
                                </p>
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    ลงทะเบียนผู้ป่วยใหม่
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'form-signup', 'type' => ActiveForm::TYPE_VERTICAL]); ?>
                <div class="row">
                    <div class=" col-12">
                        <div class="card-body">

                            <div class="form-group">
                                <?= $form->field($model, 'first_name', ['addon' => ['prepend' => ['content' => '<i class="icon-user"></i>']]])->textInput([
                                    'placeholder' => 'ชื่อ',
                                    'maxlength' => true
                                ]) ?>
                                <!-- <label class="control-label" for="first_name">ชื่อจริง</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="icon-user"></i>
                                    </span>

                                    <input type="text" name="first_name" class="form-control" placeholder="ชื่อ" aria-label="ชื่อ">
                                </div> -->
                            </div>
                            <div class="form-group">
                                <?= $form->field($model, 'last_name', ['addon' => ['prepend' => ['content' => '<i class="icon-user"></i>']]])->textInput([
                                    'placeholder' => 'นามสกุล',
                                    'maxlength' => true
                                ]) ?>

                                <!-- <label class="control-label" for="last_name">นามสกุล</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="icon-user"></i>
                                    </span>
                                    <input type="text" name="last_name" class="form-control" placeholder="นามสกุล" aria-label="นามสกุล">
                                </div> -->
                            </div>

                            <div class="form-group">
                                <?= $form->field($model, 'id_card', ['addon' => ['prepend' => ['content' => ' <i class="fa fa-address-card-o"></i>']]])->textInput([
                                    'placeholder' => 'เลขประจำตัวประชาชน',
                                    'id' => "cid",
                                    'maxlength' => true
                                ]) ?>
                                <!-- <label class="control-label" for="id_card">เลขประจำตัวประชาชน</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-address-card-o"></i>
                                    </span>
                                    <input type="text" id="cid" name="id_card" class="form-control" placeholder="เลขประจำตัวประชาชน" aria-label="เลขประจำตัวประชาชน">
                                </div> -->
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?=
                                            $form->field($model, 'day')->dropdownList(
                                                $model->getDayOptions(),
                                                [
                                                    'prompt' => 'เลือกวันที่',
                                                    'id' => 'day',
                                                    'value' => $currentday
                                                ]
                                            );
                                        ?>
                                        <?php /*
                                        <label class="control-label" for="day">วันที่</label>
                                        <select name="day" id="day" class="form-control">
                                            <?php for ($i = 1; $i <= 31; $i++) : ?>
                                                <option value="<?= $i ?>" <?= $currentday == $i ? 'selected' : '' ?>>
                                                    <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    */ ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?=
                                            $form->field($model, 'month')->dropdownList(
                                                $model->getMonthOptions(),
                                                [
                                                    'prompt' => 'เลือกเดือน',
                                                    'id' => 'month',
                                                    'value' => $currentmonth
                                                ]
                                            );
                                        ?>
                                        <?php /*
                                        <label class="control-label" for="month">เดือน</label>
                                        <select name="month" id="month" class="form-control">
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <option value="<?= $i ?>" <?= $currentmonth == $i ? 'selected' : '' ?>>
                                                    <?= Yii::$app->formatter->asDate(mktime(0, 0, 0, $i, 10), 'php:F') ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        */ ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?=
                                            $form->field($model, 'year')->dropdownList(
                                                $model->getYearOptions(),
                                                [
                                                    'prompt' => 'เลือกปี',
                                                    'id' => 'year',
                                                    'value' => $currentYear
                                                ]
                                            );
                                        ?>
                                        <?php /*
                                        <label class="control-label" for="year">ปี</label>
                                        <select name="year" id="year" class="form-control">
                                            <?php for ($i = $startYear; $i <= $currentYear; $i++) : ?>
                                                <option value="<?= $i ?>" <?= $currentYear == $i ? 'selected' : '' ?>>
                                                    <?= $i + 543; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        */ ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= $form->field($model, 'phone_number', ['addon' => ['prepend' => ['content' => '<i class="fa fa-phone"></i>']]])->textInput([
                                    'placeholder' => 'หมายเลขโทรศัพท์',
                                    'type' => 'tel',
                                    'id' => "tel",
                                    'maxlength' => true
                                ]) ?>
                                <!-- <label class="control-label" for="phone_number">หมายเลขโทรศัพท์</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                    <input type="tel" id="tel" name="phone_number" class="form-control" placeholder="หมายเลขโทรศัพท์" aria-label="หมายเลขโทรศัพท์">
                                </div> -->
                            </div>

                            <div class="form-group">
                                <?php
                                echo Html::activeHiddenInput($model, 'user_type', ['value' => $user_type])
                                ?>
                            </div>

                            <div class="form-group">
                                <div class="ml-auto">
                                    <?= Html::resetButton('ล้างข้อมูล', ['class' => 'btn btn-danger', 'id' => 'reset-form']) ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="ml-auto">
                                    <?= Html::submitButton('ลงทะเบียน', ['class' => 'btn btn-success']) ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/sweetalert2@9',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
// $this->registerJsFile(
//     '@web/js/waitMe/waitMe.min.js',
//     ['depends' => [\yii\web\JqueryAsset::className()]]
// );

$this->registerJs(
    <<<JS
// if(udhApp.isRegister()){
// 	window.location.href = '/app/apppoint/create-department'
// }


$('#reset-form').on('click',function(){
    $('#form-signup').trigger('reset');
})

function getFormData(form) {
    //serialize data function
    var formArray = form.serializeArray();

    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}

var \$form = $('#form-signup');
\$form.on('beforeSubmit', function() {
    $('#form-signup').waitMe({
        effect : 'roundBounce',
        color: '#ff518a'
    })
    var data = getFormData(\$form);
    var profile = udhApp.getProfileStorage();
    $.ajax({
        url: \$form.attr('action'),
        type: \$form.attr('method'),
        data: Object.assign(data, profile),
        dataType: 'JSON',
        success: function (data) {
            if(data.success){
                Swal.fire({
                    title: data.message,
                    text: "ไปที่นัดหมายแพทย์",
                    type: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'ตกลง',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {
                    if (result.value) {
                        window.location.href = '/app/appoint/create-department'
                    }
                })
            }else{
                Object.keys(data.validate).map(key => {
                    $(\$form).yiiActiveForm('updateAttribute', key, data.validate[key]);
                })
            }
            $('#form-signup').waitMe("hide");
        },
        error: function(error) {
            $('#form-signup').waitMe("hide");
            Swal.fire({
                title: '',
                text: error.responseJSON ? error.responseJSON.message : "Error",
                icon: 'error',
                confirmButtonText: 'ตกลง'
            })
              
        }
     });
     return false; // prevent default submit
});

function mask(str){
    var pattern = '-'
    if (str) {
        pattern = str.replace(/^(\d{1})(\d{4})(\d{5})(\d{2})(\d{1}).*/, '$1-$2-$3-$4-$5')
        //pattern = pattern.substring(0, 10) + pattern.substring(10).replace(/[0-9]/g, 'X')
    }
    return pattern
}
var m = ['1', '3', '5', '7', '8', '10', '12'];

$('#day').on('change',function(e) {
        if(parseInt($(this).val()) > 29 && $('#month').val() === '2') {
            $('#month').val('1')
        }
        if(parseInt($(this).val()) > 30 && !m.includes($('#month').val())) {
            $('#month').val('1')
        }
});

$('#month').on('change',function(e) {
    if($(this).val() === '2' && parseInt($('#day').val()) > 29) {
        // $("#day option[value='30'], #day option[value='31']").attr("disabled", "disabled");
        $('#day').val('1')
        alert('กรุณาเลือกวันที่ใหม่')
    }
    
    if(!m.includes($(this).val()) && $(this).val() !== '2' && parseInt($('#day').val()) > 30) {
        $('#day').val('1')
        alert('กรุณาเลือกวันที่ใหม่')
    }
});

JS
);
?>