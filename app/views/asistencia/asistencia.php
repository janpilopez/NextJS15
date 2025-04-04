<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use app\models\SysRrhhJornadasCab;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;
use app\assets\AgendamientoAsset;
use app\assets\CuadrillasAsset;
use app\models\SysRrhhHorarioCab;
use yii\widgets\ActiveForm;
use app\assets\AsistenciaAsset;
AsistenciaAsset::register($this);

$urlconsultas = Yii::$app->urlManager->createUrl(['asistencia']);
$consultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "urlconsultas = '$urlconsultas', consultas = '$consultas', departamento = '$departamento';";
$this->registerJs($inlineScript, View::POS_HEAD);
$this->title = 'Asistencia';
$this->params['breadcrumbs'][] = 'Asistencia';
?>

<div class="site-contact">
    
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
             <div class= 'col-md-3'>
              <?php
                echo '<label>Fecha</label>';
                echo DatePicker::widget([
                	'name' => 'fechainicio', 
                	'value' => $fechaini,
                    'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
            <div class= 'col-md-2'>
             <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos',  'options'=>[ $area => ['selected' => true]]])
              ?>
           </div> 
           <div class = 'col-md-3'>
              <?php echo '<label>Departamento</label>';
                     echo  Html::DropDownList('departamento', 'departamento',
                      ArrayHelper::map(SysAdmDepartamentos::find()->andFilterWhere(['id_sys_adm_area'=> $area])->all(), 'id_sys_adm_departamento', 'departamento'), ['class'=>'form-control input-sm', 'id'=>'departamento', 'prompt' => 'Todos',  'options'=>[ $departamento => ['selected' => true]]])
               ?>
           </div>
           <div class = 'col-md-3'>
              <?php echo '<label>Nombres</label>';
                    echo  Html::textInput('nombres', $filtro, ['class'=> 'form-control input-sm']);
               ?>
           </div>
         
    </div>
    <br>
    <div class= 'row'>
         <div class="form-group text-center">
              <?= Html::submitButton('Consultar Datos', ['class' => 'btn btn-success input-sm', 'id'=> 'consultar']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
   
   <?php if($datos):?>
   <div class= 'row'>
     <div class = 'col-md-12'>
     	 <?=  Html::a('Exportar a Excel', ['asistenciaxls','departamento'=> $departamento, 'area'=> $area, 'fechaini'=> $fechaini, 'filtro'=> $filtro], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px', 'target' => '_blank' ]);?>
     </div>
   </div>
    <div class = 'row'>
       <div class = 'col-md-12'>
           <?php echo $this->render('_tableasistencia',['datos'=> $datos, 'fechaini'=> $fechaini])?>
        </div>
     </div>
    <?php endif;?>
    <div id="loading"></div>
</div>
<?php 
   Modal::begin([
    'id' => 'modal',
    'header' => '<h4 class="modal-title">Editar Marcacion</h4>',
    'headerOptions'=>['style'=>"background-color:#EEE"],
    'size'=>'modal-lg',
    ]); ?>
<?php Modal::end(); ?>
   
