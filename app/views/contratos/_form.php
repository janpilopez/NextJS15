<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhContratos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-contratos-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
       <div class= 'col-md-8'>
        <?= $form->field($model, 'contrato')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-2'>
        <?= $form->field($model, 'plazo')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
       </div>
    </div>
    <div class= 'row'>
       <div class= 'col-md-12'>
       <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
       
       </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
