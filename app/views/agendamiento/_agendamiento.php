<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhJornadasCab;
use app\models\SysRrhhCuadrillas;
use app\assets\AgendamientoAsset;
use app\models\SysRrhhHorarioCab;
AgendamientoAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['agendamiento']);
$urlconsultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "var url='$url', urlconsultas = '$urlconsultas';";
$this->registerJs($inlineScript, View::POS_HEAD);


$userdeparta  = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->one();

$cuadrilla    = SysRrhhCuadrillas::find()->select('id_sys_rrhh_cuadrilla')->where(['id_sys_adm_area'=> $userdeparta->area == null ? '': $userdeparta->area])->asArray()->column();

?>
<div class="site-contact">
    
    <div class= 'row'>
       <div class= 'col-md-3'>
              <?php
                echo '<label>Fecha Inicio</label>';
                echo DatePicker::widget([
                	'name' => 'fechainicio', 
                	'value' => date('Y-m-d'),
                    'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
            <div class= 'col-md-3'>
              <?php echo '<label>Fecha Final</label>';
                echo DatePicker::widget([
                	'name' => 'fechafin', 
                	'value' => date('Y-m-d'),
                	'options' => ['id'=>'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
            <div class= 'col-md-3'>
             <?php echo '<label>Grupos</label>';
                   echo   Html::DropDownList('cuadrillas', 'cuadrillas', 
                       ArrayHelper::map(SysRrhhCuadrillas::find()->andFilterWhere(['id_sys_rrhh_cuadrilla'=> $cuadrilla])->andWhere(['estado'=>'A'])->all(), 'id_sys_rrhh_cuadrilla', 'cuadrilla'), ['class'=>'form-control input-sm', 'id'=>'cuadrillas', 'prompt' => 'Seleccionar..'])
            ?>
           </div> 
            <div class= 'col-md-2'>
             <br>
             <button class = 'btn btn-info input-sm pull-right' id= 'generar'>Generar Agenda</button>
           </div>
      
        
    </div>
    <br>
    <p style="font-weight: bold;">Agendamiento por dia<p>
    <div class = 'row'>
          <div class= 'col-md-3'>
              <?php  echo '<label>Horarios</label>';
                     echo   Html::DropDownList('sysjornadas', 'sysjornadas', 
                     ArrayHelper::map(SysRrhhHorarioCab::find()->select('id_sys_rrhh_horario_cab, horario')->where(['estado'=> 'A'])->asArray()->all(), 'id_sys_rrhh_horario_cab', 'horario'), ['class'=>'form-control input-sm', 'id'=>'sysjornadas', 'prompt' => 'Seleccionar..'])
               ?>
           </div>
            <div class= 'col-md-3'>
              <?php  echo '<label>Dias</label>';
                     echo   Html::DropDownList('diaslaborales', 'diaslaborales', [], ['class'=>'form-control input-sm', 'id'=>'diaslaborales', 'prompt' => 'Seleccionar..'])
               ?>
           </div>
           <div class= 'col-md-2'>
             <br>
             <button class = 'btn btn-warning input-sm pull-left' id= 'agregajornada'>Agregar Jornada</button>
           </div>
     </div>
    <br>
    <div class= 'row'>
        <div class= 'col-md-12 '> 
           <table class= 'table' id= 'tabla' style = 'font-size: 10px;'> 
               <thead id = 'cabecera'>
               </thead>
               <tbody id = 'cuerpo'>
               </tbody>
           </table>
        </div>
   </div>
    <div class="form-group text-center">
         <button class= 'btn btn-success' style= 'display: none' id= 'btnguardar'>Guardar Agendamiento</button>
    </div>
     <div id="loading"></div>
</div>
   
