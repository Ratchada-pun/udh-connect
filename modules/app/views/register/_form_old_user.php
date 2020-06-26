<?php

use app\assets\SweetAlert2Asset;
use kartik\form\ActiveForm;
use yii\helpers\Html;

SweetAlert2Asset::register($this);

Yii::$app->formatter->locale = 'th-TH';
$currentday = Yii::$app->formatter->asDate('now', 'php:d');
$currentmonth = Yii::$app->formatter->asDate('now', 'php:m');
$currentYear = Yii::$app->formatter->asDate('now', 'php:Y');
$startYear = $currentYear - 100;
$last_day_str = strtotime('last day of this month', Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s')));
$last_day = Yii::$app->formatter->asDate($last_day_str, 'php:d');

$this->title = "ลงทะเบียนผู้ป่วยเก่า";
$this->registerCssFile("@web/js/waitMe/waitMe.min.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);

?>
<style>
    .login-form label {
        color: #6c757d;
    }

    @media (max-width: 767px) {
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
                                <p style="font-size: 16pt;margin-top:5px;">
                                    นัดหมายแพทย์
                                </p>
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    ลงทะเบียนผู้ป่วยเก่า
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'form-search', 'type' => ActiveForm::TYPE_VERTICAL]); ?>

                <div class="row">
                    <div class=" col-12">
                        <div class="card-body">

                            <div class="form-group" id="filter">
                                <label class="control-label" for="filter">
                                    <b>เลข HN หรือ เลขบัตรประจำตัวประชาชน</b>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-id-card-o"></i>
                                    </span>
                                    <input type="text" id="input-filter" name="filter" class="form-control" placeholder="กรอก HN หรือ เลขบัตรประจำตัวประชาชน" aria-label="ชื่อ">
                                </div>
                            </div>


                            <div class="form-content">
                                <div class="form-group">
                                    <?= $form->field($model, 'hn', ['addon' => ['prepend' => ['content' => '<i class="icon-user"></i>']]])->textInput([
                                        'placeholder' => 'hn',
                                        'maxlength' => true,
                                        'id' => 'hn'
                                    ]) ?>
                                    <!-- <label class="control-label" for="hn">HN</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="icon-user"></i>
                                        </span>
                                        <input id="hn" type="text" name="hn" class="form-control" placeholder="HN">
                                    </div> -->
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'first_name', ['addon' => ['prepend' => ['content' => '<i class="icon-user"></i>']]])->textInput([
                                        'placeholder' => 'ชื่อ',
                                        'id' => 'first_name',
                                        'maxlength' => true
                                    ]) ?>
                                    <!-- <label class="control-label" for="first_name">ชื่อจริง</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="icon-user"></i>
                                        </span>
                                        <input id="first_name" type="text" name="first_name" class="form-control" placeholder="ชื่อ" aria-label="ชื่อ">
                                    </div> -->
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'last_name', ['addon' => ['prepend' => ['content' => '<i class="icon-user"></i>']]])->textInput([
                                        'placeholder' => 'นามสกุล',
                                        'id' => 'last_name',
                                        'maxlength' => true
                                    ]) ?>
                                    <!-- <label class="control-label" for="last_name">นามสกุล</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="icon-user"></i>
                                        </span>
                                        <input id="last_name" type="text" name="last_name" class="form-control" placeholder="นามสกุล" aria-label="นามสกุล">
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
                                                    <option value="<?= $i < 10 ? '0' . $i : $i ?>" <?= $currentday == $i ? 'selected' : '' ?>>
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
                                                    <option value="<?= $i < 10 ? '0' . $i : $i ?>" <?= $currentmonth == $i ? 'selected' : '' ?>>
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
                                </div>

                                <div class="form-group">
                                    <?php
                                    echo Html::activeHiddenInput($model, 'user_type', ['value' => $user_type])
                                    ?>
                                </div>
                                <div class="form-group">
                                    <div class="ml-auto">
                                        <button type="reset" class="btn btn-danger" id="reset-form">
                                            ล้างข้อมูล
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="ml-auto">
                                    <?= Html::submitButton('<i class="fa fa-search"></i> ค้นหา', ['class' => 'btn btn-success btn-lg btn-block text-center', 'id' => 'btn-search']) ?>
                                    <?= Html::submitButton('ลงทะเบียน', ['class' => 'btn btn-success btn-lg btn-block text-center','id'=>'btn-submit']) ?>
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
$('.form-content, #btn-submit').hide()

// $('#reset-form').on('click',function(){
//     clearForm()
// })

function clearForm() {
    $('#form-search').trigger('reset');
    $('.form-content,#btn-submit').hide()
    $('#first_name').val("")
    $('#last_name').val("")
    $('#cid').val("")
    $('#tel').val("")
    $('#year').val("")
    $('#mounh').val("")
    $('#day').val("")
    $('#btn-search,#filter').show()
}

var \$form = $('#form-search');
\$form.on('beforeSubmit', function() {
    $('#form-search').waitMe({
        effect : 'roundBounce',
        color: '#ff518a'
    })
    var data = getFormData(\$form);// \$form.serialize();
    var profile = udhApp.getProfileStorage();
    $.ajax({
        url: "/app/register/create-new-user?user=old",
        type: \$form.attr('method'),
        data: Object.assign(data, profile),
        dataType: 'JSON',
        success: function (data) {
            if(data.success){
                clearForm()
                Swal.fire({
                    title: data.message,
                    text: "ไปที่นัดหมายแพทย์",
                    type: 'success',
                    showCancelButton: false,
                    allowOutsideClick: false,
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
            $('#form-search').waitMe("hide");
        },
        error: function(error) {
            $('#form-search').waitMe("hide");
            Swal.fire({
                title: '',
                text: error.responseJSON ? error.responseJSON.message : "Error",
                icon: 'error',
                confirmButtonText: 'ตกลง'
            })
        }
    });
    // $.ajax({
    //     url: "/app/register/search-patient",
    //     type: \$form.attr('method'),
    //     data: data,
    //     dataType: 'JSON',
    //     success: function (data) {
    //         $('#form-search').waitMe("hide");
    //         if(!data) {
    //             $('.form-content').hide()
    //             Swal.fire({
    //                 title: 'Oops!',
    //                 text: "ไม่พบข้อมูล",
    //                 icon: 'warning',
    //                 confirmButtonText: 'ตกลง'
    //             })
    //         } else {
    //             $('#hn').val(data.hn || "")
    //             $('#first_name').val(data.firstName || "")
    //             $('#last_name').val(data.lastName || "")
    //             if(data.CardID){
    //                 $('#cid').val(String(data.CardID).replace(/[^0-9]/g, ""))
    //             }
    //             if(data.phone){
    //                 $('#tel').val(String(data.phone).replace(/[^0-9]/g, ""))
    //             }
    //             if(data.bday){
    //                 var year = moment(data.bday).format("YYYY")
    //                 var month = moment(data.bday).format("MM")
    //                 var day = moment(data.bday).format("DD")

    //                 $('#year').val(year).change()
    //                 $('#month').val(month).change()
    //                 $('#day').val(day).change()
    //             }
    //             $('.form-content,#btn-submit').show()
    //             $('#btn-search,#filter').hide()
    //         }
    //     },
    //     error: function(jqXHR,  textStatus,  errorThrown) {
    //         $('.form-content').hide()
    //         $('#form-search').waitMe("hide");
    //         Swal.fire({
    //             title: 'Error!',
    //             text: errorThrown,
    //             icon: 'error',
    //             confirmButtonText: 'ตกลง'
    //         })
    //     }
    // });
    return false; // prevent default submit
});

function getFormData(form) {
    //serialize data function
    var formArray = form.serializeArray();

    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}

$('#btn-search').on('click',function() {
    if(!$('#input-filter').val()) return false
    var data = \$form.serialize();
    $('#form-search').waitMe({
        effect : 'roundBounce',
        color: '#ff518a'
    })
    $.ajax({
        url: "/app/register/search-patient",
        type: \$form.attr('method'),
        data: data,
        dataType: 'JSON',
        success: function (data) {
            $('#form-search').waitMe("hide");
            if(!data) {
                $('.form-content').hide()
                Swal.fire({
                    title: 'Oops!',
                    text: "ไม่พบข้อมูล",
                    icon: 'warning',
                    confirmButtonText: 'ตกลง'
                })
            } else {
                $('#hn').val(data.hn || "")
                $('#first_name').val(data.firstName || "")
                $('#last_name').val(data.lastName || "")
                if(data.CardID){
                    $('#cid').val(String(data.CardID).replace(/[^0-9]/g, ""))
                }
                if(data.phone){
                    $('#tel').val(String(data.phone).replace(/[^0-9]/g, ""))
                }
                if(data.bday){
                    var year = moment(data.bday).format("YYYY")
                    var month = moment(data.bday).format("MM")
                    var day = moment(data.bday).format("DD")

                    $('#year').val(year).change()
                    $('#month').val(month).change()
                    $('#day').val(day).change()
                }
                $('.form-content,#btn-submit').show()
                $('#btn-search,#filter').hide()
            }
        },
        error: function(jqXHR,  textStatus,  errorThrown) {
            $('.form-content').hide()
            $('#form-search').waitMe("hide");
            Swal.fire({
                title: 'Error!',
                text: errorThrown,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            })
        }
    });
})

$('#btn-submit').on('click',function(){
    
})
JS
)
?>