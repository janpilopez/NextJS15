<?php

use kartik\color\ColorInput;
use kartik\time\TimePicker;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHorarioCab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-horario-cab-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
    <div class='row'>
       <div class= 'col-md-2'>
         <?= $form->field($model, 'horario')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-2'> 
        <?= $form->field($model,'hora_inicio')->widget(TimePicker::className(),[
                 'value' => date('g:i a', strtotime($model->hora_inicio)), 
                 'pluginOptions' => ['showMeridian' => false,
                                     'showSeconds' => false,
                                     'minuteStep' => 60,
                                     'secondStep' => 1,]]
                 );
         ?>
       </div>
       <div class = 'col-md-2'>
        <?= $form->field($model,'hora_fin')->widget(TimePicker::className(),[
                 'value' => date('g:i a', strtotime($model->hora_fin)), 
                 'pluginOptions' => ['showMeridian' => false,
                                     'showSeconds' => false,
                                     'minuteStep' => 60,
                                     'secondStep' => 1,]]
                 );
         ?>
       
       </div>
       <div class= 'col-md-2'>
             <?= $form->field($model,'hora_lunch')->widget(TimePicker::className(),[
                 'value' => date('g:i a', strtotime($model->hora_lunch)), 
                 'pluginOptions' => ['showMeridian' => false,
                                     'showSeconds' => false,
                                     'minuteStep' => 60,
                                     'secondStep' => 1,]]
                 );
         ?>
       
       </div>
       <div class= 'col-md-2'>
           <?php echo $form->field($model, 'color')->widget(ColorInput::classname(), [
              'options' => ['placeholder' => 'Seleccione', 'class'=> 'form-control input-sm'],
            ]);?>
       
       </div>
       <div class ='col-md-2'>
        <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'Inactivo'])?>
       </div>
        
    </div>
    <div class= 'row'>
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
                 'name' => 'id_sys_rrhh_horario_det',
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
