<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmActividades */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-actividades-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>

    <div class= 'row'>
      <div class= 'col-md-6'>
        <?= $form->field($model, 'actividad')->textInput(['maxlength' => true]) ?>
      
      </div>
      <div class= 'col-md-2'>
         <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
      </div>
    </div>
 
    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    </div>
  </div>
</div>
