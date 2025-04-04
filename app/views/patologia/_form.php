<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SysMedPatologiaCategoria;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedPatologia */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-patologia-form">
  <div class = 'panel panel-default'>  
     	<div class = 'panel-body'> 

        <?php $form = ActiveForm::begin(); ?>
    
        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        
        <?= $form->field($model, 'id_sys_med_patologia_categoria')->dropDownList(ArrayHelper::map(SysMedPatologiaCategoria::find()->all(), 'id', 'categoria'), ['prompt'=> 'seleccione..' ,'class'=> 'form-control']) ?>
    
        <?= $form->field($model, 'activo')->dropDownList([1=> 'Activo', 0 => 'Inactivo']) ?>
    
        <div class="form-group">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
