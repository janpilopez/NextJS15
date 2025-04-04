<?php

use kartik\date\DatePicker;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="sys-rrhh-empleados-permisos-equipos-form">
 <div class = 'panel panel-default'>
     <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class= 'col-md-4'>
                <?= $form->field($model, 'nombre_indicador')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?=  $form->field($model, 'meta')->widget(NumberControl::classname(), [
                        'displayOptions' =>  [
                            'placeholder' => '',
                            'class'=> 'form-control'
                        ],
                        'maskedInputOptions' => [
                            'groupSeparator' => '',
                            'digits' => 0,
                            'min' => 0,
                            'max' => 10000,
                            'rightAlign' => false
                        ]
                ]);?>
            </div>
        </div>
     
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
 </div>
</div>
