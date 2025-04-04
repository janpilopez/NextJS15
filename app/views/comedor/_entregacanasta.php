<?php

use yii\helpers\Html;
use yii\web\View;
$url = Yii::$app->urlManager->createUrl(['comedor']);

$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\EntregaCanastaAsset;
EntregaCanastaAsset::register($this);
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
   font-size: 95px;
   text-align: center;
}
</style>

<?php 

$img =  file_get_contents('img/sin_foto.jpg');
$data =  base64_encode($img);

?>
<div class="sys-rrhh-comedor-form">
   <div class = "row">
      <div class = "col-md-6 col-md-offset-3">
         <h1 class = "text-center" id = 'lunch'>Entrega de Canasta Navideñas</h1>
         <p class = "titulo">Total: <span id= 'contador'><?= $ncanastas?></span></p>
         <div class = "text-center">
           <?=  Html::img('data:image/jpeg;base64, '.$data, ['style'=>"width:250px;height:250px;", 'id'=> 'foto']);?>
         </div>
         <br>
        <input type="password" class = "form-control" id= "busca">
      </div>
      <div class = "col-md-3">
      </div>
   </div>
</div>
