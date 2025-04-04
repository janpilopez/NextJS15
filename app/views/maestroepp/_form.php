<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-ssoo-epp-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
      <div class= 'row'>
        <div class= 'col-md-4'>
          <?= $form->field($model, 'nombre')->textInput(['class'=>'form-control input-sm', 'maxlength' => true, 'placeholder'=> 'nombre', ]) ?>
        </div>
        <div class= 'col-md-2'>
          <?= $form->field($model, 'estado')->dropDownList(['Nuevo'=> 'Nuevo', 'Usado'=> 'Usado'], ['class'=>'form-control input-sm', 'required'=> true])?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'vida_util')->textInput(['class'=>'form-control input-sm', 'type' => 'number','maxlength' => true, 'placeholder'=> 'DIAS', 'required'=> true]) ?>
        </div>
        <div class= 'col-md-3'>
          <?= $form->field($model, 'um')->dropDownList(['PAR'=> 'PAR', 'PIEZA'=> 'PIEZA', 'CAJA'=> 'CAJA', 'UNIDAD'=> 'UNIDAD'], ['class'=>'form-control input-sm', 'required'=> true])?>
        </div>
     </div>
     
    <div class="form-group text-left">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
