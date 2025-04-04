<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCausaSalida */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-causa-salida-form">
  <div class = 'panel panel-default'>
    <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
    
        <div class= 'row'>
          <div class= 'col-md-1'>
             <?= $form->field($model, 'id_sys_rrhh_causa_salida')->textInput(['maxlength' => true]) ?>
          </div>
          <div class= 'col-md-6'>
           <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
          </div>
          <div class= 'col-md-2'>
           <?= $form->field($model, 'indemnizacion')->textInput() ?>
          </div>
            <div class= 'col-md-2'>
           <?= $form->field($model, 'bonificacion')->textInput() ?>
          </div>
         </div>
        <div class="form-group text-center">
            <?= Html::submitButton('Registrar Datos', ['class' => 'btn btn-success']) ?>
        </div>
     
        <?php ActiveForm::end(); ?>
   </div>
 </div>
 
</div>
