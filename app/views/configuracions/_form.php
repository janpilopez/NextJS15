<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysConfiguracion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-configuracion-form">
  <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
      <div class = 'col-md-2'>
        <label>Días Vencimiento</label>
      	<?= Html::textInput('Vencimiento', '30', ['class'=> 'form-control'])?>
      </div>
      <div class = 'col-md-2'>
        <label>Longitud</label>
      	<?= Html::textInput('Vencimiento', '8', ['class'=> 'form-control'])?>
      </div>
      <div class = 'col-md-2'>
        <label>Mayusculas</label>
      	<?= Html::textInput('Vencimiento', '1', ['class'=> 'form-control'])?>
      </div>
       <div class = 'col-md-2'>
        <label>Minuscula</label>
      	<?= Html::textInput('Vencimiento', '1', ['class'=> 'form-control'])?>
      </div>
       <div class = 'col-md-2'>
        <label>Alfanúmerico</label>
      	<?= Html::textInput('Vencimiento', '1', ['class'=> 'form-control'])?>
      </div>
    </div>
    <div class = 'row'>
       <div class = 'col-md-2'>
        <label>Númerico</label>
      	<?= Html::textInput('Vencimiento', '1', ['class'=> 'form-control'])?>
      </div>
      <div class = 'col-md-2'>
        <label>Intentos</label>
      	<?= Html::textInput('Vencimiento', '3', ['class'=> 'form-control'])?>
      </div>
    </div>
    
    
    <?php /*?>
    <div class= 'row'>
       <div class= 'col-md-3'>
        <?= $form->field($model, 'id_sys_conf_cod')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-3'>
         <?= $form->field($model, 'parametro')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-3'>
         <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
       </div>
    </div>
    <div class='row'>
      <div class= 'col-md-12'>
        <?= $form->field($model, 'detalle')->textInput(['maxlength' => true]) ?>
      </div>
    </div>
    */ ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
   </div>  
 </div>
</div>
