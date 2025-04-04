<?php

use app\models\SysRrhhCuadrillas;
use app\models\SysRrhhHorarioCab;
use app\models\SysRrhhJornadasCab;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillasJornadasCab */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Ajustar Agenda';
$this->params['breadcrumbs'][] = ['label' => 'Agendamiento Laboral', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-cuadrillas-jornadas-cab-form">
      
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php $form = ActiveForm::begin(); ?>
    
       <?php echo Html::hiddenInput('codagenda', $codagenda); ?>  
       
      <div class='row'>
          <div class= 'col-md-2'>
           <?php 
             echo '<label>Grupos</label>';
             echo Html::DropDownList('cuadrillas', 'cuadrillas',
                 ArrayHelper::map(SysRrhhCuadrillas::find()->where(['estado'=> 'A'])->all(), 'id_sys_rrhh_cuadrilla', 'cuadrilla'), ['id'=>'cuadrillas', 'prompt' => 'Seleccionar..', 'class'=>'form-control input-sm'])
            ?>
          </div> 
          <div class= 'col-md-4'>
            <?php 
              echo '<label>Empleados Cuadrillas</label>';
              echo DepDrop::widget([
                  'name' => 'cedulaemp',
                  'options'=>[ 'id'=> 'cedulaemp', ['class'=> 'form-control input-sm']],
                  'pluginOptions'=>[
                      'depends'=>['cuadrillas'],
                      'placeholder'=>'Select...',
                      'url'=>Url::to(['/agendamiento/empleadoscuadrillas'])
                  ]
                               
               ]);
            ?>
          </div>
      
          <div class='col-md-3'>
            <?php 
              echo '<label>Fecha Laboral</label>';
              echo DatePicker::widget([
                	'name' => 'fechalaboral', 
                	'options' => ['placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
          </div>
         <div class='col-md-3'>
            <?php  
              echo '<label>Jornadas</label>';
              echo   Html::DropDownList('sysjornadas', 'sysjornadas', 
                  ArrayHelper::map(SysRrhhHorarioCab::find()->select('id_sys_rrhh_horario_cab, horario')->where(['estado'=> 'A'])->asArray()->all(), 'id_sys_rrhh_horario_cab', 'horario'), ['class'=>'form-control input-sm', 'id'=>'sysjornadas', 'prompt' => 'Día Libre'])
             ?>
          </div>
      </div>
    <br>
    <div class="form-group text-center">
        <?= Html::submitButton('Actualizar Fecha', ['class' => 'btn btn-success', 'data-confirm'=> 'Está usted seguro que desea ajustar la agenda?']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>