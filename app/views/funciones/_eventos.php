<?php

use app\models\SysRrhhEventos;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
$this->title = 'Registro de Eventos';
$url = Yii::$app->urlManager->createUrl(['funciones']);

$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\EventosAsset;
EventosAsset::register($this);
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
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="sys-rrhh-comedor-form">
   <div class = "row">
        <div class = 'col-md-4'>
            <?php echo '<label>Eventos/Capacitaciones</label>';
                echo   Html::DropDownList('nombreEvento', 'nombreEvento', 
                       ArrayHelper::map(SysRrhhEventos::find()->where(['estado'=>1])->all(), 'idEvento', 'nombreEvento'), ['class'=>'form-control input-sm', 'id'=>'idEvento', 'prompt' => 'Todos', 'options'=>[ $evento => ['selected' => true]]])
            ?>
       </div>
      <div class = "col-md-6 col-md-offset-3">
         <h1 class = "text-center" id = 'titulo'></h1>
         <p class = "titulo">Total: <span id= 'contador'>0</span></p>
         <div class = "text-center">
           <?=  Html::img('data:image/jpeg;base64, '.$data, ['style'=>"width:250px;height:250px;", 'id'=> 'foto']);?>
         </div>
         <br>
        <input type="password" class = "form-control" id= "busca" disabled>
      </div>
      <div class = "col-md-3">
      </div>
   </div>
</div>
