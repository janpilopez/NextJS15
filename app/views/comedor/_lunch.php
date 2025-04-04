<?php

use yii\helpers\Html;
use yii\web\View;
$url = Yii::$app->urlManager->createUrl(['comedor']);

$horarios =  json_encode($horarios);

$inlineScript = "var url='$url', horarios = {$horarios};";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\ComedorAsset;
ComedorAsset::register($this);
$this->title = 'Lunch Personal';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhComedor */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
.clockdate-wrapper {
    background-color: #333;
    padding:25px;
    max-width:300px;
    width:100%;
    text-align:center;
    border-radius:5px;
    margin:0 auto;
    margin-top:15%;
}
#clock{
    background-color:#333;
    font-family: sans-serif;
    font-size:40px;
    text-shadow:0px 0px 1px #fff;
    color:#fff;
}
#clock span {
    color:#888;
    text-shadow:0px 0px 1px #333;
    font-size:30px;
    position:relative;
    top:-27px;
    left:-10px;
}
#date {
    letter-spacing:10px;
    font-size:14px;
    font-family:arial,sans-serif;
    color:#fff;
}
.titulo {

 font-size:250px;
 font-weight: bold;
}
</style>

<?php 

    $img =  file_get_contents('img/sin_foto.jpg');
    $data =  base64_encode($img);
?>
<div class="sys-rrhh-comedor-form">
   <div class = "row">
      <div class = "col-md-3">
           <div id="clockdate">
              <div class="clockdate-wrapper">
                <div id="clock"></div>
              </div>
           </div>
      </div>
      <div class = "col-md-6">
         <h1 class = "text-center" id = 'lunch'>Total: COMEDOR</h1>
         <h1 class = "text-center titulo"> <span id= 'contador' class = "titulo"><?= $totalLunchs?></span></h1>
         <br>
        <input type="password" class = "form-control" id= "busca">
      </div>
   </div>
   <br>
   <div class = "row">
      <div class = "col-md-12">
        <p id = "mjs" style= "text-align: center; color: red; font-size: 40px;"></p>
      </div>
   </div>
</div>
