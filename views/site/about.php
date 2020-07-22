<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <code><?= __FILE__ ?></code>
</div>

<?php 
$this->registerJs(<<<JS
function LinkRichMenu() {//เปลี่ยนเมนู
    var userId = 'Udeadbeefdeadbeefdeadbeefdeadbeef';
    //var richMenuId = "richmenu-349a649ee1b2e2f659ae2da8e24df4ef";
    var richMenuId = "richmenu-349a649ee1b2e2f659ae2da8e24df4ef";
    $.ajax({
      method: "POST",
      url: `https://api.line.me/v2/bot/user/\${userId}/richmenu/\${richMenuId}`,
      dataType: "JSON",
      beforeSend: function(xhr) {
       //ไลน์พี่บอล
       // xhr.setRequestHeader("Authorization", "Bearer FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU=" );
       xhr.setRequestHeader("Authorization", "Bearer uLF9THsOlQfvth3Y7bvLym0ZwPoEliKF7MszmJq4aymKwWJfYpknJ/zmWwOZsNzgrDXU0+Y7KGMrxCPi79NX1/g3iSeY5Mva1olEL4cwoJtDdznKV+7MjYP89tW6BO8/A//QjXTcoB6BdDt6ooFzB1GUYhWQfeY8sLGRXgo3xvw=" );
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
      },
    });
  }
  LinkRichMenu()
JS
);

?>