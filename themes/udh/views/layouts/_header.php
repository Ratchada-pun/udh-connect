<?php

/* @var $this \yii\web\View */
/* @var $content string */
?>
<!-- header -->
<header class="main-header">
    <div class="container_header">
        <div class="logo d-flex align-items-center">
            <a href="#" class="logo-app"> 
                <strong class="logo_icon"> 
                    <img src="<?= Yii::getAlias('@web/images/small-logo.png') ?>" 
                    alt="logo" class="img-responsive" >
                </strong> 
               
                <span class="logo-default">
                    <img src="<?= Yii::getAlias('@web/images/udh_logo.png') ?>" alt="logo" class="img-responsive" style="display: inline-block;"> 
                    <span class="logo-title" style="color:#dc147a; font-weight:500;" >
                        UDH Connect
                    </span>
                </span>
            </a>
            <div class="icon_menu full_menu">
                <a href="#!" class="menu-toggler sidebar-toggler"></a>
            </div>
        </div>

        <div class="right_detail">
            <div class="row d-flex align-items-center min-h pos-md-r">
                <div class="col-3 search_col ">
                    <div class="top_function">

                        <!-- <div class="search">
                            <a id="toggle_res_search" data-toggle="collapse" data-target="#search_form" class="res-only-view collapsed" href="javascript:void(0);" aria-expanded="false"> <i class=" icon-magnifier"></i> </a>
                            <form id="search_form" role="search" class="search-form collapse" action="#">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <button type="button" class="btn" data-target="#search_form" data-toggle="collapse" aria-label="Close">
                                        <i class="ion-android-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div> -->
                        
                    </div>
                </div>
                <div class=" col-9 d-flex justify-content-end">
                    <div class="right_bar_top d-flex align-items-center">

                        <!-- Dropdown_User -->
                        <div class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true"> 
                                <img id="user-picture" class="img-circle pro_pic" src="<?= Yii::getAlias('@web/images/about-1.jpg') ?>" alt=""> 
                            </a>
                        </div>
                        <!-- Dropdown_User_End -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>
<!-- header_End -->