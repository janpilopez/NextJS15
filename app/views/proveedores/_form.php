<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-acceso-proveedores-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
      <div class= 'row'>
        <div class= 'col-md-2'>
          <?= $form->field($model, 'cedula')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Identificación', 'disabled' => $inputDisable]) ?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'nombreProveedor')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'Nombres', 'disabled' => $inputDisable]) ?>
        </div>
        <div class= 'col-md-2'>
          <?= $form->field($model, 'nivel_riesgo')->dropDownList([1=> 'Bajo', 2=> 'Medio', 3=> 'Alto'], ['class'=>'form-control input-sm'])?>
        </div>
     </div>
     
    <div class="form-group text-left">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
