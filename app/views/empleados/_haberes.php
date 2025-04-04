<?php
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


$cont  = 0;

$meses =  [ 0 => 's/n', 1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Juio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];


if($update != 0):

    $cont =  SysRrhhEmpleadosHaberes::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula, 'id_sys_empresa'=> $model->id_sys_empresa])->count();

endif;
?>
<div class='row'> 
  <div class= 'col-md-12'>
  <?= Html::hiddenInput('haberes', $cont, ['id'=> 'datahaber']); ?>
   <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
             
             'models' => $haberes,
             'id'=> 'haberes',
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
                 'id'    => 'addjornada'
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                 'id'=> 'deljornada'
             ],
                
             'columns'=> [
                 
                 [
                     'name' => 'id_sys_rrhh_empleados_haber',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
               
                 [
                     'name' => 'id_sys_rrhh_concepto',
                     'title' => $haberes[0]->getAttributeLabel('id_sys_rrhh_concepto'),
                     'type'  => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items' => ArrayHelper::map(SysRrhhConceptos::find()->where(['estado'=>'A'])->all(), 'id_sys_rrhh_concepto', 'concepto'),
                     'options'=>['required'=> true, 'class'=> 'input-sm', 'disabled' => $disabled]
                    
                     
                 ],
          
                 [
                     'name' => 'decimo',
                     'title' => $haberes[0]->getAttributeLabel('decimo'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=>['N'=> 'No', 'S'=> 'Si'],
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'class'=> 'input-sm', 'disabled' => $disabled
                      
                     ],
                     'headerOptions'=>[
                         'style'=>'width:8%',
                     ]
                 ],
                 [
                     'name' => 'anio_ini',
                     'title' => $haberes[0]->getAttributeLabel('anio_ini'),
                     'type' => kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         'displayOptions' =>  ['placeholder'=> 'Mes Inicio','class'=> 'form-control input-sm', 'disabled' => $disabled],
                        'maskedInputOptions' => [
                        'groupSeparator' => '',
                        'digits' => 0,
                        'rightAlign' => false
                         ]
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:7%;',
                     ],
                 ],
                 [
                     'name' => 'mes_ini',
                     'title' => $haberes[0]->getAttributeLabel('mes_ini'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=> $meses,
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm', 'disabled' => $disabled
                         
                     ],
                 ],
                 [
                     'name' => 'anio_fin',
                     'title' => $haberes[0]->getAttributeLabel('anio_fin'),
                     'type' => kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         'displayOptions' =>  ['placeholder'=> 'Mes Inicio','class'=> 'form-control input-sm', 'disabled' => $disabled],
                      ],
                     'headerOptions'=>[
                         'style'=>'width:7%;',
                     ],
                  
                 ],
                 [
                     'name' => 'mes_fin',
                     'title' => $haberes[0]->getAttributeLabel('mes_ini'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=> $meses,
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm', 'disabled' => $disabled
                         
                     ],
                 ],
                 [
                     'name' => 'pago',
                     'title' => $haberes[0]->getAttributeLabel('pago'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=>['2'=> 'Mensual', '1'=> 'Quincenal'],
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm', 'disabled' => $disabled
                         
                     ],
                 ],
                 [
                     'name' => 'cantidad',
                     'title' => $haberes[0]->getAttributeLabel('cantidad'),
                     'type' =>  kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         'displayOptions' =>  ['placeholder'=> 'Mes Inicio','class'=> 'form-control input-sm', 'disabled' => $disabled],
                         'maskedInputOptions' => [
                             'prefix' => '$',
                             
                         ],
                         
                     ],
                 ],
             
             ]
         ])?>
      </div>
  </div>
</div>