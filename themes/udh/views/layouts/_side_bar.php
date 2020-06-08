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
        <li class="menu_title">
            <a href="/app/appoint/create-department"> 
                <i class="fa fa-pencil-square-o"></i>
                 <span>นัดหมายแพทย์ </span>
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
        <!-- <li class="menu_title"></li>
            <a href="#" onclick="udhApp.logout()">
                <i class="icon-logout"></i>
                <span>ออกจากระบบ </span>
            </a>
        </li> -->

        </li>
    </ul>

</div>