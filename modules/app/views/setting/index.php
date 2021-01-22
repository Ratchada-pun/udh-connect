<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TblPatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'รายชื่อผู้ลงทะเบียน';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile("@web/css/pagination.css", [
    'depends' => [\yii\bootstrap4\BootstrapAsset::className()],
]);
?>

<div class="tbl-patient-index">
    <!--main contents start-->
    <main class="content_wrapper">

        <!--page title end-->
        <div class="container-fluid">
            <!-- state start-->
            <div class="row">
                <div class=" col-12">
                    <div class="card card-shadow mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                รายชื่อผู้ลงทะเบียน
                            </div>
                        </div>
                        <div class="card-body">

                        <?php Pjax::begin(["id" => "crud-datatable-pjax"]); ?>
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'columns' => [
                                    [
                                        'class' => 'yii\grid\SerialColumn'
                                    ],
                                    [
                                        'attribute' => 'fullname',
                                        'label' => 'ชื่อ-นามสกุล',
                                        'format' => 'raw',
                                        'headerOptions' => [
                                            'style' => 'text-align:center;'
                                        ],
                                        'value' => function ($model) {
                                            return $model['first_name'] .' ' .$model['last_name'];
                                        },
                                    ],
                                    [
                                        'attribute' => 'hn',
                                        'label' => 'รหัสบัตร',
                                        'format' => 'raw',
                                        'headerOptions' => [
                                            'style' => 'text-align:center;'
                                        ],
                                        'value' => function ($model) {
                                            return $model['hn'] ? $model['hn'] : '-';
                                        },
                                    ],
                                    [
                                        'attribute' => 'brith_day',
                                        'label' => 'วัน/เดือน/ปี เกิด',
                                        'format' => 'raw',
                                        'headerOptions' => [
                                            'style' => 'text-align:center;'
                                        ],
                                        'value' => function($model){
                                            return $model['brith_day'] ? Yii::$app->formatter->asDate($model['brith_day'], 'php:d/m/Y') : '';
                                        },
                                    ],
                                    [
                                        'attribute' => 'phone_number',
                                        'label' => 'หมายเลขโทรศัพท์',
                                        'format' => 'raw',
                                        'headerOptions' => [
                                            'style' => 'text-align:center;'
                                        ],
                                        'value' => function ($model) {
                                            return $model['phone_number'] ? $model['phone_number'] : '-';
                                        },
                                    ],
                                    [
                                        'attribute' => 'user_type',
                                        'label' => 'ประเภท',
                                        'format' => 'raw',
                                        'headerOptions' => [
                                            'style' => 'text-align:center;'
                                        ],
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'action',
                                         'template' => '{delete}',
                                         'contentOptions' => [
                                            'noWrap' => true,
                                            'style' => 'text-align:center'
                                        ],
                                        'urlCreator' => function ($action, $model, $key, $index) {
                                            if ($action == 'delete') {
                                                return Url::to(['/app/setting/delete', 'id' => $key]);
                                            }
                                        },
                                        'buttons' => [
                                            'delete' => function ($url, $model, $key) {
                                                return Html::a('<span class="fa fa-trash-o"></span>', $url, [
                                                    'class' => 'btn btn-sm btn-danger btn-delete',
                                                    'title' => 'ลบ',
                                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                    'data-method' => 'post',
                                                ]);
                                            },
                
                                        ],
                                    ]
                                
                                ],
                            ]) ?>
                         <?php Pjax::begin(["id" => "crud-datatable-pjax"]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- state end-->
        </div>
    </main>
    <!--main contents end-->
</div>

<?php 
$this->registerJS(<<<JS

function init() {
    yii.confirm = function(message, okCallback, cancelCallback) {
        var url = $(this).attr('href')
        Swal.fire({
            title: 'ยืนยันลบ รายการ?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        method: 'POST',
                        url: url,
                        dataType: 'json',
                        success: function(res) {
                            resolve(res)
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorThrown
                            })
                        },
                    })
                })
            },
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'ดำเนินการสำเร็จ',
                    text: '',
                }).then(result => {
                    window.location.reload()
                })
            }
        })
    } 
}
$('#crud-datatable-pjax').on('pjax:success', function() {
    init()
})
init();
JS
);
?>