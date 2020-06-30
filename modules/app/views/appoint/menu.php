<?php

use yii\helpers\Url;

$this->registerCssFile("@web/css/mobile-menu-bs.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);

$session = Yii::$app->session;
$profile = $session->get('user');
?>
<style>
    .mobile-menu-bs ul li .icon i {
        font-size: 20px;
    }
</style>

<div class="hidden-lg hidden-md">
    <div data-toggle="tab" data-target="#tab-settings" class="content-menu-logo center-block">
        <div class="card-icon-border-large border-pink"><img src="/images/udh_logo.png" alt="logo" class="img-responsive center-block logo-menu"></div>
    </div>
    <div id="mobile-menu-bs" class="mobile-menu-bs bootstrap">
        <ul class="nav nav-tabs">

            <li class="menu-item border-bottom-pink" style="width: 20%;">
                <a href="<?= Url::to(['/app/appoint/appointments-history']) ?>" data-ajax="true" disabled="disabled" class="page-scroll page-scroll-pink appointments-history">
                    <div class="icon">
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                    <div class="h1 no-margin">
                        <span class="menu-label">
                            นัดหมายแพทย์
                        </span>
                        <p data-v-0dbf6a13=""></p>
                    </div>
                </a>
            </li>

            <li class="menu-item border-bottom-warning" style="width: 20%;">
                <a href="<?= Url::to(['/app/appoint/user-history']) ?>" class="page-scroll page-scroll-warning">
                    <div class="icon"><i class="fa fa-address-book-o fa-2x"></i></div>
                    <div class="h1 no-margin">
                        <span class="menu-label">
                            ประวัตินัดหมาย
                        </span>
                        <p data-v-0dbf6a13=""></p>
                    </div>
                </a>
            </li>

            <li class="border-bottom-danger" style="width: 20%;"></li>

            <li class="menu-item border-bottom-danger" style="width: 20%;">
                 <a href="<?= Url::to(['/app/appoint/queue-status', 'hn' => $profile['hn']]) ?>" class="page-scroll page-scroll-danger">
                    <div class="icon"><i class="fa fa-address-card-o fa-2x"></i></div>
                    <div class="h1 no-margin"><span class="menu-label">สถานะคิว</span>
                        <p data-v-0dbf6a13=""></p>
                    </div>
                </>
            </li>

            <li class="menu-item border-bottom-info" style="width: 20%;">
                <a href="#" onclick="udhApp.closeWindow()" class="page-scroll page-scroll-info sidebar-toggler">
                    <div class="icon"><i class="fa fa-times fa-2x"></i></div>
                    <div class="h1 no-margin"><span class="menu-label">ปิด</span>
                        <p data-v-0dbf6a13=""></p>
                    </div>
                </a>
            </li>

        </ul>
    </div>
</div>