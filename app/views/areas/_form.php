<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-areas-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    
     <div class= 'row'>
       <div class= 'col-md-6'>
          <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-3'>
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo' ]) ?>
       </div>
     </div>
     
    <div class="form-group text-left">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
