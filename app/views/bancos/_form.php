<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhBancos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-asistencia-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
     <?= $form->field($model, 'banco')->textInput(['maxlength' => true]) ?>
     
     <?= $form->field($model, 'cuenta')->textInput(['maxlength' => true]) ?>
     
     <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'])?>

    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
   </div>
  </div>
</div>
