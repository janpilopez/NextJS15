<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFormaPago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-forma-pago-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
       <div class= 'col-md-2'>
       <?= $form->field($model, 'id_sys_rrhh_forma_pago')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-8'>
        <?= $form->field($model, 'forma_pago')->textInput(['maxlength' => true]) ?>
       </div>
       <div class= 'col-md-2'>
        <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo']) ?>
       </div>
    </div>
     
    <div class="form-group text-center">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    
   </div>
 </div>
</div>
