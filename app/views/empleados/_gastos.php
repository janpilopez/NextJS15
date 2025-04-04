<?php

use app\models\SysAdmCanastaBasica;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use unclead\multipleinput\MultipleInputColumn;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use unclead\multipleinput\MultipleInput;
use app\models\SysRrhhEmpleadosNucleoFamiliar;
use app\models\SysRrhhEmpleadosJornada;
use app\models\SysRrhhJornadasCab;
use app\models\SysRrhhPermisos;
use kartik\select2\Select2;
use app\models\SysRrhhEmpleadosHaberes;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhContratos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhCausaSalida;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosGastos;
use app\models\SysRrhhRubrosGastos;


$cont  = 0;
$enfermedad = '';
$cargas = 0;
$canasta = SysAdmCanastaBasica::find()->where(['anio'=>date('Y')])->one();

$canasta_basica = $canasta->canasta_basica;

if($update != 0):
    $cont = SysRrhhEmpleadosGastos::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula, 'id_sys_empresa'=> $model->id_sys_empresa])->count();

    $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=>$model->id_sys_rrhh_cedula])->one();

    $enfermedad = $empleado->enfermedad;

    if($enfermedad == 'S'){
        $enfermedad = 'SI';
    }else{
        $enfermedad = 'NO';
    }

    $cargas = SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['rentas'=> 1])->count();
endif;




//Maximo de gastos de deducibles 
$max = 0;


?>

<div class='row'> 
  <div class= 'col-md-4'>
  <?= Html::hiddenInput('contratos', $cont, ['id'=> 'datagastos']); ?>
   <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
          
             'models' => $gastos,
             'id'=> 'gastos',
            /* 'attributeOptions' => [
                 'enableAjaxValidation'      => true,
                 'enableClientValidation'    => false,
                 'validateOnChange'          => false,
                 'validateOnSubmit'          => true,
                 'validateOnBlur'            => false,
             ],
             */
                
             'allowEmptyList' => true,
             'addButtonPosition' => MultipleInput::POS_HEADER,
             'addButtonOptions' => [
                 'class' => 'btn btn-xs btn-info',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                 
               
             ],
                
             'columns'=> [
                 
                 [
                     'name' => 'id_sys_rrhh_empleados_gasto',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
                       
                 [
                     'name' => 'id_sys_rrhh_rubros_gastos',
                     'title' => $gastos[0]->getAttributeLabel('id_sys_rrhh_rubros_gastos'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items' => ArrayHelper::map(SysRrhhRubrosGastos::find()->where(['id_sys_empresa'=> '001'])->all(), 'id_sys_rrhh_rubros_gastos', 'rubro'),
                     'options'=>['required'=> true, 'class'=> 'form-control input-sm', 'disabled' => $disabled],
                  
                 ],
                 [
                     'name' => 'cantidad',
                     'title' => $gastos[0]->getAttributeLabel('cantidad'),
                     'type' =>  kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         
                         'displayOptions' =>  ['class'=> 'form-control input-sm', 'onblur' => 'maxgastos.call()', 'disabled' => $disabled],
                         'maskedInputOptions' => [
                             
                             'digits' => 2,
                             
                         ],
                         
                     ],
                  
                 ],
        
             ]
         ])?>
      </div>
  </div>
      <div class="col-md-6">
          <?= Html::hiddenInput('maxdeducibles', $max, ['id'=> 'maxdeducibles']);?>
          <p  style="font-size: 16px; font-weight: bold;" class="pull-left">Total Gastos Proyectados: <span id="totalgastos">0.00</span></p>
      </div>
      <div class="col-md-6">
            <?= Html::hiddenInput('enfermedad',$enfermedad , ['id'=> 'enfermedad']); ?>
            <p  style="font-size: 16px; font-weight: bold;" class="pull-left">Trabajador o sus cargas familiares con enfermedad catastrófica : <label><?= $enfermedad ?></label></p>
      </div>
      <div class="col-md-6">
            <?= Html::hiddenInput('cargas', $cargas, ['id'=> 'numcargas']); ?>
            <p  style="font-size: 16px; font-weight: bold;" class="pull-left">Número de cargas familiares para rebaja de gastos personales : <label><?= $cargas ?></label></p>
      </div>
      <div class="col-md-6">
            <?= Html::hiddenInput('canasta_basica', $canasta_basica, ['id'=> 'canasta']); ?>
          <p  style="font-size: 16px; font-weight: bold;" class="pull-left">Rebaja de Impuesto a la Renta por Gastos Personales Proyectados  : <label id="totalgastosrenta">0.00</label></p>
      </div>
</div>