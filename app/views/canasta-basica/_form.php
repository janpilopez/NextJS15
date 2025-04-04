<?php

use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmHistorialSueldo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-canasta-basica-form">
 <div class = 'panel panel-default'>
   <div class = 'panel-body'>
    <?php $form = ActiveForm::begin(); ?>
    	<?=  $form->field($model, 'anio')->widget(NumberControl::classname(), [
                       'displayOptions' =>  [
                           'placeholder' => '',
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
        <?=  $form->field($model, 'canasta_basica')->widget(NumberControl::classname(), [
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
        <div class="form-group">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
   </div>
 </div>
</div>
