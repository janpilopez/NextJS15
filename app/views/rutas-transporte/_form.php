<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmRutas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-rutas-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>

     <div class= 'row'>
       <div class= 'col-md-5'>
           <?= $form->field($model, 'ruta')->textInput(['maxlength' => true]) ?>
        </div>
        <div class= 'col-md-2'>
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
        </div>
     </div>
      
    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
 </div>
</div>
