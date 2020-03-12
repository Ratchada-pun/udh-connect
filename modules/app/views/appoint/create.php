<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */

$this->title = 'Create Tbl Patient';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-patient-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
