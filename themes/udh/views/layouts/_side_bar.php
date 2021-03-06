<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .side_bg_img {
        background: #ff518a;
    }

    .dark_blue .sidebar-menu li.menu_title {
        background: #ff518a;
    }

    .sidebar-menu li a {
        font-size: 15px;
    }

    .sidebar-menu li a i {
        font-size: 25px;
    }

    .side_bar {
        width: 240px;
    }
</style>

<div class="side_bar dark_blue side_bg_img scroll_auto">
    <ul id="dc_accordion" class="sidebar-menu tree">

        <!-- <li class="menu_title">
            <a href="#"> <i class="fa fa-home"></i> <span>หน้าหลัก </span> </a>

        </li> -->
        <?php if (Yii::$app->user->isGuest) { ?>
            <li class="menu_title">
                <a href="/app/appoint/create-department">
                    <i class="fa fa-pencil-square-o"></i>
                    <span>นัดหมายล่วงหน้า </span>
                </a>

            </li>
            <li class="menu_title">
                <a href="<?= Url::to(['/app/appoint/user-history']) ?>">
                    <i class="fa fa-calendar-check-o"></i>
                    <span>
                        ประวัตินัดหมาย
                    </span>
                </a>
            </li>
        <?php } ?>
        <?php if (!Yii::$app->user->isGuest) { ?>
            <li class="menu_title">
                <a href="<?= Url::to(['/app/setting/index']) ?>">
                    <i class="fa fa-address-book-o"></i>
                    <span>
                        รายชื่อผู้ลงทะเบียน
                    </span>
                </a>
            </li>
            <li class="menu_title">
                <a href="<?= Url::to(['/app/setting/user-booking']) ?>">
                    <i class="fa fa-calendar-check-o"></i>
                    <span>
                        รายการนัดล่วงหน้า
                    </span>
                </a>
            </li>

            <li class="menu_title">
                <a href="/user/security/logout" data-method="post">
                    <i class="icon-logout"></i>
                    <span>ออกจากระบบ </span>
                </a>
            </li>
        <?php } ?>
        </li>
    </ul>

</div>