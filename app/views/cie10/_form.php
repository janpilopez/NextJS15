<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedCie10 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-cie10-form">
  <div class = 'panel panel-default'>  
     	<div class = 'panel-body'>
        
            <?php $form = ActiveForm::begin(); ?>
            
            <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
        
            <div class="form-group">
                <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
            </div>
        
            <?php ActiveForm::end(); ?>
       </div>
   </div>
</div>
