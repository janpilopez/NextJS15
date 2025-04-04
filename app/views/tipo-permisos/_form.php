<?php

use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-permisos-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
       <div class= 'col-md-1'>
         <?= $form->field($model, 'id_sys_rrhh_permiso')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-6'>
         <?= $form->field($model, 'permiso')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-2'>
        <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo',  'I'=> 'Inactivo']) ?>
       </div>
        <div class= 'col-md-1'>
        <?= $form->field($model, 'descuento')->dropDownList(['S'=> 'Si', 'N'=> 'No']) ?>
       </div>
       <div class = 'col-md-1'>
            <?=  $form->field($model, 'subcidio')->widget(NumberControl::classname(), [
                   'displayOptions' =>  [
                       'placeholder' => '',
                       'class'=> 'form-control'
                   ],
                   'maskedInputOptions' => [
                       'groupSeparator' => '',
                       'digits' => 2,
                       'min' => 0,
                       'max' => 100,
                       'rightAlign' => false
                   ]
               ]);?>
       </div>
    </div>   
 

    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>
    
    
    

    <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
