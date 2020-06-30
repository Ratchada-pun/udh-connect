<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
?>
<?php $this->beginContent('@udh/views/layouts/_base.php'); ?>
<div id="loader_wrpper">
    <div class="loader_style"></div>
</div>

<div class="wrapper">
    <?= $this->render('_header.php', []) ?>
    <div class="container_full">
        <?= $this->render('_side_bar.php', []) ?>
        <div class="content_wrapper">
            <div class="container-fluid">
                <!-- breadcrumb -->
                <?php /*
                <div class="page-heading">
                    <div class="row d-flex align-items-center">
                        <div class="col-12">
                            <div class="page-breadcrumb">
                                <!-- <h1>Dashboard</h1> -->
                            </div>
                        </div>
                        <div class="col-12  d-md-flex">
                            <div class="breadcrumb_nav">
                                <?= Breadcrumbs::widget([
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                    'tag' => 'ol',
                                    'options' => [
                                        'class' => 'breadcrumb'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                */?>
                <!-- breadcrumb_End -->
                <?=\yii2mod\alert\Alert::widget()?>
                <?= $content ?>
            </div>
        </div>
        <?= $this->render('_footer.php') ?>
    </div>
</div>

<?php $this->endContent(); ?>