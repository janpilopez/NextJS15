<?php

use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHextrasCab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-hextras-cab-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>

    <div class= 'row'>
       <div class= 'col-md-1'>
         <?= $form->field($model, 'id_sys_rrhh_hextras')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-8'>
         <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
       </div>
       <div class = 'col-md-2'>
          <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
       </div>
    
    </div>
    <div class ='row'>
      <div class= 'col-md-12'>
          <?php 
           
            echo  TabularInput::widget([
             
             'models' => $modeldet,
             'id'=> 'modeldet',
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
                 
                // $iddetalle,
                [
                 'name' => 'secuencia',
                 'type' => TabularColumn::TYPE_HIDDEN_INPUT
                 
                 ],
                 [
                     'name' => 'dia',
                     'title' => $modeldet[0]->getAttributeLabel('dia'),
                     'type'  => 'dropDownList',
                     'items' => [
                         '0'=>'Lunes a Viernes',
                         '1'=> 'Lunes',
                         '2'=> 'Martes',
                         '3'=> 'Miercóles',
                         '4'=> 'Jueves',
                         '5'=> 'Vienes',
                         '6'=> 'Sábado',
                         '7'=> 'Domingo',
                         
                     ],
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'required'=> true, 'class'=> 'input-sm', 
                     ],
                     'headerOptions'=>[
                         'style'=>'width:20%',
                     ],
                 ],
                 [
                     'name' => 'jornada',
                     'title' => $modeldet[0]->getAttributeLabel('jornada'),
                     'type'  => 'dropDownList',
                     'items' => [
                         'C'=>'Completa',
                         'P'=> 'Parte de la Jornada',
                         'T'=> 'Todas las Horas',
                 
                     ],
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'required'=> true, 'class'=> 'input-sm',
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:20%',
                     ],
                 ],
                 
                 
                 
                 
                 [
                     'name' => 'hora_inicio',
                     'title' => $modeldet[0]->getAttributeLabel('hora_inicio'),
                     'type' => kartik\time\TimePicker::classname(),
                     'enableError' => true,
                     
                     'options' => [
                         'pluginOptions' => ['showMeridian' => false,
                             'showSeconds' => false,
                             
                             'minuteStep' => 60,
                             'secondStep' => 1,],
                         'options' => [
    
                            // 'readonly' => true,
                         ],
                     ]
                     
                 ],
                 [
                     'name' => 'hora_fin',
                     'title' => $modeldet[0]->getAttributeLabel('hora_fin'),
                     'type' => kartik\time\TimePicker::classname(),
                     'enableError' => true,
                     
                     'options' => [
                         'pluginOptions' => ['showMeridian' => false,
                             'showSeconds' => false,
                             
                             'minuteStep' => 60,
                             'secondStep' => 1,],
                         'options' => [
                             
                             // 'readonly' => true,
                         ],
                     ]
                     
                 ],
                 
             
             ]
         ]) ?>
      </div>   
    </div>
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

   </div>
 </div>

</div>

