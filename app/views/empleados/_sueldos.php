<?php
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
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
use app\models\SysRrhhEmpleadosHaberes;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleadosSueldos;




$cont  = 0;

if($update != 0):
    $cont = SysRrhhEmpleadosSueldos ::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula, 'id_sys_empresa'=> $model->id_sys_empresa])->count();
endif;
?>

<div class='row'> 
  <div class= 'col-md-12'>
  <?= Html::hiddenInput('sueldos', $cont, ['id'=> 'datasueldos']); ?>
   <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
             
             'models' => $sueldos,
             'id'=> 'sueldos',
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
                     'name' => 'id_sys_rrhh_empleados_sueldo_cod',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
                 
                 [
                     'name' => 'fecha',
                     'title' => $sueldos[0]->getAttributeLabel('fecha'),
                     'type' =>  DatePicker::classname(),
                
                     'enableError' => true,
                     'options' => [
                         'dateFormat' => 'yyyy-MM-dd',
                         
                         'options'=> ['class'=> 'form-control input-sm', 'type' => 'date', 'disabled' => $disabled],
                         
                         
                     ],
                   
                 ],
                 
                
                 [
                     'name' => 'estado',
                     'title' => $sueldos[0]->getAttributeLabel('estado'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=>['A'=> 'Activo', 'I'=> 'Inactivo'],
                     'options' => [
                         
                         'placeholder' => 'Seleccione..',  'class'=> 'input-sm','disabled' => $disabled
                      
                     ],
                 ],
               
                 [
                     'name' => 'sueldo',
                     'title' => $sueldos[0]->getAttributeLabel('sueldo'),
                     'type' => kartik\number\NumberControl::className(),
                     'enableError' => true,
                    
                     'options' => [
                         'displayOptions' =>  ['class'=> 'form-control input-sm', 'disabled' => $disabled],
                         'maskedInputOptions' => [
                            // 'prefix' => '$',
                            
                         ],
                       
                         
                     ],
                 ],
                 [
                     'name' => 'por_anticipo',
                     'title' => $sueldos[0]->getAttributeLabel('por_anticipo'),
                     'type' => kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         'displayOptions' =>  ['class'=> 'form-control input-sm', 'onblur' => 'calculaquincena.call(this,event)', 'disabled' => $disabled],
                       
                         'maskedInputOptions' => [
                            // 'prefix' => '%',
                             'digits' => 0,
                         ],
                     
                         
                     ],
                 ],
                 
                 [
                     'name' => 'sueldo_anticipo',
                     'title' => $sueldos[0]->getAttributeLabel('sueldo_anticipo'),
                     'type' =>  kartik\number\NumberControl::className(),
                     'enableError' => true,
                     'options' => [
                         
                         'displayOptions' =>  ['class'=> 'form-control input-sm', 'reandoly'=> true, 'disabled' => $disabled],
                             'maskedInputOptions' => [
                                // 'prefix' => '$',
                                 
                             ],
                         
                     ],
                 ],
              
       
                    
             ]
         ])?>
      </div>
  </div>
</div>