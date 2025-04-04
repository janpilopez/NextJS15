<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use app\models\SysAdmAreas;
use app\models\SysRrhhHorarioCab;
use yii\widgets\ActiveForm;
$this->render('../_alertFLOTADOR');
$this->title = 'Registro de Asistencia Manual';
$this->params['breadcrumbs'][] = 'Registro de Asistencia Manual';
?>
<div class="site-contact">

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
           <div class = 'col-md-3'>
             	<?php echo '<label>Jornada</label>';
             	      echo   Html::DropDownList('jornada', 'jornada', 
                       ArrayHelper::map(SysRrhhHorarioCab::find()->all(), 'id_sys_rrhh_horario_cab', 'horario'), ['class'=>'form-control input-sm', 'id'=>'jornada', 'prompt' => 'Seleccione Jornada',  'options'=>[ $jornada => ['selected' => true]]])
              ?>
           </div>
            <div class= 'col-md-2'>
             <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos',  'options'=>[ $area => ['selected' => true]]])
              ?>
           </div> 
           <div class = 'col-md-3'>
              <?php echo '<label>Departamento</label>';
              echo DepDrop::widget([
                  'name'=> 'departamento',
                  'data'=> [$departamento => 'departamento'],
                  'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                  'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                  'pluginOptions'=>[
                      'depends'=>['area'],
                      'initialize' => true,
                      'initDepends' => ['area'],
                      'placeholder'=>'Todos',
                      'url'=>Url::to(['/consultas/listadepartamento']),
                      
                  ]
              ]);?>
           </div>
    </div>
    <br>
    <div class= 'row'>
         <div class="form-group text-center">
              <?= Html::submitButton('Registrar Marcación', ['class' => 'btn btn-success input-sm', 'id'=> 'consultar']) ?>
        </div>
    </div>
      <?php $form = ActiveForm::end(); ?>
</div>
   
