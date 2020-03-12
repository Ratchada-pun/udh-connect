<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */

$this->title = 'Update Tbl Patient: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-patient-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
