<?php

use kartik\datetime\DateTimePicker;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use phpDocumentor\Reflection\Type;
use app\models\SysRrhhEmpleados;
use app\models\SysAdmCcostos;


/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhBancos */
/* @var $form yii\widgets\ActiveForm */
/*
<div class="modal remote fade" id="modalvote">
<div class="modal-dialog">
<div class="modal-content loader-md">
*/
$empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
?>
<div class="clientes-form">
<h3><?php echo  $empleado->nombres?> </h3>
<?php $form = ActiveForm::begin([ 'id'=>'formasistencia', 'enableAjaxValidation'=>false]); ?>
    
      <?php 
           
            echo  TabularInput::widget([
             
             'models' => $model,
             'id'=> 'model',
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
                 'class' => 'hidden',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                
             ],
                
             'columns'=> [
             
                 [
                     'name' => 'fecha_marcacion',
                     'title' => $model[0]->getAttributeLabel('fecha_marcacion'),
                     'type'  => kartik\datetime\DateTimePicker::className(),
             
                     'enableError' => true,
                     'options' => [
                         'type' => DateTimePicker::TYPE_INPUT,
                         'class'=> 'input-sm','readonly' => true,
                         'pluginOptions' => [
                             //'autoclose'=>true,
                             'format' => 'yyyy/mm/dd hh:ii:ss'
                         ],
                         'options'=>['style'=>'font-size:13px;'],
                     ],
                     
                     'headerOptions'=>[
                         'style'=>'width:20%;font-size:12px;',
                     ],
                 ],
                 [
                     'name' => 'fecha_sistema',
                     'title' => $model[0]->getAttributeLabel('fecha_sistema'),
                     'type'  => kartik\datetime\DateTimePicker::className(),
                     //'value' => $model[0],
                     'enableError' => true,
                     'options' => [
                         'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                         'class'=> 'input-sm',
                         'pluginOptions' => [
                             'autoclose'=>true,
                             'format' => 'yyyy/mm/dd hh:ii:ss'
                         ],
                         'options'=>['style'=>'font-size:13px;'],
                     ],
                     'headerOptions'=>[
                         'style'=>'width:28%;font-size:12px;',
                     ],
                 ],
                 /*
                 [
                     'name' => 'fecha_sistema',
                     'title' => $model[0]->getAttributeLabel('fecha_sistema'),
                     'type'  => kartik\date\DatePicker::classname(),
                     'columnOptions'=>['width'=>'10px'],
                     'value' => date('Y-m-d'),
                     'enableError' => true,
                     'options' => [
                      
                         'class'=> 'input-sm',
                         'pluginOptions' => [
                             'autoclose'=>true,
                             'format' => 'yyyy-mm-dd'
                         ],
                         'options'=>['style'=>'font-size:11px;'],
                         'pluginEvents' =>[
                             "changeDate" => "function(e) {   var keyCode = e.keyCode || e.which; if (keyCode === 13) { e.preventDefault();}}",
                         ],
                     ],
                    
                     'headerOptions'=>[
                         'style'=>'width:20%;font-size:12px;',
                     ],
                 ],
                 [
                     'name' => 'horamarcacion',
                     'title' => $model[0]->getAttributeLabel('horamarcacion'),
                     'type'  => kartik\time\TimePicker::classname(),
                     'enableError' => true,
                     'options' => [
                         'options'=>['style'=>'font-size:12px;'],
                         'class'=> 'input-sm',
                         'pluginOptions' => [
                             'showSeconds' => true,
                             'showMeridian' => false,
                             'minuteStep' => 1,
                             'secondStep' => 5,
                            
                         ],
                         'pluginEvents' => [
                             "update" => "function(e) {   var keyCode = e.keyCode || e.which; if (keyCode === 13) { e.preventDefault();}}",
                         ],
                      
                     ],
                    
                     
                     'headerOptions'=>[
                         'style'=>'width:15%',
                     ],
                  
                 ],
                  */
                 [
                     'name' => 'tipo',
                     'title' => $model[0]->getAttributeLabel('tipo'),
                     'type'  => 'dropDownList',
                     'items' => [
                         'E'=>'Entrada',
                         'S'=> 'Salida',
                         'SD'=> 'Salida Desayuno',
                         'SA'=> 'Salida Almuerzo',
                         'SM'=> 'Salida Merienda',   
                     ],
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..',
                         'required'=> true, 
                         'class'=> 'input-sm',
                         'options'=>['style'=>'font-size:11px;']
                     ],
                     
                     'headerOptions'=>[
                         'style'=>'width:12%',
                     ],
                 ],
                  [
                     'name' => 'estado',
                     'title' => 'Estado',
                     'type'  => 'dropDownList',
                     'items' => [
                         'A'=>'Activa',
                         'S'=>'Inactiva',
                   ],
                     'enableError' => true,
                     'options' => [
                         'options'=>['style'=>'font-size:11px;'],
                         'placeholder' => 'Seleccione..',
                         'required'=> true, 
                         'class'=> 'input-sm',
                         
                     ],
                      'headerOptions'=>[
                          'style'=>'width:12%',
                      ],
                  
                     
                 ],
                
                 [
                     'name' => 'id_sys_adm_ccostos',
                     'title' => $model[0]->getAttributeLabel('id_sys_adm_ccostos'),
                     'type'  => 'dropDownList',
                     'enableError' => true,
                     'items' => ArrayHelper::map(SysAdmCcostos::find()->all(), 'id_sys_adm_ccosto', 'centro_costo'),
                     'options'=>[
                            'options'=>['style'=>'font-size:10px;'],
                             'placeholder' => 'Seleccione..', 
                             'required'=> true,
                             'class'=> 'input-sm',
                             ],
                 ],
                 
             
             ]
         ]) 
    ?>    
   <div class="form-group text-center">
       <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-primary', 'id' =>'guardar']) ?>
  </div>  
 <?php ActiveForm::end(); ?>
</div>
