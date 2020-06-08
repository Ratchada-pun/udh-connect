<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = 'UDH-Connect';
// $this->registerJsFile(
//     '@web/js/liff-starter.js',
//     ['depends' => [\yii\web\JqueryAsset::className()]]
// );
?>

<style>
	.quick-links-grid {
    display: inline-block;
    width: 100%;
    text-align: center;
}
	.quick-links-grid .ql-grid-item {
    display: inherit;
    padding: 20px 5px;
    text-align: center;
    vertical-align: middle;
    text-decoration: none;
    color: #45567c;
    padding: 25px;
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
}
</style>
<div class="site-index">

	<!-- <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div> -->

	<div class="body-content">
		<main class="content_wrapper">
			<div class="container-fluid">
				<div class="sufee-login d-flex align-content-center flex-wrap">
					<div class="container">
						<div class="login-content">

							<div class="card-header text-white bg-danger border-0  text-center">
								<div class="media p-6">
									<div class="media-body">
										<p class="btn-flat m-b-30 m-t-30">
											<strong class="">
												<p style="font-size: 20pt;">
													สถานะผู้รับบริการโรงพยาบาลอุดรธานี
												</p>
											</strong>
										</p>
									</div>
								</div>
							</div>

							<div class="login-form">
								<form>
									<div class="quick-links-grid">

										<div class="ql-grid-item">
											<a href="<?= Url::to(['/app/register/policy', 'user' => 'new']) ?>" class="btn btn-pill btn-outline-primary btn-lg btn-action" style="border:solid">
												<i class="icon-user-follow" style="font-size: 60px;"></i>
												<span class="ql-grid-title">
													<p style="font-size: 18pt;">
														ผู้ป่วยใหม่
													</p>
												</span>
											</a>
										</div>

										<div class="ql-grid-item">
											<a href="<?= Url::to(['/app/register/policy', 'user' => 'old']) ?>" class="btn btn-pill btn-outline-success btn-lg btn-action" style="border:solid">
												<i class="icon-user-following" style="font-size: 60px;"></i>
												<span class="ql-grid-title">
													<p style="font-size: 18pt;">
														ผู้ป่วยเก่า
													</p>
												</span>
											</a>
										</div>

									</div>
								</form>
							</div>

						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
