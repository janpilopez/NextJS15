<?php
use app\models\SysAdmDepartamentos;
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
use kartik\select2\Select2;
use app\models\SysRrhhEmpleadosHaberes;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhCargos;
use app\models\SysRrhhEmpleadosCargos;
use app\models\SysAdmCargos;
use app\models\SysRrhhCausaSalida;


$cont  = 0;

if($update != 0):
    $cont = SysRrhhEmpleadosCargos::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->count();
endif;
?>

<div class='row'> 
  <div class= 'col-md-12'>
  <?= Html::hiddenInput('datacargos', $cont, ['id'=> 'datacargos']); ?>
   <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
             
             'models' => $cargos,
             'id'=> 'cargos',
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
                     'name' => 'id_sys_rrhh_empleados_cargo_cod',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
                 
                 [
                     'name' => 'fecha_ingreso',
                     'title' => $cargos[0]->getAttributeLabel('fecha_ingreso'),
                     'type' => DatePicker::classname(),
                     'value' => function($data) {
                       return $data['fecha_ingreso'] == '' ? date('Y-m-d'):  $data['fecha_ingreso']  ;
                     },
                     'enableError' => true,
                     'options' => [
                         'dateFormat' => 'yyyy-MM-dd',
                         
                         'options'=> ['class'=> 'form-control input-sm', 'type' => 'date', 'disabled' => $disabled],
                         
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:7%;',
                     ],
                   
                 ],
                 [
                     'name' => 'fecha_salida',
                     'title' => $cargos[0]->getAttributeLabel('fecha_salida'),
                     'type' =>  DatePicker::classname(),
                      
                     'enableError' => true,
                     'options' => [
                         'dateFormat' => 'yyyy-MM-dd',
                         
                         'options'=> ['class'=> 'form-control input-sm', 'type' => 'date', 'disabled' => $disabled],
                         
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:7%',
                     ],
                 ],
                 
                 [
                    'name' => 'departamento',
                    'title' => $cargos[0]->getAttributeLabel('departamento'),
                    'type' => kartik\select2\Select2::className(),
                    'enableError' => true,
                   
                    'options'=>[
                        'size' => Select2::SMALL,
                        'data' => ArrayHelper::map(SysAdmDepartamentos::find()->where(['estado'=> 'A'])->orderBy(['departamento'=>'asc'])->all(), 'id_sys_adm_departamento', 'departamento'),
                        'options'=> ['placeholder' => 'Seleccione', 'disabled' => $disabled],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                   
                        
                    ],
                    'headerOptions'=>[
                        'style'=>'width:20%',
                    ],
                ],
                
                 [
                     'name' => 'cargo',
                     'title' => $cargos[0]->getAttributeLabel('cargo'),
                     'type' => kartik\select2\Select2::className(),
                     'enableError' => true,
                    
                     'options'=>[
                         'size' => Select2::SMALL,
                         'data' => ArrayHelper::map(SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->orderBy(['cargo'=>'asc'])->all(), 'id_sys_adm_cargo', 'cargo'),
                         'options'=> ['placeholder' => 'Seleccione', 'disabled' => $disabled],
                         'pluginOptions' => [
                             'allowClear' => true
                         ],
                    
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:20%',
                     ],
                 ],

                 [
                     'name' => 'activo',
                     'title' => $cargos[0]->getAttributeLabel('activo'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=>['1'=> 'Activo', '0'=> 'Inactivo'],
                     'options' => ['class'=> 'form-control input-sm', 'onchange' => 'cambiarFechaSalida.call(this,event)', 'disabled' => $disabled],
                         //'placeholder' => 'Seleccione..',  'class'=> 'input-sm',
                         
                     
                 ],
                    
             ]
         ])?>
      </div>
  </div>
</div>