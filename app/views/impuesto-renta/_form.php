<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhImpuestoRenta */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-impuesto-renta-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
      <div class= 'col-md-3'>
       <?= $form->field($model, 'fraccion_basica')->textInput(['maxlength' => true, 'placeholder'=> '0.00']) ?>
      </div>
      <div class= 'col-md-3'>
        <?= $form->field($model, 'fraccion_excedente')->textInput(['maxlength' => true, 'placeholder'=> '0.00']) ?>
      </div>
      <div class= 'col-md-3'>
        <?= $form->field($model, 'impuesto_fraccion_basica')->textInput(['maxlength' => true, 'placeholder'=> '0.00']) ?>
      </div>
      <div class= 'col-md-3'>
        <?= $form->field($model, 'impuesto_fraccion_excedente')->textInput(['maxlength' => true, 'placeholder'=> '0.00']) ?>
      </div>
    </div>  

    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
