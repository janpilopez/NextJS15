<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedPatologiaCategoria */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-patologia-categoria-form">
 	<div class = 'panel panel-default'>  
     	<div class = 'panel-body'>
     	
        <?php $form = ActiveForm::begin(); ?>
      
        <?= $form->field($model, 'categoria')->textInput(['maxlength' => true]) ?>
    
        <?= $form->field($model, 'activo')->dropDownList([1 =>'Activo', 0 => 'Inactivo']) ?>
    
        <div class="form-group">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
        
     </div>
   </div>
</div>
