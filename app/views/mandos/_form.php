<?php

use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmMandos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-mandos-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>

    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
      <div class= 'col-md-6'>
       <?= $form->field($model, 'mando')->textInput(['maxlength' => true]) ?>
      </div>
      <div class= 'col-md-3'>
        <?= $form->field($model, 'nivel')->dropDownList(['1'=> 'Nivel 1', '2'=> 'Nivel 2', '3'=> 'Nivel 3', '4'=> 'Nivel 4', '5'=> 'Nivel 5', '6'=> 'Nivel 6', '7'=> 'Nivel 7']) ?>
      </div>
     <div class= 'col-md-3'>
     <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
     </div>
    </div>
    <div class= 'row'>
       <div class='col-md-6'>
         <?= $form->field($model, 't_cobertura')->textInput(['maxlength' => true]) ?>
       </div>
       <div class='col-md-6'>
         <?= $form->field($model, 'n_entrevistas')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
  </div>
 </div>
</div>
