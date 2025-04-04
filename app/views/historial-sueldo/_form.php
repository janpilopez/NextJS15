<?php

use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmHistorialSueldo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-historial-sueldo-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    	<?=  $form->field($model, 'anio')->widget(NumberControl::classname(), [
                       'displayOptions' =>  [
                           'placeholder' => '',
                           'disabled'=> $model->autorizado,
                           'class'=> 'form-control'
                       ],
                       'maskedInputOptions' => [
                           'groupSeparator' => '',
                           'digits' => 0,
                           'min' => 2000,
                           'max' => 2060,
                           'rightAlign' => false
                       ]
         ]);?>
        <?=  $form->field($model, 'sueldo_sectorial')->widget(NumberControl::classname(), [
                       'displayOptions' =>  [
                           'placeholder' => '',
                           'class'=> 'form-control'
                       ],
                       'maskedInputOptions' => [
                           'groupSeparator' => '',
                           'digits' => 2,
                           'rightAlign' => false
                       ]
        ]);?>
        <?=  $form->field($model, 'sueldo_basico')->widget(NumberControl::classname(), [
                       'displayOptions' =>  [
                           'placeholder' => '',
                           'class'=> 'form-control'
                       ],
                       'maskedInputOptions' => [
                           'groupSeparator' => '',
                           'digits' => 2,
                           'rightAlign' => false
                       ]
        ]);?>
        <?php if (!$model->autorizado):?>
        <div class="form-group">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>
        <?php endif;?>
    <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
