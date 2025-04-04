<?php

use app\models\User;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhComedor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-comedor-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_sys_rrhh_comedor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alimento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'h_desde')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'h_hasta')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'tiempo_descuento')->textInput(['maxlength' => true]) ?>

   <?php  if(User::hasRole('JEFECOSTOS') || User::hasRole('supcostos')): ?>
   
        <?= $form->field($model, 'valor')->widget(NumberControl::classname(), [
                       'displayOptions' =>  [
                           'placeholder' => 'Valor',
                           'class'=> 'form-control'
                       ],
                       'maskedInputOptions' => [
                           'groupSeparator' => '',
                           'digits' => 2,
                           'rightAlign' => false
                       ]
                   ]);
             ?>
             
     <?php  endif;?>
    <div class="form-group">
        <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
