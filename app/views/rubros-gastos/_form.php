<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhRubrosGastos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-rubros-gastos-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
      <div class= 'col-md-6'>
        <?= $form->field($model, 'rubro')->textInput(['maxlength' => true, 'placeholder'=> 'Rubro']) ?>
        <?= $form->field($model, 'max_gasto')->textInput(['maxlength' => true, 'placeholder'=> '0.00']) ?>
      </div>
      <div class ='col-md-6'>
         <?= $form->field($model, 'detalle')->textarea(['maxlength' => true,'rows'=> '5']) ?>
      </div>
    </div>
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
   </div>
 </div>
</div>
