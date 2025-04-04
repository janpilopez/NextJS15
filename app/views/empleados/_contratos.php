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
use kartik\select2\Select2;
use app\models\SysRrhhEmpleadosHaberes;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhContratos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhCausaSalida;


$cont  = 0;

if($update != 0):
    $cont = SysRrhhEmpleadosContratos::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula, 'id_sys_empresa'=> $model->id_sys_empresa])->count();
endif;
?>

<div class='row'> 
  <div class= 'col-md-12'>
  <?= Html::hiddenInput('contratos', $cont, ['id'=> 'datacontratos']); ?>
   <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
             
             'models' => $contratos,
             'id'=> 'contratos',
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
                     'name' => 'id_sys_rrhh_empleados_contrato_cod',
                     'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 ],
                 
                 [
                     'name' => 'fecha_ingreso',
                     'title' => $contratos[0]->getAttributeLabel('fecha_ingreso'),
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
                     'title' => $contratos[0]->getAttributeLabel('fecha_salida'),
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
                     'name' => 'cargo',
                     'title' => $contratos[0]->getAttributeLabel('cargo'),
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
                         'style'=>'width:40%',
                     ],
                 ],

                 [
                     'name' => 'id_sys_rrhh_causa_salida',
                     'title' => $contratos[0]->getAttributeLabel('id_sys_rrhh_causa_salida'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items' => ArrayHelper::map(SysRrhhCausaSalida::find()->where(['id_sys_empresa'=> '001'])->all(), 'id_sys_rrhh_causa_salida', 'descripcion'),
                     'options'=>['prompt'=>'', 'class'=> 'input-sm', 'disabled' => $disabled],
                     'headerOptions'=>[
                         'style'=>'width:16%',
                     ],
                 ],
                 [
                     'name' => 'activo',
                     'title' => $contratos[0]->getAttributeLabel('activo'),
                     'type' => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items'=>['1'=> 'Activo', '0'=> 'Inactivo'],
                     'options' => ['class'=> 'form-control input-sm', 'disabled' => $disabled],
                         //'placeholder' => 'Seleccione..',  'class'=> 'input-sm',
                         
                     
                 ],
                    
             ]
         ])?>
      </div>
  </div>
</div>